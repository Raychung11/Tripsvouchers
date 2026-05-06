<?php
declare(strict_types=1);

namespace App\Controllers\Merchant;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Models\Merchant;

class DashboardController extends Controller
{
    public function index(array $params): void
    {
        $merchant = Merchant::findByUserId((int) Auth::id());
        if (!$merchant) {
            flash('error', 'Merchant profile not found.');
            redirect('/merchant/login');
        }

        $stats = [
            'redeemed_today' => (int) Database::value(
                'SELECT COUNT(*) FROM redemptions WHERE merchant_id = ? AND DATE(redeemed_at) = CURDATE()',
                [$merchant['id']]
            ),
            'redeemed_total' => (int) Database::value(
                'SELECT COUNT(*) FROM redemptions WHERE merchant_id = ?',
                [$merchant['id']]
            ),
            'fees_total' => (float) Database::value(
                'SELECT COALESCE(SUM(redemption_fee),0) FROM redemptions WHERE merchant_id = ?',
                [$merchant['id']]
            ),
            'campaigns' => (int) Database::value(
                'SELECT COUNT(*) FROM campaign_merchants WHERE merchant_id = ? AND status = "active"',
                [$merchant['id']]
            ),
        ];
        $recent = Database::all(
            'SELECT r.*, v.voucher_code, v.customer_name, c.campaign_name
             FROM redemptions r
             JOIN vouchers  v ON v.id = r.voucher_id
             JOIN campaigns c ON c.id = r.campaign_id
             WHERE r.merchant_id = ? ORDER BY r.redeemed_at DESC LIMIT 5',
            [$merchant['id']]
        );

        $this->render('merchant/dashboard', [
            'title'    => __('merchant.dashboard'),
            'merchant' => $merchant,
            'stats'    => $stats,
            'recent'   => $recent,
            'wallet_status' => Merchant::walletStatus((float) $merchant['wallet_balance']),
        ]);
    }

    public function profile(array $params): void
    {
        $merchant = Merchant::findByUserId((int) Auth::id());
        $this->render('merchant/profile', [
            'title'    => __('merchant.profile'),
            'merchant' => $merchant,
        ]);
    }

    public function updateProfile(array $params): void
    {
        $merchant = Merchant::findByUserId((int) Auth::id());
        if (!$merchant) { redirect('/merchant/login'); }

        $data = [
            'business_name'   => trim((string) $this->input('business_name', '')),
            'owner_name'      => trim((string) $this->input('owner_name', '')),
            'phone'           => trim((string) $this->input('phone', '')),
            'whatsapp'        => trim((string) $this->input('whatsapp', '')),
            'category'        => (string) $this->input('category', 'fnb'),
            'address'         => trim((string) $this->input('address', '')),
            'map_link'        => trim((string) $this->input('map_link', '')),
            'operating_hours' => trim((string) $this->input('operating_hours', '')),
            'registration_no' => trim((string) $this->input('registration_no', '')),
        ];
        flash_input($data);

        $errors = $this->validate([
            'business_name' => 'required|min:2',
            'owner_name'    => 'required|min:2',
            'phone'         => 'required|min:7',
        ], $data);
        if ($errors) {
            flash('errors', $errors);
            redirect('/merchant/profile');
        }

        Database::run(
            'UPDATE merchants SET business_name=?, owner_name=?, phone=?, whatsapp=?, category=?,
              address=?, map_link=?, operating_hours=?, registration_no=? WHERE id = ?',
            [
                $data['business_name'], $data['owner_name'], $data['phone'], $data['whatsapp'] ?: null,
                in_array($data['category'], ['fnb','hotel','retail','souvenir','attraction','transport','experience','others'], true) ? $data['category'] : 'fnb',
                $data['address'] ?: null, $data['map_link'] ?: null, $data['operating_hours'] ?: null,
                $data['registration_no'] ?: null, $merchant['id'],
            ]
        );
        flash('success', __('admin.save'));
        redirect('/merchant/profile');
    }
}
