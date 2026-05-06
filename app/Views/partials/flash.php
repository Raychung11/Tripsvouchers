<?php
/** @var array $_flash */
?>
<?php if (!empty($_flash['success'])): ?>
  <div class="alert alert-success auto-dismiss"><?= e($_flash['success']) ?></div>
<?php endif; ?>
<?php if (!empty($_flash['error'])): ?>
  <div class="alert alert-error auto-dismiss"><?= e($_flash['error']) ?></div>
<?php endif; ?>
<?php if (!empty($_flash['info'])): ?>
  <div class="alert alert-info auto-dismiss"><?= e($_flash['info']) ?></div>
<?php endif; ?>
<?php if (!empty($_errors)): ?>
  <div class="alert alert-error">
    <ul style="margin:0; padding-left:1.2em;">
      <?php foreach ($_errors as $field => $msgs): foreach ((array) $msgs as $m): ?>
        <li><?= e($m) ?></li>
      <?php endforeach; endforeach; ?>
    </ul>
  </div>
<?php endif; ?>
