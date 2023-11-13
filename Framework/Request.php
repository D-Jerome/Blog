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
     * @param  string $baseUrl
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
     * @return void
     */
    public function getMethod(): string
    {
        return $this->method;
    }
    

    /**
     * getParams
     *
     * @return void
     */
    public function getParams(): array
    {
        return $this->params;
    }


}
