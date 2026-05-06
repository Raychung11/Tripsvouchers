<div class="container-sm" style="max-width:540px; margin:0 auto;">
  <div class="card mt-3">
    <h1 class="mt-0"><?= e(__('merchant.register_title')) ?></h1>
    <form method="post" action="<?= e(url('/merchant/register')) ?>">
      <?= csrf_field() ?>
      <div class="field">
        <label><?= e(__('merchant.business_name')) ?></label>
        <input class="input" name="business_name" required value="<?= e((string) old('business_name')) ?>">
      </div>
      <div class="field">
        <label><?= e(__('merchant.owner_name')) ?></label>
        <input class="input" name="owner_name" required value="<?= e((string) old('owner_name')) ?>">
      </div>
      <div class="grid grid-2">
        <div class="field">
          <label><?= e(__('auth.email')) ?></label>
          <input class="input" type="email" name="email" required value="<?= e((string) old('email')) ?>">
        </div>
        <div class="field">
          <label><?= e(__('common.phone')) ?></label>
          <input class="input" type="tel" name="phone" required value="<?= e((string) old('phone')) ?>" placeholder="+60xxxxxxxxx">
        </div>
      </div>
      <div class="grid grid-2">
        <div class="field">
          <label>WhatsApp</label>
          <input class="input" name="whatsapp" value="<?= e((string) old('whatsapp')) ?>">
        </div>
        <div class="field">
          <label><?= e(__('merchant.category')) ?></label>
          <select class="input" name="category">
            <?php foreach (['fnb','hotel','retail','souvenir','attraction','transport','experience','others'] as $c): ?>
              <option value="<?= e($c) ?>" <?= old('category') === $c ? 'selected' : '' ?>><?= e(__('merchant.category_options.' . $c)) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="field">
        <label><?= e(__('merchant.address')) ?></label>
        <textarea class="input" name="address"><?= e((string) old('address')) ?></textarea>
      </div>
      <div class="field">
        <label><?= e(__('auth.password')) ?></label>
        <input class="input" type="password" name="password" required minlength="6">
      </div>
      <button class="btn btn-primary btn-block btn-lg"><?= e(__('auth.register')) ?></button>
    </form>
    <p class="text-center mt-2 text-muted">
      <?= e(__('auth.have_account')) ?>
      <a href="<?= e(url('/merchant/login')) ?>"><?= e(__('auth.submit')) ?></a>
    </p>
  </div>
</div>
