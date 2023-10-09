<?php

namespace App\Controller;

use Framework\BaseController;

class User extends BaseController
{
   


    public function login()
    {
        $this->view('login.html.twig', ['toto'=>'toto'] );
    }


}