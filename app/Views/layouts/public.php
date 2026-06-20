<?php
/**
 * Public layout — composes the header + content + footer partials.
 *
 * Header / footer are intentionally split into standalone include files
 * (partials/header.php and partials/footer.php) so any one-off page
 * (custom landing pages, print handouts, embedded widgets) can use them
 * directly without going through this layout.
 */
echo (new \App\Core\View())->partial('header', [
    'title'           => $title ?? '',
    'container_class' => $container_class ?? '',
]);
?>
<?= (new \App\Core\View())->partial('flash', ['_flash' => $_flash, '_errors' => $_errors]) ?>
<?= $content ?>
<?php echo (new \App\Core\View())->partial('footer'); ?>
