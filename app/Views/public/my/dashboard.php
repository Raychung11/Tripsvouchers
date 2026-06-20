<?php
/** @var string $name */ /** @var string $phone */
/** @var array  $active */ /** @var array $redeemed */ /** @var array $expired */
$totalActive = count($active);
$totalRedeemed = count($redeemed);
$totalExpired = count($expired);
$total = $totalActive + $totalRedeemed + $totalExpired;

/** Resolve the banner: campaign banner first, fall back to location banner. */
$voucherImg = function (array $v): string {
    if (!empty($v['campaign_banner'])) return asset_or_upload($v['campaign_banner']);
    if (!empty($v['location_banner'])) return asset_or_upload($v['location_banner']);
    return '';
};
?>
<style>
  .my-dash-shell { max-width: 720px; margin: 0 auto; }
  .my-dash-greet {
    background: linear-gradient(135deg, var(--c-primary), var(--c-accent));
    color: #fff; border-radius: var(--r-lg); padding: 22px 24px;
    margin-bottom: 16px;
  }
  .my-dash-greet h1 { margin: 0 0 4px; font-size: 1.4rem; }
  .my-dash-greet p  { margin: 0; opacity: .92; font-size: .92rem; }
  .my-dash-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 18px; }
  .my-dash-stat  {
    background: #fff; border: 1px solid var(--c-border); border-radius: var(--r-md);
    padding: 10px 12px; text-align: center;
  }
  .my-dash-stat .n { font-size: 1.5rem; font-weight: 800; line-height: 1; }
  .my-dash-stat .l { font-size: .72rem; text-transform: uppercase; letter-spacing: .06em;
                     color: var(--c-muted); margin-top: 4px; }
  .my-dash-stat.active .n   { color: var(--c-primary-dk); }
  .my-dash-stat.redeemed .n { color: var(--c-info, #0284c7); }
  .my-dash-stat.expired .n  { color: var(--c-muted); }

  .my-section-title {
    font-size: .82rem; text-transform: uppercase; letter-spacing: .08em;
    color: var(--c-muted); font-weight: 700; margin: 18px 4px 8px;
  }

  .v-card {
    display: flex; gap: 0; align-items: stretch;
    background: #fff; border: 1px solid var(--c-border);
    border-radius: 14px; padding: 0; overflow: hidden;
    margin-bottom: 10px; text-decoration: none; color: var(--c-text);
    transition: transform .12s ease, box-shadow .18s ease, border-color .15s ease;
    box-shadow: 0 2px 8px rgba(15,23,42,.04);
  }
  .v-card:hover {
    transform: translateY(-2px); text-decoration: none;
    box-shadow: 0 10px 24px rgba(15,23,42,.10);
    border-color: var(--c-primary);
  }
  .v-card.active { border-left: 4px solid var(--c-primary); }
  .v-card.redeemed { opacity: .85; }
  .v-card.expired  { opacity: .55; }

  /* Voucher thumbnail (campaign banner / location banner / gradient fallback) */
  .v-thumb {
    flex-shrink: 0; width: 88px; min-height: 90px;
    background-size: cover; background-position: center;
    background-color: #e2e8f0; position: relative;
  }
  .v-thumb.fallback {
    background-image:
      radial-gradient(circle at 30% 30%, rgba(102,187,106,.7), transparent 60%),
      linear-gradient(135deg, var(--c-primary, #0D47A1), var(--c-primary-dk, #093479));
  }
  .v-thumb .badge {
    position: absolute; top: 6px; left: 6px;
    background: rgba(255,255,255,.92); color: var(--c-primary-dk);
    font-size: .62rem; font-weight: 700; padding: 2px 6px;
    border-radius: 6px; text-transform: uppercase; letter-spacing: .04em;
  }

  .v-card-body { flex: 1; min-width: 0; display: flex; align-items: center; gap: 10px; padding: 12px 14px; }
  .v-card-main { flex: 1; min-width: 0; }
  .v-card-main h3 { margin: 0 0 4px; font-size: .98rem; line-height: 1.3;
                    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
  .v-card-meta { color: var(--c-muted); font-size: .8rem; line-height: 1.3; }
  .v-card-meta .code { font-family: ui-monospace, "SF Mono", Menlo, monospace; font-size: .72rem; }
  .v-card-value { font-size: 1.1rem; font-weight: 800; color: var(--c-primary-dk); white-space: nowrap; text-align: right; }
  .v-card-value .small { display: block; font-size: .62rem; font-weight: 500; color: var(--c-muted); text-transform: uppercase; letter-spacing: .06em; }

  @media (min-width: 720px) {
    .v-thumb { width: 110px; min-height: 100px; }
    .v-card-body { padding: 14px 18px; }
    .v-card-main h3 { font-size: 1.05rem; }
  }
  @media (max-width: 360px) {
    .v-thumb { width: 72px; }
  }

  .empty-state {
    text-align: center; padding: 32px 20px;
    background: #fff; border: 1px dashed var(--c-border); border-radius: var(--r-md);
  }
</style>

<div class="my-dash-shell">

  <div class="my-dash-greet">
    <h1>👋 <?= $name ? e(__('visitor.welcome_named', ['name' => $name])) : e(__('visitor.welcome_back')) ?></h1>
    <p><?= e(__('visitor.signed_in_as', ['phone' => $phone])) ?></p>
  </div>

  <div class="my-dash-stats">
    <div class="my-dash-stat active">
      <div class="n"><?= $totalActive ?></div>
      <div class="l"><?= e(__('visitor.stat_active')) ?></div>
    </div>
    <div class="my-dash-stat redeemed">
      <div class="n"><?= $totalRedeemed ?></div>
      <div class="l"><?= e(__('visitor.stat_redeemed')) ?></div>
    </div>
    <div class="my-dash-stat expired">
      <div class="n"><?= $totalExpired ?></div>
      <div class="l"><?= e(__('visitor.stat_expired')) ?></div>
    </div>
  </div>

  <!-- Active vouchers -->
  <h2 class="my-section-title">🎫 <?= e(__('visitor.section_active')) ?></h2>
  <?php if (empty($active)): ?>
    <div class="empty-state">
      <p class="text-muted" style="margin:0 0 8px;"><?= e(__('visitor.no_active')) ?></p>
      <a href="<?= e(url('/campaigns')) ?>" class="btn btn-primary btn-sm"><?= e(__('home.cta_browse')) ?></a>
    </div>
  <?php else: ?>
    <?php foreach ($active as $v):
      $img = $voucherImg($v);
    ?>
      <a class="v-card active" href="<?= e(url('/voucher/' . $v['voucher_code'])) ?>">
        <?php if ($img): ?>
          <div class="v-thumb" style="background-image: url('<?= e($img) ?>');"></div>
        <?php else: ?>
          <div class="v-thumb fallback"></div>
        <?php endif; ?>
        <div class="v-card-body">
          <div class="v-card-main">
            <h3><?= e($v['campaign_name']) ?></h3>
            <div class="v-card-meta">
              📍 <?= e($v['location_name'] ?? '—') ?>
              <?php if (!empty($v['expired_at'])): ?>
                · <?= e(__('voucher.expires')) ?> <?= e(date('Y-m-d', strtotime((string) $v['expired_at']))) ?>
              <?php endif; ?>
              <br>
              <span class="code"><?= e($v['voucher_code']) ?></span>
            </div>
          </div>
          <div class="v-card-value">
            <?= e(rm($v['voucher_value'])) ?>
            <span class="small"><?= e(__('campaign.types.' . $v['voucher_type'])) ?></span>
          </div>
        </div>
      </a>
    <?php endforeach; ?>
  <?php endif; ?>

  <!-- Redeemed -->
  <?php if (!empty($redeemed)): ?>
    <h2 class="my-section-title">✓ <?= e(__('visitor.section_redeemed')) ?></h2>
    <?php foreach ($redeemed as $v):
      $img = $voucherImg($v);
    ?>
      <a class="v-card redeemed" href="<?= e(url('/voucher/' . $v['voucher_code'])) ?>">
        <?php if ($img): ?>
          <div class="v-thumb" style="background-image: url('<?= e($img) ?>');"></div>
        <?php else: ?>
          <div class="v-thumb fallback"></div>
        <?php endif; ?>
        <div class="v-card-body">
          <div class="v-card-main">
            <h3><?= e($v['campaign_name']) ?></h3>
            <div class="v-card-meta">
              📍 <?= e($v['location_name'] ?? '—') ?>
              <?php if (!empty($v['redeemed_at'])): ?>
                · <?= e(__('voucher.status_redeemed')) ?> <?= e(date('Y-m-d', strtotime((string) $v['redeemed_at']))) ?>
              <?php endif; ?>
              <br>
              <span class="code"><?= e($v['voucher_code']) ?></span>
            </div>
          </div>
          <div class="v-card-value" style="color: var(--c-muted);">
            <?= e(rm($v['voucher_value'])) ?>
            <span class="small">✓ <?= e(__('voucher.status_redeemed')) ?></span>
          </div>
        </div>
      </a>
    <?php endforeach; ?>
  <?php endif; ?>

  <!-- Expired -->
  <?php if (!empty($expired)): ?>
    <h2 class="my-section-title">⊘ <?= e(__('visitor.section_expired')) ?></h2>
    <?php foreach ($expired as $v):
      $img = $voucherImg($v);
    ?>
      <a class="v-card expired" href="<?= e(url('/voucher/' . $v['voucher_code'])) ?>">
        <?php if ($img): ?>
          <div class="v-thumb" style="background-image: url('<?= e($img) ?>');"></div>
        <?php else: ?>
          <div class="v-thumb fallback"></div>
        <?php endif; ?>
        <div class="v-card-body">
          <div class="v-card-main">
            <h3><?= e($v['campaign_name']) ?></h3>
            <div class="v-card-meta">
              📍 <?= e($v['location_name'] ?? '—') ?> ·
              <?= e(__('voucher.status_' . $v['status'])) ?>
              <br><span class="code"><?= e($v['voucher_code']) ?></span>
            </div>
          </div>
          <div class="v-card-value" style="color: var(--c-muted);">
            <?= e(rm($v['voucher_value'])) ?>
          </div>
        </div>
      </a>
    <?php endforeach; ?>
  <?php endif; ?>

  <!-- Sign out -->
  <form method="post" action="<?= e(url('/my/logout')) ?>" class="mt-3" style="text-align:center;">
    <?= csrf_field() ?>
    <button class="btn btn-outline btn-sm" type="submit"><?= e(__('visitor.signout')) ?></button>
  </form>
</div>
