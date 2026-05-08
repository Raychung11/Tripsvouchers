<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Upload;
use App\Models\Setting;

/**
 * Admin: site-wide marketing assets.
 *
 * Lets the admin upload hero images for the public landing pages
 * (home, about, for-merchants) and override the intro copy on home
 * and about. Stored in the site_settings key/value table.
 */
class SiteController extends Controller
{
    /** Settings keys this page manages. Each may be an image or HTML. */
    private const KEYS = [
        'hero_home_image'          => 'image',
        'hero_about_image'         => 'image',
        'hero_for_merchants_image' => 'image',
        'intro_home_html'          => 'html',
        'intro_about_html'         => 'html',
    ];

    public function index(array $params): void
    {
        $values = [];
        foreach (array_keys(self::KEYS) as $k) {
            $values[$k] = Setting::get($k);
        }
        $this->render('admin/site/index', [
            'title'  => __('admin.site_settings'),
            'values' => $values,
        ]);
    }

    public function save(array $params): void
    {
        // Process image uploads + removals
        foreach (self::KEYS as $key => $type) {
            if ($type === 'image') {
                $current = Setting::get($key);

                if ($this->input($key . '_remove') === '1') {
                    if ($current) Upload::delete($current);
                    Setting::set($key, null);
                    continue;
                }

                try {
                    $uploaded = Upload::imageOrNull($_FILES[$key . '_file'] ?? null, 'site');
                } catch (\RuntimeException $e) {
                    flash('error', $e->getMessage());
                    redirect('/admin/site');
                }
                if ($uploaded) {
                    if ($current) Upload::delete($current);
                    Setting::set($key, $uploaded);
                }
            } elseif ($type === 'html') {
                $val = (string) $this->input($key, '');
                Setting::set($key, $val !== '' ? $val : null);
            }
        }

        flash('success', __('admin.save'));
        redirect('/admin/site');
    }
}
