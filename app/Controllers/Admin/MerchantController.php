<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Merchant;

class MerchantController extends Controller
{
    public function index(array $params): void
    {
        $rows = Database::all(
            'SELECT m.*, l.area_name AS location_name FROM merchants m
             LEFT JOIN locations l ON l.id = m.location_id
             ORDER BY m.status ASC, m.created_at DESC'
        );
        $this->render('admin/merchants/index', [
            'title' => __('admin.merchants'),
            'rows'  => $rows,
        ]);
    }

    public function show(array $params): void
    {
        $merchant = Merchant::find((int) $params['id']);
        if (!$merchant) { http_response_code(404); $this->render('errors/404', ['title' => 'Not found']); return; }
        $sub = Merchant::activeSubscription((int) $merchant['id']);
        $tx = Database::all(
            'SELECT * FROM merchant_wallet_transactions WHERE merchant_id = ? ORDER BY created_at DESC LIMIT 50',
            [$merchant['id']]
        );
        $redemptions = Database::all(
            'SELECT r.*, v.voucher_code, c.campaign_name FROM redemptions r
             JOIN vouchers v ON v.id = r.voucher_id
             JOIN campaigns c ON c.id = r.campaign_id
             WHERE r.merchant_id = ? ORDER BY r.redeemed_at DESC LIMIT 25',
            [$merchant['id']]
        );
        $this->render('admin/merchants/show', [
            'title'        => $merchant['business_name'],
            'merchant'     => $merchant,
            'sub'          => $sub,
            'transactions' => $tx,
            'redemptions'  => $redemptions,
        ]);
    }

    public function approve(array $params): void
    {
        $id = (int) $params['id'];
        Database::run('UPDATE merchants SET status = "active" WHERE id = ?', [$id]);
        flash('success', __('admin.approve') . ' ✓');
        redirect('/admin/merchants/' . $id);
    }

    public function suspend(array $params): void
    {
        $id = (int) $params['id'];
        Database::run('UPDATE merchants SET status = "suspended" WHERE id = ?', [$id]);
        flash('success', __('admin.suspend') . ' ✓');
        redirect('/admin/merchants/' . $id);
    }
}
