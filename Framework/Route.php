<?php

namespace Framework;

use Framework\Request;

class Route
{

    /**
     * pattern of address
     *
     * @var string
     */
    protected string $path;

    /**
     * type of method (post,get)
     *
     * @var string
     */
    protected string $method;

    /**
     * controller address
     *
     * @var string
     */
    protected string $controller;

    /**
     * method(function) to use
     *
     * @var string
     */
    protected string $action;

    /**
     * authorized roles for route
     *
     * @var array<string>
     */
    protected array $authRoles;

    /**
     * Params of request
     *
     * @var array<string, string>
     */
    protected array $params = [];

    /**
     * Params of old request
     *
     * @var string|null
     */
    protected ?string $oldParams = null;

    /**
     * token of request
     *
     * @var string|null
     */
    protected ?string $token;

    /**
     * __construct : Construct Route
     *
     * @param  string             $path       pattern of URI
     * @param  string             $method     type of method (post,get)
     * @param  string             $controller address of the controller to use
     * @param  string             $action     name of function to use
     * @param  array<int, string> $authRoles  : List of authorized Roles
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
     * @return array<string>
     */
    public function getAuthRoles(): array
    {
        return $this->authRoles;
    }


    /**
     * getParams
     *
     * @return array<string, int|string>
     */
    public function getParams(): array
    {
        return $this->params;
    }


    /**
     * setParams : Insert params of  $_GET or $_POST in Route
     *
     * @param  array<string, string> $params : params of $_POST or $_GET
     * @return void
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }


    /**
     * getoldParams
     *
     * @return string|null
     */
    public function getOldParams(): ?string
    {
        return $this->oldParams;
    }


    /**
     * setParams : Insert params of  last HTTP request in Route
     *
     * @param  string|null $oldParams : params of last HTTP Request
     * @return void
     */
    public function setOldParams(?string $oldParams): void
    {
        $this->oldParams = $oldParams;
    }


 /**
     * getToken
     *
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }


    /**
     * setToken : Insert Token in Route
     *
     * @param  string|null $token : CSRF Token
     * @return void
     */
    public function setToken(?string $token): void
    {
        $this->token = $token;
    }
}
