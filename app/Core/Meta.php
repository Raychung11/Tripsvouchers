<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Minimal Meta (Facebook) Marketing Insights client.
 *
 * Pulls daily ad-level performance metrics for a given Ad Account, with
 * focus on WhatsApp / Messenger click + conversation actions used by
 * Click-to-WhatsApp campaigns. The data is normalised to a flat row
 * schema so it can be cached in `meta_ad_metrics` and rendered on the
 * admin dashboard without any further Meta API round-trips.
 *
 * Endpoint: GET /act_{AD_ACCOUNT_ID}/insights
 * Docs:     https://developers.facebook.com/docs/marketing-api/insights
 *
 * Required permissions on the access token: `ads_read`.
 */
class Meta
{
    public static function enabled(): bool
    {
        $cfg = config('config.meta');
        return !empty($cfg['access_token']) && !empty($cfg['ad_account_id']);
    }

    /**
     * Fetch ad-level insights for [$since … $until] (YYYY-MM-DD, inclusive).
     * Returns an array of normalised rows (one per ad per day).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function insights(string $since, string $until): array
    {
        $cfg = config('config.meta');
        if (!self::enabled()) {
            throw new \RuntimeException('Meta API not configured. Set META_ACCESS_TOKEN and META_AD_ACCOUNT_ID.');
        }

        $accountId = preg_replace('/^act_/', '', (string) $cfg['ad_account_id']);
        $version = (string) ($cfg['api_version'] ?? 'v21.0');
        $url = sprintf('https://graph.facebook.com/%s/act_%s/insights', $version, $accountId);

        $params = [
            'access_token'    => $cfg['access_token'],
            'level'           => 'ad',
            'time_increment'  => 1,
            'time_range'      => json_encode(['since' => $since, 'until' => $until]),
            'fields'          => implode(',', [
                'date_start','date_stop',
                'campaign_id','campaign_name',
                'adset_id','adset_name',
                'ad_id','ad_name',
                'objective',
                'impressions','reach','clicks','spend','ctr',
                'actions','action_values','inline_link_clicks',
            ]),
            'limit' => 200,
        ];

        $rows = [];
        $next = $url . '?' . http_build_query($params);
        while ($next) {
            $resp = self::http($next);
            foreach (($resp['data'] ?? []) as $r) {
                $rows[] = self::normalise($r);
            }
            $next = $resp['paging']['next'] ?? null;
        }
        return $rows;
    }

    /**
     * Normalise a raw Meta insights row into the flat shape we store.
     */
    public static function normalise(array $r): array
    {
        $actions = is_array($r['actions'] ?? null) ? $r['actions'] : [];

        return [
            'date'                  => $r['date_start'] ?? null,
            'campaign_id'           => $r['campaign_id']   ?? null,
            'campaign_name'         => $r['campaign_name'] ?? null,
            'adset_id'              => $r['adset_id']      ?? null,
            'adset_name'            => $r['adset_name']    ?? null,
            'ad_id'                 => $r['ad_id']         ?? null,
            'ad_name'               => $r['ad_name']       ?? null,
            'objective'             => $r['objective']     ?? null,
            'impressions'           => (int)   ($r['impressions'] ?? 0),
            'reach'                 => (int)   ($r['reach']       ?? 0),
            'clicks'                => (int)   ($r['clicks']      ?? 0),
            'link_clicks'           => (int)   ($r['inline_link_clicks'] ?? self::sumActions($actions, ['link_click'])),
            'spend'                 => (float) ($r['spend']       ?? 0),
            'ctr'                   => (float) ($r['ctr']         ?? 0),
            'whatsapp_clicks'       => self::sumActions($actions, [
                'click_to_whatsapp',
                'onsite_conversion.click_to_whatsapp',
            ]),
            'messenger_clicks'      => self::sumActions($actions, [
                'click_to_messenger',
                'onsite_conversion.messaging_first_reply',
            ]),
            'conversations_started' => self::sumActions($actions, [
                'onsite_conversion.messaging_conversation_started_7d',
                'onsite_conversion.total_messaging_connection',
            ]),
            'raw_actions'           => $actions ? json_encode($actions, JSON_UNESCAPED_UNICODE) : null,
        ];
    }

    /**
     * Sum the `value` of any action whose `action_type` matches one of
     * the provided types. Meta returns values as strings — cast safely.
     *
     * @param array<int, array{action_type:string,value:string|int}> $actions
     * @param array<int,string> $types
     */
    public static function sumActions(array $actions, array $types): int
    {
        $sum = 0;
        $set = array_flip($types);
        foreach ($actions as $a) {
            if (isset($a['action_type'], $set[$a['action_type']])) {
                $sum += (int) ($a['value'] ?? 0);
            }
        }
        return $sum;
    }

    private static function http(string $url): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);
        $body = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($body === false || $code >= 400) {
            $msg = "Meta API HTTP $code";
            if ($body) {
                $decoded = json_decode((string) $body, true);
                if (isset($decoded['error']['message'])) {
                    $msg .= ': ' . $decoded['error']['message'];
                }
            } elseif ($err) {
                $msg .= ': ' . $err;
            }
            throw new \RuntimeException($msg);
        }
        $data = json_decode((string) $body, true);
        if (!is_array($data)) {
            throw new \RuntimeException('Meta API returned non-JSON response');
        }
        return $data;
    }

    /**
     * Generate plausible synthetic insights for the last N days, used when
     * no Meta credentials are configured so the admin demo still has data.
     *
     * @return array<int, array<string,mixed>>
     */
    public static function demoData(int $days = 30): array
    {
        $campaigns = [
            ['cid' => 'demo_c1', 'cname' => 'Visit Sekinchan — CTWA', 'objective' => 'OUTCOME_ENGAGEMENT'],
            ['cid' => 'demo_c2', 'cname' => 'Penang Heritage Food Walk', 'objective' => 'OUTCOME_TRAFFIC'],
            ['cid' => 'demo_c3', 'cname' => 'Sabah Coastal Experience', 'objective' => 'OUTCOME_ENGAGEMENT'],
        ];
        $rows = [];
        $rng = function (int $min, int $max) { return random_int($min, $max); };
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            foreach ($campaigns as $idx => $c) {
                $impressions = $rng(2000, 6000);
                $reach       = (int) round($impressions * (0.55 + (random_int(0, 30) / 100)));
                $clicks      = (int) round($impressions * (random_int(15, 45) / 1000)); // 1.5–4.5% CTR
                $linkClicks  = (int) round($clicks * (random_int(60, 80) / 100));
                $spend       = round($impressions * (random_int(8, 18) / 1000), 2);
                $waClicks    = $c['objective'] === 'OUTCOME_ENGAGEMENT'
                    ? (int) round($linkClicks * (random_int(35, 55) / 100))
                    : (int) round($linkClicks * (random_int(8, 18) / 100));
                $convs       = (int) round($waClicks * (random_int(40, 65) / 100));
                $rows[] = [
                    'date'                  => $date,
                    'campaign_id'           => $c['cid'],
                    'campaign_name'         => $c['cname'],
                    'adset_id'              => $c['cid'] . '_as',
                    'adset_name'            => $c['cname'] . ' · adset',
                    'ad_id'                 => $c['cid'] . '_ad' . ($idx + 1),
                    'ad_name'               => $c['cname'] . ' · ad ' . ($idx + 1),
                    'objective'             => $c['objective'],
                    'impressions'           => $impressions,
                    'reach'                 => $reach,
                    'clicks'                => $clicks,
                    'link_clicks'           => $linkClicks,
                    'spend'                 => $spend,
                    'ctr'                   => $impressions > 0 ? round($clicks / $impressions * 100, 4) : 0,
                    'whatsapp_clicks'       => $waClicks,
                    'messenger_clicks'      => (int) round($waClicks * 0.2),
                    'conversations_started' => $convs,
                    'raw_actions'           => null,
                ];
            }
        }
        return $rows;
    }
}
