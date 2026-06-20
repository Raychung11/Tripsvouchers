<?php
declare(strict_types=1);

/**
 * Hostinger entry point.
 *
 * Drop this file at /public_html/index.php (sibling of app/, config/,
 * database/, storage/, .env). Hostinger's docroot is public_html, so this
 * acts as the real front controller. We just delegate to the existing
 * public/index.php so the app's bootstrap stays in one place.
 *
 * If you prefer a flatter deploy, you can also delete public/index.php and
 * public/.htaccess and copy the body of public/index.php inline here —
 * SLV_ROOT will resolve identically because dirname(__DIR__) and __DIR__
 * both end up pointing at public_html.
 */

require __DIR__ . '/public/index.php';
