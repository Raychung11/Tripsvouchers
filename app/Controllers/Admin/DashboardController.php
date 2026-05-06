<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;

class DashboardController extends Controller
{
    public function index(array $params): void
    {
        $totals = [
            'merchants'    => (int) Database::value('SELECT COUNT(*) FROM merchants WHERE status != "suspended"'),
            'campaigns'    => (int) Database::value('SELECT COUNT(*) FROM campaigns'),
            'claimed'      => (int) Database::value('SELECT COUNT(*) FROM vouchers'),
            'redeemed'     => (int) Database::value('SELECT COUNT(*) FROM redemptions'),
            'visitors'     => (int) Database::value('SELECT COUNT(DISTINCT customer_phone) FROM vouchers'),
            'revenue'      => (float) Database::value('SELECT COALESCE(SUM(redemption_fee),0) FROM redemptions'),
            'wallet_total' => (float) Database::value('SELECT COALESCE(SUM(wallet_balance),0) FROM merchants'),
            'pending'      => (int) Database::value('SELECT COUNT(*) FROM merchants WHERE status = "pending"'),
        ];

        $daily = Database::all(
            'SELECT DATE(redeemed_at) AS d, COUNT(*) AS c
             FROM redemptions
             WHERE redeemed_at >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
             GROUP BY DATE(redeemed_at) ORDER BY d ASC'
        );

        $topMerchants = Database::all(
            'SELECT m.business_name, COUNT(r.id) AS redemptions, COALESCE(SUM(r.redemption_fee),0) AS fees
             FROM redemptions r JOIN merchants m ON m.id = r.merchant_id
             GROUP BY m.id ORDER BY redemptions DESC LIMIT 5'
        );

        $topCampaigns = Database::all(
            'SELECT c.campaign_name, COUNT(v.id) AS claimed,
              SUM(CASE WHEN v.status="redeemed" THEN 1 ELSE 0 END) AS redeemed
             FROM vouchers v JOIN campaigns c ON c.id = v.campaign_id
             GROUP BY c.id ORDER BY claimed DESC LIMIT 5'
        );

        $this->render('admin/dashboard', [
            'title'         => __('admin.dashboard'),
            'totals'        => $totals,
            'daily'         => $daily,
            'top_merchants' => $topMerchants,
            'top_campaigns' => $topCampaigns,
        ]);
    }
}
