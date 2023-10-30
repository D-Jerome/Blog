<?php

namespace App\Controller\Admin;


use Framework\BaseController;
use Framework\Session;

class Auth extends BaseController
{
    public function loggedIn()
    {
        $user = $this->session->getUser();
            $user = [
                'name' => $user->getUsername(),
                'id' => $user->getId(),
                'roleName' => $user->getRoleName()
            ];

        return $this->view('' . $user['roleName'] . '.panel.html.twig', ['login' => true, 'authUser' => $user]);
    }

    public function logout()
    {
        session_destroy();

        header('Location: /blog-project/');
    }
}
