<?php

namespace App\Controller;

use Framework\Application;
use Framework\BaseController;
use Framework\Route;

class Error extends BaseController
{

    /**
     * __construct
     *
     * @return 
     */
    public function error404()
    {
        return $this->view('frontoffice/404.html.twig',[]);

    }

}
