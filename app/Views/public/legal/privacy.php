<?php /** @var array $company */ ?>
<div class="container-md" style="max-width:780px; margin:0 auto;">
  <h1 class="mt-0"><?= e(__('legal.privacy_title')) ?></h1>
  <p class="text-muted"><?= e(__('legal.privacy_intro')) ?></p>

  <div class="card">
    <h2 class="mt-0"><?= e(__('legal.privacy_h1')) ?></h2>
    <p><?= e(__('legal.privacy_p1')) ?></p>

    <h2><?= e(__('legal.privacy_h2')) ?></h2>
    <p><?= e(__('legal.privacy_p2')) ?></p>

    <h2><?= e(__('legal.privacy_h3')) ?></h2>
    <p><?= e(__('legal.privacy_p3')) ?></p>

    <h2><?= e(__('legal.privacy_h4')) ?></h2>
    <p><?= e(__('legal.privacy_p4')) ?></p>

    <h2><?= e(__('legal.privacy_h5')) ?></h2>
    <p><?= e(__('legal.privacy_p5', ['email' => $company['email'] ?? 'support@slvgroup.my'])) ?></p>

    <p class="text-muted mt-3" style="font-size:.85rem;">
      Last updated: <?= e(date('F Y')) ?> · <?= e($company['name'] ?? 'SLV Group Sdn Bhd') ?>
    </p>
  </div>
</div>
