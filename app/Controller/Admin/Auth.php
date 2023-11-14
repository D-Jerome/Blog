<?php

namespace App\Controller\Admin;


use Framework\BaseController;
use Framework\Session;

class Auth extends BaseController
{

    /**
     * loggedIn: show user administration panel
     *
     * @return void
     */
    public function loggedIn(): void
    {
        $user = $this->session->getUser();
            $user = [
                'name' => $user->getUsername(),
                'id' => $user->getId(),
                'roleName' => $user->getRoleName()
            ];
        $this->view('backoffice/' . $user['roleName'] . '.panel.html.twig', ['login' => true, 'authUser' => $user]);
    }


    /**
     * logout m destroy session
     *
     * @return void
     */
    public function logout(): void
    {
        session_destroy();

        header('Location: /blog-project/');
    }


}
