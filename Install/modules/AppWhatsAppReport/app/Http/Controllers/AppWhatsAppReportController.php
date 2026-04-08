<?php

namespace Modules\AppWhatsAppReport\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\CarbonPeriod;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Nwidart\Modules\Facades\Module;

class AppWhatsAppReportController extends Controller
{
    protected array $featureMap = [
        'auto_reply' => [
            'label' => 'Auto Reply',
            'table' => 'whatsapp_autoresponder',
            'active_callback' => 'activeAutoReply',
            'account_filter' => 'instance_id',
        ],
        'chatbot' => [
            'label' => 'Chatbot',
            'table' => 'whatsapp_chatbot',
            'active_callback' => 'activeChatbot',
            'account_filter' => 'instance_id',
        ],
        'ai_smart_reply' => [
            'label' => 'AI Smart Reply',
            'table' => 'whatsapp_ai_smart_reply',
            'active_callback' => 'activeDefault',
            'account_filter' => 'instance_id',
        ],
        'bulk' => [
            'label' => 'Bulk Campaign',
            'table' => 'whatsapp_schedules',
            'active_callback' => 'activeBulk',
            'account_filter' => 'accounts',
        ],
        'api' => [
            'label' => 'REST API',
            'table' => null,
            'active_callback' => 'activeApi',
            'account_filter' => null,
        ],
    ];

    protected array $moduleMap = [
        'profile_info' => 'AppWhatsAppProfileInfo',
        'reports' => 'AppWhatsAppReport',
        'bulk' => 'AppWhatsAppBulk',
        'ai_smart_reply' => 'AppWhatsAppAISmartReply',
        'auto_reply' => 'AppWhatsAppAutoReply',
        'chatbot' => 'AppWhatsAppChatbot',
        'contacts' => 'AppWhatsAppContact',
        'participants_export' => 'AppWhatsAppParticipantsExport',
        'api' => 'AppWhatsAppApi',
    ];

    protected array $permissionMap = [
        'profile_info' => 'appwhatsappprofileinfo',
        'reports' => 'appwhatsappreport',
        'bulk' => 'appwhatsappbulk',
        'ai_smart_reply' => 'appwhatsappaismartreply',
        'auto_reply' => 'appwhatsappautoreply',
        'chatbot' => 'appwhatsappchatbot',
        'contacts' => 'appwhatsappcontact',
        'participants_export' => 'appwhatsappparticipantsexport',
        'api' => 'appwhatsappapi',
    ];

    public function index(Request $request)
    {
        $teamId = (int) $request->team_id;
        [$startDate, $endDate] = \Core::parseDateRange($request, 30);

        $startTs = $startDate->copy()->startOfDay()->timestamp;
        $endTs = $endDate->copy()->endOfDay()->timestamp;
        $selectedAccount = trim((string) $request->input('account', ''));
        $selectedFeature = trim((string) $request->input('feature', 'all'));

        $accounts = $this->getAccounts($teamId);
        $featureKeys = $this->resolveFeatureKeys($selectedFeature);
        $selectedAccountRow = $selectedAccount !== '' ? $accounts->firstWhere('id_secure', $selectedAccount) : null;
        $accountToken = $selectedAccountRow ? $selectedAccountRow->token : null;
        $accountId = $selectedAccountRow ? (int) $selectedAccountRow->id : null;
        $accountsForTable = $selectedAccount !== ''
            ? $accounts->where('id_secure', $selectedAccount)->values()
            : $accounts->values();

        $featureRows = collect($featureKeys)->map(function (string $feature) use ($teamId, $accountToken, $accountId, $startTs, $endTs) {
            $metrics = $this->featureMetrics($feature, $teamId, $accountToken, $accountId, $startTs, $endTs);

            return (object) array_merge([
                'key' => $feature,
                'label' => __($this->featureMap[$feature]['label']),
            ], $metrics);
        })->values();

        $summary = [
            'records' => (int) $featureRows->sum('records'),
            'active' => (int) $featureRows->sum('active'),
            'sent' => (int) $featureRows->sum('sent'),
            'failed' => (int) $featureRows->sum('failed'),
            'accounts' => (int) $accountsForTable->count(),
        ];

        $quota = $this->getQuotaSummary($teamId);
        $permissionsInfo = $this->getPermissionsSummary();
        $accountRows = $accountsForTable->map(function ($account) use ($teamId, $featureKeys, $startTs, $endTs) {
            $rows = collect($featureKeys)->mapWithKeys(function (string $feature) use ($teamId, $account, $startTs, $endTs) {
                return [$feature => $this->featureMetrics($feature, $teamId, $account->token, (int) $account->id, $startTs, $endTs)];
            });

            return (object) [
                'id_secure' => $account->id_secure,
                'name' => $account->name,
                'username' => $account->username,
                'avatar' => $account->avatar,
                'token' => $account->token,
                'records' => (int) $rows->sum('records'),
                'active' => (int) $rows->sum('active'),
                'sent' => (int) $rows->sum('sent'),
                'failed' => (int) $rows->sum('failed'),
                'features' => $rows,
            ];
        })->sortByDesc('sent')->values();

        $featureOptions = ['all' => __('All features')];
        foreach ($this->availableFeatureMap() as $feature => $config) {
            $featureOptions[$feature] = __($config['label']);
        }

        $chart = [
            'categories' => $featureRows->pluck('label')->values()->all(),
            'series' => [
                [
                    'name' => __('Sent'),
                    'color' => '#22c55e',
                    'data' => $featureRows->pluck('sent')->map(fn ($value) => (int) $value)->values()->all(),
                ],
                [
                    'name' => __('Failed'),
                    'color' => '#ef4444',
                    'data' => $featureRows->pluck('failed')->map(fn ($value) => (int) $value)->values()->all(),
                ],
            ],
        ];

        $trendChart = $this->buildTrendChart($featureKeys, $teamId, $accountToken, $accountId, $startDate, $endDate);

        return view('appwhatsappreport::index', compact(
            'accounts',
            'accountRows',
            'chart',
            'endDate',
            'featureOptions',
            'featureRows',
            'permissionsInfo',
            'quota',
            'selectedAccount',
            'selectedFeature',
            'startDate',
            'summary',
            'trendChart'
        ));
    }

    protected function getQuotaSummary(int $teamId): array
    {
        $stats = DB::table('whatsapp_stats')->where('team_id', $teamId)->first();
        $monthlyLimit = (int) \Access::permission('whatsapp_message_per_month');
        $sentByMonth = (int) ($stats->wa_total_sent_by_month ?? 0);

        return [
            'limit' => $monthlyLimit,
            'sent_by_month' => $sentByMonth,
            'remaining' => $monthlyLimit === -1 ? -1 : max(0, $monthlyLimit - $sentByMonth),
            'is_unlimited' => $monthlyLimit === -1,
        ];
    }

    protected function getPermissionsSummary(): array
    {
        $hasProfileInfoModule = $this->featureAvailable('profile_info');
        $hasReportModule = $this->featureAvailable('reports');
        $hasBulkModule = $this->featureAvailable('bulk');
        $hasAiSmartReplyModule = $this->featureAvailable('ai_smart_reply');
        $hasAutoReplyModule = $this->featureAvailable('auto_reply');
        $hasChatbotModule = $this->featureAvailable('chatbot');
        $hasContactsModule = $this->featureAvailable('contacts');
        $hasParticipantsExportModule = $this->featureAvailable('participants_export');
        $hasApiModule = $this->featureAvailable('api');

        return [
            'enabled' => (bool) \Access::permission('appchannelwhatsappunofficial'),
            'features' => collect([
                $hasProfileInfoModule ? ['label' => __('Profile Info'), 'enabled' => (bool) \Access::permission('appwhatsappprofileinfo')] : null,
                $hasReportModule ? ['label' => __('Reports'), 'enabled' => (bool) \Access::permission('appwhatsappreport')] : null,
                $hasBulkModule ? ['label' => __('Bulk campaigns'), 'enabled' => (bool) \Access::permission('appwhatsappbulk')] : null,
                $hasAiSmartReplyModule ? ['label' => __('AI Smart Reply'), 'enabled' => (bool) \Access::permission('appwhatsappaismartreply')] : null,
                $hasAutoReplyModule ? ['label' => __('Auto Reply'), 'enabled' => (bool) \Access::permission('appwhatsappautoreply')] : null,
                $hasChatbotModule ? ['label' => __('Chatbot'), 'enabled' => (bool) \Access::permission('appwhatsappchatbot')] : null,
                $hasContactsModule ? ['label' => __('Contacts'), 'enabled' => (bool) \Access::permission('appwhatsappcontact')] : null,
                $hasParticipantsExportModule ? ['label' => __('Export participants'), 'enabled' => (bool) \Access::permission('appwhatsappparticipantsexport')] : null,
                $hasApiModule ? ['label' => __('REST API'), 'enabled' => (bool) \Access::permission('appwhatsappapi')] : null,
            ])->filter()->values()->all(),
            'limits' => [
                ['label' => __('Monthly messages'), 'value' => (int) \Access::permission('whatsapp_message_per_month')],
                ['label' => __('Chatbot item limit'), 'value' => (int) \Access::permission('whatsapp_chatbot_item_limit')],
                ['label' => __('Maximum contact groups'), 'value' => (int) \Access::permission('whatsapp_bulk_max_contact_group')],
                ['label' => __('Max numbers per group'), 'value' => (int) \Access::permission('whatsapp_bulk_max_phone_numbers')],
            ],
        ];
    }

    protected function getAccounts(int $teamId): Collection
    {
        return DB::table('accounts')
            ->where('team_id', $teamId)
            ->where('social_network', 'whatsapp_unofficial')
            ->where('category', 'profile')
            ->where('login_type', 2)
            ->where('status', 1)
            ->orderBy('name')
            ->get();
    }

    protected function resolveFeatureKeys(string $selectedFeature): array
    {
        $availableFeatures = $this->availableFeatureMap();

        if ($selectedFeature !== '' && $selectedFeature !== 'all' && isset($availableFeatures[$selectedFeature])) {
            return [$selectedFeature];
        }

        return array_keys($availableFeatures);
    }

    protected function featureMetrics(string $feature, int $teamId, ?string $instanceId, ?int $accountId, int $startTs, int $endTs): array
    {
        if ($feature === 'api') {
            return $this->apiMetrics($teamId, $instanceId, $accountId);
        }

        $config = $this->featureMap[$feature];
        $query = DB::table($config['table'])->where('team_id', $teamId);

        $this->applyAccountFilter($query, $config['account_filter'], $instanceId, $accountId);
        $this->applyDateFilter($query, $startTs, $endTs);

        $records = (int) (clone $query)->count();
        $sent = (int) ((clone $query)->sum('sent') ?? 0);
        $failed = (int) ((clone $query)->sum('failed') ?? 0);
        $active = (int) $this->{$config['active_callback']}((clone $query));

        return [
            'records' => $records,
            'active' => $active,
            'sent' => $sent,
            'failed' => $failed,
        ];
    }

    protected function applyDateFilter(Builder $query, int $startTs, int $endTs): void
    {
        $query->whereRaw('COALESCE(NULLIF(changed, 0), NULLIF(created, 0), 0) BETWEEN ? AND ?', [$startTs, $endTs]);
    }

    protected function activeDefault(Builder $query): int
    {
        return (int) $query->where('status', 1)->count();
    }

    protected function activeAutoReply(Builder $query): int
    {
        return (int) $query->where('status', 1)->count();
    }

    protected function activeChatbot(Builder $query): int
    {
        return (int) $query->where('status', 1)->where('run', 1)->count();
    }

    protected function activeBulk(Builder $query): int
    {
        return (int) $query->where('status', 1)->count();
    }

    protected function activeApi(): int
    {
        return $this->featureAvailable('api')
            ? (int) \Access::permission('appwhatsappapi')
            : 0;
    }

    protected function apiMetrics(int $teamId, ?string $instanceId, ?int $accountId): array
    {
        if (!$this->featureAvailable('api')) {
            return [
                'records' => 0,
                'active' => 0,
                'sent' => 0,
                'failed' => 0,
            ];
        }

        $stats = DB::table('whatsapp_stats')->where('team_id', $teamId)->first();
        $sent = ($instanceId !== null || $accountId !== null) ? 0 : (int) ($stats->wa_api_count ?? 0);

        return [
            'records' => $sent,
            'active' => $this->activeApi(),
            'sent' => $sent,
            'failed' => 0,
        ];
    }

    protected function availableFeatureMap(): array
    {
        return collect($this->featureMap)
            ->filter(fn ($config, string $feature) => $this->featureAvailable($feature))
            ->all();
    }

    protected function featureAvailable(string $feature): bool
    {
        $module = $this->moduleMap[$feature] ?? null;
        $permissionKey = $this->permissionMap[$feature] ?? null;

        if ($module !== null && !$this->moduleInstalled($module)) {
            return false;
        }

        if ($permissionKey !== null && !\Access::canAccess($permissionKey, false)) {
            return false;
        }

        return true;
    }

    protected function moduleInstalled(string $module): bool
    {
        try {
            $modulePath = base_path("modules/{$module}");
            if (!is_dir($modulePath) || !is_file($modulePath . DIRECTORY_SEPARATOR . 'module.json')) {
                return false;
            }

            $moduleInstance = Module::find($module);

            return $moduleInstance ? $moduleInstance->isEnabled() : false;
        } catch (\Throwable $e) {
            return false;
        }
    }

    protected function buildTrendChart(array $featureKeys, int $teamId, ?string $instanceId, ?int $accountId, $startDate, $endDate): array
    {
        $categories = collect(CarbonPeriod::create($startDate->copy()->startOfDay(), $endDate->copy()->startOfDay()))
            ->map(fn ($date) => $date->format('Y-m-d'))
            ->values();

        $sentMap = $categories->mapWithKeys(fn ($date) => [$date => 0])->all();
        $failedMap = $categories->mapWithKeys(fn ($date) => [$date => 0])->all();

        foreach ($featureKeys as $feature) {
            $table = $this->featureMap[$feature]['table'];
            if ($table === null) {
                continue;
            }


            $query = DB::table($table)
                ->selectRaw("DATE(FROM_UNIXTIME(COALESCE(NULLIF(changed, 0), NULLIF(created, 0), 0))) as report_date")
                ->selectRaw("SUM(COALESCE(sent, 0)) as total_sent")
                ->selectRaw("SUM(COALESCE(failed, 0)) as total_failed")
                ->where('team_id', $teamId);

            $this->applyAccountFilter($query, $this->featureMap[$feature]['account_filter'], $instanceId, $accountId);
            $this->applyDateFilter($query, $startDate->copy()->startOfDay()->timestamp, $endDate->copy()->endOfDay()->timestamp);

            $rows = $query
                ->groupBy('report_date')
                ->orderBy('report_date')
                ->get();

            foreach ($rows as $row) {
                $date = (string) $row->report_date;
                if (!array_key_exists($date, $sentMap)) {
                    continue;
                }

                $sentMap[$date] += (int) ($row->total_sent ?? 0);
                $failedMap[$date] += (int) ($row->total_failed ?? 0);
            }
        }

        return [
            'categories' => $categories->map(fn ($date) => \Carbon\Carbon::parse($date)->format('M d'))->all(),
            'series' => [
                [
                    'name' => __('Sent'),
                    'type' => 'areaspline',
                    'data' => array_values($sentMap),
                ],
                [
                    'name' => __('Failed'),
                    'color' => '#ef4444',
                    'type' => 'line',
                    'data' => array_values($failedMap),
                ],
            ],
        ];
    }

    protected function applyAccountFilter(Builder $query, ?string $accountFilter, ?string $instanceId, ?int $accountId): void
    {
        if ($accountFilter === 'instance_id' && $instanceId !== null) {
            $query->where('instance_id', $instanceId);
            return;
        }

        if ($accountFilter === 'accounts' && $accountId !== null) {
            $query->where(function ($sub) use ($accountId) {
                $sub->whereRaw('JSON_CONTAINS(accounts, ?)', ['[' . $accountId . ']'])
                    ->orWhere('accounts', '=', '[' . $accountId . ']')
                    ->orWhere('accounts', 'like', '[' . $accountId . ',%')
                    ->orWhere('accounts', 'like', '%,' . $accountId . ',%')
                    ->orWhere('accounts', 'like', '%,' . $accountId . ']');
            });
        }
    }
}







