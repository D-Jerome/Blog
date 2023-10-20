<?php

namespace App\Controller;

use Framework\BaseController;
use Framework\Request;
use Framework\Session;

class Home extends BaseController
{
    public function home()
    {
       
        if (isset($_GET['auth'])){
           return $this->view('home.html.twig',['error' => true]);        
        }
        $this->view('home.html.twig',[ 'user' => Session::getSessionByKey('authName')]);
    }

    public function login()
    {
        echo "je suis la page de login";
    }
}