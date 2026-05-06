<?php /** @var array $joined */ /** @var array $available */ ?>
<h1 class="mt-0"><?= e(__('merchant.campaigns')) ?></h1>

<div class="card">
  <h3 class="mt-0">Joined</h3>
  <?php if (empty($joined)): ?>
    <p class="text-muted">—</p>
  <?php else: ?>
    <ul style="list-style:none; padding:0;">
      <?php foreach ($joined as $c): ?>
        <li style="padding:10px 0; border-bottom:1px solid var(--c-border);">
          <strong><?= e($c['campaign_name']) ?></strong>
          <span class="pill <?= $c['link_status'] === 'active' ? 'pill-success' : 'pill-warning' ?>"><?= e($c['link_status']) ?></span>
          <div class="text-muted" style="font-size:.85rem;"><?= e($c['location_name'] ?? '') ?></div>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>

<div class="card">
  <h3 class="mt-0">Available</h3>
  <?php if (empty($available)): ?>
    <p class="text-muted">No new campaigns to join.</p>
  <?php else: ?>
    <ul style="list-style:none; padding:0;">
      <?php foreach ($available as $c): ?>
        <li style="padding:10px 0; border-bottom:1px solid var(--c-border); display:flex; justify-content:space-between; align-items:center; gap:12px;">
          <div>
            <strong><?= e($c['campaign_name']) ?></strong>
            <div class="text-muted" style="font-size:.85rem;"><?= e($c['location_name'] ?? '') ?></div>
          </div>
          <form method="post" action="<?= e(url('/merchant/campaigns/' . $c['id'] . '/join')) ?>">
            <?= csrf_field() ?>
            <button class="btn btn-primary btn-sm">Join</button>
          </form>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
