<?php

namespace Framework;

use Webmozart\Assert\Assert;

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
        $inputServer = \Safe\filter_input_array(INPUT_SERVER, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        Assert::notNull($inputServer);
        Assert::isArray($inputServer);
        Assert::keyExists($inputServer, 'REQUEST_URI');

        $parseURI = \Safe\parse_url($inputServer['REQUEST_URI'], PHP_URL_PATH);
        if (is_string($parseURI)) {
            $this->uri = (str_replace($baseUrl, '', $parseURI)) ;
        }
        Assert::keyExists($inputServer, 'REQUEST_METHOD');
        $this->method = $inputServer['REQUEST_METHOD'];
    }
    //end __construct()


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
