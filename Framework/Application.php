<?php

namespace Framework;

use Framework\Exception\InvalidUserException;
use Framework\Exception\MultipleRouteFoundException;
use Framework\Exception\NoRouteFoundException;

final class Application
{
    /**
     * http request information
     *
     * @var Request
     */
    private readonly Request $request;

    /**
     *  router object
     *
     * @var Router
     */
    private readonly Router $router;

    /**
     * datas from config file
     *
     * @var array<string,mixed>
     */
    private static array $config;


    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        self::$config = \Safe\json_decode(\Safe\file_get_contents(__DIR__ . "/../config/config.json"), true);
        $this->request = new Request(self::$config['baseUrl']);
        $this->router = new Router();

    }//end __construct()


    /**
     * router of application
     *
     * @return void
     */
    public function run()
    {

        try {
            $foundRoute = $this->router->findRoute($this->request);
            if (!$foundRoute instanceof \Framework\Route) {
                throw new NoRouteFoundException();
            }

            $controller = $foundRoute->getController();
            $action = $foundRoute->getaction();
            $authRoles = $foundRoute->getAuthRoles();
            $route = new $controller($foundRoute);

            if (!$route->isAuthorize($authRoles)) {
                header('Location: '. self::getBaseUrl() .'/?auth=0');
            }

            if ($route->isAuthorize($authRoles)) {

                $id = null;
                if (\Safe\preg_match_all('/\{(\w*)\}/', $foundRoute->getPath(), $paramNames)) {
                    $routeMatcher = \Safe\preg_replace('/\{(\w*)\}/', '(\S*)', $foundRoute->getPath());
                    $routeMatcher = str_replace('/', '\/', $routeMatcher);
                    \Safe\preg_match_all("~^$routeMatcher$~", $this->request->getUri(), $params, PREG_UNMATCHED_AS_NULL);
                    $paramsValues = [];

                    foreach ($paramNames[1] as $key => $names) {
                        $paramsValues[$names] = $params[$key + 1][0];
                    }
                    $typeObj = strtolower(substr($controller, strrpos($controller, "\\") + 1));
                    $paramsKeys = array_keys($paramsValues);
                    foreach ($paramsKeys as $paramsKey) {
                        if (stripos((string)$paramsKey, $typeObj . 'id') >= 0 && stripos((string)$paramsKey, $typeObj . 'id') !== false) {
                            $id = $paramsValues[$paramsKey];
                        }

                    }//end foreach

                    $route->$action($id);
                } else {
                    $route->$action();

                }//end if

            }//end if

        } catch (NoRouteFoundException $e) {
            $msgErr = $e->getMessage();
            header('Location: '. self::getBaseUrl() .'/404');
        } catch (MultipleRouteFoundException $e) {
            $msgErr = $e->getMessage();

        }
    }


    /**
     * getDatasource : get the config information of database in array
     *
     * @return array<string,string>
     */
    public static function getDatasource(): array
    {
            return self::$config['database'];
    }


    /**
     * getEmailSource: get the config information of email in array
     *
     * @return array<string,bool|int|string>
     */
    public static function getEmailSource(): array
    {
        return self::$config['email'];
    }


    /**
     * getBaseUrl: get the config information of base Url address in
     *
     * @return string
     */
    public static function getBaseUrl(): string
    {
        return self::$config['baseUrl'];
    }


    /**
     * getFilter
     *
     * @return array<array<array<string,string>>>
     */
    public static function getFilter(): array
    {
        return self::$config['filter'];
    }


}
