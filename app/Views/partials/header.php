<?php
/**
 * Standalone full-document header.
 *
 * Renders <!doctype>, <head>, the topbar, and opens <main><div class="container">.
 * Pair with partials/footer.php to close the document.
 *
 * Usage from a controller-rendered view (paired with footer.php):
 *
 *     <?= (new \App\Core\View())->partial('header', [
 *         'title'           => 'Page title',           // optional
 *         'description'     => '...',                  // optional, defaults to app tagline
 *         'container_class' => 'container-sm',         // optional CSS class on the container
 *         'extra_head'      => '<link rel="...">',     // optional raw HTML injected into <head>
 *     ]) ?>
 *
 *     ... your page content ...
 *
 *     <?= (new \App\Core\View())->partial('footer') ?>
 *
 * Usage from a one-off PHP page (e.g. a custom landing page or print
 * handout) outside the View system:
 *
 *     require __DIR__ . '/../app/Views/partials/header.php';
 *     echo "<h1>Hello</h1>";
 *     require __DIR__ . '/../app/Views/partials/footer.php';
 */
$lang        = \App\Core\Lang::current();
$pageTitle   = isset($title) && $title !== '' ? $title . ' · ' . __('app.name') : __('app.name');
$description = $description ?? __('app.tagline');
?>
<!doctype html>
<html lang="<?= e($lang) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="theme-color" content="#0D47A1">
  <title><?= e($pageTitle) ?></title>
  <meta name="description" content="<?= e($description) ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
  <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
  <?php if (!empty($extra_head)) echo $extra_head; ?>
</head>
<body>
<?= (new \App\Core\View())->partial('topbar') ?>
<main>
  <div class="container <?= e($container_class ?? '') ?>" style="padding-top:24px;">
