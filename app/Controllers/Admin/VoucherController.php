<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;

class VoucherController extends Controller
{
    public function index(array $params): void
    {
        $rows = Database::all(
            'SELECT v.*, c.campaign_name, m.business_name FROM vouchers v
             JOIN campaigns c ON c.id = v.campaign_id
             LEFT JOIN merchants m ON m.id = v.merchant_id
             ORDER BY v.claimed_at DESC LIMIT 200'
        );
        $this->render('admin/vouchers/index', [
            'title' => __('admin.vouchers'),
            'rows'  => $rows,
        ]);
    }

    public function redemptions(array $params): void
    {
        $rows = Database::all(
            'SELECT r.*, v.voucher_code, v.customer_name, v.customer_phone, c.campaign_name, m.business_name
             FROM redemptions r
             JOIN vouchers v ON v.id = r.voucher_id
             JOIN campaigns c ON c.id = r.campaign_id
             JOIN merchants m ON m.id = r.merchant_id
             ORDER BY r.redeemed_at DESC LIMIT 200'
        );
        $this->render('admin/redemptions/index', [
            'title' => __('admin.redemptions'),
            'rows'  => $rows,
        ]);
    }

    public function wallet(array $params): void
    {
        $rows = Database::all(
            'SELECT m.id, m.business_name, m.wallet_balance, m.subscription_status, m.status,
              (SELECT COALESCE(SUM(amount),0) FROM wallet_topups WHERE merchant_id = m.id AND payment_status = "paid") AS total_topup,
              (SELECT COALESCE(SUM(redemption_fee),0) FROM redemptions WHERE merchant_id = m.id) AS total_fees
             FROM merchants m ORDER BY m.business_name'
        );
        $totals = [
            'wallet'  => (float) Database::value('SELECT COALESCE(SUM(wallet_balance),0) FROM merchants'),
            'topups'  => (float) Database::value('SELECT COALESCE(SUM(amount),0) FROM wallet_topups WHERE payment_status = "paid"'),
            'revenue' => (float) Database::value('SELECT COALESCE(SUM(redemption_fee),0) FROM redemptions'),
        ];
        $this->render('admin/wallet/index', [
            'title'  => __('admin.wallet'),
            'rows'   => $rows,
            'totals' => $totals,
        ]);
    }
}
