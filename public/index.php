<?php
declare(strict_types=1);

/**
 * SLV Voucher Goodie Platform — front controller.
 */

define('SLV_ROOT', dirname(__DIR__));

require SLV_ROOT . '/config/config.php';

// PSR-4 style autoload for App\* under app/
spl_autoload_register(function (string $class): void {
    if (!str_starts_with($class, 'App\\')) {
        return;
    }
    $relative = str_replace('\\', '/', substr($class, 4));
    $path = SLV_ROOT . '/app/' . $relative . '.php';
    if (is_file($path)) {
        require $path;
    }
});

require SLV_ROOT . '/app/Core/helpers.php';

session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline): bool {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
});

set_exception_handler(function (\Throwable $e): void {
    $debug = (bool) env('APP_DEBUG', false);
    http_response_code(500);
    if ($debug) {
        echo '<pre style="font-family:monospace;padding:1rem;background:#fee;color:#900">';
        echo htmlspecialchars((string) $e, ENT_QUOTES);
        echo '</pre>';
    } else {
        echo '<h1>Something went wrong.</h1>';
    }
    error_log((string) $e);
});

\App\Core\Lang::boot();

$router = new \App\Core\Router();
require SLV_ROOT . '/app/routes.php';
$router->dispatch();
