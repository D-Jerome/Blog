<?php

namespace App\Controller;

use Framework\Application;
use Framework\BaseController;
use Framework\Route;

class Pagination extends BaseController
{

    protected Route $route;
    
    protected int $currentPage = 1;
    
    protected int $previousPage;

    protected int $nextPage;

    protected int $totalPages;

    protected int $perPage = 8;
    

    public function __construct(Route $route, int $totalPages , ?int $currentPage, ?int $perPage)
    {
        $this->route = $route;
        $this->totalPages = $totalPages;
        if (isset($currentPage)) {
            $this->currentPage = $currentPage;
        }
        if (isset($perPage)) {
            $this->perPage = $perPage;
        }
       

    }
    
    
    public function getCurrentPage():int
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
     * @return array
     */
    public function pagesInformations():array
    {

        if ((int)(ceil(($this->totalPages / $this->perPage))) === 1) {
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

        $temp = ($this->route->getParams());
        unset($temp['page']);
        $this->getRoute()->setParams($temp);
        $query = http_build_query($this->route->getParams());
        if (!empty($query)) {
            $query = "&$query";
        }
        $pages['previousUri'] = Application::getBaseUrl(). $this->route->getPath() . '?page=' . ($this->currentPage - 1 ) . $query;
        $pages['nextUri'] = Application::getBaseUrl(). $this->route->getPath() . '?page=' . ($this->currentPage + 1 ) . $query;
        
        return $pages;
    }


}