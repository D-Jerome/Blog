<?php

namespace Framework;

use Framework\Route;
use PhpParser\Node\Expr\Cast\Bool_;
use App\Model\Manager\{PostManager, CommentManager, UserManager};
use Framework\Helpers\Text;

class Router
{

    protected array $routes;

    public function __construct()
    {
        $routes = json_decode(file_get_contents(__DIR__ . '/../config/routes.json'), true);
        foreach ($routes as $route) {
            $this->routes[] = new Route($route['path'], $route['method'], $route['controller'], $route['action'], $route['authorize']);
        }
    }

    public function findRoute(Request $request): ?Route 
    {
        foreach ($this->routes as $route) {
            if ($route->getMethod() === $request->getMethod()) {
                preg_match_all('/\{(\w*)\}/', $route->getPath(), $paramNames);
                $routeMatcher = preg_replace('/\{(\w*)\}/', '(\S*)', $route->getPath());         
                $routeMatcher = str_replace('/', '\/', $routeMatcher);
                
                if ($route->getPath() === $request->getUri()) {
                    $route->setParams($request->getParams());
                    return $route;
                }
                
                if (preg_match_all("~^$routeMatcher$~", $request->getUri(), $params, PREG_UNMATCHED_AS_NULL)) {
                    $paramsValues = [];
                    foreach ($paramNames[1] as $key => $names) {
                        $paramsValues[$names] = $params[$key + 1][0];
                    }
                     
                    $typeControllerObj = substr($route->getController(),strrpos($route->getController(),"\\") + 1);
                    if ($this->validateRoute($typeControllerObj ,$paramsValues)) {
                        $route->setParams($request->getParams());
                        return $route;
                    }
                }     
            }
        }
        return null;
    }

    private function validateRoute(string $typeObj, array $matches): bool
    {
        // dd($matches);
        $valid = false;
        $matchesKey = array_keys($matches);
        
        $objectManagerName = "App\\Model\\Manager\\". $typeObj ."Manager";
        if ('' !== ($matches[$matchesKey[0]]) && '' !== ($matches[$matchesKey[1]]) && is_numeric($matches[$matchesKey[1]])) {
            $objectManager = new $objectManagerName(Application::getDatasource());
            if ($objectManager->verifyCouple($matches[$matchesKey[1]], $matches[$matchesKey[0]]) === 1) {
                $valid = true;
            }
        }
        return $valid;
    }
}
