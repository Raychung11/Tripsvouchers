<?php
/** @var array $campaigns */
$heroImg   = \App\Models\Setting::get('hero_home_image');
$introHtml = \App\Models\Setting::get('intro_home_html');
$signedIn  = !empty($_SESSION['visitor_phone_last9']);

$heroStyle = 'margin: -24px -16px 24px; border-radius: 0;';
if ($heroImg) {
    $heroStyle .= ' background-image: linear-gradient(135deg, rgba(13,148,136,.78) 0%, rgba(15,118,110,.65) 60%, rgba(17,94,89,.78) 100%), url('
                . e(asset_or_upload($heroImg)) . '); background-size: cover; background-position: center;';
}
?>
<style>
  /* ── Visitor action cards (Claim / Find) ────────────────────────────── */
  .v-actions {
    display: grid; gap: 14px; margin: -8px 0 24px;
    grid-template-columns: 1fr;
  }
  @media (min-width: 720px) { .v-actions { grid-template-columns: 1fr 1fr; gap: 18px; } }

  .v-action {
    display: flex; align-items: center; gap: 16px;
    background: #fff; border: 1px solid var(--c-border);
    border-radius: 16px; padding: 18px 20px;
    text-decoration: none; color: var(--c-text);
    box-shadow: 0 4px 18px rgba(15,23,42,.06);
    transition: transform .15s ease, box-shadow .2s ease, border-color .15s ease;
    position: relative; overflow: hidden;
  }
  .v-action:hover, .v-action:focus-visible {
    transform: translateY(-2px); text-decoration: none; outline: none;
    box-shadow: 0 10px 28px rgba(15,23,42,.10);
  }
  .v-action::after {
    content: ''; position: absolute; right: -40px; top: -40px;
    width: 140px; height: 140px; border-radius: 50%; opacity: .12;
    pointer-events: none;
  }
  .v-action.claim { border-color: rgba(245,158,11,.5); }
  .v-action.claim::after { background: var(--c-accent, #f59e0b); }
  .v-action.claim:hover { border-color: var(--c-accent, #f59e0b); }
  .v-action.find  { border-color: rgba(13,148,136,.5); }
  .v-action.find::after  { background: var(--c-primary, #0d9488); }
  .v-action.find:hover  { border-color: var(--c-primary, #0d9488); }

  .v-action .icon {
    flex-shrink: 0; width: 56px; height: 56px; border-radius: 14px;
    display: grid; place-items: center; font-size: 1.7rem;
  }
  .v-action.claim .icon { background: linear-gradient(135deg,#fef3c7,#fde68a); }
  .v-action.find  .icon { background: linear-gradient(135deg,#ccfbf1,#99f6e4); }

  .v-action .body { flex: 1; min-width: 0; }
  .v-action .title { font-size: 1.1rem; font-weight: 700; margin: 0 0 2px; line-height: 1.2; }
  .v-action .sub { color: var(--c-muted); font-size: .9rem; margin: 0; line-height: 1.4; }
  .v-action .arrow { color: var(--c-muted); font-size: 1.4rem; flex-shrink: 0; transition: transform .15s ease; }
  .v-action:hover .arrow { transform: translateX(4px); color: var(--c-text); }

  .v-ai-link {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 14px; border-radius: 999px;
    background: rgba(15,23,42,.04); color: var(--c-text);
    font-size: .9rem; font-weight: 500; text-decoration: none;
    transition: background .15s ease;
  }
  .v-ai-link:hover { background: rgba(13,148,136,.12); text-decoration: none; }
  .v-ai-wrap { text-align: center; margin: -10px 0 28px; }
</style>

<section class="hero" style="<?= $heroStyle ?>">
  <div class="hero-content">
    <h1><?= e(__('home.hero_title')) ?></h1>
    <p><?= e(__('home.hero_subtitle')) ?></p>
  </div>
</section>

<!-- ── Two primary visitor actions ─────────────────────────────────── -->
<section class="v-actions" aria-label="<?= e(__('home.cta_claim') . ' / ' . __('home.cta_find')) ?>">
  <a href="<?= e(url('/campaigns')) ?>" class="v-action claim">
    <span class="icon">🎁</span>
    <div class="body">
      <p class="title"><?= e(__('home.cta_claim')) ?></p>
      <p class="sub"><?= e(__('home.cta_claim_sub')) ?></p>
    </div>
    <span class="arrow" aria-hidden="true">→</span>
  </a>
  <a href="<?= e(url($signedIn ? '/my/vouchers' : '/my')) ?>" class="v-action find">
    <span class="icon">🎟</span>
    <div class="body">
      <p class="title"><?= e($signedIn ? __('visitor.your_vouchers') : __('home.cta_find')) ?></p>
      <p class="sub"><?= e(__('home.cta_find_sub')) ?></p>
    </div>
    <span class="arrow" aria-hidden="true">→</span>
  </a>
</section>

<div class="v-ai-wrap">
  <a href="<?= e(url('/chat')) ?>" class="v-ai-link">
    💬 <?= e(__('home.cta_ai')) ?> →
  </a>
</div>

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
