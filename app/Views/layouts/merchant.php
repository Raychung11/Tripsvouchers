<?php
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$active = fn (string $p) => str_starts_with($path, $p) ? 'active' : '';
?>
<!doctype html>
<html lang="<?= e($_lang) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="theme-color" content="#0d9488">
  <title><?= e(($title ?? __('merchant.dashboard')) . ' · ' . __('app.name')) ?></title>
  <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body>
  <?= (new \App\Core\View())->partial('topbar') ?>
  <div class="app-shell">
    <aside class="app-side">
      <h3><?= e(__('nav.merchant_portal')) ?></h3>
      <a href="<?= e(url('/merchant/dashboard')) ?>" class="<?= $active('/merchant/dashboard') ?>"><?= e(__('merchant.dashboard')) ?></a>
      <a href="<?= e(url('/merchant/scanner')) ?>" class="<?= $active('/merchant/scanner') ?>"><?= e(__('merchant.scanner')) ?></a>
      <a href="<?= e(url('/merchant/redemptions')) ?>" class="<?= $active('/merchant/redemptions') ?>"><?= e(__('merchant.redemptions')) ?></a>
      <a href="<?= e(url('/merchant/wallet')) ?>" class="<?= $active('/merchant/wallet') ?>"><?= e(__('merchant.wallet')) ?></a>
      <a href="<?= e(url('/merchant/subscription')) ?>" class="<?= $active('/merchant/subscription') ?>"><?= e(__('merchant.subscription')) ?></a>
      <a href="<?= e(url('/merchant/campaigns')) ?>" class="<?= $active('/merchant/campaigns') ?>"><?= e(__('merchant.campaigns')) ?></a>
      <a href="<?= e(url('/merchant/marketing')) ?>" class="<?= $active('/merchant/marketing') ?>"><?= e(__('merchant.marketing')) ?></a>
      <a href="<?= e(url('/merchant/profile')) ?>" class="<?= $active('/merchant/profile') ?>"><?= e(__('merchant.profile')) ?></a>
      <hr>
      <form method="post" action="<?= e(url('/merchant/logout')) ?>">
        <?= csrf_field() ?>
        <button class="btn btn-outline btn-block btn-sm" type="submit"><?= e(__('nav.logout')) ?></button>
      </form>
    </aside>
    <main class="app-main">
      <?= (new \App\Core\View())->partial('flash', ['_flash' => $_flash, '_errors' => $_errors]) ?>
      <?= $content ?>
    </main>
  </div>
  <?= \App\Core\View::popScripts() ?>
  <script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
