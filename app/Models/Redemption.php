<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Redemption
{
    /**
     * Atomically redeem a voucher at a merchant. Returns the new redemption row.
     *
     * @throws \RuntimeException on validation failure or insufficient wallet.
     */
    public static function redeem(array $voucher, array $merchant, ?int $userId = null): array
    {
        $fee = (float) config('config.pricing.redemption_fee', 1.50);

        return Database::transaction(function () use ($voucher, $merchant, $userId, $fee) {
            // Lock merchant row.
            $m = Database::fetch('SELECT id, wallet_balance, subscription_status, status FROM merchants WHERE id = ? FOR UPDATE', [$merchant['id']]);
            if (!$m || $m['status'] !== 'active') {
                throw new \RuntimeException(__('redemption.merchant_not_active'));
            }
            if ($m['subscription_status'] !== 'active') {
                throw new \RuntimeException(__('redemption.sub_expired'));
            }
            $balance = (float) $m['wallet_balance'];
            $min = (float) config('config.pricing.wallet_min', 100);
            if ($balance < $min) {
                throw new \RuntimeException(__('redemption.wallet_low', ['min' => $min]));
            }

            // Lock voucher row.
            $v = Database::fetch('SELECT * FROM vouchers WHERE id = ? FOR UPDATE', [$voucher['id']]);
            if (!$v) {
                throw new \RuntimeException(__('redemption.invalid'));
            }
            if ($v['status'] === 'redeemed') {
                throw new \RuntimeException(__('redemption.already'));
            }
            if ($v['status'] === 'expired') {
                throw new \RuntimeException(__('redemption.expired'));
            }
            if ($v['status'] === 'void') {
                throw new \RuntimeException(__('redemption.void'));
            }
            if (!empty($v['expired_at']) && strtotime((string) $v['expired_at']) < time()) {
                Database::run('UPDATE vouchers SET status = "expired" WHERE id = ?', [$v['id']]);
                throw new \RuntimeException(__('redemption.expired'));
            }

            // Verify merchant participates in the campaign.
            $isMember = Database::fetch(
                'SELECT id FROM campaign_merchants WHERE campaign_id = ? AND merchant_id = ? AND status = "active"',
                [$v['campaign_id'], $merchant['id']]
            );
            if (!$isMember) {
                throw new \RuntimeException(__('redemption.not_a_member'));
            }

            $newBalance = round($balance - $fee, 2);
            Database::run('UPDATE merchants SET wallet_balance = ? WHERE id = ?', [$newBalance, $merchant['id']]);
            Database::run(
                'UPDATE vouchers SET status = "redeemed", merchant_id = ?, redeemed_at = NOW() WHERE id = ?',
                [$merchant['id'], $v['id']]
            );

            $redemptionId = Database::insert(
                'INSERT INTO redemptions
                  (voucher_id, campaign_id, merchant_id, redemption_fee,
                   wallet_balance_before, wallet_balance_after, redeemed_by, redeemed_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, NOW())',
                [$v['id'], $v['campaign_id'], $merchant['id'], $fee, $balance, $newBalance, $userId]
            );

            Database::insert(
                'INSERT INTO merchant_wallet_transactions
                  (merchant_id, type, amount, balance_before, balance_after, description, reference_type, reference_id)
                 VALUES (?, "redemption", ?, ?, ?, ?, "redemption", ?)',
                [$merchant['id'], -$fee, $balance, $newBalance, 'Voucher ' . $v['voucher_code'], $redemptionId]
            );

            return [
                'id' => $redemptionId,
                'voucher_id' => $v['id'],
                'fee' => $fee,
                'balance_before' => $balance,
                'balance_after' => $newBalance,
            ];
        });
    }

    public static function listForMerchant(int $merchantId, int $limit = 100): array
    {
        return Database::all(
            'SELECT r.*, v.voucher_code, v.customer_name, v.customer_phone, c.campaign_name
             FROM redemptions r
             JOIN vouchers  v ON v.id = r.voucher_id
             JOIN campaigns c ON c.id = r.campaign_id
             WHERE r.merchant_id = ?
             ORDER BY r.redeemed_at DESC LIMIT ' . (int) $limit,
            [$merchantId]
        );
    }
}
