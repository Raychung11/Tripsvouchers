<?php
declare(strict_types=1);

namespace App\Core;

class Router
{
    /** @var array<string, array<int, array{pattern:string,handler:array{0:string,1:string},middleware:array<string>}>> */
    private array $routes = [
        'GET' => [], 'POST' => [], 'PUT' => [], 'DELETE' => [],
    ];

    /** @var array<string> */
    private array $groupMiddleware = [];

    public function get(string $path, array $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, array $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    public function group(array $middleware, callable $callback): void
    {
        $previous = $this->groupMiddleware;
        $this->groupMiddleware = array_merge($previous, $middleware);
        $callback($this);
        $this->groupMiddleware = $previous;
    }

    private function add(string $method, string $path, array $handler, array $middleware): void
    {
        // Convert /merchants/{id} -> regex
        $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $path);
        $pattern = '#^' . rtrim($pattern, '/') . '/?$#';
        $this->routes[$method][] = [
            'pattern'    => $pattern,
            'handler'    => $handler,
            'middleware' => array_merge($this->groupMiddleware, $middleware),
        ];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        // Method override via _method in POST forms
        if ($method === 'POST' && isset($_POST['_method'])) {
            $override = strtoupper((string) $_POST['_method']);
            if (in_array($override, ['PUT', 'DELETE'], true)) {
                $method = $override;
            }
        }
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $uri = rtrim($uri, '/') ?: '/';

        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['pattern'], $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                foreach ($route['middleware'] as $mw) {
                    $this->runMiddleware($mw);
                }

                [$controllerClass, $action] = $route['handler'];
                $controller = new $controllerClass();
                $controller->{$action}($params);
                return;
            }
        }

        http_response_code(404);
        $view = new View();
        echo $view->render('errors/404', ['title' => 'Not found']);
    }

    private function runMiddleware(string $name): void
    {
        switch ($name) {
            case 'auth.admin':
                if (!Auth::user() || !in_array(Auth::user()['role'], ['admin', 'gov'], true)) {
                    flash('error', __('auth.login_required'));
                    redirect('/admin/login');
                }
                break;
            case 'auth.merchant':
                if (!Auth::user() || Auth::user()['role'] !== 'merchant') {
                    flash('error', __('auth.login_required'));
                    redirect('/merchant/login');
                }
                break;
            case 'csrf':
                if (!Csrf::verify($_POST['_csrf'] ?? null)) {
                    http_response_code(419);
                    echo 'CSRF token mismatch.';
                    exit;
                }
                break;
        }
    }
}
