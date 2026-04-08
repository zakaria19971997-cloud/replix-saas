<?php

namespace Modules\AdminMarketplace\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\AdminAddons\Models\Addon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AdminMarketplaceController extends Controller
{
    protected const API_BASE_URL = 'https://replix.app/api/marketplace/';

    public function addons(Request $request)
    {
        $addons = Addon::orderByDesc('is_main')
        ->orderByDesc('id')
        ->paginate(20);

        $remoteProducts = [];
        $response = Http::withoutVerifying()->get(self::API_BASE_URL . 'all-products');

        if ($response->ok() && isset($response['data'])) {
            foreach ($response['data'] as $item) {
                $remoteProducts[$item['product_id']] = [
                    'version'     => $item['version'],
                    'name'        => $item['name'] ?? '',
                    'description' => $item['description'] ?? '',
                    'thumbnail'   => $item['thumbnail'] ?? null,
                ];
            }
        }

        $addons->getCollection()->transform(function($addon) use ($remoteProducts) {
            $module = \Module::find($addon->module_name??'');

            if ($module) {
                $menu = $module->get('menu');
                $addon->version         = $addon->version != "" ? $addon->version : $menu['version'] ?? null;
                $addon->uri         = $menu['uri'] ?? null;
                $addon->name        = $menu['name'] ?? \Str::headline($addon->module_name);
                $addon->color       = $menu['color'] ?? '#000';
                $addon->icon        = $menu['icon'] ?? 'fa-light fa-puzzle';
                $addon->description = $menu['description'] ?? __('Powerful, plug-and-play modules to extend your website with new features, tools, and integrations—easy to install and manage for everyone.');
            } else {
                $addon->uri         = "";
                $addon->name        = \Str::headline($addon->module_name);
                $addon->color       = '#000';
                $addon->icon        = 'fa-light fa-puzzle';
                $addon->description = __('Powerful, plug-and-play modules to extend your website with new features, tools, and integrations—easy to install and manage for everyone.');
            }

            $addon->has_update = false;
            if (isset($remoteProducts[$addon->product_id])) {
                $remoteVersion = $remoteProducts[$addon->product_id]['version'];
                if (version_compare($remoteVersion, $addon->version, '>')) {
                    $addon->has_update = true;
                    $addon->latest_version = $remoteVersion;
                }

                $addon->name        = $remoteProducts[$addon->product_id]['name'] ?? $addon->name;
                $addon->description = $remoteProducts[$addon->product_id]['description'] ?? $addon->description;
                $addon->thumbnail   = $remoteProducts[$addon->product_id]['thumbnail'] ?? null;
            }

            return $addon;
        });

        return view(module('key') . '::addons', compact('addons'));
    }

    public function index(Request $request)
    {
        $page    = $request->get('page', 1);
        $perPage = $request->get('per_page', 30);

        // Get list of modules from marketplace API
        $response = Http::withoutVerifying()->get(self::API_BASE_URL . 'products', [
            'page'     => $page,
            'per_page' => $perPage,
            'search'   => $request->get('search'),
        ]);

        if (!$response->ok() || !isset($response['data'], $response['meta'])) {
            return back()->with('error', __('Unable to load Marketplace data.'));
        }

        // Get locally installed addons
        $installedAddons = Addon::where('source', 1)->get()->keyBy('product_id');

        // Map status for each module
        $data = collect($response['data'])->map(function ($item) use ($installedAddons) {
            $addon = $installedAddons[$item['id']] ?? null;

            $item['installed'] = $addon ? true : false;
            $item['installed_version'] = $addon->version ?? null;
            $item['addon_status'] = $addon->status ?? null; // 1 = active, 0 = deactive

            if ($addon && $addon->status == 1) {
                $item['has_update'] = version_compare($item['version'], $addon->version, '>');
            } else {
                $item['has_update'] = false;
            }

            return $item;
        });

        $meta = $response['meta'];

        $keepQueryKeys = ['search', 'page', 'per_page', 'filter']; 
        $query = collect($request->query())->only($keepQueryKeys)->toArray();
        $modules = new LengthAwarePaginator(
            $data,
            $meta['total'],
            $meta['per_page'],
            $meta['current_page'],
            [
                'path' => $request->url(), 
                'query' => $query
            ]
        );

        return view(module('key') . '::index', compact('modules'));
    }

    public function detail($slug)
    {
        $response = Http::withoutVerifying()->get(self::API_BASE_URL . 'product-detail/' . $slug);

        if (!$response->ok() || empty($response['product'])) {
            return redirect()->route('admin.marketplace.index')->with('error', 'Module not found.');
        }

        $product   = $response['product'];
        $faqs      = $response['faqs'] ?? [];
        $support   = $response['support'] ?? [];
        $changelog = $response['changelog'] ?? [];

        $addon = Addon::where('product_id', $product['id'])->where('source', 1)->first();

        $installed         = $addon ? true : false;
        $installedStatus   = $addon->status ?? null;   // 1 = active, 0 = deactive
        $installedVersion  = $addon->version ?? null;
        $hasUpdate         = $addon && $addon->status == 1
            ? version_compare($product['version'], $addon->version, '>')
            : false;

        return view(module('key') . '::show', compact(
            'product', 'faqs', 'support', 'changelog',
            'installed', 'installedStatus', 'installedVersion', 'hasUpdate'
        ));
    }

    public function install(Request $request){
        ms([
            "status" => 1,
            "data" => view(module('key') . '::install')->render()
        ]);
    }

    public function doInstall(Request $request)
    {
        return response()->json([
            'status'  => 0,
            'message' => __('Marketplace install is disabled. Please use the ZIP upload feature to install modules.'),
        ]);
    }

    public function doInstallZip(Request $request)
    {
        $request->validate([
            'module_zip' => 'required|file|mimes:zip',
        ]);

        // 1. Save the uploaded zip file to a temporary directory
        $zipFile = $request->file('module_zip');
        $tempPath = storage_path('app/tmp/' . uniqid() . '.zip');
        \File::ensureDirectoryExists(dirname($tempPath));
        $zipFile->move(dirname($tempPath), basename($tempPath));

        $zip = new \ZipArchive();
        if ($zip->open($tempPath) === true) {
            // 2. Get the root directory name in the zip (module folder)
            $firstEntry = $zip->getNameIndex(0);
            $moduleRoot = trim(explode('/', $firstEntry)[0]);

            // 3. Check for the existence of [moduleRoot]/module.json inside the zip
            $moduleJsonExists = false;
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entryName = $zip->getNameIndex($i);
                if ($entryName === $moduleRoot . '/module.json') {
                    $moduleJsonExists = true;
                    break;
                }
            }
            if (!$moduleJsonExists) {
                $zip->close();
                \File::delete($tempPath);
                return response()->json([
                    'status'  => 0,
                    'message' => __('The uploaded ZIP does not contain a valid module (missing module.json).')
                ]);
            }

            // 4. (Optional) Read meta info from module.json inside the zip
            $moduleMeta = [];
            $moduleJsonIndex = $zip->locateName($moduleRoot . '/module.json');
            if ($moduleJsonIndex !== false) {
                $moduleJsonContent = $zip->getFromIndex($moduleJsonIndex);
                $moduleMeta = json_decode($moduleJsonContent, true);
            }

            // 5. If the module already exists, remove it before installing new
            $extractPath = base_path('modules/' . $moduleRoot);
            if (\File::exists($extractPath)) {
                \File::deleteDirectory($extractPath);
            }

            // 6. Extract the module to the modules/ directory
            $zip->extractTo(base_path('modules'));
            $zip->close();
        } else {
            \File::delete($tempPath);
            return response()->json([
                'status'  => 0,
                'message' => __('Unable to open the ZIP file.'),
            ]);
        }
        \File::delete($tempPath);

        // 7. Save module info to the addons table
        try {
            $addon = Addon::updateOrCreate(
                ['module_name' => $moduleRoot],
                [
                    'id_secure'     => rand_string(),
                    'source'        => 2,
                    'status'        => 1,
                    'is_main'       => 0,
                    'version'       => $moduleMeta['version'] ?? '',
                    'install_path'  => base_path('modules/' . $moduleRoot),
                    'relative_path' => 'modules/' . $moduleRoot,
                    'created'       => now()->timestamp,
                    'changed'       => now()->timestamp,
                ]
            );
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 0,
                'message' => __('Module installed, but failed to save info to the database: :error', ['error' => $e->getMessage()])
            ]);
        }

        return response()->json([
            'status'   => 1,
            'message'  => __('Module ":module" has been installed successfully!', ['module' => $moduleRoot]),
            'id'       => $addon->id_secure,
            'module'   => $moduleRoot,
            'version'  => $moduleMeta['version'] ?? null,
            'meta'     => $moduleMeta,
        ]);
    }

    public function doUpdate(Request $request, $productId)
    {
        return response()->json([
            'status'  => 0,
            'message' => __('Marketplace update is disabled. Please use the ZIP upload feature to update modules.'),
        ]);
    }

    public function destroy(Request $request, $id_secure)
    {
        $addon = Addon::where('id_secure', $id_secure)->first();

        if (!$addon) {
            return response()->json([
                'status'  => 0,
                'message' => __('Addon not found.'),
            ]);
        }

        if ($addon->is_main == 1) {
            return response()->json([
                'status'  => 0,
                'message' => __('You cannot delete main script'),
            ]);
        }

        $moduleName = $addon->module_name??'';

        try {
            // Delete the Laravel module by Artisan command
            if ($module = \Module::find($moduleName)) {
                \Artisan::call('module:delete', [
                    'module' => $moduleName,
                    '--force' => true,
                ]);

                // Remove directory manually if still exists (just in case)
                $modulePath = $module->getPath();
                if (\File::exists($modulePath)) {
                    \File::deleteDirectory($modulePath);
                }
            }
        } catch (\Exception $e) {
            \Log::warning("[Addon Destroy] Failed to delete module '{$moduleName}': " . $e->getMessage());
            return response()->json([
                'status'  => 0,
                'message' => __('Failed to delete the module: :error', ['error' => $e->getMessage()])
            ]);
        }

        $addon->delete();

        return response()->json([
            'status'  => 1,
            'message' => __('Addon has been permanently deleted.')
        ]);
    }

    public function active(Request $request, $id_secure)
    {
        $addon = Addon::where('id_secure', $id_secure)->first();
        if (!$addon) {
            return response()->json(['status' => 0, 'message' => __('Addon not found.')]);
        }

        if ($addon->is_main == 1) {
            return response()->json(['status' => 0, 'message' => __('You cannot active main script')]);
        }

        $moduleName = $addon->module_name ?? '';

        try {
            if ($module = \Module::find($moduleName)) {
                // Enable module
                \Artisan::call('module:enable', ['module' => $moduleName]);

                // Run module migrations nếu có
                \Artisan::call('module:migrate', ['module' => $moduleName]);

                // 4. Migrate từng file trong module + database chính
                try {
                    $migrationsDirs = [];

                    if ($moduleName) {
                        $migrationsDirs[] = base_path("Modules/{$moduleName}/Database/Migrations");
                    }

                    // luôn chạy migrations chính
                    $migrationsDirs[] = base_path("database/migrations");

                    foreach ($migrationsDirs as $dir) {
                        if (!is_dir($dir)) continue;

                        $files = collect(glob($dir . '/*.php'))
                            ->sort()
                            ->values();

                        foreach ($files as $file) {
                            try {
                                $relative = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file);
                                $relative = str_replace('\\', '/', $relative);

                                \Artisan::call('migrate', [
                                    '--path'           => $relative,
                                    '--force'          => true,
                                    '--no-interaction' => true,
                                ]);

                                \Log::info("[{$moduleName}] Migrated: {$relative}");
                            } catch (\Throwable $e) {
                                \Log::warning("[{$moduleName}] Migration failed at {$file}: " . $e->getMessage());
                                continue;
                            }
                        }
                    }

                    \Artisan::call('optimize:clear');
                } catch (\Throwable $e) {
                    \Log::warning("[{$moduleName}] Migration loop failed: " . $e->getMessage());
                }

                // Publish module assets
                \Artisan::call('module:publish', ['module' => $moduleName]);
            }

            $addon->status = 1;
            $addon->save();

            return response()->json(['status' => 1]);
        } catch (\Exception $e) {
            \Log::error("[Addon Activate] {$moduleName} failed → " . $e->getMessage());
            return response()->json([
                'status'  => 0,
                'message' => __('Failed to activate the addon: :error', ['error' => $e->getMessage()]),
            ]);
        }
    }

    public function deactive(Request $request, $id_secure)
    {
        $addon = Addon::where('id_secure', $id_secure)->first();
        if (!$addon) {
            return response()->json([
                'status' => 0,
                'message' => __('Addon not found.'),
            ]);
        }

        if ($addon->is_main == 1) {
            return response()->json([
                'status'  => 0,
                'message' => __('You cannot deactive main script'),
            ]);
        }

        $moduleName = $addon->module_name??'';
        try {
            if ($module = \Module::find($moduleName)) {
                \Artisan::call('module:disable', ['module' => $moduleName]);
            }
            $addon->status = 0;
            $addon->save();

            return response()->json([
                'status' => 1,
            ]);
        } catch (\Exception $e) {
            \Log::warning("[Addon Deactivate] Failed: " . $e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => __('Failed to deactivate the addon: :error', ['error' => $e->getMessage()]),
            ]);
        }
    }

}