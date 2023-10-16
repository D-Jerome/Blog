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
        return true;
        if (!empty($_SESSION)){
            
            if (in_array( $authRoles , $_SESSION['role']  ) || $authRoles === 'all'){
                return true;
            }
        }
        return false;    
    }
}