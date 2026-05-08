<?php /** @var array $company */ /** @var array $pricing */
$sub    = number_format((float) ($pricing['subscription_fee'] ?? 150), 0);
$fee    = number_format((float) ($pricing['redemption_fee'] ?? 1.5), 2);
$wallet = number_format((float) ($pricing['wallet_min'] ?? 100), 0);
?>
<div class="container-md" style="max-width:780px; margin:0 auto;">
  <h1 class="mt-0"><?= e(__('legal.terms_title')) ?></h1>
  <p class="text-muted"><?= e(__('legal.terms_intro')) ?></p>

  <div class="card">
    <h2 class="mt-0"><?= e(__('legal.terms_h1')) ?></h2>
    <p><?= e(__('legal.terms_p1')) ?></p>

    <h2><?= e(__('legal.terms_h2')) ?></h2>
    <p><?= e(__('legal.terms_p2', ['sub' => $sub, 'fee' => $fee, 'wallet' => $wallet])) ?></p>

    <h2><?= e(__('legal.terms_h3')) ?></h2>
    <p><?= e(__('legal.terms_p3')) ?></p>

    <h2><?= e(__('legal.terms_h4')) ?></h2>
    <p><?= e(__('legal.terms_p4')) ?></p>

    <h2><?= e(__('legal.terms_h5')) ?></h2>
    <p><?= e(__('legal.terms_p5')) ?></p>

    <p class="text-muted mt-3" style="font-size:.85rem;">
      Last updated: <?= e(date('F Y')) ?> · <?= e($company['name'] ?? 'SLV Group Sdn Bhd') ?>
    </p>
  </div>
</div>
