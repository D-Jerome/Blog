<?php

declare(strict_types=1);

namespace Framework;

class Router
{
    /**
     * Routes of config file
     *
     * @var array<Route>
     */
    protected array $routes;

    /**
     * __construct : Construct all routes of config file
     */
    public function __construct()
    {
        $routes = \Safe\json_decode(\Safe\file_get_contents(__DIR__.'/../config/routes.json'), true);
        if (true === \is_array($routes)) {
            foreach ($routes as $route) {
                $this->routes[] = new Route($route['path'], $route['method'], $route['controller'], $route['action'], $route['authorize']);
            }
        }
    }
    // end _construct()

    /**
     * findRoute: compare and match route and request
     *
     * @param Request $request Request object of page
     */
    public function findRoute(Request $request): ?Route
    {
        foreach ($this->routes as $route) {
            if ($route->getMethod() === $request->getMethod()) {
                \Safe\preg_match_all('/\{(\w*)\}/', $route->getPath(), $paramNames);
                $routeMatcher = \Safe\preg_replace('/\{(\w*)\}/', '(\S*)', $route->getPath());
                $routeMatcher = str_replace('/', '\/', $routeMatcher);
                if ($route->getPath() === $request->getUri()) {
                    return $route;
                }

                if (\Safe\preg_match_all("~^{$routeMatcher}$~", $request->getUri(), $params, \PREG_UNMATCHED_AS_NULL)) {
                    $paramsValues = [];
                    foreach ($paramNames[1] as $key => $names) {
                        $paramsValues[(string) $names] = $params[$key + 1][0];
                    }
                    $typeControllerObj = substr($route->getController(), strrpos($route->getController(), '\\') + 1);
                    if ($this->validateRoute($typeControllerObj, $paramsValues)) {
                        return $route;
                    }
                }// end if
            }// endif
        }// end foreach

        return null;
    }

    /**
     * validateRoute: verify the Existance of page and return True or False
     *
     * @param string               $typeObj Object used
     * @param array<string,string> $matches Result of preg_match route
     */
    private function validateRoute(string $typeObj, array $matches): bool
    {
        $valid = false;
        $matchesKey = array_keys($matches);
        $objectManagerName = 'App\\Model\\Manager\\'.$typeObj.'Manager';
        $getInstance = 'get'.$typeObj.'Instance';
        if (!empty($matches[$matchesKey[0]]) && !empty($matches[$matchesKey[1]]) && is_numeric($matches[$matchesKey[1]])) {
            $objectManager = $objectManagerName::$getInstance(Config::getDatasource());
            if (1 === $objectManager->verifyCouple($matches[$matchesKey[1]], $matches[$matchesKey[0]])) {
                $valid = true;
            }
        }// end if

        return $valid;
    }
}
