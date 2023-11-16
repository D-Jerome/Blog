<?php

namespace Framework;

class Request
{

    protected string $uri;

    protected string $method;

    protected array $params = [];


    /**
     * __construct
     *
     * @param  string $baseUrl : base of url directory
     * @return void
     */
    public function __construct(string $baseUrl)
    {
        $this->params = $_GET ?: $_POST;
        $this->uri = str_replace($baseUrl, '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        $this->method = $_SERVER['REQUEST_METHOD'];

    }//end __construct


    /**
     * getUri
     *
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
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
     * getParams
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }


}
