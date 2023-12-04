<?php

namespace Framework;

use Framework\Security\Session;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extra\Intl\IntlExtension;

abstract class BaseController
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
            return false;
        }

        if ($this->tokenVerify() !== true) {
            return false;
        }
        return true;
    }


    /**
     * ckeck and group filter information pass by user
     *
     * @return array<string, null|string|int>|null
     */
    public function groupFilterDataUser(): ?array
    {
        $filterReturn = (new HttpParams())->getParamsGet();
        $filterReturn['sort'] = isset(($filterReturn)['sort']) ? (string)($filterReturn['sort']) : 'createdAt';
        $filterReturn['dir'] = isset(($filterReturn)['dir']) ? (string)($filterReturn['dir']) : 'DESC';
        $filterReturn['list'] = !empty(($filterReturn)['list']) ? (string)($filterReturn)['list'] : null;
        if (isset(($filterReturn)['listSelect']) === true) {
            $filterReturn['listSelect'] = ($filterReturn['listSelect']) !== '---' ? $filterReturn['listSelect'] : null;
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
     */
    protected function tokenVerify(): bool
    {

        if ($this->getRoute()->getMethod() !== 'POST') {
            return true;
        }

        if ($this->session->getToken() === (new HttpParams())->getParamsPost()['token']) {
            return true;
        }

        return false;
    }



}
