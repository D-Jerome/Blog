<?php

declare(strict_types=1);

namespace Framework;

class Route
{
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
    public function __construct(
        /**
         * pattern of address
         */
        protected string $path,
        /**
         * type of method (post,get)
         */
        protected string $method,
        /**
         * controller address
         */
        protected string $controller,
        /**
         * method(function) to use
         */
        protected string $action,
        /**
         * authorized roles for route
         */
        protected array $authRoles
    ) {
    }
    // end __construct

    /**
     * getPath
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * getMethod
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * getController
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * getAction
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
}
