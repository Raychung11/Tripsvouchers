<?php /** @var array $campaign */ /** @var array $merchants */ /** @var bool $claimable */ ?>
<article class="card" style="overflow:hidden; padding:0;">
  <div style="background: linear-gradient(135deg, var(--c-primary), var(--c-primary-dk)); color:#fff; padding:24px;">
    <div class="pill" style="background:rgba(255,255,255,.18); color:#fff;"><?= e(__('campaign.types.' . $campaign['voucher_type'])) ?></div>
    <h1 style="margin:.4em 0 .2em;"><?= e($campaign['campaign_name']) ?></h1>
    <p style="margin:0; opacity:.9;">📍 <?= e($campaign['location_name'] ?? '') ?> · <?= e($campaign['location_state'] ?? '') ?></p>
    <p class="font-bold" style="font-size:1.6rem; margin: 16px 0 0;"><?= e(rm($campaign['voucher_value'])) ?></p>
  </div>
  <div style="padding:20px;">
    <p><?= nl2br(e($campaign['description'] ?? '')) ?></p>
    <div class="grid grid-2 mt-2">
      <div><strong><?= e(__('campaign.starts')) ?>:</strong> <?= e($campaign['start_date'] ?: '—') ?></div>
      <div><strong><?= e(__('campaign.ends')) ?>:</strong> <?= e($campaign['end_date'] ?: '—') ?></div>
    </div>
    <p class="text-muted mt-2">
      <?= (int) $campaign['claim_limit'] > 0
            ? e(__('campaign.limit', ['n' => (int) $campaign['claim_limit']]))
            : e(__('campaign.unlimited')) ?>
    </p>
    <?php if ($claimable): ?>
      <a href="<?= e(url('/claim/' . $campaign['id'])) ?>" class="btn btn-accent btn-lg btn-block mt-2"><?= e(__('campaign.claim_now')) ?></a>
    <?php else: ?>
      <div class="alert alert-warning"><?= e(__('claim.closed')) ?></div>
    <?php endif; ?>
  </div>
</article>

<?php if (!empty($campaign['terms'])): ?>
  <div class="card">
    <h3 class="mt-0"><?= e(__('campaign.terms')) ?></h3>
    <p class="text-muted" style="white-space:pre-line;"><?= e($campaign['terms']) ?></p>
  </div>
<?php endif; ?>

<div class="card">
  <h3 class="mt-0"><?= e(__('campaign.merchants')) ?></h3>
  <?php if (empty($merchants)): ?>
    <p class="text-muted"><?= e(__('campaign.no_campaigns')) ?></p>
  <?php else: ?>
    <ul style="list-style:none; padding:0; margin:0;">
      <?php foreach ($merchants as $m): ?>
        <li style="padding:10px 0; border-bottom:1px solid var(--c-border);">
          <strong><?= e($m['business_name']) ?></strong>
          <div class="text-muted" style="font-size:.9rem;">
            <?= e(__('merchant.category_options.' . $m['category'])) ?>
            <?php if (!empty($m['address'])): ?> · <?= e($m['address']) ?><?php endif; ?>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
