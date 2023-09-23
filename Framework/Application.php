<?php 

namespace Framework;

final class Application
{
     private Request $request;
     private Router $router;





    public function __construct()
    {
        $this->request = new Request();
        $this->router = new Router();

    }
    
    public function run()
    {
        $foundRoute = $this->router->findRoute($this->request);
        if (null === $foundRoute){
            die('route not found');
        }
        $controller = $foundRoute->getController();
        $action =  $foundRoute->getaction();
        $route = new $controller;
        $route->$action();
    }
    
    


}