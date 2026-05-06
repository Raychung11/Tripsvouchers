<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Campaign;
use App\Models\Merchant;
use App\Models\Location;

class HomeController extends Controller
{
    /** Merchant pitch landing page (for sales demos / partner outreach). */
    public function forMerchants(array $params): void
    {
        $stats = [
            'merchants'   => (int) Database::value("SELECT COUNT(*) FROM merchants WHERE status = 'active' AND subscription_status = 'active'"),
            'campaigns'   => (int) Database::value("SELECT COUNT(*) FROM campaigns WHERE status = 'active'"),
            'locations'   => (int) Database::value("SELECT COUNT(*) FROM locations WHERE status = 'active'"),
            'claimed'     => (int) Database::value('SELECT COUNT(*) FROM vouchers'),
            'redeemed'    => (int) Database::value('SELECT COUNT(*) FROM redemptions'),
            'visitors'    => (int) Database::value('SELECT COUNT(DISTINCT customer_phone) FROM vouchers'),
            'this_month'  => (int) Database::value("SELECT COUNT(*) FROM redemptions WHERE redeemed_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)"),
            'avg_value'   => (float) Database::value("SELECT COALESCE(AVG(voucher_value),0) FROM campaigns WHERE status = 'active'"),
            'total_value' => (float) Database::value(
                'SELECT COALESCE(SUM(c.voucher_value),0) FROM redemptions r JOIN campaigns c ON c.id = r.campaign_id'
            ),
        ];

        $featured = Database::all(
            "SELECT m.business_name, m.category, l.area_name, l.state,
                COALESCE((SELECT COUNT(*) FROM redemptions r WHERE r.merchant_id = m.id), 0) AS redemptions
             FROM merchants m
             LEFT JOIN locations l ON l.id = m.location_id
             WHERE m.status = 'active' AND m.subscription_status = 'active'
             ORDER BY redemptions DESC, m.business_name ASC LIMIT 6"
        );

        $this->render('public/for_merchants', [
            'title'    => __('for_merchants.meta_title'),
            'stats'    => $stats,
            'featured' => $featured,
            'pricing'  => [
                'subscription' => (float) config('config.pricing.subscription_fee', 150),
                'redemption'   => (float) config('config.pricing.redemption_fee', 1.50),
                'wallet_min'   => (float) config('config.pricing.wallet_min', 100),
            ],
        ]);
    }

    /** Lightweight health probe for uptime monitors / load balancers. */
    public function health(array $params): void
    {
        $db = false;
        try {
            $db = (int) Database::value('SELECT 1') === 1;
        } catch (\Throwable) {
            $db = false;
        }
        $this->json([
            'ok'   => $db,
            'db'   => $db,
            'env'  => (string) env('APP_ENV', 'production'),
            'time' => date('c'),
        ], $db ? 200 : 503);
    }

    public function index(array $params): void
    {
        $campaigns = Campaign::active();
        $this->render('public/home', [
            'title'     => __('app.tagline'),
            'campaigns' => $campaigns,
        ]);
    }

    public function campaigns(array $params): void
    {
        $this->render('public/campaigns', [
            'title'     => __('campaign.list_title'),
            'campaigns' => Campaign::active(),
            'locations' => Location::active(),
        ]);
    }

    public function campaign(array $params): void
    {
        $id = (int) $params['id'];
        $campaign = Campaign::find($id);
        if (!$campaign) {
            // try slug
            $campaign = Campaign::findBySlug((string) $params['id']);
        }
        if (!$campaign) {
            http_response_code(404);
            $this->render('errors/404', ['title' => 'Not found']);
            return;
        }
        $this->render('public/campaign', [
            'title'     => $campaign['campaign_name'],
            'campaign'  => $campaign,
            'merchants' => Campaign::merchants((int) $campaign['id']),
            'claimable' => Campaign::claimable($campaign),
        ]);
    }

    public function merchants(array $params): void
    {
        $this->render('public/merchants', [
            'title'     => __('nav.merchants'),
            'merchants' => Merchant::publicListing(),
        ]);
    }

    public function about(array $params): void
    {
        $this->render('public/about', ['title' => __('nav.about')]);
    }

    public function switchLang(array $params): void
    {
        $code = (string) ($params['code'] ?? 'en');
        $available = (array) config('config.lang.available', ['en']);
        if (in_array($code, $available, true)) {
            $_SESSION['lang'] = $code;
        }
        $back = $_GET['back'] ?? '/';
        // Avoid open redirect: only same-origin paths.
        if (!preg_match('#^/[^\s]*$#', (string) $back)) {
            $back = '/';
        }
        redirect($back);
    }
}
