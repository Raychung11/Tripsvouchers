<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Location
{
    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM locations WHERE id = ?', [$id]);
    }

    public static function all(): array
    {
        return Database::all('SELECT * FROM locations ORDER BY state, area_name');
    }

    public static function active(): array
    {
        return Database::all("SELECT * FROM locations WHERE status = 'active' ORDER BY state, area_name");
    }
}
