<?php

namespace core;

require_once ROOT_PATH . '/app/middleware/HotelStaffMiddleware.php';
class Router
{
    private $routes = [];
    private $params = [];
    private $middlewares = [];

    public function addRoute($path, $handler)
    {
        $this->routes[$path] = [
            'pattern' => '#^' . str_replace('/', '\/', $path) . '$#',
            'handler' => $handler
        ];
    }

    public function addMiddleware($path, $middleware)
    {
        $this->middlewares[$path] = (array) $middleware;
    }

    // Thực thi middleware
    private function runMiddleware($middlewares, $next)
    {
        if (empty($middlewares)) {
            return $next();
        }

        $middleware = array_shift($middlewares);
        $middlewareClass = "app\\Middleware\\{$middleware}";

        if (class_exists($middlewareClass)) {
            $middlewareInstance = new $middlewareClass();
            return $middlewareInstance->handle(function () use ($middlewares, $next) {
                return $this->runMiddleware($middlewares, $next);
            });
        }

        throw new \Exception("Middleware not found: {$middleware}");
    }

    public function match($url)
    {
        if (isset($this->routes[$url])) {

            return $this->routes[$url]['handler'];
        }

        foreach ($this->routes as $path => $route) {
            if (preg_match($route['pattern'], $url, $matches)) {
                array_shift($matches);
                $this->params = $matches;
                return $route['handler'];
            }
        }


        return false;
    }

    public function dispatch()
    {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $fullUrl = $_SERVER['REQUEST_URI'];

            $basePath = parse_url(SITE_URL, PHP_URL_PATH) ?? '';
            $path = parse_url($fullUrl, PHP_URL_PATH);


            if ($basePath && strpos($path, $basePath) === 0) {
                $path = substr($path, strlen($basePath));
            }

            // Clean path
            $path = '/' . trim($path, '/');
            $handler = $this->match($path);

            if ($handler) {
                list($controller, $action) = explode('@', $handler);

                $controller = NAMESPACE_CONTROLLERS . '\\' . $controller;
                if (!class_exists($controller)) {
                    throw new \Exception("Controller not found: {$controller}");
                }

                $middlewares = $this->middlewares[$path] ?? [];
                if (!empty($middlewares)) {
                }

                $callback = function () use ($controller, $action) {
                    $controllerObject = new $controller();
                    return call_user_func_array([$controllerObject, $action], $this->params);
                };

                return $this->runMiddleware($middlewares, $callback);
            }

            throw new \Exception("No route found for: " . $path);
        } catch (\Exception $e) {
            $this->handleError($e);
        }
    }


    private function handleError(\Exception $e)
    {



        // Thêm thông tin debug chi tiết

        $controllerDir = ROOT_PATH . '/app/controllers';
        if (is_dir($controllerDir)) {
            $files = scandir($controllerDir);
        } else {
        }

        if (DEBUG_MODE) {
            throw $e;
        }

        header("HTTP/1.1 500 Internal Server Error");
        include VIEW_PATH . '/errors/500.php';
    }
}
