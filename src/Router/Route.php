<?php

namespace TheRouter\Router;


class Route
{

    private $path;
    private $callable;
    private $matches = [];
    private $params = [];
    private $globals = [];
    private $router;

    public function __construct(Router $router, string $path, $callable)
    {
        $this->path = trim($path, '/');
        $this->callable = $callable;
        $this->router = $router;
    }

    public function match(string $url)
    {
        $url = trim($url, '/');
        $path = preg_replace_callback('#:([\w]+)#', [$this, 'paramMatch'], $this->path);
        $regex = "#^$path$#i";

        if (!preg_match($regex, $url, $matches)) {
            return false;
        }

        array_shift($matches);
        $this->matches = $matches;

        return true;
    }

    public function setMatch(string $regex) : boolean
    {
        $url = $this->path;
        
        if (!preg_match($regex, $url, $matches)) {
            return false;
        }

        array_shift($matches);
        $this->matches = $matches;

        return true;
    }

    private function paramMatch(array $match)
    {
        if (isset($this->params[$match[1]])) {
            return '(' . $this->params[$match[1]] . ')';
        }

        return '([^/]+)';
    }

    public function call()
    {
        if (is_string($this->callable)) {
            $params = explode('@', $this->callable);
            $controller = Router::$controllersPath . $params[0];
            $controller = new $controller();
            extract($this->globals);

            return call_user_func_array([$controller, $params[1]], $this->matches);
        } else {
            return call_user_func_array($this->callable, $this->matches);
        }
    }

    public function where(string $param, string $regex)
    {
        $this->params[$param] = str_replace('(', '(?:', $regex);

        return $this;
    }

    public function with(array $params): Route
    {
        foreach ($params as $param => $value) {
            if (!array_key_exists($param, $globals))
                $this->globals[$param] = $value;
        }

        extract($this->globals);

        return $this;
    }

    public function getUrl(array $params) : string
    {
        $path = $this->path;

        foreach ($params as $k => $v) {
            $path = str_replace(":$k", $v, $path);
        }

        return $path == null || $path == '' ? '/' : $path;
    }

    public function name(string $name)
    {
        if (!array_key_exists($name, $this->router->namedRoutes))
            $this->router->namedRoutes[$name] = $this;

        return $this;
    }

}