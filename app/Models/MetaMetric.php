<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class MetaMetric
{
    /**
     * Upsert a normalised insights row keyed on (date, ad_id).
     */
    public static function upsert(array $r): void
    {
        Database::run(
            'INSERT INTO meta_ad_metrics
              (date, campaign_id, campaign_name, adset_id, adset_name,
               ad_id, ad_name, objective,
               impressions, reach, clicks, link_clicks, spend, ctr,
               whatsapp_clicks, messenger_clicks, conversations_started,
               raw_actions, synced_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
             ON DUPLICATE KEY UPDATE
               campaign_id=VALUES(campaign_id),
               campaign_name=VALUES(campaign_name),
               adset_id=VALUES(adset_id),
               adset_name=VALUES(adset_name),
               ad_name=VALUES(ad_name),
               objective=VALUES(objective),
               impressions=VALUES(impressions),
               reach=VALUES(reach),
               clicks=VALUES(clicks),
               link_clicks=VALUES(link_clicks),
               spend=VALUES(spend),
               ctr=VALUES(ctr),
               whatsapp_clicks=VALUES(whatsapp_clicks),
               messenger_clicks=VALUES(messenger_clicks),
               conversations_started=VALUES(conversations_started),
               raw_actions=VALUES(raw_actions),
               synced_at=NOW()',
            [
                $r['date'],
                $r['campaign_id'], $r['campaign_name'],
                $r['adset_id'], $r['adset_name'],
                $r['ad_id'], $r['ad_name'], $r['objective'],
                (int) $r['impressions'], (int) $r['reach'],
                (int) $r['clicks'], (int) $r['link_clicks'],
                (float) $r['spend'], (float) $r['ctr'],
                (int) $r['whatsapp_clicks'], (int) $r['messenger_clicks'],
                (int) $r['conversations_started'],
                $r['raw_actions'] ?? null,
            ]
        );
    }

    /** Aggregate totals across the date range. */
    public static function totals(string $since, string $until): array
    {
        $row = Database::fetch(
            'SELECT
                COALESCE(SUM(impressions),0)           AS impressions,
                COALESCE(SUM(reach),0)                 AS reach,
                COALESCE(SUM(clicks),0)                AS clicks,
                COALESCE(SUM(link_clicks),0)           AS link_clicks,
                COALESCE(SUM(spend),0)                 AS spend,
                COALESCE(SUM(whatsapp_clicks),0)       AS whatsapp_clicks,
                COALESCE(SUM(messenger_clicks),0)      AS messenger_clicks,
                COALESCE(SUM(conversations_started),0) AS conversations_started
             FROM meta_ad_metrics WHERE date BETWEEN ? AND ?',
            [$since, $until]
        ) ?? [];
        // CTR is impressions-weighted, so compute from totals
        $impr = (int) ($row['impressions'] ?? 0);
        $row['ctr'] = $impr > 0 ? round(((int) ($row['clicks'] ?? 0)) / $impr * 100, 2) : 0;
        $row['cpc'] = ((int) ($row['link_clicks'] ?? 0)) > 0
            ? round(((float) ($row['spend'] ?? 0)) / (int) $row['link_clicks'], 2) : 0;
        $row['cost_per_wa'] = ((int) ($row['whatsapp_clicks'] ?? 0)) > 0
            ? round(((float) ($row['spend'] ?? 0)) / (int) $row['whatsapp_clicks'], 2) : 0;
        return $row;
    }

    public static function daily(string $since, string $until): array
    {
        return Database::all(
            'SELECT date,
                COALESCE(SUM(impressions),0)     AS impressions,
                COALESCE(SUM(clicks),0)          AS clicks,
                COALESCE(SUM(spend),0)           AS spend,
                COALESCE(SUM(whatsapp_clicks),0) AS whatsapp_clicks,
                COALESCE(SUM(conversations_started),0) AS conversations_started
             FROM meta_ad_metrics WHERE date BETWEEN ? AND ?
             GROUP BY date ORDER BY date ASC',
            [$since, $until]
        );
    }

    public static function topCampaigns(string $since, string $until, int $limit = 10): array
    {
        return Database::all(
            'SELECT campaign_id, campaign_name, objective,
                COALESCE(SUM(impressions),0)     AS impressions,
                COALESCE(SUM(clicks),0)          AS clicks,
                COALESCE(SUM(spend),0)           AS spend,
                COALESCE(SUM(whatsapp_clicks),0) AS whatsapp_clicks,
                COALESCE(SUM(conversations_started),0) AS conversations_started
             FROM meta_ad_metrics
             WHERE date BETWEEN ? AND ? AND campaign_id IS NOT NULL
             GROUP BY campaign_id, campaign_name, objective
             ORDER BY whatsapp_clicks DESC, clicks DESC
             LIMIT ' . (int) $limit,
            [$since, $until]
        );
    }

    public static function lastSync(): ?string
    {
        $v = Database::value('SELECT MAX(synced_at) FROM meta_ad_metrics');
        return $v ? (string) $v : null;
    }
}
