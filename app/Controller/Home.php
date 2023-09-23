<?php

namespace App\Controller;

use Framework\BaseController;

class Home extends BaseController
{
    public function home()
    {
       $this->view('home.html.twig',['name' => 'Jerome']);        
        
    }

    public function login()
    {
        echo "je suis la page de login";
    }
}