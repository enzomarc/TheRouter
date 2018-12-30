<?php

namespace TheRouter\Router;


class Router
{

    private $url;
    private $routes = [];
    public $namedRoutes = [];
    public static $controllersPath = "App\\Controller\\";

    public function __construct(string $url = null)
    {
        if (is_null($url)) {
            $this->url = $_SERVER['REQUEST_URI'];
        } else {
            $this->url = $url;
        }
    }

    public function get(string $path, $callable, string $name = null) : Route
    {
        return $this->add($path, $callable, 'GET', $name);
    }

    public function post(string $path, $callable, string $name = null) : Route
    {
        return $this->add($path, $callable, 'POST', $name);
    }

    public function resource(string $model, $controller)
    {
        if (endsWith($model, 'ies'))
            $single = substr($model, 0, strlen($model) - 3) . 'y';
        elseif (endsWith($model, 's'))
            $single = substr($model, 0, strlen($model) - 1);

        $this->add($model, $controller . '@index', 'GET', $model . '.index');
        $this->add($model . '/create', $controller . '@create', 'GET', $model . '.create');
        $this->add($model, $controller . '@store', 'POST', $model . '.store');
        $this->add($model . '/:' . $single, $controller . '@show', 'GET', $model . '.show');
        $this->add($model . '/:' . $single . '/edit', $controller . '@edit', 'GET', $model . '.edit');
        $this->add($model . '/:' . $single, $controller . '@update', 'POST', $model . '.update');
        $this->add($model . '/:' . $single . '/delete', $controller . '@destroy', 'GET', $model . '.destroy');
    }

    private function add(string $path, $callable, string $method, string $name = null) : Route
    {
        $route = new Route($this, $path, $callable, self::$controllersPath);
        $this->routes[$method][] = $route;

        if (is_string($callable) && $name === null) {
            $name = $callable;
        }

        if ($name) {
            $this->namedRoutes[$name] = $route;
        }

        return $route;
    }

    public function run()
    {
        if (!isset($this->routes[$_SERVER['REQUEST_METHOD']])) {
            throw new RouterException('REQUEST_METHOD does not exist');
        }

        foreach ($this->routes[$_SERVER['REQUEST_METHOD']] as $route) {
            if ($route->match($this->url)) {
                return $route->call();
            }
        }

        throw new RouterException('No matching routes');
    }

    public function url(string $name, array $params = []): string
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new RouterException('No route matches this name ' . $name);
        }

        return '/' . $this->namedRoutes[$name]->getUrl($params);
    }

    public function redirect(string $name, array $params = []): void
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new RouterException('No route matches this name ' . $name);
        }

        header('location: /' . $this->namedRoutes[$name]->getUrl($params));
    }

    public static function setDefaultNamespace(string $namespace): void
    {
        self::$controllersPath = $namespace;
    }

    public function routes()
    {
        return $this->routes;
    }

}