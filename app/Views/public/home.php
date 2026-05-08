<?php
/** @var array $campaigns */
$heroImg = \App\Models\Setting::get('hero_home_image');
$introHtml = \App\Models\Setting::get('intro_home_html');
$heroStyle = 'margin: -24px -16px 24px; border-radius: 0;';
if ($heroImg) {
    $heroStyle .= ' background-image: linear-gradient(135deg, rgba(13,148,136,.78) 0%, rgba(15,118,110,.65) 60%, rgba(17,94,89,.78) 100%), url(' . e(asset_or_upload($heroImg)) . '); background-size: cover; background-position: center;';
}
?>
<section class="hero" style="<?= $heroStyle ?>">
  <div class="hero-content">
    <h1><?= e(__('home.hero_title')) ?></h1>
    <p><?= e(__('home.hero_subtitle')) ?></p>
    <div class="hero-cta">
      <a href="<?= e(url('/campaigns')) ?>" class="btn btn-accent btn-lg"><?= e(__('home.cta_browse')) ?></a>
      <a href="<?= e(url('/chat')) ?>" class="btn btn-outline btn-lg" style="background:#fff;"><?= e(__('home.cta_chat')) ?></a>
    </div>
  </div>
</section>

<?php if ($introHtml): ?>
  <section class="card mb-3" style="background:#fff;">
    <?= $introHtml /* admin-controlled HTML, intentionally unescaped */ ?>
  </section>
<?php endif; ?>

<h2 class="mb-2"><?= e(__('campaign.list_title')) ?></h2>
<?php if (empty($campaigns)): ?>
  <div class="card text-center text-muted"><?= e(__('campaign.no_campaigns')) ?></div>
<?php else: ?>
  <div class="grid grid-3">
    <?php foreach ($campaigns as $c): ?>
      <a href="<?= e(url('/campaigns/' . $c['id'])) ?>" class="card" style="display:block;color:inherit;">
        <div class="pill pill-success mb-1"><?= e(__('campaign.types.' . $c['voucher_type'])) ?></div>
        <h3 style="margin:.2em 0;"><?= e($c['campaign_name']) ?></h3>
        <p class="text-muted" style="margin:0 0 8px;"><?= e($c['location_name'] ?? '') ?> · <?= e($c['location_state'] ?? '') ?></p>
        <p class="font-bold" style="color:var(--c-primary-dk); font-size:1.1rem;"><?= e(rm($c['voucher_value'])) ?></p>
        <p class="text-muted" style="font-size:.85rem; margin-top:8px;">
          <?= e(__('campaign.ends')) ?>: <?= e($c['end_date'] ?: '—') ?>
        </p>
      </a>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<h2 class="mt-4 mb-2"><?= e(__('home.how_title')) ?></h2>
<div class="grid grid-3">
  <div class="card">
    <div class="brand-logo" style="margin-bottom:8px;">1</div>
    <h3><?= e(__('home.how_step1')) ?></h3>
  </div>
  <div class="card">
    <div class="brand-logo" style="margin-bottom:8px;">2</div>
    <h3><?= e(__('home.how_step2')) ?></h3>
  </div>
  <div class="card">
    <div class="brand-logo" style="margin-bottom:8px;">3</div>
    <h3><?= e(__('home.how_step3')) ?></h3>
  </div>
</div>

<div class="card mt-3 text-center">
  <h3 class="mt-0"><?= e(__('home.merchant_cta')) ?></h3>
  <div class="flex gap-1" style="justify-content:center; flex-wrap:wrap;">
    <a href="<?= e(url('/for-merchants')) ?>" class="btn btn-primary btn-lg"><?= e(__('for_merchants.hero_cta_alt')) ?></a>
    <a href="<?= e(url('/merchant/register')) ?>" class="btn btn-accent btn-lg"><?= e(__('auth.register')) ?></a>
  </div>
</div>
