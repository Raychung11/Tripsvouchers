<?php
/** @var string $since */ /** @var string $until */
/** @var array  $totals */ /** @var array $daily */ /** @var array $top_campaigns */
/** @var ?string $last_sync */ /** @var bool $enabled */

$presetActive = function (int $days) use ($since, $until): string {
    $u = date('Y-m-d');
    $s = date('Y-m-d', strtotime("-" . ($days - 1) . " days"));
    return ($since === $s && $until === $u) ? 'btn-primary' : 'btn-outline';
};
?>
<style>
  .meta-toolbar {
    display: flex; flex-wrap: wrap; gap: 12px;
    align-items: center; justify-content: space-between;
    margin-bottom: 16px;
  }
  .meta-toolbar .preset {
    display: flex; gap: 6px; flex-wrap: wrap;
  }
  .meta-status {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 4px 10px; border-radius: 999px; font-size: .78rem;
    font-weight: 600; text-transform: uppercase; letter-spacing: .04em;
  }
  .meta-status.live { background: #dcfce7; color: #166534; }
  .meta-status.demo { background: #fef3c7; color: #92400e; }
  .meta-grid { display: grid; gap: 16px; grid-template-columns: 1fr; }
  @media (min-width: 720px) { .meta-grid { grid-template-columns: repeat(4, 1fr); } }
  .meta-stat {
    background: #fff; border: 1px solid var(--c-border);
    border-radius: var(--r-md); padding: 16px;
  }
  .meta-stat .label { color: var(--c-muted); font-size: .78rem; text-transform: uppercase; letter-spacing: .06em; }
  .meta-stat .value { font-size: 1.7rem; font-weight: 800; color: var(--c-text); margin-top: 4px; }
  .meta-stat .sub   { color: var(--c-muted); font-size: .8rem; margin-top: 2px; }
  .meta-stat.wa     { background: linear-gradient(135deg, #ecfdf5 0%, #fff 100%); border-color: #6ee7b7; }
  .meta-stat.wa .value { color: #047857; }
  .meta-bar-row { display: flex; align-items: flex-end; gap: 4px; height: 160px; }
  .meta-bar-row .col {
    flex: 1; display: flex; flex-direction: column-reverse; gap: 2px; min-width: 8px;
  }
  .meta-bar-row .col .imp { background: #cbd5e1; border-radius: 3px 3px 0 0; }
  .meta-bar-row .col .wa  { background: #10b981; }
</style>

<div class="meta-toolbar">
  <div>
    <h1 class="mt-0 mb-0"><?= e(__('admin.meta.title')) ?></h1>
    <p class="text-muted" style="margin:4px 0 0; font-size:.9rem;">
      <?php if ($enabled): ?>
        <span class="meta-status live">● <?= e(__('admin.meta.status_live')) ?></span>
      <?php else: ?>
        <span class="meta-status demo">● <?= e(__('admin.meta.status_demo')) ?></span>
        <?= e(__('admin.meta.demo_hint')) ?>
      <?php endif; ?>
      <?php if ($last_sync): ?>
        · <?= e(__('admin.meta.last_sync')) ?>: <?= e($last_sync) ?>
      <?php endif; ?>
    </p>
  </div>
  <form method="post" action="<?= e(url('/admin/meta/sync')) ?>">
    <?= csrf_field() ?>
    <button class="btn btn-primary"><?= e(__('admin.meta.sync_now')) ?> ↻</button>
  </form>
</div>

<!-- Date range filter -->
<form method="get" action="<?= e(url('/admin/meta')) ?>" class="card flex-between" style="padding: 14px 16px; flex-wrap: wrap; gap: 12px;">
  <div class="preset">
    <?php foreach ([7 => '7d', 14 => '14d', 30 => '30d', 90 => '90d'] as $d => $lbl):
      $u = date('Y-m-d');
      $s = date('Y-m-d', strtotime("-" . ($d - 1) . " days"));
    ?>
      <a href="?since=<?= e($s) ?>&until=<?= e($u) ?>" class="btn btn-sm <?= $presetActive($d) ?>"><?= e($lbl) ?></a>
    <?php endforeach; ?>
  </div>
  <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
    <label style="margin:0; font-size:.85rem;"><?= e(__('admin.meta.from')) ?>
      <input type="date" name="since" value="<?= e($since) ?>" class="input" style="min-height:38px; padding:6px 10px;">
    </label>
    <label style="margin:0; font-size:.85rem;"><?= e(__('admin.meta.to')) ?>
      <input type="date" name="until" value="<?= e($until) ?>" class="input" style="min-height:38px; padding:6px 10px;">
    </label>
    <button class="btn btn-sm btn-outline"><?= e(__('common.search')) ?></button>
  </div>
</form>

<!-- KPI cards -->
<div class="meta-grid mt-2">
  <div class="meta-stat wa">
    <div class="label">📱 <?= e(__('admin.meta.kpi_whatsapp')) ?></div>
    <div class="value"><?= number_format((int) $totals['whatsapp_clicks']) ?></div>
    <div class="sub"><?= e(__('admin.meta.kpi_conversations')) ?>: <?= number_format((int) $totals['conversations_started']) ?></div>
  </div>
  <div class="meta-stat">
    <div class="label">👁 <?= e(__('admin.meta.kpi_impressions')) ?></div>
    <div class="value"><?= number_format((int) $totals['impressions']) ?></div>
    <div class="sub"><?= e(__('admin.meta.kpi_reach')) ?>: <?= number_format((int) $totals['reach']) ?></div>
  </div>
  <div class="meta-stat">
    <div class="label">🖱 <?= e(__('admin.meta.kpi_clicks')) ?></div>
    <div class="value"><?= number_format((int) $totals['clicks']) ?></div>
    <div class="sub">CTR <?= number_format((float) $totals['ctr'], 2) ?>%</div>
  </div>
  <div class="meta-stat">
    <div class="label">💰 <?= e(__('admin.meta.kpi_spend')) ?></div>
    <div class="value">RM <?= number_format((float) $totals['spend'], 2) ?></div>
    <div class="sub">
      <?= e(__('admin.meta.kpi_cost_per_wa')) ?>:
      RM <?= number_format((float) ($totals['cost_per_wa'] ?? 0), 2) ?>
    </div>
  </div>
</div>

<!-- Daily chart -->
<div class="card mt-3">
  <h3 class="mt-0"><?= e(__('admin.meta.daily_title')) ?></h3>
  <?php if (empty($daily)): ?>
    <p class="text-muted"><?= e(__('admin.meta.no_data')) ?></p>
  <?php else:
    $maxImp = max(array_map(fn ($d) => (int) $d['impressions'], $daily)) ?: 1;
    $maxWa  = max(array_map(fn ($d) => (int) $d['whatsapp_clicks'], $daily)) ?: 1;
  ?>
    <div class="meta-bar-row">
      <?php foreach ($daily as $d):
        $hImp = max(2, (int) round(((int) $d['impressions'] / $maxImp) * 110));
        $hWa  = max(0, (int) round(((int) $d['whatsapp_clicks'] / $maxWa) * 80));
      ?>
        <div class="col" title="<?= e($d['date']) ?> · imp <?= e($d['impressions']) ?> · WA <?= e($d['whatsapp_clicks']) ?>">
          <div class="imp" style="height: <?= $hImp ?>px;"></div>
          <div class="wa"  style="height: <?= $hWa ?>px;"></div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="text-muted mt-1" style="font-size:.85rem; display:flex; gap:14px; flex-wrap:wrap;">
      <span><span style="display:inline-block;width:10px;height:10px;background:#cbd5e1;vertical-align:middle;"></span> <?= e(__('admin.meta.kpi_impressions')) ?></span>
      <span><span style="display:inline-block;width:10px;height:10px;background:#10b981;vertical-align:middle;"></span> <?= e(__('admin.meta.kpi_whatsapp')) ?></span>
    </div>
  <?php endif; ?>
</div>

<!-- Top campaigns -->
<div class="card">
  <h3 class="mt-0"><?= e(__('admin.meta.top_campaigns_title')) ?></h3>
  <?php if (empty($top_campaigns)): ?>
    <p class="text-muted"><?= e(__('admin.meta.no_data')) ?></p>
  <?php else: ?>
    <div class="table-wrap">
      <table class="table">
        <thead><tr>
          <th><?= e(__('admin.meta.col_campaign')) ?></th>
          <th><?= e(__('admin.meta.col_objective')) ?></th>
          <th><?= e(__('admin.meta.kpi_impressions')) ?></th>
          <th><?= e(__('admin.meta.kpi_clicks')) ?></th>
          <th>📱 <?= e(__('admin.meta.kpi_whatsapp')) ?></th>
          <th>💬 <?= e(__('admin.meta.kpi_conversations')) ?></th>
          <th><?= e(__('admin.meta.kpi_spend')) ?></th>
        </tr></thead>
        <tbody>
        <?php foreach ($top_campaigns as $c): ?>
          <tr>
            <td><strong><?= e($c['campaign_name']) ?></strong>
              <div class="text-muted font-mono" style="font-size:.75rem;"><?= e($c['campaign_id']) ?></div></td>
            <td><span class="pill"><?= e($c['objective'] ?? '—') ?></span></td>
            <td><?= number_format((int) $c['impressions']) ?></td>
            <td><?= number_format((int) $c['clicks']) ?></td>
            <td class="font-bold"><?= number_format((int) $c['whatsapp_clicks']) ?></td>
            <td><?= number_format((int) $c['conversations_started']) ?></td>
            <td>RM <?= number_format((float) $c['spend'], 2) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php if (!$enabled): ?>
  <div class="card" style="background:#fffbeb; border-color:#fde68a;">
    <h3 class="mt-0">⚙️ <?= e(__('admin.meta.setup_title')) ?></h3>
    <p class="text-muted"><?= e(__('admin.meta.setup_intro')) ?></p>
    <ol style="line-height:1.7; padding-left:1.2em;">
      <li><?= __('admin.meta.setup_step_1') ?></li>
      <li><?= __('admin.meta.setup_step_2') ?></li>
      <li><?= __('admin.meta.setup_step_3') ?></li>
      <li><?= __('admin.meta.setup_step_4') ?></li>
    </ol>
    <pre style="background:#0f172a; color:#e2e8f0; padding:14px; border-radius:8px; overflow:auto; font-size:.85rem;">META_ACCESS_TOKEN=EAAB...your-system-user-token...
META_AD_ACCOUNT_ID=1234567890
META_API_VERSION=v21.0
META_CRON_TOKEN=any-random-string-for-cron-auth</pre>
  </div>
<?php endif; ?>
