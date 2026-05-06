<?php
declare(strict_types=1);

namespace App\Core;

class Lang
{
    private static string $current = 'en';
    /** @var array<string, array<string, string|array>> */
    private static array $messages = [];

    public static function boot(): void
    {
        $available = config('config.lang.available', ['en', 'zh', 'ms']);
        $default = config('config.lang.default', 'en');

        // Allow ?lang=zh to switch
        if (isset($_GET['lang']) && in_array($_GET['lang'], $available, true)) {
            $_SESSION['lang'] = $_GET['lang'];
        }

        $current = $_SESSION['lang'] ?? $default;
        if (!in_array($current, $available, true)) {
            $current = $default;
        }
        self::$current = $current;

        $file = SLV_ROOT . '/app/Lang/' . $current . '.php';
        $fallback = SLV_ROOT . '/app/Lang/en.php';
        $messages = is_file($fallback) ? require $fallback : [];
        if ($current !== 'en' && is_file($file)) {
            $messages = array_replace_recursive($messages, require $file);
        }
        self::$messages = $messages;
    }

    public static function current(): string
    {
        return self::$current;
    }

    public static function get(string $key, array $replace = []): string
    {
        $segments = explode('.', $key);
        $value = self::$messages;
        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $key;
            }
            $value = $value[$segment];
        }
        if (!is_string($value)) {
            return $key;
        }
        foreach ($replace as $k => $v) {
            $value = str_replace(':' . $k, (string) $v, $value);
        }
        return $value;
    }

    public static function isRtl(): bool
    {
        return false;
    }
}
