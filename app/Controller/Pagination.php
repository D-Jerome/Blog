<?php

declare(strict_types=1);

namespace App\Controller;

use Framework\BaseController;
use Framework\Config;
use Framework\HttpParams;
use Framework\Route;

class Pagination extends BaseController
{
    /**
     * actual page
     */
    protected int $currentPage = 1;

    /**
     * number of previous page
     */
    protected int $previousPage;

    /**
     * number of next page
     */
    protected int $nextPage;

    /**
     * number of item by page
     */
    protected int $perPage = 8;

    /**
     * __construct
     */
    public function __construct(/**
         * route found of router
         */
        protected Route $route, /**
         * total pages for pagination
         */
        protected int $totalPages
    ) {
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
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * setCurrentPage
     */
    public function setCurrentPage(int $currentPage): void
    {
        $this->currentPage = $currentPage;
    }

    /**
     * getPerPage
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * setPerPage : set number of articles per page
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
        if ((1 === (int) ceil($this->totalPages / $this->perPage)) || 0 === $this->totalPages) {
            $pages['nextActive'] = false;
            $pages['previousActive'] = false;
        } elseif ($this->currentPage >= ceil($this->totalPages / $this->perPage)) {
            $pages['previousActive'] = true;
            $pages['nextActive'] = false;
        } elseif (1 === $this->currentPage) {
            $pages['previousActive'] = false;
            $pages['nextActive'] = true;
        } else {
            $pages['nextActive'] = true;
            $pages['previousActive'] = true;
        }// end if

        $temp = (new HttpParams())->getParamsGet();
        unset($temp['page']);
        $query = isset($temp) ? http_build_query($temp) : '';
        if ('' !== $query && '0' !== $query) {
            $query = "&{$query}";
        }
        $pages['previousUri'] = Config::getBaseUrl() . $this->route->getPath() . '?page=' . ($this->currentPage - 1) . $query;
        $pages['nextUri'] = Config::getBaseUrl() . $this->route->getPath() . '?page=' . ($this->currentPage + 1) . $query;

        return $pages;
    }
}
