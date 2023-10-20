<?php
namespace Framework;

class Request
{

    protected string $uri;
    protected string $method;
    protected array $params = [];

    public function __construct(string $baseUrl)
    {  
        $this->params = $_GET ?: $_POST;
        $this->uri = str_replace($baseUrl,'', parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH));
        $this->method = $_SERVER['REQUEST_METHOD'];

    }

    public function getUri()
    {
        return $this->uri;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getParams()
    {
        return $this->params;
    }

}