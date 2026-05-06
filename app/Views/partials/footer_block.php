<footer class="footer">
  <div class="footer-inner">
    <div>
      <strong><?= e(__('app.name')) ?></strong> · <?= e(__('app.tagline')) ?>
    </div>
    <div>
      <a href="<?= e(url('/about')) ?>"><?= e(__('nav.about')) ?></a> ·
      <a href="<?= e(url('/for-merchants')) ?>"><?= e(__('for_merchants.nav')) ?></a> ·
      <a href="<?= e(url('/merchant/login')) ?>"><?= e(__('nav.merchant_portal')) ?></a> ·
      <a href="<?= e(url('/admin/login')) ?>"><?= e(__('nav.admin')) ?></a>
    </div>
    <div class="footer-credit">
      <?= e(__('app.positioning')) ?><br>
      <?= e(__('common.powered_by')) ?> &middot; &copy; <?= date('Y') ?>
    </div>
  </div>
</footer>
