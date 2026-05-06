<?php
declare(strict_types=1);

namespace App\Core;

/**
 * AES-256-CBC encrypted, HMAC-authenticated tokens for voucher QR URLs.
 *
 * The QR code embeds a URL like:
 *   https://voucher.slvgroup.my/redeem?v=<encrypted_token>
 *
 * The token never exposes raw database IDs.
 */
class Token
{
    public static function encrypt(array $payload): string
    {
        $key = self::key();
        $iv = random_bytes(16);
        $plaintext = json_encode($payload, JSON_UNESCAPED_UNICODE);
        $cipher = openssl_encrypt($plaintext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        if ($cipher === false) {
            throw new \RuntimeException('Encryption failed');
        }
        $hmac = hash_hmac('sha256', $iv . $cipher, $key, true);
        return self::b64url($iv . $hmac . $cipher);
    }

    public static function decrypt(string $token): ?array
    {
        $key = self::key();
        $raw = self::b64urlDecode($token);
        if ($raw === false || strlen($raw) < 16 + 32 + 1) {
            return null;
        }
        $iv = substr($raw, 0, 16);
        $hmac = substr($raw, 16, 32);
        $cipher = substr($raw, 48);
        $expected = hash_hmac('sha256', $iv . $cipher, $key, true);
        if (!hash_equals($expected, $hmac)) {
            return null;
        }
        $plain = openssl_decrypt($cipher, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        if ($plain === false) {
            return null;
        }
        $data = json_decode($plain, true);
        return is_array($data) ? $data : null;
    }

    private static function key(): string
    {
        $raw = (string) config('config.key', '');
        if (str_starts_with($raw, 'base64:')) {
            $key = base64_decode(substr($raw, 7), true);
            if ($key !== false && strlen($key) === 32) {
                return $key;
            }
        }
        // Fallback: derive a stable key from APP_NAME if no key set (dev only).
        return substr(hash('sha256', (string) config('config.name', 'slv'), true), 0, 32);
    }

    private static function b64url(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function b64urlDecode(string $data): string|false
    {
        $pad = strlen($data) % 4;
        if ($pad) {
            $data .= str_repeat('=', 4 - $pad);
        }
        return base64_decode(strtr($data, '-_', '+/'), true);
    }

    /**
     * Generate a public voucher code in the form:
     * SLV-{LOC}-{YEAR}-{RAND6}
     */
    public static function voucherCode(string $locationSlug = 'GEN'): string
    {
        $loc = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $locationSlug)) ?: 'GEN';
        $loc = substr($loc, 0, 4);
        $rand = strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
        return sprintf('SLV-%s-%s-%s', $loc, date('Y'), $rand);
    }
}
