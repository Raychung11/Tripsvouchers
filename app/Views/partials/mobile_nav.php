<?php
/**
 * Mobile bottom-tab navigation, native-web-app style.
 *
 * - Always-visible 5-button bar fixed to the bottom of the viewport on
 *   screens < 880px wide; hidden on desktop where the topbar/sidebar
 *   already provide navigation.
 * - Tabs adapt to the current actor role (visitor / merchant / admin).
 * - 5th button is "More" — opens a slide-up bottom sheet listing the
 *   secondary destinations + language switcher + auth links.
 * - Safe-area aware (iOS notch / home indicator handled via env()).
 *
 * Styles + script are co-located inside this partial so a single file
 * upload deploys the whole mobile-nav component end-to-end.
 */

$role = \App\Core\Auth::role();
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$lang = \App\Core\Lang::current();
$backUrl = urlencode($_SERVER['REQUEST_URI'] ?? '/');

$isActive = function (string $url) use ($path): bool {
    if ($url === '/')        return $path === '/';
    if ($url === '/admin')    return $path === '/admin' || $path === '/admin/dashboard';
    if ($url === '/merchant') return $path === '/merchant' || $path === '/merchant/dashboard';
    return $path === $url || str_starts_with($path, $url . '/');
};

// Role-specific tabs (max 4 + "More" = 5 total)
if ($role === 'admin' || $role === 'gov') {
    $tabs = [
        ['url' => '/admin',                'icon' => '📊', 'label' => __('admin.dashboard')],
        ['url' => '/admin/merchants',      'icon' => '🏪', 'label' => __('admin.merchants')],
        ['url' => '/admin/campaigns',      'icon' => '🎯', 'label' => __('admin.campaigns')],
        ['url' => '/admin/analytics',      'icon' => '📈', 'label' => __('admin.analytics')],
    ];
    $more = [
        ['url' => '/admin/locations',   'label' => __('admin.locations')],
        ['url' => '/admin/vouchers',    'label' => __('admin.vouchers')],
        ['url' => '/admin/redemptions', 'label' => __('admin.redemptions')],
        ['url' => '/admin/wallet',      'label' => __('admin.wallet')],
        ['url' => '/admin/meta',        'label' => __('admin.meta.nav')],
        ['url' => '/admin/site',        'label' => __('admin.site_settings')],
    ];
    $logoutUrl = '/admin/logout';
} elseif ($role === 'merchant') {
    $tabs = [
        ['url' => '/merchant',             'icon' => '📊', 'label' => __('merchant.dashboard')],
        ['url' => '/merchant/scanner',     'icon' => '📷', 'label' => __('merchant.scanner'),     'primary' => true],
        ['url' => '/merchant/wallet',      'icon' => '💼', 'label' => __('merchant.wallet')],
        ['url' => '/merchant/redemptions', 'icon' => '📋', 'label' => __('merchant.redemptions')],
    ];
    $more = [
        ['url' => '/merchant/subscription', 'label' => __('merchant.subscription')],
        ['url' => '/merchant/marketing',    'label' => __('merchant.marketing')],
        ['url' => '/merchant/campaigns',    'label' => __('merchant.campaigns')],
        ['url' => '/merchant/profile',      'label' => __('merchant.profile')],
    ];
    $logoutUrl = '/merchant/logout';
} else {
    $tabs = [
        ['url' => '/',           'icon' => '🏠', 'label' => __('nav.home')],
        ['url' => '/campaigns',  'icon' => '🎫', 'label' => __('nav.campaigns')],
        ['url' => '/chat',       'icon' => '💬', 'label' => __('nav.chat'),     'primary' => true],
        ['url' => '/merchants',  'icon' => '🏪', 'label' => __('nav.merchants')],
    ];
    $more = [
        ['url' => '/for-merchants', 'label' => __('for_merchants.nav')],
        ['url' => '/about',         'label' => __('nav.about')],
        ['url' => '/contact',       'label' => __('footer.col_contact')],
        ['url' => '/merchant/login','label' => __('nav.login')],
        ['url' => '/admin/login',   'label' => __('nav.admin')],
    ];
    $logoutUrl = null;
}

$languages = [
    'ms' => ['flag' => '🇲🇾', 'label' => 'Bahasa Melayu'],
    'en' => ['flag' => '🇬🇧', 'label' => 'English'],
    'zh' => ['flag' => '🇨🇳', 'label' => '中文'],
];
?>
<style>
/* ── Bottom tab bar ─────────────────────────────────────────────────── */
.mobile-nav { display: none; }
@media (max-width: 879px) {
  .mobile-nav {
    display: grid; grid-auto-flow: column; grid-auto-columns: 1fr;
    position: fixed; bottom: 0; left: 0; right: 0; z-index: 90;
    background: rgba(255,255,255,.92);
    backdrop-filter: saturate(180%) blur(14px);
    -webkit-backdrop-filter: saturate(180%) blur(14px);
    border-top: 1px solid rgba(15,23,42,.08);
    padding-bottom: env(safe-area-inset-bottom, 0);
    box-shadow: 0 -2px 14px rgba(15,23,42,.06);
  }
  /* Reserve space at the bottom so content isn't hidden behind the bar */
  body { padding-bottom: calc(64px + env(safe-area-inset-bottom, 0)); }
  /* Hide the topbar text links on mobile — bottom nav takes over */
  .topbar nav > a { display: none !important; }
  .topbar { box-shadow: none; }
}
.mn-tab {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  gap: 3px; padding: 8px 4px; min-height: 60px;
  text-decoration: none; color: #64748b;
  background: transparent; border: 0; cursor: pointer;
  font-family: inherit; font-size: .68rem;
  position: relative;
  transition: color .15s ease;
  -webkit-tap-highlight-color: rgba(13,148,136,.2);
}
.mn-tab:hover, .mn-tab:focus-visible { color: var(--c-primary, #0d9488); text-decoration: none; outline: none; }
.mn-tab.active { color: var(--c-primary, #0d9488); }
.mn-tab.active::before {
  content: ''; position: absolute; top: 0; left: 50%;
  transform: translateX(-50%);
  width: 28px; height: 3px;
  background: var(--c-primary, #0d9488);
  border-radius: 0 0 4px 4px;
}
.mn-icon { font-size: 1.45rem; line-height: 1; }
.mn-label { font-size: .68rem; font-weight: 500; line-height: 1.1; }
.mn-tab.primary .mn-icon {
  background: linear-gradient(135deg, #0d9488, #f59e0b);
  color: #fff; width: 44px; height: 44px; border-radius: 50%;
  display: grid; place-items: center;
  box-shadow: 0 4px 14px rgba(13,148,136,.35);
  margin-top: -16px;  /* lift the FAB-ish primary action */
}
.mn-tab.primary { padding-top: 18px; }
.mn-tab.primary.active::before { display: none; }

/* ── Slide-up "More" sheet ──────────────────────────────────────────── */
.mobile-sheet {
  position: fixed; inset: 0; z-index: 110;
  visibility: hidden; opacity: 0;
  transition: opacity .2s ease, visibility .2s ease;
}
.mobile-sheet.open { visibility: visible; opacity: 1; }
.ms-backdrop {
  position: absolute; inset: 0; background: rgba(15,23,42,.55);
  cursor: pointer;
}
.ms-panel {
  position: absolute; bottom: 0; left: 0; right: 0;
  background: #fff; border-radius: 18px 18px 0 0;
  padding: 12px 16px calc(20px + env(safe-area-inset-bottom, 0));
  transform: translateY(100%);
  transition: transform .25s cubic-bezier(.32,.72,.32,1);
  max-height: 88vh; overflow-y: auto;
  box-shadow: 0 -10px 40px rgba(15,23,42,.25);
}
.mobile-sheet.open .ms-panel { transform: translateY(0); }
.ms-handle {
  width: 40px; height: 4px; background: #cbd5e1;
  border-radius: 2px; margin: 4px auto 16px;
}
.ms-title { font-size: .78rem; text-transform: uppercase; letter-spacing: .08em;
  color: #64748b; margin: 14px 4px 8px; font-weight: 700; }
.ms-list { list-style: none; padding: 0; margin: 0;
  display: flex; flex-direction: column; gap: 2px; }
.ms-list a, .ms-list button {
  display: flex; align-items: center; gap: 12px;
  padding: 12px 14px; border-radius: 10px;
  color: #0f172a; text-decoration: none; font-size: .96rem;
  background: transparent; border: 0; width: 100%;
  font-family: inherit; cursor: pointer; text-align: left;
}
.ms-list a:hover, .ms-list button:hover { background: #f1f5f9; text-decoration: none; }
.ms-list a.active { background: var(--c-primary-lt, #ccfbf1); color: var(--c-primary-dk, #0f766e); font-weight: 600; }
.ms-list .arrow { margin-left: auto; color: #94a3b8; }
.ms-langs { display: flex; gap: 6px; flex-wrap: wrap; padding: 4px; }
.ms-langs a {
  flex: 1; min-width: 90px; text-align: center;
  padding: 10px; border-radius: 10px; background: #f1f5f9;
  font-size: .9rem; color: #0f172a; text-decoration: none;
}
.ms-langs a.active { background: var(--c-primary, #0d9488); color: #fff; }
.ms-logout { border-top: 1px solid var(--c-border, #e5e7eb); margin-top: 12px; padding-top: 8px; }
.ms-logout button { color: #dc2626; font-weight: 600; }

/* Lock body scroll while sheet is open */
body.sheet-open { overflow: hidden; }
</style>

<nav class="mobile-nav" role="navigation" aria-label="Primary mobile navigation">
  <?php foreach ($tabs as $t):
    $active  = $isActive($t['url']);
    $primary = !empty($t['primary']);
    $cls = array_filter(['mn-tab', $active ? 'active' : '', $primary ? 'primary' : '']);
  ?>
    <a href="<?= e(url($t['url'])) ?>"
       class="<?= e(implode(' ', $cls)) ?>"
       <?= $active ? 'aria-current="page"' : '' ?>>
      <span class="mn-icon"><?= $t['icon'] ?></span>
      <span class="mn-label"><?= e($t['label']) ?></span>
    </a>
  <?php endforeach; ?>
  <button type="button" class="mn-tab" data-mobile-more aria-haspopup="true" aria-controls="mobileSheet">
    <span class="mn-icon">☰</span>
    <span class="mn-label"><?= e(__('nav.more')) ?></span>
  </button>
</nav>

<div class="mobile-sheet" id="mobileSheet" data-mobile-sheet aria-hidden="true">
  <div class="ms-backdrop" data-mobile-close></div>
  <div class="ms-panel" role="dialog" aria-modal="true" aria-label="<?= e(__('nav.more')) ?>">
    <div class="ms-handle" data-mobile-close></div>

    <?php if (!empty($more)): ?>
      <div class="ms-title"><?= e(__('nav.more')) ?></div>
      <ul class="ms-list">
        <?php foreach ($more as $m): ?>
          <li>
            <a href="<?= e(url($m['url'])) ?>" class="<?= $isActive($m['url']) ? 'active' : '' ?>">
              <span><?= e($m['label']) ?></span>
              <span class="arrow">›</span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <div class="ms-title"><?= e(__('common.language')) ?></div>
    <div class="ms-langs">
      <?php foreach ($languages as $code => $info): ?>
        <a href="<?= e(url('/lang/' . $code)) ?>?back=<?= e($backUrl) ?>"
           class="<?= $lang === $code ? 'active' : '' ?>">
          <?= $info['flag'] ?> <?= e($info['label']) ?>
        </a>
      <?php endforeach; ?>
    </div>

    <?php if ($logoutUrl): ?>
      <div class="ms-logout">
        <ul class="ms-list">
          <li>
            <form method="post" action="<?= e(url($logoutUrl)) ?>" style="margin:0;">
              <?= csrf_field() ?>
              <button type="submit">
                ↩ <?= e(__('nav.logout')) ?>
              </button>
            </form>
          </li>
        </ul>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
(function () {
  var sheet = document.querySelector('[data-mobile-sheet]');
  if (!sheet) return;
  var openBtn = document.querySelector('[data-mobile-more]');
  var closers = sheet.querySelectorAll('[data-mobile-close]');

  function open() {
    sheet.classList.add('open');
    sheet.setAttribute('aria-hidden', 'false');
    document.body.classList.add('sheet-open');
    if (openBtn) openBtn.setAttribute('aria-expanded', 'true');
  }
  function close() {
    sheet.classList.remove('open');
    sheet.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('sheet-open');
    if (openBtn) openBtn.setAttribute('aria-expanded', 'false');
  }
  if (openBtn) openBtn.addEventListener('click', function () {
    sheet.classList.contains('open') ? close() : open();
  });
  closers.forEach(function (el) { el.addEventListener('click', close); });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && sheet.classList.contains('open')) close();
  });
  // Close after clicking any link inside the sheet (back-forward UX)
  sheet.querySelectorAll('a').forEach(function (a) {
    a.addEventListener('click', function () { setTimeout(close, 50); });
  });
})();
</script>
