<?php
declare(strict_types=1);

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        static $cache = [];
        [$file, $path] = array_pad(explode('.', $key, 2), 2, null);
        if (!isset($cache[$file])) {
            $full = SLV_ROOT . '/config/' . $file . '.php';
            $cache[$file] = is_file($full) ? require $full : [];
        }
        if ($path === null) {
            return $cache[$file];
        }
        $value = $cache[$file];
        foreach (explode('.', $path) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }
        return $value;
    }
}

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('url')) {
    function url(string $path = '/'): string
    {
        $base = (string) config('config.url', '');
        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('asset_or_upload')) {
    /**
     * Resolve a banner / image reference. Accepts either:
     * - A full URL (http://… or https://…) — returned unchanged
     * - A path stored by the upload helper (/uploads/...) — prefixed with APP_URL
     * - A relative path — prefixed with APP_URL
     */
    function asset_or_upload(?string $value): string
    {
        if (!$value) return '';
        if (preg_match('#^https?://#i', $value)) {
            return $value;
        }
        return url(ltrim($value, '/'));
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path, int $status = 302): never
    {
        header('Location: ' . (str_starts_with($path, 'http') ? $path : url($path)), true, $status);
        exit;
    }
}

if (!function_exists('back')) {
    function back(): never
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        redirect($referer);
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = ''): mixed
    {
        $old = $_SESSION['_old'] ?? [];
        return $old[$key] ?? $default;
    }
}

if (!function_exists('flash')) {
    function flash(string $key, mixed $value = null): mixed
    {
        if ($value === null) {
            $val = $_SESSION['_flash'][$key] ?? null;
            unset($_SESSION['_flash'][$key]);
            return $val;
        }
        $_SESSION['_flash'][$key] = $value;
        return null;
    }
}

if (!function_exists('flash_input')) {
    function flash_input(array $input): void
    {
        $_SESSION['_old'] = $input;
    }
}

if (!function_exists('clear_old')) {
    function clear_old(): void
    {
        unset($_SESSION['_old']);
    }
}

if (!function_exists('__')) {
    function __(string $key, array $replace = []): string
    {
        return \App\Core\Lang::get($key, $replace);
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        $token = \App\Core\Csrf::token();
        return '<input type="hidden" name="_csrf" value="' . e($token) . '">';
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return \App\Core\Csrf::token();
    }
}

if (!function_exists('rm')) {
    /** Format a money value as RM x.xx */
    function rm(float|int|string|null $value): string
    {
        return 'RM ' . number_format((float) $value, 2);
    }
}

if (!function_exists('current_url')) {
    function current_url(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        return $scheme . '://' . $host . $uri;
    }
}

if (!function_exists('logger')) {
    function logger(string $message, array $context = []): void
    {
        $dir = SLV_ROOT . '/storage/logs';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $line = sprintf(
            "[%s] %s %s\n",
            date('c'),
            $message,
            $context ? json_encode($context, JSON_UNESCAPED_UNICODE) : ''
        );
        @file_put_contents($dir . '/app.log', $line, FILE_APPEND);
    }
}
