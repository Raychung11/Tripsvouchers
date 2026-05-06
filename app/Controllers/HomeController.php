<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Campaign;
use App\Models\Merchant;
use App\Models\Location;

class HomeController extends Controller
{
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
