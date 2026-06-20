<?php
/** @var array $company */
$waNumber = preg_replace('/\D+/', '', (string) ($company['whatsapp'] ?? ''));
$waLink = $waNumber !== '' ? 'https://wa.me/' . $waNumber : '#';
?>
<div class="container-md" style="max-width:780px; margin:0 auto;">
  <h1 class="mt-0"><?= e(__('legal.contact_title')) ?></h1>
  <p class="text-muted"><?= e(__('legal.contact_intro')) ?></p>

  <div class="grid grid-2">
    <div class="card">
      <h3 class="mt-0">📱 <?= e(__('footer.whatsapp_label')) ?></h3>
      <p class="text-muted"><?= e(__('legal.contact_general')) ?> · <?= e(__('legal.contact_merchant')) ?></p>
      <p class="font-mono" style="font-size:1.1rem;"><?= e($company['phone'] ?? '+60 ' . $waNumber) ?></p>
      <a href="<?= e($waLink) ?>" target="_blank" rel="noopener" class="btn btn-success btn-block">
        Open WhatsApp →
      </a>
    </div>
    <div class="card">
      <h3 class="mt-0">✉️ <?= e(__('footer.email_label')) ?></h3>
      <p class="text-muted"><?= e(__('legal.contact_press')) ?> · <?= e(__('legal.contact_general')) ?></p>
      <p class="font-mono" style="font-size:1.05rem; word-break:break-all;"><?= e($company['email'] ?? '') ?></p>
      <a href="mailto:<?= e($company['email'] ?? '') ?>" class="btn btn-primary btn-block">
        Send email →
      </a>
    </div>
  </div>

  <div class="card">
    <h3 class="mt-0">📍 <?= e(__('legal.contact_address')) ?></h3>
    <p style="margin:0;">
      <strong><?= e($company['name'] ?? 'SLV Group Sdn Bhd') ?></strong><br>
      <?= nl2br(e($company['address'] ?? '')) ?><br>
      <span class="text-muted">
        Reg: <?= e($company['reg_no'] ?? '—') ?>
      </span>
    </p>
  </div>
</div>
