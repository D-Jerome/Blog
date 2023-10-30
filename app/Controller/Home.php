<?php

namespace App\Controller;

use Framework\BaseController;
use Framework\Request;
use Framework\Session;

class Home extends BaseController
{
    public function home()
    {
        $user = $this->session->getUser();
        if (null === $user) {
            return $this->view('home.html.twig', ['error' => false ]);
        }
        
            $user = [
                'name' => $user->getUsername(),
                'id' => $user->getId()
            ];
        

        $this->view('home.html.twig', ['authUser' => $user]);
    }

    public function login()
    {
        echo "je suis la page de login";
    }
}
