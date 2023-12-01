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
     * @param  Route $route Route found
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
     * @param  string                                                            $template Name of the template
     * @param  array<string, array<bool|int|object|string>|int|string|null|bool> $params   Params to show in template
     * @return void
     */
    protected function view(string $template, array $params): void
    {
        \Safe\ob_start();
           echo $this->twig->render($template, $params);
        \Safe\ob_end_flush();
    }


    /**
     * isAuthorize: verify if user has the right to access to the page
     *
     * @param  array<string> $authRoles roles authorized in page
     * @return bool
     */
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
            return FALSE;
        }

        if ($this->tokenVerify() !== TRUE){
            return FALSE;
        }
        return TRUE;
    }


    /**
     * ckeck and group filter information pass by user
     *
     * @return array<string, null|string|int>
     */
    public function groupFilterDataUser(): array
    {
        $filterReturn['sort'] = isset(($this->getRoute()->getParams())['sort']) ? (string)($this->getRoute()->getParams())['sort'] : 'createdAt';
        $filterReturn['dir'] = isset(($this->getRoute()->getParams())['dir']) ? (string)($this->getRoute()->getParams())['dir'] : 'DESC';
        $filterReturn['list'] = !empty(($this->getRoute()->getParams())['list']) ? (string)($this->getRoute()->getParams())['list'] : null;
        if (isset(($this->getRoute()->getParams())['listSelect']) === true) {
            $filterReturn['listSelect'] = (($this->getRoute()->getParams())['listSelect']) !== '---' ? ($this->getRoute()->getParams())['listSelect'] : null;
        } else {
            $filterReturn['listSelect'] = null;
        }

        if ($filterReturn['listSelect'] === null && $filterReturn['list'] !== null) {
            $filterReturn['list'] = null;
        }

        if ($filterReturn['list'] === null && $filterReturn['listSelect'] !== null) {
            $filterReturn['listSelect'] = null;
        }

        return $filterReturn;
    }


    /**
     * CSRF token
     *
     * @return bool
     *
     */
    protected function tokenVerify(): bool
    {

        if ($this->getRoute()->getMethod() !== 'POST'){
            return TRUE;
        }
        
        if ($this->session->getUser()->getToken() === $this->getRoute()->getToken()){
            return TRUE;
        }

        return FALSE;
    }
}
