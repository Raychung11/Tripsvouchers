<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;

class AnalyticsController extends Controller
{
    public function index(array $params): void
    {
        $daily = Database::all(
            'SELECT DATE(claimed_at) AS d,
              COUNT(*) AS claimed,
              SUM(CASE WHEN status="redeemed" THEN 1 ELSE 0 END) AS redeemed
             FROM vouchers
             WHERE claimed_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             GROUP BY DATE(claimed_at) ORDER BY d ASC'
        );
        $byLocation = Database::all(
            'SELECT l.area_name, l.state, COUNT(v.id) AS claimed
             FROM vouchers v
             JOIN campaigns c ON c.id = v.campaign_id
             LEFT JOIN locations l ON l.id = c.location_id
             GROUP BY l.id ORDER BY claimed DESC LIMIT 10'
        );
        $byCategory = Database::all(
            'SELECT m.category, COUNT(r.id) AS redemptions
             FROM redemptions r JOIN merchants m ON m.id = r.merchant_id
             GROUP BY m.category ORDER BY redemptions DESC'
        );
        $this->render('admin/analytics/index', [
            'title'       => __('admin.analytics'),
            'daily'       => $daily,
            'by_location' => $byLocation,
            'by_category' => $byCategory,
        ]);
    }

    public function export(array $params): void
    {
        $type = (string) $params['type'];
        $rows = match ($type) {
            'redemptions' => Database::all(
                'SELECT r.id, r.redeemed_at, v.voucher_code, v.customer_name, v.customer_phone,
                  c.campaign_name, m.business_name, r.redemption_fee
                 FROM redemptions r
                 JOIN vouchers v ON v.id = r.voucher_id
                 JOIN campaigns c ON c.id = r.campaign_id
                 JOIN merchants m ON m.id = r.merchant_id
                 ORDER BY r.redeemed_at DESC'
            ),
            'vouchers' => Database::all(
                'SELECT v.id, v.voucher_code, v.customer_name, v.customer_phone, v.status,
                  v.claimed_at, v.redeemed_at, c.campaign_name
                 FROM vouchers v JOIN campaigns c ON c.id = v.campaign_id
                 ORDER BY v.claimed_at DESC'
            ),
            'merchants' => Database::all(
                'SELECT id, business_name, owner_name, email, phone, category,
                  wallet_balance, subscription_status, status, created_at FROM merchants ORDER BY id'
            ),
            default => null,
        };
        if (!is_array($rows)) {
            http_response_code(400);
            echo 'Unsupported export type.';
            return;
        }
        $filename = $type . '-' . date('Ymd-His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $out = fopen('php://output', 'w');
        if ($rows) {
            fputcsv($out, array_keys($rows[0]));
            foreach ($rows as $row) {
                fputcsv($out, array_values($row));
            }
        }
        fclose($out);
        exit;
    }
}
