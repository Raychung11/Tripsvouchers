<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Token;

class Voucher
{
    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM vouchers WHERE id = ?', [$id]);
    }

    public static function findByCode(string $code): ?array
    {
        return Database::fetch('SELECT * FROM vouchers WHERE voucher_code = ?', [$code]);
    }

    public static function findByPhone(int $campaignId, string $phone): ?array
    {
        return Database::fetch(
            'SELECT * FROM vouchers WHERE campaign_id = ? AND customer_phone = ? LIMIT 1',
            [$campaignId, $phone]
        );
    }

    public static function countForCampaign(int $campaignId): int
    {
        return (int) Database::value('SELECT COUNT(*) FROM vouchers WHERE campaign_id = ?', [$campaignId]);
    }

    public static function create(array $campaign, string $name, string $phone, ?string $locationSlug = null): array
    {
        $code = Token::voucherCode($locationSlug ?? 'GEN');
        $expiry = $campaign['end_date'] ?: date('Y-m-d', strtotime('+90 days'));

        $payload = [
            'c'   => $code,
            'cid' => (int) $campaign['id'],
            'exp' => (string) $expiry,
            'iat' => time(),
        ];
        $token = Token::encrypt($payload);

        $id = Database::insert(
            'INSERT INTO vouchers (campaign_id, voucher_code, qr_token, customer_name, customer_phone, status, claimed_at, expired_at)
             VALUES (?, ?, ?, ?, ?, "claimed", NOW(), ?)',
            [$campaign['id'], $code, $token, $name, $phone, $expiry . ' 23:59:59']
        );
        return self::find((int) $id);
    }

    public static function qrUrl(array $voucher): string
    {
        return url('/redeem?v=' . urlencode($voucher['qr_token']));
    }

    public static function publicShowUrl(array $voucher): string
    {
        return url('/voucher/' . $voucher['voucher_code']);
    }

    public static function isExpired(array $voucher): bool
    {
        if (!empty($voucher['expired_at'])) {
            return strtotime((string) $voucher['expired_at']) < time();
        }
        return false;
    }
}
