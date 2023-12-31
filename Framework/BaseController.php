<?php

declare(strict_types=1);

namespace Framework;

use Framework\Security\Session;
use Twig\Environment;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;
use Webmozart\Assert\Assert;

abstract class BaseController
{
    /**
     * twig environment
     */
    protected Environment $twig;

    /**
     * session information
     */
    protected Session $session;

    /**
     * __construct :
     *
     * @param Route $route Route found
     */
    public function __construct(/**
         * route found
         */
        protected Route $route
    ) {
        $this->session = new Session();
        $loader = new FilesystemLoader(__DIR__.'/../app/templates');
        $this->twig = new Environment(
            $loader,
            [
                // 'cache' => __DIR__ . '/../app/var/cache',
            ]
        );
        $this->twig->addExtension(new IntlExtension());
    }
    // end __construct

    /**
     * getRoute
     */
    protected function getRoute(): Route
    {
        return $this->route;
    }

    /**
     * view : Twig Template view construct
     *
     * @param string              $template Name of the template
     * @param array<string,mixed> $params   Params to show in template
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
        if (\in_array('all', $authRoles, true)) {
            return true;
        }

        $user = $this->session->getUser();

        if (!$user instanceof \Framework\Security\AuthUser) {
            return false;
        }

        if (!\in_array($user->getRoleName(), $authRoles, true)) {
            return false;
        }

        return $this->tokenVerify();
    }

    /**
     * CSRF token
     */
    protected function tokenVerify(): bool
    {
        if ('POST' !== $this->getRoute()->getMethod()) {
            return true;
        }
        $postDatas = (new HttpParams())->getParamsPost();
        Assert::notEmpty($postDatas);
        Assert::keyExists($postDatas, 'token');

        return $this->session->getToken() === $postDatas['token'];
    }
}
