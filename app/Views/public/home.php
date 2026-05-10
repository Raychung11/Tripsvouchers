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

  /* ── Home campaigns grid (2 × up to 8 = 16 cards max) ──────────────── */
  .hp-camps {
    display: grid; gap: 14px;
    grid-template-columns: 1fr;
  }
  @media (min-width: 720px) {
    .hp-camps { grid-template-columns: 1fr 1fr; gap: 18px; }
  }
  .hp-camp {
    display: flex; gap: 0; align-items: stretch;
    background: #fff; border: 1px solid var(--c-border);
    border-radius: 14px; overflow: hidden;
    text-decoration: none; color: inherit;
    box-shadow: 0 2px 10px rgba(15,23,42,.04);
    transition: transform .15s ease, box-shadow .2s ease, border-color .15s ease;
    min-height: 130px;
  }
  .hp-camp:hover {
    text-decoration: none; transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(15,23,42,.10);
    border-color: var(--c-primary, #0d9488);
  }
  .hp-camp .thumb {
    flex-shrink: 0; width: 130px;
    background-size: cover; background-position: center;
    background-color: #e2e8f0; position: relative;
  }
  .hp-camp .thumb.fallback {
    background-image:
      radial-gradient(circle at 30% 30%, rgba(245,158,11,.7), transparent 60%),
      linear-gradient(135deg, var(--c-primary, #0d9488), var(--c-primary-dk, #0f766e));
  }
  .hp-camp .thumb::after {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(90deg, transparent 60%, rgba(255,255,255,.15));
  }
  .hp-camp .body {
    flex: 1; min-width: 0;
    padding: 14px 16px; display: flex; flex-direction: column;
  }
  .hp-camp .row1 {
    display: flex; align-items: center; justify-content: space-between;
    gap: 8px; margin-bottom: 6px;
  }
  .hp-camp .row1 .pill { padding: 2px 8px; font-size: .68rem; }
  .hp-camp .value { color: var(--c-primary-dk); font-weight: 800; font-size: 1.05rem; white-space: nowrap; }
  .hp-camp h3 {
    margin: 0 0 4px; font-size: .98rem; line-height: 1.3;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
    overflow: hidden;
  }
  .hp-camp .meta { color: var(--c-muted); font-size: .82rem; margin: auto 0 0; }
  @media (max-width: 480px) {
    .hp-camp .thumb { width: 100px; }
    .hp-camp .body { padding: 10px 12px; }
  }
  .hp-view-all {
    text-align: center; margin: 18px 0 0;
  }
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
<?php else:
  $perPage = 16; // 2 columns × 8 rows
  $shown = array_slice($campaigns, 0, $perPage);
  $remaining = max(0, count($campaigns) - $perPage);
?>
  <div class="hp-camps">
    <?php foreach ($shown as $c):
      $hasBanner = !empty($c['banner_image']);
      $thumbStyle = $hasBanner
        ? 'background-image: url(\'' . e(asset_or_upload($c['banner_image'])) . '\');'
        : '';
    ?>
      <a href="<?= e(url('/campaigns/' . $c['id'])) ?>" class="hp-camp">
        <div class="thumb <?= $hasBanner ? '' : 'fallback' ?>" style="<?= $thumbStyle ?>"></div>
        <div class="body">
          <div class="row1">
            <span class="pill pill-success"><?= e(__('campaign.types.' . $c['voucher_type'])) ?></span>
            <span class="value"><?= e(rm($c['voucher_value'])) ?></span>
          </div>
          <h3><?= e($c['campaign_name']) ?></h3>
          <p class="meta">
            📍 <?= e($c['location_name'] ?? '—') ?>
            <?php if (!empty($c['end_date'])): ?>
              · <?= e(__('campaign.ends')) ?> <?= e($c['end_date']) ?>
            <?php endif; ?>
          </p>
        </div>
      </a>
    <?php endforeach; ?>
  </div>

  <?php if ($remaining > 0): ?>
    <div class="hp-view-all">
      <a href="<?= e(url('/campaigns')) ?>" class="btn btn-outline">
        <?= e(__('home.cta_browse')) ?> (+<?= $remaining ?>) →
      </a>
    </div>
  <?php endif; ?>
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
