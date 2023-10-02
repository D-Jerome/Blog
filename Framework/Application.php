<?php 

namespace Framework;

final class Application
{
     private Request $request;
     private Router $router;
     private array $config;

    public function __construct()
    {
        $this->config = json_decode(file_get_contents(__DIR__ . '/../config/config.json'),true);
        $this->request = new Request($this->config['baseUrl']);
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
    
    public function getDatasource()
    {
        return $this->config['database'];
    }


}