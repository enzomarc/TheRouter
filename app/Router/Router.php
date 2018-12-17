<?php

namespace App\Router;


class Router
{

    private $url;
    private $routes = [];
    public $namedRoutes = [];
    public $controllersPath = "App\\Controller\\";

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function get(string $path, $callable, string $name = null) : Route
    {
        return $this->add($path, $callable, 'GET', $name);
    }

    public function post(string $path, $callable, string $name = null) : Route
    {
        return $this->add($path, $callable, 'POST', $name);
    }

    private function add(string $path, $callable, string $method, string $name = null) : Route
    {
        $route = new Route($this, $path, $callable, $this->controllersPath);
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

    public function url(string $name, array $params = []) : string
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new RouterException('No route matches this name');
        }

        return $this->namedRoutes[$name]->getUrl($params);
    }

}