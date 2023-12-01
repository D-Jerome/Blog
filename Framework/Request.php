<?php

namespace Framework;

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
     * params of page(post or get)
     *
     * @var array<string,mixed>
     */
    protected array $params = [];

    /**
     * Token from Request
     *
     * @var string|null
     */
    protected ?string $token;

    /**
     * __construct
     *
     * @param string $baseUrl Base of url directory
     *
     * @return void
     */
    public function __construct(string $baseUrl)
    {
        /**
         * @var null|false|array<string,mixed> $input
         */
        $input = \Safe\filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: \Safe\filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $input = ($input === null) ? [] : $input;
        if (array_key_exists('token', $input)){
            $this->token = $input['token'];
            unset($input['token']);
            $this->params = $input;

        }else{
            $this->params = $input;
            $this->token = null;
        }
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


    /**
     * getParams
     *
     * @return array<string, string>
     */
    public function getParams(): array
    {
        return $this->params;
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

}
