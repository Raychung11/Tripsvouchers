<?php /** @var array $campaign */ /** @var bool $claimable */ ?>
<div class="container-sm" style="max-width:520px; margin:0 auto;">
  <a href="<?= e(url('/campaigns/' . $campaign['id'])) ?>" class="text-muted">← <?= e(__('common.back')) ?></a>
  <article class="card mt-2" style="padding:0; overflow:hidden;">
    <?php
    $bannerImg = $campaign['banner_image'] ?? null;
    $bannerCss = 'linear-gradient(135deg, var(--c-primary), var(--c-accent))';
    if ($bannerImg) {
        $bannerCss = 'linear-gradient(135deg, rgba(13,148,136,.78), rgba(245,158,11,.78)), url(\'' . e(asset_or_upload($bannerImg)) . '\') center / cover no-repeat';
    }
    ?>
    <div style="background: <?= $bannerCss ?>; color:#fff; padding:24px;">
      <h1 style="margin:0; font-size:1.4rem;"><?= e($campaign['campaign_name']) ?></h1>
      <p style="margin:6px 0 0; opacity:.9;">
        📍 <?= e($campaign['location_name'] ?? '') ?>
      </p>
      <p class="font-bold mt-2" style="font-size:1.4rem;"><?= e(rm($campaign['voucher_value'])) ?></p>
    </div>
    <div style="padding:20px;">
      <h2 class="mt-0"><?= e(__('claim.title')) ?></h2>
      <p class="text-muted"><?= e(__('claim.subtitle')) ?></p>

      <?php if (!$claimable): ?>
        <div class="alert alert-warning"><?= e(__('claim.closed')) ?></div>
      <?php else: ?>
        <form method="post" action="<?= e(url('/claim/' . $campaign['id'])) ?>" novalidate>
          <?= csrf_field() ?>
          <div class="field">
            <label for="name"><?= e(__('claim.name')) ?></label>
            <input class="input" type="text" id="name" name="name" required
                   value="<?= e((string) old('name')) ?>" autocomplete="name">
          </div>
          <div class="field">
            <label for="phone"><?= e(__('claim.phone')) ?></label>
            <input class="input" type="tel" id="phone" name="phone" required
                   value="<?= e((string) old('phone')) ?>"
                   inputmode="tel" autocomplete="tel" placeholder="+60xxxxxxxxx">
          </div>
          <div class="field checkbox">
            <input type="checkbox" id="consent" name="consent" value="1" <?= old('consent') ? 'checked' : '' ?>>
            <label for="consent" style="font-weight:400;"><?= e(__('claim.consent')) ?></label>
          </div>
          <button class="btn btn-accent btn-lg btn-block"><?= e(__('claim.submit')) ?></button>
        </form>
      <?php endif; ?>
    </div>
  </article>
</div>
