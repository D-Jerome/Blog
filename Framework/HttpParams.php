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
    protected mixed $paramsReferer;


    /**
     * __construct keep auth user information
     *
     * @return void
     */
    public function __construct()
    {


        $this->paramsGet = \Safe\filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $this->paramsPost = \Safe\filter_input_array(INPUT_POST, );
        Assert::notEmpty(\Safe\filter_input_array(INPUT_SERVER, FILTER_SANITIZE_URL));
        if (array_key_exists('HTTP_REFERER', \Safe\filter_input_array(INPUT_SERVER, FILTER_SANITIZE_URL))) {
            $this->paramsReferer = parse_url(\Safe\filter_input_array(INPUT_SERVER, FILTER_SANITIZE_URL)['HTTP_REFERER'], PHP_URL_QUERY);

        }// end if



    }//end __construct


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
