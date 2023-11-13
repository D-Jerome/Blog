<?php

namespace Framework;

use ArrayObject;
use Framework\Exception\MultipleRouteFoundException;
use Framework\Exception\NoRouteFoundException;

final class Application
{
    private Request $request;
    private Router $router;
    private static array $config;

    public function __construct()
    {
        self::$config = json_decode(file_get_contents(__DIR__ . '/../config/config.json'), true);
        $this->request = new Request(self::$config['baseUrl']);
        $this->router = new Router();
    }
    
    /**
     * router of application
     *
     * @return void
     */
    public function run()
    {
        $msgErr = false;
        try {
            $foundRoute = $this->router->findRoute($this->request);
            if (null === $foundRoute) {
                throw new NoRouteFoundException;
            }
            $controller = $foundRoute->getController();
            $action =  $foundRoute->getaction();
            $authRoles = $foundRoute->getAuthRoles();
            $route = new $controller($foundRoute);
            if (!$route->isAuthorize($authRoles)) {
                header('Location: /blog-project/?auth=0');
            }

            if ($route->isAuthorize($authRoles)) {
           
             
                if (preg_match_all('/\{(\w*)\}/', $foundRoute->getPath(), $paramNames)) {
                    
                    $routeMatcher = preg_replace('/\{(\w*)\}/', '(\S*)', $foundRoute->getPath());
                    $routeMatcher = str_replace('/', '\/', $routeMatcher);
                    
                    if (preg_match_all("~^$routeMatcher$~", $this->request->getUri(), $params, PREG_UNMATCHED_AS_NULL)) {
                        $paramsValues = [];
                        foreach ($paramNames[1] as $key => $names) {
                            $paramsValues[$names] = $params[$key + 1][0];
                        }
                        $paramsKeys = array_keys($paramsValues);
                        foreach ($paramsKeys as $paramsKey) {
                            if (stripos($paramsKey, 'id') !== 0 && (stripos($paramsKey, 'id'))) {

                                $id = $paramsValues[$paramsKey];
                            }
                        }
                       
                    }   
                    $route->$action($id);
                } else {
                    $route->$action();
                }
                
            }
        } catch (NoRouteFoundException $e) {
            $msgErr = $e->getMessage();
        } catch (MultipleRouteFoundException $e) {
            $msgErr = $e->getMessage();
        }
    }

    public static function getDatasource()
    {
        return self::$config['database'];
    }

    public static function getEmailSource()
    {
        return self::$config['email'];
    }

    public static function getBaseUrl()
    {
        return self::$config['baseUrl'];
    }
}
