<?php /** @var array $daily */ /** @var array $by_location */ /** @var array $by_category */ ?>
<h1 class="mt-0"><?= e(__('admin.analytics')) ?></h1>

<div class="card">
  <h3 class="mt-0">Last 30 days · claims vs. redemptions</h3>
  <?php if (empty($daily)): ?>
    <p class="text-muted">—</p>
  <?php else: ?>
    <?php $maxClaim = max(array_map(fn($d) => (int) $d['claimed'], $daily)) ?: 1; ?>
    <div style="display:flex; align-items:flex-end; gap:4px; height:160px;">
      <?php foreach ($daily as $d):
        $hC = max(2, (int) round(((int) $d['claimed'] / $maxClaim) * 150));
        $hR = max(0, (int) round(((int) $d['redeemed'] / $maxClaim) * 150));
      ?>
        <div title="<?= e($d['d']) ?> · claimed <?= e($d['claimed']) ?> / redeemed <?= e($d['redeemed']) ?>"
             style="flex:1; display:flex; flex-direction:column-reverse; gap:1px;">
          <div style="background: var(--c-primary); height: <?= $hC ?>px; border-radius:3px 3px 0 0;"></div>
          <div style="background: var(--c-accent); height: <?= $hR ?>px;"></div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="text-muted mt-1" style="font-size:.85rem;">
      <span style="display:inline-block; width:10px; height:10px; background:var(--c-primary);"></span> Claimed ·
      <span style="display:inline-block; width:10px; height:10px; background:var(--c-accent);"></span> Redeemed
    </div>
  <?php endif; ?>
</div>

<div class="grid grid-2">
  <div class="card">
    <h3 class="mt-0">Top locations (claims)</h3>
    <?php if (empty($by_location)): ?>
      <p class="text-muted">—</p>
    <?php else: ?>
      <table class="table">
        <thead><tr><th>Area</th><th>State</th><th>Claimed</th></tr></thead>
        <tbody>
          <?php foreach ($by_location as $l): ?>
            <tr><td><?= e($l['area_name'] ?? '—') ?></td><td><?= e($l['state'] ?? '—') ?></td><td><?= e($l['claimed']) ?></td></tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
  <div class="card">
    <h3 class="mt-0">By category (redemptions)</h3>
    <?php if (empty($by_category)): ?>
      <p class="text-muted">—</p>
    <?php else: ?>
      <table class="table">
        <thead><tr><th>Category</th><th>Redemptions</th></tr></thead>
        <tbody>
          <?php foreach ($by_category as $c): ?>
            <tr><td><?= e(__('merchant.category_options.' . $c['category'])) ?></td><td><?= e($c['redemptions']) ?></td></tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <h3 class="mt-0"><?= e(__('admin.export')) ?></h3>
  <div class="flex gap-1">
    <a class="btn btn-outline" href="<?= e(url('/admin/export/redemptions')) ?>">Redemptions CSV</a>
    <a class="btn btn-outline" href="<?= e(url('/admin/export/vouchers')) ?>">Vouchers CSV</a>
    <a class="btn btn-outline" href="<?= e(url('/admin/export/merchants')) ?>">Merchants CSV</a>
  </div>
</div>
