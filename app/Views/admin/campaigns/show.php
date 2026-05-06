<?php /** @var array $campaign */ /** @var array $merchants */ /** @var array $available */ /** @var array $stats */ ?>
<a href="<?= e(url('/admin/campaigns')) ?>" class="text-muted">← <?= e(__('common.back')) ?></a>
<div class="flex-between mt-1">
  <h1 class="mt-0"><?= e($campaign['campaign_name']) ?></h1>
  <a class="btn btn-outline" href="<?= e(url('/admin/campaigns/' . $campaign['id'] . '/edit')) ?>"><?= e(__('admin.edit')) ?></a>
</div>

<div class="grid grid-3">
  <div class="stat success">
    <div class="stat-label"><?= e(__('admin.totals.claimed')) ?></div>
    <div class="stat-value"><?= e((string) $stats['claimed']) ?></div>
  </div>
  <div class="stat info">
    <div class="stat-label"><?= e(__('admin.totals.redeemed')) ?></div>
    <div class="stat-value"><?= e((string) $stats['redeemed']) ?></div>
  </div>
  <div class="stat">
    <div class="stat-label"><?= e(__('campaign.value')) ?></div>
    <div class="stat-value"><?= e(rm($campaign['voucher_value'])) ?></div>
  </div>
</div>

<div class="card">
  <h3 class="mt-0">Public claim URL</h3>
  <div class="font-mono" style="background:#f1f5f9; padding:10px 12px; border-radius:8px;">
    <?= e(url('/claim/' . $campaign['id'])) ?>
  </div>
  <p class="text-muted mt-1">Share this with the AI Tour Guide / posters / WhatsApp groups.</p>
</div>

<div class="grid grid-2">
  <div class="card">
    <h3 class="mt-0">Participating merchants (<?= count($merchants) ?>)</h3>
    <?php if (empty($merchants)): ?>
      <p class="text-muted">—</p>
    <?php else: ?>
      <ul style="list-style:none; padding:0;">
        <?php foreach ($merchants as $m): ?>
          <li style="padding:6px 0; border-bottom:1px solid var(--c-border);">
            <a href="<?= e(url('/admin/merchants/' . $m['id'])) ?>"><?= e($m['business_name']) ?></a>
            <span class="text-muted" style="font-size:.85rem;">· <?= e($m['location_name'] ?? '') ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
  <div class="card">
    <h3 class="mt-0"><?= e(__('admin.attach_merchant')) ?></h3>
    <form method="post" action="<?= e(url('/admin/campaigns/' . $campaign['id'] . '/merchants')) ?>">
      <?= csrf_field() ?>
      <div class="field">
        <select class="input" name="merchant_id" required>
          <option value="">—</option>
          <?php foreach ($available as $m): ?>
            <option value="<?= e($m['id']) ?>"><?= e($m['business_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button class="btn btn-primary btn-block"><?= e(__('admin.save')) ?></button>
    </form>
  </div>
</div>
