<?php
$lang = \App\Core\Lang::current();
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$isActive = fn (string $p) => $path === $p || str_starts_with($path, $p . '/') ? 'active' : '';

$languages = [
    'ms' => ['flag' => '🇲🇾', 'short' => 'BM', 'label' => 'Bahasa Melayu'],
    'en' => ['flag' => '🇬🇧', 'short' => 'EN', 'label' => 'English'],
    'zh' => ['flag' => '🇨🇳', 'short' => '中文', 'label' => '中文'],
];
$currentLang = $languages[$lang] ?? $languages['ms'];
$backUrl = urlencode($_SERVER['REQUEST_URI'] ?? '/');
?>
<header class="topbar">
  <div class="topbar-inner">
    <a href="<?= e(url('/')) ?>" class="brand">
      <span class="brand-logo">SLV</span>
      <span class="hide-sm"><?= e(__('app.name')) ?></span>
    </a>
    <nav>
      <a href="<?= e(url('/campaigns')) ?>" class="<?= $isActive('/campaigns') ?>"><?= e(__('nav.campaigns')) ?></a>
      <a href="<?= e(url('/merchants')) ?>" class="<?= $isActive('/merchants') ?>"><?= e(__('nav.merchants')) ?></a>
      <a href="<?= e(url('/chat')) ?>" class="<?= $isActive('/chat') ?>"><?= e(__('nav.chat')) ?></a>
      <a href="<?= e(url('/for-merchants')) ?>" class="<?= $isActive('/for-merchants') ?>"
         style="background:var(--c-accent);color:#fff;font-weight:600;">
        <?= e(__('for_merchants.nav')) ?>
      </a>
      <details class="lang-dropdown" data-lang-dropdown>
        <summary aria-label="<?= e(__('common.language')) ?>">
          <span class="flag"><?= $currentLang['flag'] ?></span>
          <span class="short"><?= e($currentLang['short']) ?></span>
        </summary>
        <div class="lang-menu">
          <?php foreach ($languages as $code => $info): ?>
            <a href="<?= e(url('/lang/' . $code)) ?>?back=<?= e($backUrl) ?>"
               class="lang-option <?= $lang === $code ? 'active' : '' ?>">
              <span class="flag"><?= $info['flag'] ?></span>
              <span class="label"><?= e($info['label']) ?></span>
              <?php if ($lang === $code): ?><span class="check">✓</span><?php endif; ?>
            </a>
          <?php endforeach; ?>
        </div>
      </details>
      <?php if (\App\Core\Auth::check()): ?>
        <?php if (\App\Core\Auth::role() === 'merchant'): ?>
          <a href="<?= e(url('/merchant')) ?>" class="hide-sm"><?= e(__('nav.merchant_portal')) ?></a>
        <?php elseif (in_array(\App\Core\Auth::role(), ['admin','gov'], true)): ?>
          <a href="<?= e(url('/admin')) ?>" class="hide-sm"><?= e(__('nav.admin')) ?></a>
        <?php endif; ?>
      <?php else: ?>
        <a href="<?= e(url('/merchant/login')) ?>" class="hide-sm"><?= e(__('nav.login')) ?></a>
      <?php endif; ?>
    </nav>
  </div>
</header>
