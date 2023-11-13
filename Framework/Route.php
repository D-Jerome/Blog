<?php

namespace Framework;

use Framework\Request;


class Route
{

    protected string $path;
    
    protected string $method;
    
    protected string $controller;
    
    protected string $action;
    
    protected array $authRoles;
    
    protected array $params=[];

    
    /**
     * __construct : Construct Route 
     *
     * @param  string $path
     * @param  string $method
     * @param  string $controller
     * @param  string $action
     * @param  array $authRoles
     * @return void
     */
    public function __construct(string $path, string $method, string $controller, string $action, array $authRoles)
    {
        $this->path = $path;
        $this->method = $method;
        $this->controller = $controller;
        $this->action = $action;
        $this->authRoles = $authRoles;
   
      
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


    public function getAuthRoles()
    {
        return $this->authRoles;
    }


    public function getParams()
    {
        return $this->params;
    }
    

    /**
     * setParams : Insert params of  $_GET or $_POST in Route
     *
     * @param  array $params
     * @return void
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }
}
