<div class="container-sm" style="max-width:420px; margin:0 auto;">
  <div class="card mt-3">
    <h1 class="mt-0"><?= e(__('merchant.login_title')) ?></h1>
    <form method="post" action="<?= e(url('/merchant/login')) ?>">
      <?= csrf_field() ?>
      <div class="field">
        <label><?= e(__('auth.email')) ?></label>
        <input class="input" type="email" name="email" required value="<?= e((string) old('email')) ?>" autocomplete="email">
      </div>
      <div class="field">
        <label><?= e(__('auth.password')) ?></label>
        <input class="input" type="password" name="password" required autocomplete="current-password">
      </div>
      <button class="btn btn-primary btn-block btn-lg"><?= e(__('auth.submit')) ?></button>
    </form>
    <p class="text-center mt-2 text-muted">
      <?= e(__('auth.no_account')) ?>
      <a href="<?= e(url('/merchant/register')) ?>"><?= e(__('auth.register')) ?></a>
    </p>
  </div>
</div>
