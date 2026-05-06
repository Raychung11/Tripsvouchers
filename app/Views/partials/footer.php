<?php
/**
 * Standalone full-document footer.
 *
 * Closes the <main><div class="container"> opened in partials/header.php,
 * renders the visible footer block, flushes any pushed scripts, loads the
 * shared app.js bundle and closes </body></html>.
 *
 * See partials/header.php for usage examples.
 */
?>
  </div>
</main>
<?= (new \App\Core\View())->partial('footer_block') ?>
<?= \App\Core\View::popScripts() ?>
<script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
