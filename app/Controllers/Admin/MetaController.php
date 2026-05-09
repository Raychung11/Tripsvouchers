<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Meta;
use App\Models\MetaMetric;

/**
 * Admin: Meta (Facebook) advertising insights.
 *
 * - GET  /admin/meta            — date-filtered dashboard
 * - POST /admin/meta/sync       — manual sync (admin button, CSRF protected)
 * - GET  /admin/meta/sync       — cron-triggered sync, requires ?token=…
 *
 * If META_ACCESS_TOKEN / META_AD_ACCOUNT_ID are unset the sync falls
 * back to synthetic 30-day data so the demo UI is populated.
 */
class MetaController extends Controller
{
    public function index(array $params): void
    {
        $until = (string) ($_GET['until'] ?? date('Y-m-d'));
        $since = (string) ($_GET['since'] ?? date('Y-m-d', strtotime('-29 days', strtotime($until))));

        $this->render('admin/meta/index', [
            'title'         => __('admin.meta.title'),
            'since'         => $since,
            'until'         => $until,
            'totals'        => MetaMetric::totals($since, $until),
            'daily'         => MetaMetric::daily($since, $until),
            'top_campaigns' => MetaMetric::topCampaigns($since, $until),
            'last_sync'     => MetaMetric::lastSync(),
            'enabled'       => Meta::enabled(),
        ]);
    }

    /** Manual sync triggered by the admin button. */
    public function sync(array $params): void
    {
        $this->doSync(30);
        redirect('/admin/meta');
    }

    /**
     * Cron-friendly sync at GET /admin/meta/sync?token=<META_CRON_TOKEN>.
     * Returns plain text so it's easy to read in cron logs.
     */
    public function cronSync(array $params): void
    {
        $expected = (string) config('config.meta.cron_token', '');
        $given    = (string) ($_GET['token'] ?? '');
        if ($expected === '' || !hash_equals($expected, $given)) {
            http_response_code(403);
            echo "forbidden\n";
            return;
        }
        $days = max(1, min(90, (int) ($_GET['days'] ?? 7)));
        $count = $this->doSync($days);
        header('Content-Type: text/plain; charset=utf-8');
        echo "ok · synced $count rows · " . date('c') . "\n";
    }

    /**
     * Pull insights for the last $days days and upsert into meta_ad_metrics.
     * Falls back to synthetic demo data if no Meta credentials are set.
     */
    private function doSync(int $days): int
    {
        $until = date('Y-m-d');
        $since = date('Y-m-d', strtotime("-{$days} days"));

        try {
            $rows = Meta::enabled()
                ? Meta::insights($since, $until)
                : Meta::demoData($days);
        } catch (\Throwable $e) {
            logger('Meta sync failed: ' . $e->getMessage());
            flash('error', __('admin.meta.sync_failed', ['msg' => $e->getMessage()]));
            return 0;
        }

        $count = 0;
        foreach ($rows as $r) {
            if (empty($r['ad_id']) || empty($r['date'])) continue;
            try {
                MetaMetric::upsert($r);
                $count++;
            } catch (\Throwable $e) {
                logger('Meta upsert failed: ' . $e->getMessage(), $r);
            }
        }

        if (!Meta::enabled()) {
            flash('info', __('admin.meta.demo_synced', ['n' => $count]));
        } else {
            flash('success', __('admin.meta.live_synced', ['n' => $count]));
        }
        return $count;
    }
}
