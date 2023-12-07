<?php

namespace App\Controller;

use Framework\Application;
use Framework\BaseController;
use Framework\Route;

class Error extends BaseController
{
    /**
     * __construct
     *
     * @return void
     */
    public function error404(): void
    {
        $userSession = $this->session->getUser();

        $user = $userSession instanceof \Framework\Security\AuthUser ? $userSession->getAllUserInfo() : null;
        $this->view('frontoffice/404.html.twig', ['baseUrl' => Application::getBaseUrl(), 'authUser' => $user]);
    }
}
