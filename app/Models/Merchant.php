<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Merchant
{
    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM merchants WHERE id = ?', [$id]);
    }

    public static function findByUserId(int $userId): ?array
    {
        return Database::fetch('SELECT * FROM merchants WHERE user_id = ?', [$userId]);
    }

    public static function all(?string $status = null): array
    {
        if ($status) {
            return Database::all('SELECT * FROM merchants WHERE status = ? ORDER BY created_at DESC', [$status]);
        }
        return Database::all('SELECT * FROM merchants ORDER BY created_at DESC');
    }

    public static function publicListing(): array
    {
        return Database::all(
            "SELECT m.*, l.area_name AS location_name, l.state AS location_state
             FROM merchants m
             LEFT JOIN locations l ON l.id = m.location_id
             WHERE m.status = 'active' AND m.subscription_status = 'active'
             ORDER BY l.area_name, m.business_name"
        );
    }

    public static function walletStatus(float $balance): string
    {
        if ($balance < 100) return 'disabled';
        if ($balance < 120) return 'critical';
        if ($balance < 150) return 'warning';
        return 'healthy';
    }

    public static function canRedeem(array $merchant): array
    {
        if (($merchant['status'] ?? '') !== 'active') {
            return [false, __('redemption.merchant_not_active')];
        }
        if (($merchant['subscription_status'] ?? '') !== 'active') {
            return [false, __('redemption.sub_expired')];
        }
        $min = (float) config('config.pricing.wallet_min', 100);
        if ((float) $merchant['wallet_balance'] < $min) {
            return [false, __('redemption.wallet_low', ['min' => $min])];
        }
        return [true, ''];
    }

    public static function adjustWallet(int $merchantId, float $delta, string $type, string $description, ?string $refType = null, ?int $refId = null): array
    {
        return Database::transaction(function () use ($merchantId, $delta, $type, $description, $refType, $refId) {
            $row = Database::fetch('SELECT id, wallet_balance FROM merchants WHERE id = ? FOR UPDATE', [$merchantId]);
            if (!$row) {
                throw new \RuntimeException('Merchant not found');
            }
            $before = (float) $row['wallet_balance'];
            $after  = round($before + $delta, 2);
            if ($after < 0) {
                throw new \RuntimeException('Insufficient wallet balance');
            }
            Database::run('UPDATE merchants SET wallet_balance = ? WHERE id = ?', [$after, $merchantId]);
            Database::insert(
                'INSERT INTO merchant_wallet_transactions
                  (merchant_id, type, amount, balance_before, balance_after, description, reference_type, reference_id)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                [$merchantId, $type, $delta, $before, $after, $description, $refType, $refId]
            );
            return ['before' => $before, 'after' => $after];
        });
    }

    public static function activeSubscription(int $merchantId): ?array
    {
        return Database::fetch(
            'SELECT * FROM merchant_subscriptions
             WHERE merchant_id = ? AND status = "active"
             ORDER BY expiry_date DESC LIMIT 1',
            [$merchantId]
        );
    }
}
