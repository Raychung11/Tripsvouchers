<?php
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$active = fn (string $p) => str_starts_with($path, $p) ? 'active' : '';
?>
<!doctype html>
<html lang="<?= e($_lang) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="theme-color" content="#0f172a">
  <title><?= e(($title ?? __('admin.dashboard')) . ' · Admin · ' . __('app.name')) ?></title>
  <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body>
  <?= (new \App\Core\View())->partial('topbar') ?>
  <div class="app-shell">
    <aside class="app-side">
      <h3><?= e(__('nav.admin')) ?></h3>
      <a href="<?= e(url('/admin/dashboard')) ?>" class="<?= $active('/admin/dashboard') ?>"><?= e(__('admin.dashboard')) ?></a>
      <a href="<?= e(url('/admin/merchants')) ?>" class="<?= $active('/admin/merchants') ?>"><?= e(__('admin.merchants')) ?></a>
      <a href="<?= e(url('/admin/campaigns')) ?>" class="<?= $active('/admin/campaigns') ?>"><?= e(__('admin.campaigns')) ?></a>
      <a href="<?= e(url('/admin/locations')) ?>" class="<?= $active('/admin/locations') ?>"><?= e(__('admin.locations')) ?></a>
      <a href="<?= e(url('/admin/vouchers')) ?>" class="<?= $active('/admin/vouchers') ?>"><?= e(__('admin.vouchers')) ?></a>
      <a href="<?= e(url('/admin/redemptions')) ?>" class="<?= $active('/admin/redemptions') ?>"><?= e(__('admin.redemptions')) ?></a>
      <a href="<?= e(url('/admin/wallet')) ?>" class="<?= $active('/admin/wallet') ?>"><?= e(__('admin.wallet')) ?></a>
      <a href="<?= e(url('/admin/analytics')) ?>" class="<?= $active('/admin/analytics') ?>"><?= e(__('admin.analytics')) ?></a>
      <a href="<?= e(url('/admin/meta')) ?>" class="<?= $active('/admin/meta') ?>">📱 <?= e(__('admin.meta.nav')) ?></a>
      <a href="<?= e(url('/admin/site')) ?>" class="<?= $active('/admin/site') ?>"><?= e(__('admin.site_settings')) ?></a>
      <hr>
      <form method="post" action="<?= e(url('/admin/logout')) ?>">
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
