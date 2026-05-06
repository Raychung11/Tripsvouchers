<?php
declare(strict_types=1);

namespace App\Core;

class View
{
    private string $base;
    /** @var array<string> */
    private static array $scriptStack = [];

    public function __construct()
    {
        $this->base = SLV_ROOT . '/app/Views/';
    }

    public static function pushScript(string $tag): void
    {
        self::$scriptStack[] = $tag;
    }

    public static function popScripts(): string
    {
        $out = implode("\n", self::$scriptStack);
        self::$scriptStack = [];
        return $out;
    }

    public function render(string $template, array $data = [], ?string $layout = null): string
    {
        $file = $this->base . $template . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException("View [$template] not found at $file");
        }

        $data['_user'] = Auth::user();
        $data['_lang'] = Lang::current();
        $data['_flash'] = [
            'success' => flash('success'),
            'error'   => flash('error'),
            'info'    => flash('info'),
        ];
        $data['_errors'] = flash('errors') ?: [];

        $content = $this->capture($file, $data);

        // Auto-detect layout if not specified
        if ($layout === null) {
            if (str_starts_with($template, 'admin/')) {
                $layout = 'layouts/admin';
            } elseif (str_starts_with($template, 'merchant/')) {
                $layout = 'layouts/merchant';
            } elseif (str_starts_with($template, 'errors/')) {
                $layout = 'layouts/public';
            } elseif (!str_starts_with($template, 'layouts/') && !str_starts_with($template, 'partials/')) {
                $layout = 'layouts/public';
            }
        }

        if ($layout) {
            $layoutFile = $this->base . $layout . '.php';
            if (!is_file($layoutFile)) {
                throw new \RuntimeException("Layout [$layout] not found");
            }
            clear_old();
            return $this->capture($layoutFile, array_merge($data, ['content' => $content]));
        }

        clear_old();
        return $content;
    }

    private function capture(string $file, array $data): string
    {
        ob_start();
        extract($data, EXTR_SKIP);
        require $file;
        return (string) ob_get_clean();
    }

    public function partial(string $name, array $data = []): string
    {
        return $this->capture($this->base . 'partials/' . $name . '.php', $data);
    }
}
