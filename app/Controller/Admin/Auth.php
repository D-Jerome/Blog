<?php

namespace App\Controller\Admin;

use Framework\BaseController;
use Framework\Session;

class Auth extends BaseController
{
    public function loggedIn()
    {     
        return $this->view(Session::getSessionByKey('roleName') . '.panel.html.twig', ['login' => true, 'user' => Session::getSessionByKey('authName')]);
    }

    public function logout()
    {
        session_destroy();

        header('Location: /blog-project/');
    }
}