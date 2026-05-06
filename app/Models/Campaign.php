<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Campaign
{
    public static function find(int $id): ?array
    {
        return Database::fetch(
            'SELECT c.*, l.area_name AS location_name, l.slug AS location_slug, l.state AS location_state
             FROM campaigns c LEFT JOIN locations l ON l.id = c.location_id
             WHERE c.id = ?',
            [$id]
        );
    }

    public static function findBySlug(string $slug): ?array
    {
        return Database::fetch(
            'SELECT c.*, l.area_name AS location_name, l.slug AS location_slug, l.state AS location_state
             FROM campaigns c LEFT JOIN locations l ON l.id = c.location_id
             WHERE c.slug = ?',
            [$slug]
        );
    }

    public static function active(): array
    {
        return Database::all(
            "SELECT c.*, l.area_name AS location_name, l.slug AS location_slug, l.state AS location_state
             FROM campaigns c LEFT JOIN locations l ON l.id = c.location_id
             WHERE c.status = 'active'
               AND (c.start_date IS NULL OR c.start_date <= CURDATE())
               AND (c.end_date IS NULL OR c.end_date >= CURDATE())
             ORDER BY c.created_at DESC"
        );
    }

    public static function all(): array
    {
        return Database::all(
            'SELECT c.*, l.area_name AS location_name FROM campaigns c
             LEFT JOIN locations l ON l.id = c.location_id ORDER BY c.created_at DESC'
        );
    }

    public static function merchants(int $campaignId): array
    {
        return Database::all(
            'SELECT m.*, cm.status AS link_status, l.area_name AS location_name
             FROM campaign_merchants cm
             JOIN merchants m ON m.id = cm.merchant_id
             LEFT JOIN locations l ON l.id = m.location_id
             WHERE cm.campaign_id = ? AND cm.status = "active"
             ORDER BY m.business_name',
            [$campaignId]
        );
    }

    public static function claimable(array $campaign): bool
    {
        if ($campaign['status'] !== 'active') {
            return false;
        }
        $today = date('Y-m-d');
        if ($campaign['start_date'] && $campaign['start_date'] > $today) return false;
        if ($campaign['end_date']   && $campaign['end_date']   < $today) return false;
        if ((int) $campaign['claim_limit'] > 0) {
            $count = Voucher::countForCampaign((int) $campaign['id']);
            if ($count >= (int) $campaign['claim_limit']) {
                return false;
            }
        }
        return true;
    }

    public static function isMerchantInCampaign(int $campaignId, int $merchantId): bool
    {
        $row = Database::fetch(
            'SELECT id FROM campaign_merchants WHERE campaign_id = ? AND merchant_id = ? AND status = "active"',
            [$campaignId, $merchantId]
        );
        return $row !== null;
    }

    public static function slugify(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value);
        $value = trim((string) $value, '-');
        return $value !== '' ? $value : 'campaign-' . substr(bin2hex(random_bytes(3)), 0, 6);
    }
}
