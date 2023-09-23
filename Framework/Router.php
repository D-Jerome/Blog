<?php

namespace Framework;

class Router
{

    protected array $routes;


    public function __construct()
    {
        $routes = json_decode(file_get_contents(__DIR__ . '/../config/routes.json'),true);
        foreach($routes as $route)
        {
            $this->routes[] = new Route($route['path'],$route['method'],$route['controller'],$route['action']);

        }
        
    }

    public function findRoute(Request $request): ?Route
    {
        foreach($this->routes as $route)
        {
            if($route->getPath() === $request->getUri()) {
                return $route;
            }
        }
        return null;
    }
}