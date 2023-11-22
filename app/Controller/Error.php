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
     * @return void
     */
    public function error404(): void
    {
        $this->view('frontoffice/404.html.twig',[]);

    }

}
