<?php

namespace Framework;
use function Safe\parse_url;

class Request
{
    /**
     * URI of page
     *
     * @var string
     */
    protected string $uri;

    /**
     * method used (post, get)
     *
     * @var string
     */
    protected string $method;

    /**
     * __construct
     *
     * @param string $baseUrl Base of url directory
     *
     * @return void
     */
    public function __construct(string $baseUrl)
    {
        $this->uri = str_replace($baseUrl, '', \Safe\parse_url(\Safe\filter_input_array(INPUT_SERVER, FILTER_SANITIZE_FULL_SPECIAL_CHARS)['REQUEST_URI'], PHP_URL_PATH));
        $this->method = \Safe\filter_input_array(INPUT_SERVER, FILTER_SANITIZE_FULL_SPECIAL_CHARS)['REQUEST_METHOD'];
    }//end __construct()


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


}
