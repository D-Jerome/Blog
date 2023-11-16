<?php

namespace Framework;

use Framework\Security\Session;
use \Twig\Environment;
use \Twig\Loader\FilesystemLoader;
use Twig\Extra\Intl\IntlExtension;

class BaseController
{
    
    protected Environment $twig;
    
    protected Session $session;
    
    protected Route $route;
    

    /**
     * __construct : 
     *
     * @param  Route $route
     * @return void
     */
    public function __construct(Route $route)
    {

        $this->session = new Session();
        $loader = new FilesystemLoader(__DIR__ . '/../app/templates');
        $this->twig = new Environment(
            $loader, [
            // 'cache' => __DIR__ . '/../app/var/cache',
            ]
        );
        $this->twig->addExtension(new IntlExtension());
        $this->route= $route;
   
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
     * @param  string $template : NAme of the template
     * @param  array  $params   : params to show in template
     * @return void
     */
    protected function view(string $template, array $params)
    {
         echo $this->twig->render($template, $params);
    }
    
    
    /**
     * isAuthorize: verify if user has the right to access to the page
     *
     * @param  array $authRoles : roles authorized in page
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
