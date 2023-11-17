<?php

namespace Framework;

use Framework\Security\Session;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extra\Intl\IntlExtension;

class BaseController
{

    /**
     * twig environment
     *
     * @var Environment
     */
    protected Environment $twig;

    /**
     * session information
     *
     * @var Session
     */
    protected Session $session;

    /**
     * route found
     *
     * @var Route
     */
    protected Route $route;


    /**
     * __construct :
     *
     * @param Route $route Route found
     * @return void
     */
    public function __construct(Route $route)
    {

        $this->session = new Session();
        $loader = new FilesystemLoader(__DIR__ . '/../app/templates');
        $this->twig = new Environment(
            $loader,
            [
            // 'cache' => __DIR__ . '/../app/var/cache',
            ]
        );
        $this->twig->addExtension(new IntlExtension());
        $this->route = $route;

    }//end __construct


    /**
     * getRoute
     *
     * @return Route
     */
    protected function getRoute(): Route
    {
        return $this->route;
    }


    /**
     * view : Twig Template view construct
     *
     * @param string $template Name of the template
     * @param array<string, int|bool|string|array<string, int|string|bool>> $params Params to show in template
     * @return void
     */
    protected function view(string $template, array $params): void
    {
        echo $this->twig->render($template, $params);
    }


    /**
     * isAuthorize: verify if user has the right to access to the page
     *
     * @param  array<string> $authRoles : roles authorized in page
     * @return bool
     */
    public function isAuthorize(array $authRoles): bool
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
