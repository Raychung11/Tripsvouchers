<?php /** @var array $merchant */ ?>
<h1 class="mt-0"><?= e(__('merchant.profile')) ?></h1>
<form method="post" action="<?= e(url('/merchant/profile')) ?>" class="card" style="max-width:680px;">
  <?= csrf_field() ?>
  <div class="grid grid-2">
    <div class="field">
      <label><?= e(__('merchant.business_name')) ?></label>
      <input class="input" name="business_name" value="<?= e((string) old('business_name', $merchant['business_name'])) ?>" required>
    </div>
    <div class="field">
      <label><?= e(__('merchant.owner_name')) ?></label>
      <input class="input" name="owner_name" value="<?= e((string) old('owner_name', $merchant['owner_name'])) ?>" required>
    </div>
  </div>
  <div class="grid grid-2">
    <div class="field">
      <label><?= e(__('common.phone')) ?></label>
      <input class="input" name="phone" value="<?= e((string) old('phone', $merchant['phone'])) ?>" required>
    </div>
    <div class="field">
      <label>WhatsApp</label>
      <input class="input" name="whatsapp" value="<?= e((string) old('whatsapp', $merchant['whatsapp'] ?? '')) ?>">
    </div>
  </div>
  <div class="grid grid-2">
    <div class="field">
      <label><?= e(__('merchant.category')) ?></label>
      <select class="input" name="category">
        <?php foreach (['fnb','hotel','retail','souvenir','attraction','transport','experience','others'] as $c): ?>
          <option value="<?= e($c) ?>" <?= ($merchant['category'] === $c) ? 'selected' : '' ?>>
            <?= e(__('merchant.category_options.' . $c)) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="field">
      <label>Registration No.</label>
      <input class="input" name="registration_no" value="<?= e((string) old('registration_no', $merchant['registration_no'] ?? '')) ?>">
    </div>
  </div>
  <div class="field">
    <label><?= e(__('merchant.address')) ?></label>
    <textarea class="input" name="address"><?= e((string) old('address', $merchant['address'] ?? '')) ?></textarea>
  </div>
  <div class="grid grid-2">
    <div class="field">
      <label>Google Map link</label>
      <input class="input" name="map_link" value="<?= e((string) old('map_link', $merchant['map_link'] ?? '')) ?>" placeholder="https://maps.google.com/?q=…">
    </div>
    <div class="field">
      <label>Operating hours</label>
      <input class="input" name="operating_hours" value="<?= e((string) old('operating_hours', $merchant['operating_hours'] ?? '')) ?>" placeholder="11:00 - 22:00 daily">
    </div>
  </div>
  <button class="btn btn-primary btn-lg"><?= e(__('admin.save')) ?></button>
</form>
