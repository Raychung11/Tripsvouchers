<?php /** @var array $campaigns */ ?>
<h1 class="mt-0 mb-2"><?= e(__('campaign.list_title')) ?></h1>

<?php if (empty($campaigns)): ?>
  <div class="card text-center text-muted"><?= e(__('campaign.no_campaigns')) ?></div>
<?php else: ?>
  <div class="grid grid-2">
    <?php foreach ($campaigns as $c): ?>
      <article class="card">
        <div class="flex-between mb-1">
          <span class="pill pill-success"><?= e(__('campaign.types.' . $c['voucher_type'])) ?></span>
          <span class="font-bold" style="color:var(--c-primary-dk);"><?= e(rm($c['voucher_value'])) ?></span>
        </div>
        <h2 style="margin:.2em 0;"><?= e($c['campaign_name']) ?></h2>
        <p class="text-muted" style="margin:0 0 6px;">
          📍 <?= e($c['location_name'] ?? '') ?> · <?= e($c['location_state'] ?? '') ?>
        </p>
        <p style="margin:8px 0;"><?= nl2br(e($c['description'] ?? '')) ?></p>
        <p class="text-muted" style="font-size:.9rem;">
          <?= e(__('campaign.starts')) ?>: <?= e($c['start_date'] ?: '—') ?> ·
          <?= e(__('campaign.ends')) ?>: <?= e($c['end_date'] ?: '—') ?>
        </p>
        <a href="<?= e(url('/campaigns/' . $c['id'])) ?>" class="btn btn-primary btn-block mt-2"><?= e(__('campaign.claim_now')) ?></a>
      </article>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
