<?php

namespace Framework;

use Framework\Security\Session;
use \Twig\Environment;
use \Twig\Loader\FilesystemLoader;


class BaseController
{
    protected Environment $twig;
    protected Session $session;

    public function __construct()
    {

        $this->session = new Session();
        $loader = new FilesystemLoader(__DIR__ . '/../app/templates');
        $this->twig = new Environment($loader, [
            // 'cache' => __DIR__ . '/../app/var/cache',
        ]);
    }

    protected function view(string $template, array $params)
    {
        echo $this->twig->render($template, $params);
    }

    public function isAuthorize(array $authRoles)
    {
        
        if (in_array('all', $authRoles, true)) {
            return true;
        }
        
        $user = $this->session->getUser();

        if ($user === null) {
            return false;
        }

        if (!in_array($user->getRoleName(), $authRoles, true)) {
            return false;
        }
        return true;

    }
}
