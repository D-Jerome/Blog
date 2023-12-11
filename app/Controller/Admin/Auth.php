<?php

namespace App\Controller\Admin;

use Framework\{Application,Config};
use Framework\BaseController;
use Framework\Session;
use Webmozart\Assert\Assert;

class Auth extends BaseController
{
    /**
     * loggedIn: show user administration panel
     *
     * @return void
     */
    public function loggedIn(): void
    {
        $user = (($this->session)->getUser());
        Assert::isArray($user);
        Assert::keyExists($user, 'roleName');
        $this->view('backoffice/' . $user['roleName'] . '.panel.html.twig', ['baseUrl' => Config::getBaseUrl(), 'login' => true, 'authUser' => $user]);
    }


    /**
     * logout m destroy session
     *
     * @return void
     */
    public function logout(): void
    {
        \Safe\session_destroy();

        header('Location: ' . Config::getBaseUrl() . '/');
    }
}
