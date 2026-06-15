<?php

/**
 * Temporary deployment diagnostic — DELETE this file after fixing the site.
 */
header('Content-Type: text/plain; charset=utf-8');
ini_set('display_errors', '1');
error_reporting(E_ALL);
set_time_limit(30);

register_shutdown_function(function (): void {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        echo "\nFATAL: {$error['message']}\n";
        echo "File: {$error['file']}:{$error['line']}\n";
    }
});

function say(string $message): void
{
    echo $message;
    if (function_exists('ob_flush')) {
        @ob_flush();
    }
    flush();
}

say("=== Dantana Server Check ===\n\n");

$phpOk = version_compare(PHP_VERSION, '8.2.0', '>=');
say('PHP version: ' . PHP_VERSION . ($phpOk ? " OK\n" : " FAIL (Laravel 11 needs 8.2+)\n"));
say('Document root: ' . ($_SERVER['DOCUMENT_ROOT'] ?? 'unknown') . "\n");
say('Script path: ' . __FILE__ . "\n\n");

$base = dirname(__DIR__);

$paths = [
    'vendor/autoload.php' => $base . '/vendor/autoload.php',
    '.env' => $base . '/.env',
    'bootstrap/app.php' => $base . '/bootstrap/app.php',
    'storage/logs' => $base . '/storage/logs',
];

foreach ($paths as $label => $path) {
    say("$label: " . (file_exists($path) ? 'EXISTS' : 'MISSING') . "\n");
}

say('storage writable: ' . (is_writable($base . '/storage') ? 'YES' : 'NO') . "\n");
say('bootstrap/cache writable: ' . (is_writable($base . '/bootstrap/cache') ? 'YES' : 'NO') . "\n");

$envValues = [];
if (file_exists($base . '/.env')) {
    $env = file_get_contents($base . '/.env');
    say('APP_KEY set: ' . (preg_match('/^APP_KEY=base64:.+/m', $env) ? 'YES' : 'NO (run: php artisan key:generate)') . "\n");

    foreach (['APP_ENV', 'APP_DEBUG', 'DB_CONNECTION', 'DB_HOST', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD', 'SESSION_DRIVER', 'CACHE_STORE'] as $key) {
        if (preg_match('/^' . preg_quote($key, '/') . '=(.*)$/m', $env, $m)) {
            $envValues[$key] = trim($m[1], " \t\"'");
        }
    }

    say('APP_ENV: ' . ($envValues['APP_ENV'] ?? 'not set') . "\n");
    say('APP_DEBUG: ' . ($envValues['APP_DEBUG'] ?? 'not set') . "\n");
    say('DB_HOST: ' . ($envValues['DB_HOST'] ?? 'not set') . "\n");
    say('DB_DATABASE: ' . ($envValues['DB_DATABASE'] ?? 'not set') . "\n");
    say('DB_USERNAME: ' . ($envValues['DB_USERNAME'] ?? 'not set') . "\n");
    say('DB_PASSWORD set: ' . (! empty($envValues['DB_PASSWORD']) ? 'YES' : 'NO') . "\n");
    say('SESSION_DRIVER: ' . ($envValues['SESSION_DRIVER'] ?? 'not set') . "\n");
    say('CACHE_STORE: ' . ($envValues['CACHE_STORE'] ?? 'not set') . "\n");
}

say("\n--- Raw MySQL test (before Laravel) ---\n");

if (($envValues['DB_CONNECTION'] ?? '') === 'mysql') {
    try {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s',
            $envValues['DB_HOST'] ?? 'localhost',
            $envValues['DB_PORT'] ?? '3306',
            $envValues['DB_DATABASE'] ?? ''
        );
        $pdo = new PDO(
            $dsn,
            $envValues['DB_USERNAME'] ?? '',
            $envValues['DB_PASSWORD'] ?? '',
            [PDO::ATTR_TIMEOUT => 5, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        say("Raw MySQL connection: OK\n");
    } catch (Throwable $e) {
        say('Raw MySQL connection FAIL: ' . $e->getMessage() . "\n");
        say("Fix DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD in .env\n");
        say("Hostinger DB names usually look like: u623700213_dantana\n\n");
    }
} else {
    say("Skipped (DB_CONNECTION is not mysql)\n");
}

say("\n--- Autoload test ---\n");

if (! file_exists($base . '/vendor/autoload.php')) {
    say("FAIL: vendor/ folder is missing.\n");
    exit;
}

try {
    require $base . '/vendor/autoload.php';
    say("Autoload: OK\n");
} catch (Throwable $e) {
    say('Autoload FAIL: ' . $e->getMessage() . "\n");
    exit;
}

say("\n--- Laravel boot test ---\n");

try {
    $app = require $base . '/bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    say("Laravel boot: OK\n");
} catch (Throwable $e) {
    say('Laravel boot FAIL: ' . $e->getMessage() . "\n");
    say('File: ' . $e->getFile() . ':' . $e->getLine() . "\n";
    exit;
}

say("\n--- Full request test (/login) ---\n");

try {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $request = Illuminate\Http\Request::create('/login', 'GET');
    $response = $kernel->handle($request);
    say('HTTP status: ' . $response->getStatusCode() . "\n");

    if ($response->getStatusCode() >= 500) {
        say("Response body (first 500 chars):\n");
        say(substr($response->getContent(), 0, 500) . "\n");
    } else {
        say("Login route: OK\n");
    }

    $kernel->terminate($request, $response);
} catch (Throwable $e) {
    say('Request FAIL: ' . $e->getMessage() . "\n");
    say('File: ' . $e->getFile() . ':' . $e->getLine() . "\n";
}

say("\nDelete public/check.php when done.\n");
