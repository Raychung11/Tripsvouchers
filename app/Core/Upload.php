<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Image upload helper.
 *
 * Files are validated by MIME (via finfo, never trust client-provided
 * Content-Type) and saved under public/uploads/{bucket}/ with a random
 * SHA1-based filename. The original extension is mapped from the detected
 * MIME so a malicious .php uploaded as image/jpeg still ends up named .jpg.
 *
 * The bucket folder is auto-created. A .htaccess in public/uploads/ denies
 * PHP execution as defence in depth.
 *
 * Usage in a controller (with multipart/form-data form):
 *
 *     try {
 *         $path = Upload::imageOrNull($_FILES['banner'], 'campaigns');
 *         if ($path) { $data['banner_image'] = $path; }
 *     } catch (\RuntimeException $e) {
 *         flash('error', $e->getMessage());
 *         redirect(...);
 *     }
 */
class Upload
{
    public const MAX_BYTES = 4 * 1024 * 1024; // 4 MB

    /** @var array<string,string> mime → extension */
    private const ALLOWED = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];

    /**
     * Save an uploaded image. Returns a public-relative path
     * like "/uploads/campaigns/abc123.jpg" suitable for use in src=.
     *
     * If $file is not present (no upload), returns null.
     * Throws RuntimeException with a translated message on validation failure.
     */
    public static function imageOrNull(?array $file, string $bucket): ?string
    {
        if (!$file || !is_array($file)) {
            return null;
        }
        $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($error === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($error !== UPLOAD_ERR_OK) {
            throw new \RuntimeException(self::errorMessage($error));
        }

        $tmp = (string) ($file['tmp_name'] ?? '');
        if ($tmp === '' || !is_uploaded_file($tmp)) {
            throw new \RuntimeException(__('common.upload.invalid'));
        }

        $size = (int) ($file['size'] ?? 0);
        if ($size <= 0 || $size > self::MAX_BYTES) {
            throw new \RuntimeException(__('common.upload.too_large', [
                'max' => (int) (self::MAX_BYTES / 1024 / 1024),
            ]));
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = (string) $finfo->file($tmp);
        if (!isset(self::ALLOWED[$mime])) {
            throw new \RuntimeException(__('common.upload.bad_type'));
        }

        // Bonus sanity check — getimagesize ensures it parses as an image
        if (@getimagesize($tmp) === false) {
            throw new \RuntimeException(__('common.upload.bad_type'));
        }

        $ext = self::ALLOWED[$mime];
        $bucket = self::sanitizeBucket($bucket);
        $dir = SLV_ROOT . '/public/uploads/' . $bucket;
        if (!is_dir($dir)) {
            if (!@mkdir($dir, 0775, true) && !is_dir($dir)) {
                throw new \RuntimeException(__('common.upload.write_failed'));
            }
        }

        $name = bin2hex(random_bytes(8)) . '.' . $ext;
        $dest = $dir . '/' . $name;
        if (!@move_uploaded_file($tmp, $dest)) {
            throw new \RuntimeException(__('common.upload.write_failed'));
        }
        @chmod($dest, 0644);

        return '/uploads/' . $bucket . '/' . $name;
    }

    /**
     * Delete a previously-uploaded image given its stored path.
     * Silently ignores files outside public/uploads/.
     */
    public static function delete(?string $path): void
    {
        if (!$path) return;
        if (!str_starts_with($path, '/uploads/')) return;
        $abs = SLV_ROOT . '/public' . $path;
        $real = realpath($abs);
        $base = realpath(SLV_ROOT . '/public/uploads');
        if ($real && $base && str_starts_with($real, $base) && is_file($real)) {
            @unlink($real);
        }
    }

    private static function sanitizeBucket(string $bucket): string
    {
        $bucket = strtolower($bucket);
        $bucket = preg_replace('/[^a-z0-9_-]/', '', $bucket) ?: 'misc';
        return substr($bucket, 0, 32);
    }

    private static function errorMessage(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => __('common.upload.too_large', [
                'max' => (int) (self::MAX_BYTES / 1024 / 1024),
            ]),
            UPLOAD_ERR_PARTIAL    => __('common.upload.partial'),
            UPLOAD_ERR_NO_TMP_DIR,
            UPLOAD_ERR_CANT_WRITE => __('common.upload.write_failed'),
            default               => __('common.upload.invalid'),
        };
    }
}
