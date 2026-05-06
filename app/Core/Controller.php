<?php
declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected View $view;

    public function __construct()
    {
        $this->view = new View();
    }

    protected function render(string $template, array $data = [], ?string $layout = null): void
    {
        echo $this->view->render($template, $data, $layout);
    }

    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    protected function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function all(): array
    {
        return array_merge($_GET, $_POST);
    }

    protected function validate(array $rules, array $data): array
    {
        $errors = [];
        foreach ($rules as $field => $ruleset) {
            $value = $data[$field] ?? null;
            foreach (explode('|', $ruleset) as $rule) {
                if ($rule === 'required' && (is_null($value) || $value === '')) {
                    $errors[$field][] = __('validation.required', ['field' => $field]);
                }
                if ($rule === 'email' && $value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = __('validation.email', ['field' => $field]);
                }
                if ($rule === 'numeric' && $value !== null && $value !== '' && !is_numeric($value)) {
                    $errors[$field][] = __('validation.numeric', ['field' => $field]);
                }
                if (str_starts_with($rule, 'min:')) {
                    $min = (int) substr($rule, 4);
                    if ($value !== null && strlen((string) $value) < $min) {
                        $errors[$field][] = __('validation.min', ['field' => $field, 'min' => $min]);
                    }
                }
                if (str_starts_with($rule, 'gte:')) {
                    $min = (float) substr($rule, 4);
                    if ($value !== null && (float) $value < $min) {
                        $errors[$field][] = __('validation.gte', ['field' => $field, 'min' => $min]);
                    }
                }
            }
        }
        return $errors;
    }
}
