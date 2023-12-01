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
     * params of page(post or get)
     *
     * @var array<string,mixed>
     */
    protected array $params = [];

    /**
     * params of precedent page
     *
     * @var string|null
     */
    protected ?string $oldParams = null ;

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

        $oldParams = parse_url(\Safe\filter_input_array(INPUT_SERVER, FILTER_SANITIZE_URL)['HTTP_REFERER'], PHP_URL_QUERY);
        if ($oldParams !== null) {
            $this->oldParams = '?'.$oldParams;
        }
        
        $input = \Safe\filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: \Safe\filter_input_array(INPUT_POST, );
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


    /**
     * getOldParams
     *
     * @return string|null
     */
    public function getOldParams(): ?string
    {
        return $this->oldParams;
    }

}
