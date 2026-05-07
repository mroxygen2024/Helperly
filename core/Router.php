<?php

declare(strict_types=1);

class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => []
    ];
    
    /** @var callable|null */
    private $fallbackHandler = null;

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, callable $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function setFallback(callable $handler): void
    {
        $this->fallbackHandler = $handler;
    }

    public function dispatch(string $method, string $path): void
    {
        if (isset($this->routes[$method][$path])) {
            call_user_func($this->routes[$method][$path]);
            return;
        }

        if ($this->fallbackHandler !== null) {
            call_user_func($this->fallbackHandler, $method, $path);
            return;
        }

        $this->abort();
    }

    public function abort(): void
    {
        http_response_code(404);
        renderView('errors/404', [
            'title' => 'Not Found',
            'message' => 'The requested route could not be matched.',
        ]);
        exit;
    }
}
