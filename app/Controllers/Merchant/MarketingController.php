<?php
declare(strict_types=1);

namespace App\Controllers\Merchant;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Models\Campaign;
use App\Models\Merchant;

class MarketingController extends Controller
{
    public function index(array $params): void
    {
        $merchant = Merchant::findByUserId((int) Auth::id());
        $campaigns = Database::all(
            'SELECT c.*, l.area_name AS location_name FROM campaign_merchants cm
             JOIN campaigns c ON c.id = cm.campaign_id
             LEFT JOIN locations l ON l.id = c.location_id
             WHERE cm.merchant_id = ? AND cm.status = "active"
             ORDER BY c.created_at DESC',
            [$merchant['id']]
        );
        $this->render('merchant/marketing', [
            'title'     => __('merchant.marketing'),
            'merchant'  => $merchant,
            'campaigns' => $campaigns,
        ]);
    }

    public function poster(array $params): void
    {
        $merchant = Merchant::findByUserId((int) Auth::id());
        $campaign = Campaign::find((int) $params['campaign']);
        if (!$campaign) { http_response_code(404); $this->render('errors/404', ['title' => 'Not found']); return; }
        $this->render('merchant/poster', [
            'title'     => $campaign['campaign_name'],
            'merchant'  => $merchant,
            'campaign'  => $campaign,
            'claim_url' => url('/claim/' . $campaign['id']),
        ]);
    }
}
