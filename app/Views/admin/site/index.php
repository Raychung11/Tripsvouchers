<?php /** @var array $values */ ?>
<h1 class="mt-0"><?= e(__('admin.site_settings')) ?></h1>
<p class="text-muted"><?= e(__('admin.site_settings_intro')) ?></p>

<form method="post" action="<?= e(url('/admin/site')) ?>" enctype="multipart/form-data" class="card" style="max-width:820px;">
  <?= csrf_field() ?>

  <?php
  $imageFields = [
      'hero_home_image'          => ['label' => __('admin.site_hero_home'),          'hint' => __('admin.site_hero_home_hint')],
      'hero_about_image'         => ['label' => __('admin.site_hero_about'),         'hint' => __('admin.site_hero_about_hint')],
      'hero_for_merchants_image' => ['label' => __('admin.site_hero_for_merchants'), 'hint' => __('admin.site_hero_for_merchants_hint')],
  ];
  foreach ($imageFields as $key => $info):
      $current = $values[$key] ?? null;
  ?>
    <div class="field">
      <label><?= e($info['label']) ?></label>
      <p class="help" style="margin-top:0;"><?= e($info['hint']) ?></p>

      <?php if ($current): ?>
        <div class="upload-preview mb-1" style="max-width:480px;">
          <img src="<?= e(asset_or_upload($current)) ?>" alt="">
          <label class="upload-remove">
            <input type="checkbox" name="<?= e($key) ?>_remove" value="1">
            <?= e(__('common.upload.remove')) ?>
          </label>
        </div>
      <?php endif; ?>

      <input class="input" type="file" name="<?= e($key) ?>_file" accept="image/*">
      <p class="help"><?= e(__('common.upload.help', ['max' => 4])) ?></p>
    </div>
  <?php endforeach; ?>

  <hr>

  <div class="field">
    <label><?= e(__('admin.site_intro_home')) ?></label>
    <p class="help" style="margin-top:0;"><?= e(__('admin.site_intro_home_hint')) ?></p>
    <textarea class="input" name="intro_home_html" rows="6"
              placeholder="&lt;p&gt;…&lt;/p&gt;"><?= e((string) ($values['intro_home_html'] ?? '')) ?></textarea>
  </div>

  <div class="field">
    <label><?= e(__('admin.site_intro_about')) ?></label>
    <p class="help" style="margin-top:0;"><?= e(__('admin.site_intro_about_hint')) ?></p>
    <textarea class="input" name="intro_about_html" rows="6"
              placeholder="&lt;p&gt;…&lt;/p&gt;"><?= e((string) ($values['intro_about_html'] ?? '')) ?></textarea>
  </div>

  <button class="btn btn-primary btn-lg"><?= e(__('admin.save')) ?></button>
</form>
