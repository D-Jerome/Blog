<?php

declare(strict_types=1);

namespace App\Controller;

use Framework\BaseController;
use Framework\Config;

class Error extends BaseController
{
    /**
     * __construct
     */
    public function error404(): void
    {
        $userSession = $this->session->getUser();

        $user = $userSession instanceof \Framework\Security\AuthUser ? $userSession->getAllUserInfo() : null;
        $this->view('frontoffice/404.html.twig', ['baseUrl' => Config::getBaseUrl(), 'authUser' => $user]);
    }
}
