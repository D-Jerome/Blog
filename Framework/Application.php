<?php

declare(strict_types=1);

namespace Framework;

use Framework\Exception\MultipleRouteFoundException;
use Framework\Exception\NoRouteFoundException;

final class Application
{
    /**
     * http request information
     */
    private readonly Request $request;

    /**
     *  router object
     */
    private readonly Router $router;

    /**
     * __construct
     */
    public function __construct()
    {
        new Config();
        $this->request = new Request(Config::getbaseUrl());
        $this->router = new Router();
    }
    // end __construct(

    /**
     * router of application
     */
    public function run(): void
    {
        try {
            $foundRoute = $this->router->findRoute($this->request);
            if (false === $foundRoute instanceof \Framework\Route) {
                throw new NoRouteFoundException();
            }

            $controller = $foundRoute->getController();
            $action = $foundRoute->getaction();
            $authRoles = $foundRoute->getAuthRoles();
            /**
             *  controller object
             *
             * @var BaseController
             */
            $route = new $controller($foundRoute);

            if (false === $route->isAuthorize($authRoles)) {
                header('Location: '.Config::getBaseUrl().'/?auth=0');
            }
            if ($route->isAuthorize($authRoles)) {
                $id = null;
                if (\Safe\preg_match_all('/\{(\w*)\}/', $foundRoute->getPath(), $paramNames)) {
                    $routeMatcher = \Safe\preg_replace('/\{(\w*)\}/', '(\S*)', $foundRoute->getPath());
                    $routeMatcher = str_replace('/', '\/', $routeMatcher);
                    \Safe\preg_match_all("~^{$routeMatcher}$~", $this->request->getUri(), $params, \PREG_UNMATCHED_AS_NULL);
                    $paramsValues = [];

                    foreach ($paramNames[1] as $key => $names) {
                        $paramsValues[$names] = $params[$key + 1][0];
                    }
                    $typeObj = strtolower(substr($controller, strrpos($controller, '\\') + 1));
                    $paramsKeys = array_keys($paramsValues);
                    foreach ($paramsKeys as $paramsKey) {
                        if (stripos((string) $paramsKey, $typeObj.'id') >= 0 && false !== stripos((string) $paramsKey, $typeObj.'id')) {
                            $id = (int) $paramsValues[$paramsKey];
                        }
                    }// end foreach

                    $route->{$action}($id);
                } else {
                    $route->{$action}();
                }// end if
            }
        } catch (NoRouteFoundException $e) {
            header('Location: '.Config::getBaseUrl().'/404');
        } catch (MultipleRouteFoundException $e) {
            header('Location: '.Config::getBaseUrl().'/404');
        }
    }
}
