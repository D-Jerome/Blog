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

    protected array $params = [];


    /**
     * __construct : Construct Route
     *
     * @param  string $path : pattern of URI
     * @param  string $method : type of method (post,get)
     * @param  string $controller : address of the controller to use
     * @param  string $action: name of function to use
     * @param  array  $authRoles : List of authorized Roles
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
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }


    /**
     * getMethod
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }


    /**
     * getController
     *
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }


    /**
     * getAction
     *
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }


    /**
     * getAuthRoles
     *
     * @return array
     */
    public function getAuthRoles(): array
    {
        return $this->authRoles;
    }


    /**
     * getParams
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }


    /**
     * setParams : Insert params of  $_GET or $_POST in Route
     *
     * @param  array $params : params of $_POST or $_GET
     * @return void
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }


}
