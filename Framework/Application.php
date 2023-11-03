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
            $route = new $controller;

            if (!$route->isAuthorize($authRoles)) {
                header('Location: /blog-project/?auth=0');
            }

            if ($route->isAuthorize($authRoles)) {
                //à refactoriser
                if (($action === 'post') ||
                    ($action === 'deletePost') ||
                    ($action === 'modifyPost') ||
                    ($action === 'modifiedPost') ||
                    ($action === 'unpublishPost') ||
                    ($action === 'publishPost') ||
                    ($action === 'disableUser') ||
                    ($action === 'enableUser') ||
                    ($action === 'modifyUser') ||
                    ($action === 'modifiedUser') ||
                    ($action === 'addComment') ||
                    ($action === 'addedComment') ||
                    ($action === 'deleteComment') ||
                    ($action === 'modifyComment') ||
                    ($action === 'modifiedComment') ||
                    ($action === 'unpublishComment') ||
                    ($action === 'publishComment') 
                ) {

                    $uri = (explode('-', $this->request->getUri()));
                  
                    $id = current(array_filter($uri, function ($num) {
                        return is_numeric($num) == true;
                    }));

                    $route->$action($id);
                } else {
                    $route->$action();
                }
                //

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
}
