<?php /** @var array $rows */ ?>
<h1 class="mt-0"><?= e(__('merchant.redemptions')) ?></h1>
<div class="card">
  <?php if (empty($rows)): ?>
    <p class="text-muted"><?= e(__('merchant.no_redemptions')) ?></p>
  <?php else: ?>
    <div class="table-wrap">
      <table class="table">
        <thead><tr>
          <th><?= e(__('common.date')) ?></th>
          <th><?= e(__('voucher.code')) ?></th>
          <th>Campaign</th>
          <th><?= e(__('common.name')) ?></th>
          <th><?= e(__('common.phone')) ?></th>
          <th>Fee</th>
          <th>Wallet after</th>
        </tr></thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= e($r['redeemed_at']) ?></td>
            <td class="font-mono"><?= e($r['voucher_code']) ?></td>
            <td><?= e($r['campaign_name']) ?></td>
            <td><?= e($r['customer_name']) ?></td>
            <td><?= e($r['customer_phone']) ?></td>
            <td><?= e(rm($r['redemption_fee'])) ?></td>
            <td><?= e(rm($r['wallet_balance_after'])) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
