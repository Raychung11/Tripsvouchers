<?php /** @var array $totals */ /** @var array $daily */ /** @var array $top_merchants */ /** @var array $top_campaigns */ ?>
<h1 class="mt-0"><?= e(__('admin.dashboard')) ?></h1>

<?php if (!empty($totals['pending']) && $totals['pending'] > 0): ?>
  <div class="alert alert-warning">
    <?= e(__('admin.pending_approval')) ?>: <strong><?= e((string) $totals['pending']) ?></strong>
    <a class="btn btn-sm btn-outline mt-1" href="<?= e(url('/admin/merchants')) ?>"><?= e(__('admin.merchants')) ?></a>
  </div>
<?php endif; ?>

<div class="grid grid-4">
  <div class="stat info"><div class="stat-label"><?= e(__('admin.totals.merchants')) ?></div><div class="stat-value"><?= e((string) $totals['merchants']) ?></div></div>
  <div class="stat info"><div class="stat-label"><?= e(__('admin.totals.campaigns')) ?></div><div class="stat-value"><?= e((string) $totals['campaigns']) ?></div></div>
  <div class="stat success"><div class="stat-label"><?= e(__('admin.totals.claimed')) ?></div><div class="stat-value"><?= e((string) $totals['claimed']) ?></div></div>
  <div class="stat success"><div class="stat-label"><?= e(__('admin.totals.redeemed')) ?></div><div class="stat-value"><?= e((string) $totals['redeemed']) ?></div></div>
  <div class="stat"><div class="stat-label"><?= e(__('admin.totals.visitors')) ?></div><div class="stat-value"><?= e((string) $totals['visitors']) ?></div></div>
  <div class="stat success"><div class="stat-label"><?= e(__('admin.totals.revenue')) ?></div><div class="stat-value"><?= e(rm($totals['revenue'])) ?></div></div>
  <div class="stat"><div class="stat-label"><?= e(__('admin.totals.wallet_total')) ?></div><div class="stat-value"><?= e(rm($totals['wallet_total'])) ?></div></div>
  <div class="stat warning"><div class="stat-label"><?= e(__('admin.pending_approval')) ?></div><div class="stat-value"><?= e((string) $totals['pending']) ?></div></div>
</div>

<?php if (!empty($meta)): ?>
  <div class="card mt-3" style="background: linear-gradient(135deg,#ecfdf5,#fff); border-color:#6ee7b7;">
    <div class="flex-between">
      <div>
        <h3 class="mt-0" style="color:#047857;">📱 <?= e(__('admin.meta.dashboard_title')) ?></h3>
        <p class="text-muted" style="margin:0; font-size:.9rem;">
          <?= e(__('admin.meta.dashboard_sub', ['days' => 30])) ?>
        </p>
      </div>
      <a class="btn btn-outline btn-sm" href="<?= e(url('/admin/meta')) ?>">
        <?= e(__('admin.meta.nav')) ?> →
      </a>
    </div>
    <div class="grid grid-4 mt-2">
      <div class="stat success">
        <div class="stat-label">📱 <?= e(__('admin.meta.kpi_whatsapp')) ?></div>
        <div class="stat-value"><?= number_format((int) $meta['whatsapp_clicks']) ?></div>
      </div>
      <div class="stat success">
        <div class="stat-label">💬 <?= e(__('admin.meta.kpi_conversations')) ?></div>
        <div class="stat-value"><?= number_format((int) $meta['conversations_started']) ?></div>
      </div>
      <div class="stat info">
        <div class="stat-label">🖱 <?= e(__('admin.meta.kpi_clicks')) ?></div>
        <div class="stat-value"><?= number_format((int) $meta['clicks']) ?></div>
      </div>
      <div class="stat">
        <div class="stat-label">💰 <?= e(__('admin.meta.kpi_spend')) ?></div>
        <div class="stat-value">RM <?= number_format((float) $meta['spend'], 0) ?></div>
      </div>
    </div>
  </div>
<?php endif; ?>

<div class="grid grid-2 mt-3">
  <div class="card">
    <h3 class="mt-0"><?= e(__('admin.daily_redemption')) ?></h3>
    <?php if (empty($daily)): ?>
      <p class="text-muted">—</p>
    <?php else: ?>
      <?php
      $max = max(array_map(fn ($d) => (int) $d['c'], $daily)) ?: 1;
      ?>
      <div style="display:flex; align-items:flex-end; gap:6px; height:140px;">
        <?php foreach ($daily as $d):
          $h = max(4, (int) round(((int) $d['c'] / $max) * 130));
        ?>
          <div title="<?= e($d['d']) ?> · <?= e($d['c']) ?>" style="flex:1; background: linear-gradient(180deg, var(--c-primary), var(--c-primary-dk)); height: <?= $h ?>px; border-radius: 4px;"></div>
        <?php endforeach; ?>
      </div>
      <div style="display:flex; justify-content:space-between; font-size:.7rem; color:var(--c-muted); margin-top:6px;">
        <span><?= e($daily[0]['d'] ?? '') ?></span>
        <span><?= e(end($daily)['d'] ?? '') ?></span>
      </div>
    <?php endif; ?>
  </div>

  <div class="card">
    <h3 class="mt-0"><?= e(__('admin.top_merchants')) ?></h3>
    <?php if (empty($top_merchants)): ?>
      <p class="text-muted">—</p>
    <?php else: ?>
      <table class="table">
        <thead><tr><th>Merchant</th><th>Redemptions</th><th>Fees</th></tr></thead>
        <tbody>
          <?php foreach ($top_merchants as $m): ?>
            <tr><td><?= e($m['business_name']) ?></td><td><?= e($m['redemptions']) ?></td><td><?= e(rm($m['fees'])) ?></td></tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <h3 class="mt-0"><?= e(__('admin.top_campaigns')) ?></h3>
  <?php if (empty($top_campaigns)): ?>
    <p class="text-muted">—</p>
  <?php else: ?>
    <table class="table">
      <thead><tr><th>Campaign</th><th>Claimed</th><th>Redeemed</th></tr></thead>
      <tbody>
        <?php foreach ($top_campaigns as $c): ?>
          <tr><td><?= e($c['campaign_name']) ?></td><td><?= e($c['claimed']) ?></td><td><?= e($c['redeemed']) ?></td></tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
