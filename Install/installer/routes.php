<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;

// GET: show installer form
$getHandler = function (Request $request) {
    return view('install');
};

// POST: process installation
$postHandler = function (Request $request) {
    $data = json_decode($request->getContent(), true);

    $errors = [];
    if (empty($data['site_name'])) {
        $errors['site_name'] = 'Site name is required';
    }
    if (empty($data['timezone'])) {
        $errors['timezone'] = 'Full name is required';
    }
    if (empty($data['fullname'])) {
        $errors['fullname'] = 'Full name is required';
    }
    if (empty($data['admin_email'])) {
        $errors['admin_email'] = 'Email is required';
    } elseif (!filter_var($data['admin_email'], FILTER_VALIDATE_EMAIL)) {
        $errors['admin_email'] = 'Invalid email format';
    }
    if (empty($data['admin_username'])) {
        $errors['admin_username'] = 'Username is required';
    }
    if (empty($data['admin_password'])) {
        $errors['admin_password'] = 'Password is required';
    }
    if (($data['admin_password'] ?? '') !== ($data['admin_password_confirm'] ?? '')) {
        $errors['admin_password_confirm'] = 'Password confirmation does not match';
    }
    if (empty($data['database_host'])) {
        $errors['database_host'] = 'Database host is required';
    }
    if (empty($data['database_name'])) {
        $errors['database_name'] = 'Database name is required';
    }
    if (empty($data['database_username'])) {
        $errors['database_username'] = 'Database username is required';
    }
    if (!empty($errors)) {
        return new JsonResponse([
            'success' => false,
            'message' => 'Validation error',
            'errors'  => $errors
        ]);
    }

    try {
        $dsn = "mysql:host={$data['database_host']};dbname={$data['database_name']};charset=utf8";
        $pdo = new \PDO($dsn, $data['database_username'], $data['database_password']);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    } catch (\PDOException $e) {
        return new JsonResponse([
            'success' => false,
            'message' => 'Cannot connect to database: ' . $e->getMessage(),
            'errors'  => ['database_host' => 'Database connection failed.']
        ]);
    }

    $site_url = str_replace("installer", "", url('/'));
    $site_url = str_replace("installer/", "", $site_url);
    $newVars = [
        'SITE_TITLE'    => $data['site_name'],
        'APP_NAME'      => $data['site_name'],
        'APP_URL'       => $site_url,
        'APP_TIMEZONE'  => $data['timezone'] ?? 'UTC',
        'APP_INSTALLED' => 'true',
        'DB_HOST'       => $data['database_host'],
        'DB_DATABASE'   => $data['database_name'],
        'DB_USERNAME'   => $data['database_username'],
        'DB_PASSWORD'   => $data['database_password'],
    ];
    updateEnvVars(base_path('.env'), $newVars);

    global $container;
    $appConfig = $container->make('config');

    $appConfig->set('database.default', 'mysql');

    $appConfig->set('database.connections.mysql', [
        'driver' => 'mysql',
        'host' => $data['database_host'],
        'port' => env('DB_PORT', '3306'),
        'database' => $data['database_name'],
        'username' => $data['database_username'],
        'password' => $data['database_password'],
        'unix_socket' => env('DB_SOCKET', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => null,
        'options' => extension_loaded('pdo_mysql') ? array_filter([
            PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
        ]) : [],
    ]);

    try {
        $cachedConfigFile = base_path('bootstrap/cache/config.php');
        if (File::exists($cachedConfigFile)) {
            File::delete($cachedConfigFile);
        }
    } catch (\Exception $e) {
        error_log('Config clear failed: ' . $e->getMessage());
    }

    try {
        $migrator = $container->make('migrator');
        $schema = $container->make('db.schema');
        try {
            $schema->create('migrations', function ($table) {
                $table->increments('id');
                $table->string('migration');
                $table->integer('batch');
            });
        } catch (\PDOException $e) {
            if ($e->getCode() !== '42S01') {
                throw $e;
            }
            error_log('Migration warning: Table `migrations` already exists - ' . $e->getMessage());
        }

        $migrator->run(base_path('database/migrations'), ['--force' => true]);

    } catch (\Exception $e) {
        $errorMessage = 'Migrate failed: ' . $e->getMessage();

        if ($e instanceof \PDOException && $e->getCode() === '42S01') {
            error_log('Migration warning: A table already exists in the database - ' . $e->getMessage());
        } else {
            return new JsonResponse([
                'success' => false,
                'message' => $errorMessage,
                'errors'  => ['migrate' => 'Migrate database failed!']
            ]);
        }
    }

    $result = createAdminUser($pdo, $data);
    if (!$result['success']) {
        return new JsonResponse($result);
    }

    insertPurchaseAddon($pdo, [
        'product_id'    => 1,
        'module_name'   => 'main',
        'purchase_code' => 'activated',
        'version'       => '1.0',
        'install_path'  => '',
    ]);

    // Persist APP_INSTALLED=true to Railway env vars so it survives redeployments
    $railwayToken     = getenv('RAILWAY_API_TOKEN');
    $railwayProjectId = getenv('RAILWAY_PROJECT_ID');
    $railwayEnvId     = getenv('RAILWAY_ENVIRONMENT_ID');
    $railwayServiceId = getenv('RAILWAY_SERVICE_ID');

    if ($railwayToken && $railwayProjectId && $railwayEnvId && $railwayServiceId) {
        try {
            Http::withoutVerifying()
                ->withHeaders(['Authorization' => 'Bearer ' . $railwayToken])
                ->timeout(10)
                ->post('https://backboard.railway.app/graphql/v2', [
                    'query' => 'mutation VariableCollectionUpsert($input: VariableCollectionUpsertInput!) { variableCollectionUpsert(input: $input) }',
                    'variables' => [
                        'input' => [
                            'projectId'     => $railwayProjectId,
                            'environmentId' => $railwayEnvId,
                            'serviceId'     => $railwayServiceId,
                            'variables'     => ['APP_INSTALLED' => 'true'],
                        ]
                    ]
                ]);
        } catch (\Exception $e) {
            error_log('Railway API update failed: ' . $e->getMessage());
        }
    }

    return new JsonResponse([
        'success' => true,
        'message' => 'Installation successful!'
    ]);
};

// Register routes for both direct access (/) and nginx-proxied access (/installer, /installer/)
$this->router->get('/', $getHandler);
$this->router->get('/installer', $getHandler);
$this->router->get('/installer/', $getHandler);
$this->router->post('/', $postHandler);
$this->router->post('/installer', $postHandler);
$this->router->post('/installer/', $postHandler);
