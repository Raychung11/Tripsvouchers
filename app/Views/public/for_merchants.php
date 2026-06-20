<?php
/** @var array $stats */ /** @var array $featured */ /** @var array $pricing */
$sub    = number_format($pricing['subscription'], 0);
$fee    = number_format($pricing['redemption'], 2);
$wallet = number_format($pricing['wallet_min'], 0);
$heroImg = \App\Models\Setting::get('hero_for_merchants_image');
?>
<style>
  .pitch-hero {
    margin: -24px -16px 32px;
    background:
      radial-gradient(circle at 80% -20%, rgba(245, 158, 11, .35), transparent 55%),
      radial-gradient(circle at -10% 110%, rgba(13, 148, 136, .35), transparent 55%),
      linear-gradient(135deg, #0f172a 0%, #093479 60%, #062459 100%);
    color: #fff; padding: 72px 24px 56px; text-align: center;
    position: relative; overflow: hidden;
  }
  <?php if ($heroImg): ?>
  .pitch-hero {
    background:
      linear-gradient(135deg, rgba(15,23,42,.88) 0%, rgba(17,94,89,.78) 60%, rgba(19,78,74,.85) 100%),
      url('<?= e(asset_or_upload($heroImg)) ?>') center / cover no-repeat;
  }
  <?php endif; ?>
  .pitch-hero .eyebrow {
    display: inline-block; padding: 6px 14px; border-radius: 999px;
    background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.25);
    text-transform: uppercase; font-size: .75rem; letter-spacing: .12em;
    margin-bottom: 18px;
  }
  .pitch-hero h1 { font-size: 2.4rem; line-height: 1.15; max-width: 820px; margin: 0 auto; }
  .pitch-hero p.lead { max-width: 640px; margin: 18px auto 28px; opacity: .92; font-size: 1.1rem; }
  .pitch-hero .cta-row { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
  .pitch-hero .note { font-size: .85rem; opacity: .75; margin-top: 18px; }

  .live-strip {
    background: var(--c-surface); border: 1px solid var(--c-border);
    border-radius: var(--r-lg); padding: 18px 24px; box-shadow: var(--shadow-md);
    margin-top: -36px; position: relative; z-index: 2;
  }
  .live-strip .eyebrow {
    color: var(--c-muted); font-size: .72rem; text-transform: uppercase;
    letter-spacing: .12em; margin-bottom: 10px; text-align: center;
  }
  .live-strip .grid {
    display: grid; gap: 16px;
    grid-template-columns: repeat(2, 1fr);
  }
  @media (min-width: 720px) { .live-strip .grid { grid-template-columns: repeat(6, 1fr); } }
  .live-strip .stat { background: transparent; border: 0; padding: 6px; text-align: center; }
  .live-strip .stat-value { font-size: 1.6rem; }

  section.pitch { padding: 48px 0; }
  section.pitch h2 {
    font-size: 1.8rem; margin: 0 0 8px; text-align: center;
  }
  section.pitch p.section-sub {
    text-align: center; color: var(--c-muted);
    max-width: 600px; margin: 0 auto 28px;
  }

  .why-grid { display: grid; gap: 16px; grid-template-columns: 1fr; }
  @media (min-width: 720px)  { .why-grid { grid-template-columns: repeat(2, 1fr); } }
  @media (min-width: 1000px) { .why-grid { grid-template-columns: repeat(4, 1fr); } }
  .why-card {
    background: #fff; border: 1px solid var(--c-border);
    border-radius: var(--r-lg); padding: 20px; height: 100%;
  }
  .why-card .icon {
    width: 44px; height: 44px; border-radius: 12px;
    background: linear-gradient(135deg, var(--c-primary), var(--c-accent));
    color: #fff; display: grid; place-items: center; font-size: 1.2rem;
    margin-bottom: 12px; font-weight: 700;
  }
  .why-card h3 { margin: 0 0 6px; font-size: 1.05rem; }
  .why-card p  { margin: 0; color: var(--c-muted); font-size: .92rem; line-height: 1.55; }

  .how-steps {
    display: grid; gap: 24px;
    grid-template-columns: 1fr;
    counter-reset: step;
  }
  @media (min-width: 720px) { .how-steps { grid-template-columns: repeat(3, 1fr); } }
  .how-step {
    background: #fff; border: 1px solid var(--c-border);
    border-radius: var(--r-lg); padding: 28px; position: relative;
  }
  .how-step::before {
    counter-increment: step;
    content: counter(step);
    position: absolute; top: -22px; left: 24px;
    width: 44px; height: 44px; border-radius: 50%;
    background: var(--c-primary); color: #fff;
    display: grid; place-items: center; font-weight: 800; font-size: 1.2rem;
    box-shadow: 0 6px 16px rgba(13,71,161,.35);
  }

  .pricing-grid {
    display: grid; gap: 16px; grid-template-columns: 1fr;
  }
  @media (min-width: 720px) { .pricing-grid { grid-template-columns: repeat(3, 1fr); } }
  .price-card {
    background: #fff; border: 1px solid var(--c-border);
    border-radius: var(--r-lg); padding: 28px; text-align: center;
    box-shadow: var(--shadow-sm);
  }
  .price-card.highlighted {
    border-color: var(--c-primary);
    box-shadow: 0 14px 30px rgba(13,71,161,.18);
    transform: translateY(-4px);
  }
  .price-card .amount { font-size: 2.4rem; font-weight: 800; color: var(--c-primary-dk); }
  .price-card .unit   { color: var(--c-muted); font-size: .9rem; margin-bottom: 12px; }
  .price-card h3      { margin: 0 0 6px; font-size: 1.05rem; }
  .price-card p       { color: var(--c-muted); font-size: .9rem; margin: 0; line-height: 1.55; }

  .roi-card {
    background: linear-gradient(135deg, #ecfeff 0%, #fff 60%);
    border: 1px solid var(--c-border); border-radius: var(--r-lg);
    padding: 28px; max-width: 760px; margin: 0 auto;
  }
  .roi-card .row { display: grid; grid-template-columns: 1fr; gap: 14px; }
  @media (min-width: 720px) { .roi-card .row { grid-template-columns: 1fr 1fr; } }
  .roi-card label { font-weight: 600; font-size: .9rem; }
  .roi-card .result {
    background: #fff; border: 1px solid var(--c-border);
    border-radius: var(--r-md); padding: 16px; margin-top: 16px;
    display: grid; gap: 8px;
  }
  .roi-card .result .row { font-size: 1rem; }
  .roi-card .result .net {
    background: linear-gradient(90deg, var(--c-primary), var(--c-accent));
    color: #fff; padding: 12px 14px; border-radius: var(--r-md);
    font-weight: 700; font-size: 1.1rem;
    display: flex; justify-content: space-between; align-items: center;
  }
  .roi-card .result .multiple {
    text-align: center; font-size: 2rem; font-weight: 800;
    color: var(--c-primary-dk); margin-top: 4px;
  }

  .featured-grid {
    display: grid; gap: 12px; grid-template-columns: 1fr;
  }
  @media (min-width: 720px) { .featured-grid { grid-template-columns: repeat(3, 1fr); } }
  .featured-card {
    background: #fff; border: 1px solid var(--c-border);
    border-radius: var(--r-md); padding: 16px;
  }
  .featured-card .badge {
    display: inline-block; padding: 2px 10px; border-radius: 999px;
    background: var(--c-primary-lt); color: var(--c-primary-dk);
    font-size: .7rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .04em;
  }

  .faq details {
    background: #fff; border: 1px solid var(--c-border);
    border-radius: var(--r-md); padding: 14px 18px; margin-bottom: 10px;
    cursor: pointer;
  }
  .faq details[open] { border-color: var(--c-primary); box-shadow: var(--shadow-sm); }
  .faq summary { font-weight: 600; outline: none; list-style: none; padding-right: 24px; position: relative; }
  .faq summary::after {
    content: '+'; position: absolute; right: 0; top: -2px;
    font-size: 1.4rem; color: var(--c-muted); font-weight: 400;
  }
  .faq details[open] summary::after { content: '−'; }
  .faq details > p { margin: 10px 0 0; color: var(--c-muted); line-height: 1.6; }

  .final-cta {
    margin: 48px -16px 0;
    background: linear-gradient(135deg, var(--c-primary) 0%, #093479 100%);
    color: #fff; padding: 56px 24px; text-align: center;
    border-radius: 0;
  }
  .final-cta h2 { font-size: 2rem; margin: 0 0 12px; }
  .final-cta p  { opacity: .9; max-width: 540px; margin: 0 auto 24px; }
</style>

<!-- ─── Hero ─────────────────────────────────────────────────────────── -->
<section class="pitch-hero">
  <div class="eyebrow"><?= e(__('for_merchants.hero_eyebrow')) ?></div>
  <h1><?= e(__('for_merchants.hero_title')) ?></h1>
  <p class="lead"><?= e(__('for_merchants.hero_subtitle', ['fee' => $fee])) ?></p>
  <div class="cta-row">
    <a href="<?= e(url('/merchant/register')) ?>" class="btn btn-accent btn-lg">
      <?= e(__('for_merchants.hero_cta')) ?> →
    </a>
    <a href="#how" class="btn btn-outline btn-lg" style="background:rgba(255,255,255,.95);color:#0f172a;border-color:transparent;">
      <?= e(__('for_merchants.hero_cta_alt')) ?>
    </a>
  </div>
  <p class="note"><?= e(__('for_merchants.hero_note')) ?></p>
</section>

<!-- ─── Live stats strip ─────────────────────────────────────────────── -->
<div class="live-strip">
  <div class="eyebrow">⚡ <?= e(__('for_merchants.live_eyebrow')) ?></div>
  <div class="grid">
    <div class="stat"><div class="stat-value"><?= e((string) $stats['merchants']) ?></div><div class="stat-label"><?= e(__('for_merchants.live_merchants')) ?></div></div>
    <div class="stat"><div class="stat-value"><?= e((string) $stats['campaigns']) ?></div><div class="stat-label"><?= e(__('for_merchants.live_campaigns')) ?></div></div>
    <div class="stat"><div class="stat-value"><?= e((string) $stats['locations']) ?></div><div class="stat-label"><?= e(__('for_merchants.live_locations')) ?></div></div>
    <div class="stat"><div class="stat-value"><?= e((string) $stats['claimed']) ?></div><div class="stat-label"><?= e(__('for_merchants.live_claimed')) ?></div></div>
    <div class="stat"><div class="stat-value"><?= e((string) $stats['redeemed']) ?></div><div class="stat-label"><?= e(__('for_merchants.live_redeemed')) ?></div></div>
    <div class="stat"><div class="stat-value"><?= e((string) $stats['visitors']) ?></div><div class="stat-label"><?= e(__('for_merchants.live_visitors')) ?></div></div>
  </div>
</div>

<!-- ─── Why merchants join ───────────────────────────────────────────── -->
<section class="pitch">
  <h2><?= e(__('for_merchants.why_title')) ?></h2>
  <p class="section-sub"><?= e(__('app.tagline')) ?></p>

  <div class="why-grid">
    <?php
    $items = [
      ['🎯', 'why_1_title', 'why_1_body'],
      ['💸', 'why_2_title', 'why_2_body'],
      ['🖼️', 'why_3_title', 'why_3_body'],
      ['📱', 'why_4_title', 'why_4_body'],
      ['📊', 'why_5_title', 'why_5_body'],
      ['🌏', 'why_6_title', 'why_6_body'],
      ['🏛️', 'why_7_title', 'why_7_body'],
      ['🔌', 'why_8_title', 'why_8_body'],
    ];
    foreach ($items as [$icon, $tKey, $bKey]):
    ?>
      <div class="why-card">
        <div class="icon"><?= $icon ?></div>
        <h3><?= e(__('for_merchants.' . $tKey)) ?></h3>
        <p><?= e(__('for_merchants.' . $bKey, ['fee' => $fee])) ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- ─── How it works ─────────────────────────────────────────────────── -->
<section class="pitch" id="how">
  <h2><?= e(__('for_merchants.how_title')) ?></h2>
  <div class="how-steps mt-3">
    <div class="how-step">
      <h3><?= e(__('for_merchants.how_1_title')) ?></h3>
      <p class="text-muted" style="margin:0;"><?= e(__('for_merchants.how_1_body', ['sub' => $sub, 'wallet' => $wallet])) ?></p>
    </div>
    <div class="how-step">
      <h3><?= e(__('for_merchants.how_2_title')) ?></h3>
      <p class="text-muted" style="margin:0;"><?= e(__('for_merchants.how_2_body')) ?></p>
    </div>
    <div class="how-step">
      <h3><?= e(__('for_merchants.how_3_title')) ?></h3>
      <p class="text-muted" style="margin:0;"><?= e(__('for_merchants.how_3_body', ['fee' => $fee])) ?></p>
    </div>
  </div>
</section>

<!-- ─── Pricing ─────────────────────────────────────────────────────── -->
<section class="pitch" id="pricing">
  <h2><?= e(__('for_merchants.pricing_title')) ?></h2>

  <div class="pricing-grid mt-3">
    <div class="price-card">
      <h3><?= e(__('for_merchants.pricing_card1_title')) ?></h3>
      <div class="amount">RM<?= e($sub) ?></div>
      <div class="unit"><?= e(__('for_merchants.pricing_card1_unit')) ?></div>
      <p><?= e(__('for_merchants.pricing_card1_body')) ?></p>
    </div>
    <div class="price-card highlighted">
      <h3><?= e(__('for_merchants.pricing_card3_title')) ?></h3>
      <div class="amount">RM<?= e($fee) ?></div>
      <div class="unit"><?= e(__('for_merchants.pricing_card3_unit')) ?></div>
      <p><?= e(__('for_merchants.pricing_card3_body')) ?></p>
    </div>
    <div class="price-card">
      <h3><?= e(__('for_merchants.pricing_card2_title')) ?></h3>
      <div class="amount">RM<?= e($wallet) ?></div>
      <div class="unit"><?= e(__('for_merchants.pricing_card2_unit')) ?></div>
      <p><?= e(__('for_merchants.pricing_card2_body', ['wallet' => $wallet])) ?></p>
    </div>
  </div>
</section>

<!-- ─── ROI calculator ───────────────────────────────────────────────── -->
<section class="pitch" id="roi">
  <h2><?= e(__('for_merchants.roi_title')) ?></h2>
  <p class="section-sub"><?= e(__('for_merchants.roi_subtitle')) ?></p>

  <div class="roi-card">
    <div class="row">
      <div>
        <label for="roi-redemptions"><?= e(__('for_merchants.roi_redemptions')) ?></label>
        <input type="range" id="roi-redemptions" min="5" max="300" step="5" value="50" style="width:100%; margin: 8px 0;">
        <div class="text-muted"><span id="roi-redemptions-val">50</span> / month</div>
      </div>
      <div>
        <label for="roi-spend"><?= e(__('for_merchants.roi_avg_spend')) ?></label>
        <input type="range" id="roi-spend" min="10" max="200" step="5" value="40" style="width:100%; margin: 8px 0;">
        <div class="text-muted">RM <span id="roi-spend-val">40</span> per customer</div>
      </div>
    </div>
    <div class="result">
      <div class="row"><span class="text-muted"><?= e(__('for_merchants.roi_platform')) ?></span> <strong id="roi-cost">—</strong></div>
      <div class="row"><span class="text-muted"><?= e(__('for_merchants.roi_revenue')) ?></span> <strong id="roi-revenue">—</strong></div>
      <div class="net"><span><?= e(__('for_merchants.roi_net')) ?></span> <span id="roi-net">—</span></div>
      <div class="multiple"><span id="roi-mult">—×</span></div>
      <div class="text-muted text-center" style="font-size:.85rem;"><?= e(__('for_merchants.roi_multiple')) ?></div>
    </div>
  </div>
</section>

<!-- ─── Featured merchants ───────────────────────────────────────────── -->
<?php if (!empty($featured)): ?>
<section class="pitch">
  <h2><?= e(__('for_merchants.featured_title')) ?></h2>
  <p class="section-sub"><?= e(__('for_merchants.featured_body')) ?></p>

  <div class="featured-grid">
    <?php foreach ($featured as $m): ?>
      <div class="featured-card">
        <span class="badge"><?= e(__('merchant.category_options.' . $m['category'])) ?></span>
        <h3 style="margin:8px 0 4px;"><?= e($m['business_name']) ?></h3>
        <div class="text-muted" style="font-size:.85rem;">
          📍 <?= e($m['area_name'] ?? '') ?><?= !empty($m['state']) ? ', ' . e($m['state']) : '' ?>
        </div>
        <?php if ((int) $m['redemptions'] > 0): ?>
          <div class="text-muted mt-1" style="font-size:.85rem;">
            ✓ <?= e((string) $m['redemptions']) ?> <?= e(__('admin.totals.redeemed')) ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- ─── FAQ ──────────────────────────────────────────────────────────── -->
<section class="pitch faq" id="faq">
  <h2><?= e(__('for_merchants.faq_title')) ?></h2>
  <div style="max-width: 760px; margin: 24px auto 0;">
    <?php for ($i = 1; $i <= 6; $i++): ?>
      <details>
        <summary><?= e(__('for_merchants.faq_q' . $i, ['wallet' => $wallet])) ?></summary>
        <p><?= e(__('for_merchants.faq_a' . $i, ['wallet' => $wallet])) ?></p>
      </details>
    <?php endfor; ?>
  </div>
</section>

<!-- ─── Final CTA ────────────────────────────────────────────────────── -->
<section class="final-cta">
  <h2><?= e(__('for_merchants.cta_title')) ?></h2>
  <p><?= e(__('for_merchants.cta_body')) ?></p>
  <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
    <a class="btn btn-accent btn-lg" href="<?= e(url('/merchant/register')) ?>">
      <?= e(__('for_merchants.cta_primary', ['sub' => $sub])) ?> →
    </a>
    <a class="btn btn-outline btn-lg" style="background:rgba(255,255,255,.12); color:#fff; border-color:rgba(255,255,255,.4);" href="<?= e(url('/merchant/login')) ?>">
      <?= e(__('for_merchants.cta_secondary')) ?>
    </a>
  </div>
  <p class="text-muted mt-3" style="color:rgba(255,255,255,.75); font-size:.9rem;">
    <?= e(__('for_merchants.contact_line')) ?>
  </p>
</section>

<?php
$subFee = (float) $pricing['subscription'];
$perRedemption = (float) $pricing['redemption'];
\App\Core\View::pushScript('<script>
(function(){
  const SUB = ' . $subFee . ';
  const FEE = ' . $perRedemption . ';
  const fmt = n => "RM " + Number(n).toLocaleString("en-MY", {minimumFractionDigits:2, maximumFractionDigits:2});
  const r  = document.getElementById("roi-redemptions");
  const s  = document.getElementById("roi-spend");
  const rv = document.getElementById("roi-redemptions-val");
  const sv = document.getElementById("roi-spend-val");
  const cost = document.getElementById("roi-cost");
  const rev  = document.getElementById("roi-revenue");
  const net  = document.getElementById("roi-net");
  const mult = document.getElementById("roi-mult");
  function calc(){
    const red = +r.value, sp = +s.value;
    rv.textContent = red; sv.textContent = sp;
    const monthlyPlatform = (SUB / 12) + (red * FEE);
    const monthlyRevenue  = red * sp;
    const monthlyNet      = monthlyRevenue - monthlyPlatform;
    const multiple        = monthlyPlatform > 0 ? monthlyRevenue / monthlyPlatform : 0;
    cost.textContent = fmt(monthlyPlatform) + " / mo";
    rev.textContent  = fmt(monthlyRevenue)  + " / mo";
    net.textContent  = fmt(monthlyNet)      + " / mo";
    mult.textContent = (Math.round(multiple * 10) / 10).toLocaleString() + "×";
  }
  r.addEventListener("input", calc); s.addEventListener("input", calc);
  calc();
})();
</script>');
?>
