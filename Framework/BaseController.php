<?php 

namespace Framework;

use \Twig\Environment;
use \Twig\Loader\FilesystemLoader;


class BaseController
{
     protected Environment $twig;

     public function __construct()
     {
        if (session_status() === PHP_SESSION_NONE){
            session_start();
        }  

        $loader = new FilesystemLoader(__DIR__ . '/../app/templates');
        $this->twig = new Environment($loader, [
            // 'cache' => __DIR__ . '/../app/var/cache',
        ]);   
     }

    protected function view(string $template, array $params)
    {
        echo $this->twig->render($template, $params);

    }

    public function isAuthorize( array $authRoles)
    {
        echo "verif session";
        var_dump($_SESSION);
        if (Session::checkSessionKey('auth')){
            echo 'session';
            if (in_array( Session::getSessionByKey('role') , $authRoles   ) || $authRoles === 'all'){
                echo 'session role';
                return true;
            }
        }
        
        if ( in_array('all' , $authRoles)){
            var_dump($authRoles);
            return true;
        }
        return false;    
    }
}