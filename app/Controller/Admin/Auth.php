<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Framework\BaseController;
use Framework\Config;
use Framework\Security\AuthUser;
use Framework\Session;
use Webmozart\Assert\Assert;

class Auth extends BaseController
{
    /**
     * loggedIn: show user administration panel
     */
    public function loggedIn(): void
    {
        $user = $this->session->getUser();
        Assert::isInstanceof($user, AuthUser::class);

        $this->view('backoffice/' . $user->getRoleName() . '.panel.html.twig', ['baseUrl' => Config::getBaseUrl(), 'login' => true, 'authUser' => $user]);
    }

    /**
     * logout m destroy session
     */
    public function logout(): void
    {
        \Safe\session_destroy();

        header('Location: ' . Config::getBaseUrl() . '/');
    }
}
