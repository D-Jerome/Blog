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
    
    } //end __construct

    
    /**
     * getPath
     *
     * @return void
     */
    public function getPath()
    {
        return $this->path;
    }

    
    /**
     * getMethod
     *
     * @return void
     */
    public function getMethod()
    {
        return $this->method;
    }

    
    /**
     * getController
     *
     * @return void
     */
    public function getController()
    {
        return $this->controller;
    }

    
    /**
     * getAction
     *
     * @return void
     */
    public function getAction()
    {
        return $this->action;
    }

    
    /**
     * getAuthRoles
     *
     * @return void
     */
    public function getAuthRoles()
    {
        return $this->authRoles;   
    }

    
    /**
     * getParams
     *
     * @return void
     */
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
