<?php /** @var array $merchant */ /** @var array $campaigns */ ?>
<h1 class="mt-0"><?= e(__('merchant.marketing')) ?></h1>

<?php if (empty($campaigns)): ?>
  <div class="card text-center text-muted">
    Join a campaign to access marketing assets.
    <a class="btn btn-primary mt-2" href="<?= e(url('/merchant/campaigns')) ?>"><?= e(__('merchant.campaigns')) ?></a>
  </div>
<?php else: ?>
  <div class="grid grid-2">
    <?php foreach ($campaigns as $c): ?>
      <div class="card">
        <h3 class="mt-0"><?= e($c['campaign_name']) ?></h3>
        <p class="text-muted"><?= e($c['location_name'] ?? '') ?></p>
        <a class="btn btn-primary btn-block" target="_blank"
           href="<?= e(url('/merchant/marketing/poster/' . $c['id'])) ?>">
          📄 Poster (print/save)
        </a>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
