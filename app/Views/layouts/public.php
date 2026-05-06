<!doctype html>
<html lang="<?= e($_lang) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="theme-color" content="#0d9488">
  <title><?= e(($title ?? '') . ($title ?? '' ? ' · ' : '') . __('app.name')) ?></title>
  <meta name="description" content="<?= e(__('app.tagline')) ?>">
  <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body>
  <?= (new \App\Core\View())->partial('topbar') ?>
  <main>
    <div class="container <?= e($container_class ?? '') ?>" style="padding-top:24px;">
      <?= (new \App\Core\View())->partial('flash', ['_flash' => $_flash, '_errors' => $_errors]) ?>
      <?= $content ?>
    </div>
  </main>
  <?= (new \App\Core\View())->partial('footer') ?>
  <?= \App\Core\View::popScripts() ?>
  <script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
