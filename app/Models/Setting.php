<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Key/value site_settings store. Cached per-request after first read.
 * Empty/null values are returned as null so callers can fall back to
 * defaults (translations, gradient placeholders, etc.).
 */
class Setting
{
    /** @var array<string,?string>|null */
    private static ?array $cache = null;

    public static function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }
        $rows = Database::all('SELECT `key`, `value` FROM site_settings');
        $out = [];
        foreach ($rows as $r) {
            $out[$r['key']] = $r['value'] === '' ? null : $r['value'];
        }
        self::$cache = $out;
        return $out;
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $all = self::all();
        $v = $all[$key] ?? null;
        return $v !== null && $v !== '' ? $v : $default;
    }

    public static function set(string $key, ?string $value): void
    {
        Database::run(
            'INSERT INTO site_settings (`key`, `value`) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
            [$key, $value]
        );
        if (self::$cache !== null) {
            self::$cache[$key] = $value === '' ? null : $value;
        }
    }
}
