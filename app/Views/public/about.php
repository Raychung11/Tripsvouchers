<?php
$banner = \App\Models\Setting::get('hero_about_image');
$introHtml = \App\Models\Setting::get('intro_about_html');
?>
<?php if ($banner): ?>
  <div class="page-banner mb-3" style="background-image: url('<?= e(asset_or_upload($banner)) ?>');"></div>
<?php endif; ?>

<div class="container-md" style="margin: 0 auto;">
  <h1><?= e(__('app.name')) ?></h1>
  <p class="text-muted"><?= e(__('app.tagline')) ?></p>

  <?php if ($introHtml): ?>
    <div class="card"><?= $introHtml /* admin-controlled HTML */ ?></div>
  <?php endif; ?>

  <div class="card">
    <h2 class="mt-0">Core Value</h2>
    <p>Government promotes tourism. AI Tour Guide drives traffic. Visitors receive rewards. Merchants gain customers.</p>
    <p><strong><?= e(__('app.positioning')) ?></strong></p>
  </div>

  <div class="grid grid-2">
    <div class="card">
      <h3 class="mt-0">For Visitors</h3>
      <ul>
        <li>Chat with AI Tour Guide</li>
        <li>Receive instant voucher</li>
        <li>Save QR code</li>
        <li>Redeem at participating merchants</li>
      </ul>
    </div>
    <div class="card">
      <h3 class="mt-0">For Merchants</h3>
      <ul>
        <li>Annual subscription RM150</li>
        <li>Wallet system, RM1.50 per redemption</li>
        <li>QR scanner + manual entry</li>
        <li>Marketing assets &amp; analytics</li>
      </ul>
    </div>
  </div>
</div>
