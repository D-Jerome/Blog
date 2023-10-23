<?php

namespace App\Controller\Admin;


use Framework\BaseController;
use Framework\Session;

class Auth extends BaseController
{
    public function loggedIn()
    {     
        $user = [
            'name'=> Session::getSessionByKey('authName'),
            'id'=> Session::getSessionByKey('auth')
        ];
        return $this->view(Session::getSessionByKey('roleName') . '.panel.html.twig', ['login' => true, 'authUser' => $user]);
    }

    public function logout()
    {
        session_destroy();

        header('Location: /blog-project/');
    }
}