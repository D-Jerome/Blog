<?php

namespace App\Controller;

use Framework\Application;
use Framework\BaseController;
use Framework\Route;
use Framework\HttpParams;

class Pagination extends BaseController
{
    /**
     * route found of router
     *
     * @var Route
     */
    protected Route $route;

    /**
     * actual page
     *
     * @var int
     */
    protected int $currentPage = 1;

    /**
     * number of previous page
     *
     * @var int
     */
    protected int $previousPage;

    /**
     * number of next page
     *
     * @var int
     */
    protected int $nextPage;

    /**
     * total pages for pagination
     *
     * @var int
     */
    protected int $totalPages;

    /**
     * number of item by page
     *
     * @var int
     */
    protected int $perPage = 8;


    /**
     * __construct
     *
     * @param Route $route
     * @param int   $totalPages
     */
    public function __construct(Route $route, int $totalPages)
    {
        $this->route = $route;
        $this->totalPages = $totalPages;
        $params = (new HttpParams())->getParamsGet();
        if (isset($params['page'])) {
            $this->currentPage = (int) $params['page'];
        }

        if (isset($params['perPage'])) {
            $this->perPage = (int) $params['perPage'];
        }

    }


    /**
     * get the actual page
     *
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }


    /**
     * setCurrentPage
     *
     * @param  int $currentPage
     * @return void
     */
    public function setCurrentPage(int $currentPage): void
    {
        $this->currentPage = $currentPage;
    }


    /**
     * getPerPage
     *
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }


    /**
     * setPerPage : set number of articles per page
     *
     * @param  int $perPage
     * @return void
     */
    public function setPerPage(int $perPage): void
    {
        $this->perPage = $perPage;
    }


    /**
     * isActivePaging : Return information of pagination buttons
     *
     * @return array<string, string|bool|int>
     */
    public function pagesInformations(): array
    {

        if (((int) (ceil(($this->totalPages / $this->perPage))) === 1) || $this->totalPages === 0) {
            $pages['nextActive'] = false;
            $pages['previousActive'] = false;
        } elseif ($this->currentPage >= (ceil(($this->totalPages / $this->perPage)))) {
            $pages['previousActive'] = true;
            $pages['nextActive'] = false;
        } elseif ($this->currentPage === 1) {
            $pages['previousActive'] = false;
            $pages['nextActive'] = true;
        } else {
            $pages['nextActive'] = true;
            $pages['previousActive'] = true;
        }//end if

        $temp = (new HttpParams())->getParamsGet();
        unset($temp['page']);
        if (isset($temp) !== false) {
            $query = http_build_query($temp);
        }else{
            $query = '';
        }
        if (!empty($query)) {
            $query = "&$query";
        }
        $pages['previousUri'] = Application::getBaseUrl(). $this->route->getPath() . '?page=' . ($this->currentPage - 1) . $query;
        $pages['nextUri'] = Application::getBaseUrl(). $this->route->getPath() . '?page=' . ($this->currentPage + 1) . $query;

        return $pages;
    }


}
