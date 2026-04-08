@if(Access::permission('appwhatsappreport'))
@php
    $teamId = (int) request('team_id');
    $moduleEnabled = function (string $module): bool {
        try {
            $modulePath = base_path("modules/{$module}");
            if (!is_dir($modulePath) || !is_file($modulePath . DIRECTORY_SEPARATOR . 'module.json')) {
                return false;
            }

            $moduleInstance = \Nwidart\Modules\Facades\Module::find($module);

            return $moduleInstance ? $moduleInstance->isEnabled() : false;
        } catch (\Throwable $e) {
            return false;
        }
    };

    $hasProfileInfoModule = $moduleEnabled('AppWhatsAppProfileInfo');
    $hasReportModule = $moduleEnabled('AppWhatsAppReport');
    $hasChatModule = $moduleEnabled('AppWhatsAppChat');
    $hasBulkModule = $moduleEnabled('AppWhatsAppBulk');
    $hasAiSmartReplyModule = $moduleEnabled('AppWhatsAppAISmartReply');
    $hasAutoReplyModule = $moduleEnabled('AppWhatsAppAutoReply');
    $hasChatbotModule = $moduleEnabled('AppWhatsAppChatbot');
    $hasContactsModule = $moduleEnabled('AppWhatsAppContact');
    $hasApiModule = $moduleEnabled('AppWhatsAppApi');

    $hasStatsTable = \Schema::hasTable('whatsapp_stats');
    $hasAccountsTable = \Schema::hasTable('accounts');
    $hasSchedulesTable = \Schema::hasTable('whatsapp_schedules');
    $hasChatbotTable = \Schema::hasTable('whatsapp_chatbot');
    $hasAutoReplyTable = \Schema::hasTable('whatsapp_autoresponder');
    $hasAiReplyTable = \Schema::hasTable('whatsapp_ai_smart_reply');
    $hasChatTable = \Schema::hasTable('whatsapp_chat_conversations');

    $stats = ($teamId && $hasStatsTable) ? DB::table('whatsapp_stats')->where('team_id', $teamId)->first() : null;

    $accountsQuery = ($teamId && $hasAccountsTable)
        ? DB::table('accounts')
            ->where('team_id', $teamId)
            ->where('social_network', 'whatsapp_unofficial')
            ->where('category', 'profile')
            ->where('login_type', 2)
            ->where('status', 1)
        : null;

    $accounts = $accountsQuery ? $accountsQuery->count() : 0;
    $accountRows = $accountsQuery ? $accountsQuery->orderBy('name')->limit(5)->get() : collect();

    $activeBulk = ($teamId && $hasSchedulesTable)
        ? DB::table('whatsapp_schedules')->where('team_id', $teamId)->where('status', 1)->count()
        : 0;
    $activeChatbot = ($teamId && $hasChatbotTable)
        ? DB::table('whatsapp_chatbot')->where('team_id', $teamId)->where('status', 1)->where('run', 1)->count()
        : 0;
    $activeAutoReply = ($teamId && $hasAutoReplyTable)
        ? DB::table('whatsapp_autoresponder')->where('team_id', $teamId)->where('status', 1)->count()
        : 0;
    $activeAi = ($teamId && $hasAiReplyTable)
        ? DB::table('whatsapp_ai_smart_reply')->where('team_id', $teamId)->where('status', 1)->count()
        : 0;

    $monthlyLimit = (int) Access::permission('whatsapp_message_per_month');
    $monthlySent = (int) ($stats->wa_total_sent_by_month ?? 0);
    $monthlyRemaining = $monthlyLimit === -1 ? -1 : max(0, $monthlyLimit - $monthlySent);
    $apiCount = (int) ($stats->wa_api_count ?? 0);
    $bulkSent = (int) ($stats->wa_bulk_sent_count ?? 0);
    $bulkFailed = (int) ($stats->wa_bulk_failed_count ?? 0);
    $chatbotCount = (int) ($stats->wa_chatbot_count ?? 0);
    $autoReplyCount = ($teamId && $hasAutoReplyTable)
        ? (int) DB::table('whatsapp_autoresponder')->where('team_id', $teamId)->sum('sent')
        : 0;
    $totalSent = (int) ($stats->wa_total_sent ?? 0);
    $chatCount = ($teamId && $hasChatTable) ? DB::table('whatsapp_chat_conversations')->where('team_id', $teamId)->count() : 0;
    $activeAutomations = $activeBulk + $activeChatbot + $activeAutoReply + $activeAi;
    $quotaPercent = $monthlyLimit > 0 ? min(100, round(($monthlySent / max(1, $monthlyLimit)) * 100, 1)) : 0;

    $featureChips = collect([
        ['label' => __('Profile Info'), 'enabled' => (bool) Access::permission('appwhatsappprofileinfo'), 'installed' => $hasProfileInfoModule],
        ['label' => __('Reports'), 'enabled' => (bool) Access::permission('appwhatsappreport'), 'installed' => $hasReportModule],
        ['label' => __('Live Chat'), 'enabled' => (bool) Access::permission('appwhatsappchat'), 'installed' => $hasChatModule],
        ['label' => __('Bulk'), 'enabled' => (bool) Access::permission('appwhatsappbulk'), 'installed' => $hasBulkModule],
        ['label' => __('AI Reply'), 'enabled' => (bool) Access::permission('appwhatsappaismartreply'), 'installed' => $hasAiSmartReplyModule],
        ['label' => __('Auto Reply'), 'enabled' => (bool) Access::permission('appwhatsappautoreply'), 'installed' => $hasAutoReplyModule],
        ['label' => __('Chatbot'), 'enabled' => (bool) Access::permission('appwhatsappchatbot'), 'installed' => $hasChatbotModule],
        ['label' => __('Contacts'), 'enabled' => (bool) Access::permission('appwhatsappcontact'), 'installed' => $hasContactsModule],
        ['label' => __('REST API'), 'enabled' => (bool) Access::permission('appwhatsappapi'), 'installed' => $hasApiModule],
    ])->where('installed', true)->values();
    $enabledFeatureCount = $featureChips->filter(fn ($item) => $item['enabled'])->count();

    $limits = [
        ['label' => __('Monthly messages'), 'value' => $monthlyLimit],
        ['label' => __('Chatbot items'), 'value' => (int) Access::permission('whatsapp_chatbot_item_limit')],
        ['label' => __('Contact groups'), 'value' => (int) Access::permission('whatsapp_bulk_max_contact_group')],
        ['label' => __('Phones / group'), 'value' => (int) Access::permission('whatsapp_bulk_max_phone_numbers')],
    ];

    $activityRows = collect([
        ['label' => __('Bulk Campaign'), 'sent' => $bulkSent, 'meta' => __('Failed: :count', ['count' => number_format($bulkFailed)]), 'icon' => 'fa-paper-plane', 'class' => 'info', 'installed' => $hasBulkModule],
        ['label' => __('REST API'), 'sent' => $apiCount, 'meta' => __('Usage count'), 'icon' => 'fa-code', 'class' => 'primary', 'installed' => $hasApiModule],
        ['label' => __('Chatbot'), 'sent' => $chatbotCount, 'meta' => __('Sent replies'), 'icon' => 'fa-robot', 'class' => 'warning', 'installed' => $hasChatbotModule],
        ['label' => __('Auto Reply'), 'sent' => $autoReplyCount, 'meta' => __('Sent replies'), 'icon' => 'fa-reply', 'class' => 'success', 'installed' => $hasAutoReplyModule],
        ['label' => __('AI Smart Reply'), 'sent' => $activeAi, 'meta' => __('Active rules'), 'icon' => 'fa-sparkles', 'class' => 'danger', 'installed' => $hasAiSmartReplyModule],
    ])->where('installed', true)->values();

    $summaryCards = collect([
        ['label' => __('Connected profiles'), 'value' => number_format($accounts), 'meta' => __('Active WhatsApp Unofficial accounts'), 'icon' => 'fa-mobile-screen-button', 'class' => 'success'],
        ['label' => __('Messages left this month'), 'value' => $monthlyRemaining === -1 ? __('Unlimited') : number_format($monthlyRemaining), 'meta' => __('Used: :count', ['count' => number_format($monthlySent)]), 'icon' => 'fa-messages-dollar', 'class' => 'primary'],
        ['label' => __('Active automations'), 'value' => number_format($activeAutomations), 'meta' => __('AI: :ai | Chatbot: :chatbot | Auto: :auto | Bulk: :bulk', ['ai' => number_format($activeAi), 'chatbot' => number_format($activeChatbot), 'auto' => number_format($activeAutoReply), 'bulk' => number_format($activeBulk)]), 'icon' => 'fa-bolt', 'class' => 'warning'],
        ['label' => __('Delivery totals'), 'value' => number_format($bulkSent), 'meta' => __('Failed: :count | API: :api', ['count' => number_format($bulkFailed), 'api' => number_format($apiCount)]), 'icon' => 'fa-paper-plane', 'class' => 'info'],
        ['label' => __('Total messages sent'), 'value' => number_format($totalSent), 'meta' => __('Lifetime total'), 'icon' => 'fa-chart-line-up', 'class' => 'danger'],
        ['label' => __('Live conversations'), 'value' => number_format($chatCount), 'meta' => __('Tracked in Live Chat'), 'icon' => 'fa-comments', 'class' => 'success', 'installed' => $hasChatModule],
    ])->filter(fn ($item) => ($item['installed'] ?? true) === true)->values();
@endphp

<div class="card border-gray-300 shadow-none overflow-hidden mb-4">
    <div class="card-header border-0 p-4 pb-0 d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between gap-12">
        <div class="d-flex flex-column gap-4">
            <div class="fw-6 fs-18 text-gray-900">{{ __('WhatsApp Report') }}</div>
            <div class="fs-13 text-gray-600">{{ __('Monitor unofficial WhatsApp profiles, message quota, automation activity, current limits, and top account usage from one dashboard card.') }}</div>
        </div>
        <a href="{{ route('app.whatsappreport.index', ['team_id' => $teamId ?: null]) }}" class="btn btn-outline btn-dark btn-sm">
            <i class="fa-light fa-chart-column me-1"></i>{{ __('Open report') }}
        </a>
    </div>

    <div class="card-body p-4">
        <div class="row g-3 mb-4">
            @foreach($summaryCards as $card)
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="border rounded-3 p-3 h-100 d-flex align-items-center justify-content-between gap-12">
                        <div class="min-w-0">
                            <div class="fs-12 text-gray-500">{{ $card['label'] }}</div>
                            <div class="fw-7 fs-22 text-gray-900">{{ $card['value'] }}</div>
                            <div class="fs-11 text-gray-500">{{ $card['meta'] }}</div>
                        </div>
                        <div class="size-42 rounded-3 bg-{{ $card['class'] }}-100 text-{{ $card['class'] }} d-flex align-items-center justify-content-center fs-20 flex-shrink-0">
                            <i class="fa-light {{ $card['icon'] }}"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row g-4">
            <div class="col-12 col-xxl-5">
                <div class="border rounded-3 p-3 h-100">
                    <div class="d-flex align-items-center justify-content-between gap-12 mb-3">
                        <div class="fw-6 text-gray-900">{{ __('Feature activity') }}</div>
                        <span class="badge badge-light-primary text-primary">{{ __('This month') }}</span>
                    </div>
                    <div class="row g-2">
                        @foreach($activityRows as $item)
                            <div class="col-12 col-md-6">
                                <div class="border rounded-3 p-3 h-100 d-flex align-items-center justify-content-between gap-12">
                                    <div class="min-w-0">
                                        <div class="fw-5 text-gray-900">{{ $item['label'] }}</div>
                                        <div class="fs-11 text-gray-500">{{ $item['meta'] }}</div>
                                    </div>
                                    <div class="text-end flex-shrink-0">
                                        <div class="fw-7 fs-18 text-gray-900">{{ number_format($item['sent']) }}</div>
                                        <div class="size-34 rounded-3 bg-{{ $item['class'] }}-100 text-{{ $item['class'] }} d-inline-flex align-items-center justify-content-center fs-16 mt-2">
                                            <i class="fa-light {{ $item['icon'] }}"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-12 col-xxl-4">
                <div class="border rounded-3 p-3 h-100">
                    <div class="fw-6 text-gray-900 mb-3">{{ __('Enabled features') }}</div>
                    <div class="d-flex flex-wrap gap-8 mb-4">
                        @foreach($featureChips as $item)
                            <span class="badge {{ $item['enabled'] ? 'badge-light-success text-success' : 'badge-light-danger text-danger' }}">{{ $item['label'] }}</span>
                        @endforeach
                    </div>

                    <div class="d-flex align-items-center justify-content-between gap-12 mb-3">
                        <div class="fw-6 text-gray-900">{{ __('Current limits') }}</div>
                        <span class="badge badge-light-primary text-primary">{{ number_format($enabledFeatureCount) }} {{ __('features enabled') }}</span>
                    </div>

                    <div class="row g-2 mb-3">
                        @foreach($limits as $item)
                            <div class="col-6">
                                <div class="bg-light rounded-3 px-3 py-2 h-100">
                                    <div class="fs-11 text-gray-500 mb-1">{{ $item['label'] }}</div>
                                    <div class="fw-6 text-gray-900">{{ (int) $item['value'] === -1 ? __('Unlimited') : number_format((int) $item['value']) }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="border rounded-3 p-3">
                        <div class="d-flex align-items-center justify-content-between gap-12 mb-2">
                            <div class="fw-5 text-gray-900">{{ __('Monthly quota usage') }}</div>
                            <div class="fw-6 text-gray-900">{{ $monthlyLimit === -1 ? __('Unlimited') : ($quotaPercent . '%') }}</div>
                        </div>
                        <div class="progress h-8 bg-light-primary mb-2">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $monthlyLimit === -1 ? 0 : $quotaPercent }}%"></div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between fs-11 text-gray-500">
                            <span>{{ __('Used: :count', ['count' => number_format($monthlySent)]) }}</span>
                            <span>{{ $monthlyRemaining === -1 ? __('Unlimited left') : __(':count left', ['count' => number_format($monthlyRemaining)]) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xxl-3">
                <div class="border rounded-3 p-3 h-100">
                    <div class="d-flex align-items-center justify-content-between gap-12 mb-3">
                        <div class="fw-6 text-gray-900">{{ __('Top accounts') }}</div>
                        <span class="fs-11 text-gray-500">{{ __('Connected profiles') }}</span>
                    </div>
                    <div class="d-flex flex-column gap-10">
                        @forelse($accountRows as $account)
                            <div class="d-flex align-items-center gap-10 border rounded-3 p-2">
                                <div class="size-42 rounded-circle overflow-hidden border flex-shrink-0 bg-light d-flex align-items-center justify-content-center">
                                    @if(!empty($account->avatar))
                                        <img src="{{ Media::url($account->avatar) }}" class="wp-100 hp-100 object-fit-cover" alt="{{ $account->name }}">
                                    @else
                                        <span class="fw-6 text-primary">{{ mb_substr($account->name ?: 'WA', 0, 2) }}</span>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-fill">
                                    <div class="fw-6 text-gray-900 text-truncate">{{ $account->name }}</div>
                                    <div class="fs-11 text-gray-500 text-truncate">{{ $account->username ?: __('WhatsApp profile') }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-gray-500 fs-12">{{ __('No WhatsApp profiles found.') }}</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
