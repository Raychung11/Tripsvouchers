<?php
declare(strict_types=1);

namespace App\Controllers\Merchant;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Models\Campaign;
use App\Models\Merchant;

class CampaignController extends Controller
{
    public function index(array $params): void
    {
        $merchant = Merchant::findByUserId((int) Auth::id());
        $joined = Database::all(
            'SELECT c.*, cm.status AS link_status, l.area_name AS location_name
             FROM campaign_merchants cm
             JOIN campaigns c ON c.id = cm.campaign_id
             LEFT JOIN locations l ON l.id = c.location_id
             WHERE cm.merchant_id = ?
             ORDER BY c.created_at DESC',
            [$merchant['id']]
        );
        $available = Database::all(
            "SELECT c.*, l.area_name AS location_name
             FROM campaigns c
             LEFT JOIN locations l ON l.id = c.location_id
             WHERE c.status = 'active' AND c.id NOT IN (
                 SELECT campaign_id FROM campaign_merchants WHERE merchant_id = ?
             )
             ORDER BY c.created_at DESC",
            [$merchant['id']]
        );
        $this->render('merchant/campaigns', [
            'title'     => __('merchant.campaigns'),
            'merchant'  => $merchant,
            'joined'    => $joined,
            'available' => $available,
        ]);
    }

    public function join(array $params): void
    {
        $merchant = Merchant::findByUserId((int) Auth::id());
        $campaignId = (int) $params['id'];
        $campaign = Campaign::find($campaignId);
        if (!$campaign) {
            flash('error', 'Campaign not found.');
            redirect('/merchant/campaigns');
        }
        if (($merchant['subscription_status'] ?? '') !== 'active') {
            flash('error', __('merchant.sub_expired_msg'));
            redirect('/merchant/subscription');
        }
        Database::run(
            'INSERT INTO campaign_merchants (campaign_id, merchant_id, status)
             VALUES (?, ?, "active")
             ON DUPLICATE KEY UPDATE status = "active"',
            [$campaignId, $merchant['id']]
        );
        flash('success', __('admin.save'));
        redirect('/merchant/campaigns');
    }
}
