<?php

namespace Framework;

use Webmozart\Assert\Assert;

use function Safe\parse_url;

class HttpParams
{
    /**
     * Params Get of Request
     *
     * @var array<string,string|int|null>
     */
    protected ?array $paramsGet;

    /**
     * Params Post of Request
     *
     * @var array<string,array<int,string>|string|int|null>
     */
    protected ?array $paramsPost;

    /**
     * Old params of Request
     *
     * @var mixed
     */
    protected mixed $paramsReferer = null;


    /**
     * __construct keep auth user information
     *
     * @return void
     */
    public function __construct()
    {
        $this->paramsGet = \Safe\filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $this->paramsPost = \Safe\filter_input_array(INPUT_POST);
        $serverData = \Safe\filter_input_array(INPUT_SERVER, FILTER_SANITIZE_URL);
        Assert::isArray($serverData);
        Assert::keyExists($serverData, 'HTTP_REFERER');
        if (!empty($serverData) && ($serverData['HTTP_REFERER']) !== null) {
            $this->paramsReferer = parse_url($serverData['HTTP_REFERER'], PHP_URL_QUERY);
        }
    }
    //end __construct


    /**
     * getParamsGet
     *
     * @return  array<string,string|int|null>|null
     */
    public function getParamsGet(): ?array
    {
        return  $this->paramsGet;
    }


    /**
     * getParamsPost
     *
     * @return  array<string,array<int,string>|int|string|null>|null
     */
    public function getParamsPost(): ?array
    {
        return  $this->paramsPost;
    }


    /**
     * getParamsReferer
     *
     * @return mixed
     */
    public function getParamsReferer(): mixed
    {
        return  $this->paramsReferer;
    }
}
