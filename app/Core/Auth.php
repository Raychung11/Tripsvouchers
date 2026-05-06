<?php
declare(strict_types=1);

namespace App\Core;

class Auth
{
    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id'    => (int) $user['id'],
            'name'  => $user['name'] ?? '',
            'email' => $user['email'] ?? '',
            'role'  => $user['role'] ?? 'merchant',
        ];
    }

    public static function logout(): void
    {
        unset($_SESSION['user']);
        session_regenerate_id(true);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function id(): ?int
    {
        return self::user()['id'] ?? null;
    }

    public static function role(): ?string
    {
        return self::user()['role'] ?? null;
    }

    public static function attempt(string $email, string $password, ?string $expectedRole = null): ?array
    {
        $row = Database::fetch('SELECT * FROM users WHERE email = ? LIMIT 1', [$email]);
        if (!$row) {
            return null;
        }
        if (!password_verify($password, $row['password'])) {
            return null;
        }
        if ($expectedRole && $row['role'] !== $expectedRole && !($expectedRole === 'admin' && $row['role'] === 'gov')) {
            return null;
        }
        if (($row['status'] ?? 'active') !== 'active') {
            return null;
        }
        self::login($row);
        return $row;
    }
}
