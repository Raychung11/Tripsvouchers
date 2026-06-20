<?php
// Loads .env values into getenv()/$_ENV/$_SERVER and exposes app config.

if (!function_exists('slv_load_env')) {
    function slv_load_env(string $path): void
    {
        if (!is_readable($path)) {
            return;
        }
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) {
                continue;
            }
            if (!str_contains($line, '=')) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if (strlen($value) >= 2 && $value[0] === '"' && substr($value, -1) === '"') {
                $value = substr($value, 1, -1);
            }
            if (getenv($key) === false) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
}

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? getenv($key);
        if ($value === false || $value === null || $value === '') {
            return $default;
        }
        return match (strtolower((string) $value)) {
            'true', '(true)'   => true,
            'false', '(false)' => false,
            'null', '(null)'   => null,
            default            => $value,
        };
    }
}

slv_load_env(dirname(__DIR__) . '/.env');

date_default_timezone_set((string) env('APP_TIMEZONE', 'Asia/Kuala_Lumpur'));

return [
    'name'      => env('APP_NAME', 'SLV Voucher Goodie'),
    'env'       => env('APP_ENV', 'production'),
    'debug'     => (bool) env('APP_DEBUG', false),
    'url'       => rtrim((string) env('APP_URL', 'http://localhost:8000'), '/'),
    'timezone'  => env('APP_TIMEZONE', 'Asia/Kuala_Lumpur'),
    'key'       => env('APP_KEY', ''),

    'pricing' => [
        'subscription_fee'  => (float) env('SUBSCRIPTION_ANNUAL_FEE', 150),
        'redemption_fee'    => (float) env('VOUCHER_REDEMPTION_FEE', 1.50),
        'wallet_min'        => (float) env('WALLET_MIN_BALANCE', 100),
        'wallet_min_reload' => (float) env('WALLET_MIN_RELOAD', 100),
    ],

    'billplz' => [
        'base_url'      => env('BILLPLZ_BASE_URL', 'https://www.billplz-sandbox.com/api/v3'),
        'api_key'       => env('BILLPLZ_API_KEY', ''),
        'collection_id' => env('BILLPLZ_COLLECTION_ID', ''),
        'x_signature'   => env('BILLPLZ_X_SIGNATURE', ''),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY', ''),
        'model'   => env('OPENAI_MODEL', 'gpt-4o-mini'),
    ],

    // Meta (Facebook) Marketing API — sync ad performance + WhatsApp clicks
    // into the admin dashboard. Falls back to demo data if no token is set.
    'meta' => [
        'access_token'   => env('META_ACCESS_TOKEN', ''),
        'ad_account_id'  => env('META_AD_ACCOUNT_ID', ''),     // numeric, no `act_` prefix
        'api_version'    => env('META_API_VERSION', 'v21.0'),
        // Secret used to authorise GET /admin/meta/sync from a cron job
        'cron_token'     => env('META_CRON_TOKEN', ''),
    ],

    'lang' => [
        'default'   => env('DEFAULT_LANG', 'ms'),
        'available' => ['en', 'zh', 'ms'],
    ],

    // Company / contact info shown in the footer, contact page,
    // and any legal copy. Edit here once and it propagates everywhere.
    'company' => [
        'name'        => 'SLV Group Sdn Bhd',
        'reg_no'      => env('COMPANY_REG_NO', '202301-XXXXXX'),
        'address'     => env('COMPANY_ADDRESS', 'Kuala Lumpur, Malaysia'),
        'email'       => env('COMPANY_EMAIL', 'support@slvgroup.my'),
        'phone'       => env('COMPANY_PHONE', '+60 3-1234 5678'),
        // Digits only, used to build wa.me URLs
        'whatsapp'    => env('COMPANY_WHATSAPP', '60123456789'),
        'social' => [
            'facebook'  => env('SOCIAL_FACEBOOK',  'https://facebook.com/slvgroup'),
            'instagram' => env('SOCIAL_INSTAGRAM', 'https://instagram.com/slvgroup'),
            'tiktok'    => env('SOCIAL_TIKTOK',    'https://tiktok.com/@slvgroup'),
        ],
    ],
];
