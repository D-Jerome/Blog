<?php
namespace Framework;

use Framework\Request;


class Route
{
    protected string $path;
    protected string $method;
    protected string $controller;
    protected string $action;

    public function __construct(string $path, string $method , string $controller, string $action)
    {
        $this->path = $path;
        $this->method = $method;
        $this->controller = $controller;
        $this->action = $action;

    }

    public function getPath()
    {         
        return $this->path;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getController()
    {
        return $this->controller;
    }
    
    public function getAction()
    {
        return $this->action;
    }

}