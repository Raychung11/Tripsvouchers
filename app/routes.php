<?php
/** @var \App\Core\Router $router */

use App\Controllers as C;

// ─── Health check (for Hostinger / uptime monitors) ───────────────────────
$router->get('/healthz', [C\HomeController::class, 'health']);

// ─── Public ────────────────────────────────────────────────────────────────
$router->get('/',                          [C\HomeController::class, 'index']);
$router->get('/campaigns',                 [C\HomeController::class, 'campaigns']);
$router->get('/campaigns/{id}',            [C\HomeController::class, 'campaign']);
$router->get('/merchants',                 [C\HomeController::class, 'merchants']);
$router->get('/about',                     [C\HomeController::class, 'about']);
$router->get('/for-merchants',             [C\HomeController::class, 'forMerchants']);
$router->get('/join',                      [C\HomeController::class, 'forMerchants']);
$router->get('/privacy',                   [C\HomeController::class, 'privacy']);
$router->get('/terms',                     [C\HomeController::class, 'terms']);
$router->get('/contact',                   [C\HomeController::class, 'contact']);

// Voucher claim (visitor)
$router->get('/claim/{campaign}',          [C\VoucherController::class, 'claimForm']);
$router->post('/claim/{campaign}',         [C\VoucherController::class, 'claim'], ['csrf']);
$router->get('/voucher/{code}',            [C\VoucherController::class, 'show']);

// Voucher redemption (merchant scans this URL)
$router->get('/redeem',                    [C\VoucherController::class, 'redeemPreview']);

// AI Tour Guide
$router->get('/chat',                      [C\ChatController::class, 'index']);
$router->post('/chat/message',             [C\ChatController::class, 'send'], ['csrf']);

// Language switcher
$router->get('/lang/{code}',               [C\HomeController::class, 'switchLang']);

// ─── Merchant ──────────────────────────────────────────────────────────────
$router->get('/merchant/login',            [C\Merchant\AuthController::class, 'showLogin']);
$router->post('/merchant/login',           [C\Merchant\AuthController::class, 'login'], ['csrf']);
$router->get('/merchant/register',         [C\Merchant\AuthController::class, 'showRegister']);
$router->post('/merchant/register',        [C\Merchant\AuthController::class, 'register'], ['csrf']);
$router->post('/merchant/logout',          [C\Merchant\AuthController::class, 'logout'], ['csrf']);

$router->group(['auth.merchant'], function (\App\Core\Router $r) {
    $r->get('/merchant',                   [C\Merchant\DashboardController::class, 'index']);
    $r->get('/merchant/dashboard',         [C\Merchant\DashboardController::class, 'index']);
    $r->get('/merchant/profile',           [C\Merchant\DashboardController::class, 'profile']);
    $r->post('/merchant/profile',          [C\Merchant\DashboardController::class, 'updateProfile'], ['csrf']);

    $r->get('/merchant/wallet',            [C\Merchant\WalletController::class, 'index']);
    $r->get('/merchant/wallet/topup',      [C\Merchant\WalletController::class, 'topupForm']);
    $r->post('/merchant/wallet/topup',     [C\Merchant\WalletController::class, 'topup'], ['csrf']);

    $r->get('/merchant/subscription',      [C\Merchant\SubscriptionController::class, 'index']);
    $r->post('/merchant/subscription/pay', [C\Merchant\SubscriptionController::class, 'pay'], ['csrf']);

    $r->get('/merchant/scanner',           [C\Merchant\ScannerController::class, 'index']);
    $r->post('/merchant/scanner/redeem',   [C\Merchant\ScannerController::class, 'redeem'], ['csrf']);

    $r->get('/merchant/redemptions',       [C\Merchant\RedemptionController::class, 'index']);

    $r->get('/merchant/marketing',         [C\Merchant\MarketingController::class, 'index']);
    $r->get('/merchant/marketing/poster/{campaign}', [C\Merchant\MarketingController::class, 'poster']);

    $r->get('/merchant/campaigns',         [C\Merchant\CampaignController::class, 'index']);
    $r->post('/merchant/campaigns/{id}/join',  [C\Merchant\CampaignController::class, 'join'], ['csrf']);
});

// ─── Admin ─────────────────────────────────────────────────────────────────
$router->get('/admin/login',               [C\Admin\AuthController::class, 'showLogin']);
$router->post('/admin/login',              [C\Admin\AuthController::class, 'login'], ['csrf']);
$router->post('/admin/logout',             [C\Admin\AuthController::class, 'logout'], ['csrf']);

$router->group(['auth.admin'], function (\App\Core\Router $r) {
    $r->get('/admin',                      [C\Admin\DashboardController::class, 'index']);
    $r->get('/admin/dashboard',            [C\Admin\DashboardController::class, 'index']);

    $r->get('/admin/merchants',            [C\Admin\MerchantController::class, 'index']);
    $r->get('/admin/merchants/{id}',       [C\Admin\MerchantController::class, 'show']);
    $r->post('/admin/merchants/{id}/approve', [C\Admin\MerchantController::class, 'approve'], ['csrf']);
    $r->post('/admin/merchants/{id}/suspend', [C\Admin\MerchantController::class, 'suspend'], ['csrf']);

    $r->get('/admin/locations',            [C\Admin\LocationController::class, 'index']);
    $r->get('/admin/locations/create',     [C\Admin\LocationController::class, 'create']);
    $r->post('/admin/locations',           [C\Admin\LocationController::class, 'store'], ['csrf']);
    $r->get('/admin/locations/{id}/edit',  [C\Admin\LocationController::class, 'edit']);
    $r->post('/admin/locations/{id}',      [C\Admin\LocationController::class, 'update'], ['csrf']);

    $r->get('/admin/campaigns',            [C\Admin\CampaignController::class, 'index']);
    $r->get('/admin/campaigns/create',     [C\Admin\CampaignController::class, 'create']);
    $r->post('/admin/campaigns',           [C\Admin\CampaignController::class, 'store'], ['csrf']);
    $r->get('/admin/campaigns/{id}',       [C\Admin\CampaignController::class, 'show']);
    $r->get('/admin/campaigns/{id}/edit',  [C\Admin\CampaignController::class, 'edit']);
    $r->post('/admin/campaigns/{id}',      [C\Admin\CampaignController::class, 'update'], ['csrf']);
    $r->post('/admin/campaigns/{id}/merchants', [C\Admin\CampaignController::class, 'attachMerchant'], ['csrf']);

    $r->get('/admin/vouchers',             [C\Admin\VoucherController::class, 'index']);
    $r->get('/admin/redemptions',          [C\Admin\VoucherController::class, 'redemptions']);
    $r->get('/admin/wallet',               [C\Admin\VoucherController::class, 'wallet']);
    $r->get('/admin/analytics',            [C\Admin\AnalyticsController::class, 'index']);
    $r->get('/admin/export/{type}',        [C\Admin\AnalyticsController::class, 'export']);

    $r->get('/admin/site',                 [C\Admin\SiteController::class, 'index']);
    $r->post('/admin/site',                [C\Admin\SiteController::class, 'save'], ['csrf']);
});

// ─── Billplz ───────────────────────────────────────────────────────────────
$router->post('/billplz/callback',         [C\BillplzController::class, 'callback']);
$router->get('/billplz/redirect',          [C\BillplzController::class, 'redirect']);
$router->get('/billplz/dev-pay',           [C\BillplzController::class, 'devPay']);
$router->post('/billplz/dev-pay',          [C\BillplzController::class, 'devPayConfirm'], ['csrf']);
