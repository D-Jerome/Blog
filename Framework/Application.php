<?php

namespace Framework;

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
        $foundRoute = $this->router->findRoute($this->request);
        if (null === $foundRoute) {
            die('route not found');
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
            if (($action === 'post') || ($action === 'deletePost') || ($action === 'modifyPost') || ($action === 'modifiedPost') || ($action === 'deleteUser') || ($action === 'modifyUser') || ($action === 'modifiedUser') || ($action === 'addComment') || ($action === 'addedComment')) {

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
    }

    public static function getDatasource()
    {
        return self::$config['database'];
    }

    
}
