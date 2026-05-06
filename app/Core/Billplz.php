<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Minimal Billplz v3 integration.
 *
 * Docs: https://www.billplz.com/api
 *
 * - createBill(): for merchant subscription / wallet topup
 * - verifyCallback(): server-to-server x_signature verification
 * - verifyRedirect(): redirect-back x_signature verification
 */
class Billplz
{
    public static function enabled(): bool
    {
        $cfg = config('config.billplz');
        return !empty($cfg['api_key']) && !empty($cfg['collection_id']);
    }

    public static function createBill(array $payload): array
    {
        $cfg = config('config.billplz');
        if (!self::enabled()) {
            // Dev fallback — return a fake bill so flows work without credentials.
            $fake = 'DEV-' . bin2hex(random_bytes(6));
            return [
                'id'   => $fake,
                'url'  => url('/billplz/dev-pay?bill=' . urlencode($fake) . '&ref=' . urlencode($payload['reference_1'] ?? '')),
                'paid' => false,
            ];
        }

        $form = array_filter([
            'collection_id' => $cfg['collection_id'],
            'email'         => $payload['email'] ?? '',
            'mobile'        => $payload['mobile'] ?? '',
            'name'          => $payload['name'] ?? '',
            'amount'        => (int) round(((float) $payload['amount']) * 100), // cents
            'callback_url'  => $payload['callback_url'],
            'redirect_url'  => $payload['redirect_url'] ?? null,
            'description'   => $payload['description'] ?? 'SLV Voucher',
            'reference_1_label' => $payload['reference_1_label'] ?? null,
            'reference_1'   => $payload['reference_1'] ?? null,
            'reference_2_label' => $payload['reference_2_label'] ?? null,
            'reference_2'   => $payload['reference_2'] ?? null,
        ], fn ($v) => $v !== null && $v !== '');

        $ch = curl_init($cfg['base_url'] . '/bills');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD        => $cfg['api_key'] . ':',
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($form),
            CURLOPT_TIMEOUT        => 15,
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code < 200 || $code >= 300 || !$body) {
            throw new \RuntimeException('Billplz createBill failed: HTTP ' . $code . ' ' . (string) $body);
        }
        $data = json_decode((string) $body, true);
        return [
            'id'   => $data['id'] ?? '',
            'url'  => $data['url'] ?? '',
            'paid' => (bool) ($data['paid'] ?? false),
            'raw'  => $data,
        ];
    }

    /** Verify x_signature on Billplz server callback (POST). */
    public static function verifyCallback(array $payload): bool
    {
        $cfg = config('config.billplz');
        if (empty($cfg['x_signature'])) {
            // No signature configured — reject in production, allow in dev.
            return env('APP_ENV') !== 'production';
        }
        $signature = $payload['x_signature'] ?? '';
        $source = '';
        ksort($payload);
        foreach ($payload as $k => $v) {
            if ($k === 'x_signature') {
                continue;
            }
            $source .= $k . $v;
        }
        $expected = hash_hmac('sha256', $source, $cfg['x_signature']);
        return hash_equals($expected, $signature);
    }

    /** Verify x_signature on Billplz redirect (GET). */
    public static function verifyRedirect(array $payload): bool
    {
        $cfg = config('config.billplz');
        if (empty($cfg['x_signature'])) {
            return env('APP_ENV') !== 'production';
        }
        $signature = $payload['billplz']['x_signature'] ?? ($payload['x_signature'] ?? '');
        $source = '';
        $billplz = $payload['billplz'] ?? $payload;
        ksort($billplz);
        foreach ($billplz as $k => $v) {
            if ($k === 'x_signature') {
                continue;
            }
            $source .= 'billplz' . $k . $v;
        }
        $expected = hash_hmac('sha256', $source, $cfg['x_signature']);
        return hash_equals($expected, $signature);
    }
}
