<?php

namespace App\Controller;

use Framework\BaseController;

class Home extends BaseController
{
    public function home()
    {
        ob_start();
        $this->view('home.html.twig',['name' => 'Jerome']);        
        $content = ob_get_clean();
        require(dirname(__DIR__).'/templates/layouts/default.php'); 
    }

    public function login()
    {
        echo "je suis la page de login";
    }
}