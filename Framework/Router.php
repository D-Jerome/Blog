<?php

namespace Framework;

use Framework\Route;

class Router
{

    protected array $routes;


    public function __construct()
    {
        $routes = json_decode(file_get_contents(__DIR__ . '/../config/routes.json'),true);
        foreach($routes as $route)
        {
            $this->routes[] = new Route($route['path'],$route['method'],$route['controller'],$route['action'],$route['authorize']);
            
        }
        
    }

    public function findRoute(Request $request): ?Route
    {
       
        
        foreach($this->routes as $route)
        {
            if ($route->getMethod() === $request->getMethod()){    
                
                $pattern = '~^' . $route->getPath() . '$~';

                
                if (preg_match_all($pattern, $request->getUri(), $matches, PREG_UNMATCHED_AS_NULL) != null ) {
                    return $route;
                }elseif($route->getPath() === $request->getUri()) {
                    
                    return $route;
                }
            }    
        }
        return null;
    }
}