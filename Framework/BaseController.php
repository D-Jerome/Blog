<?php 

namespace Framework;

use \Twig\Environment;
use \Twig\Loader\FilesystemLoader;


class BaseController
{
     protected Environment $twig;

     public function __construct()
     {
          
        $loader = new FilesystemLoader(__DIR__ . '/../app/templates');
        $this->twig = new Environment($loader, [
            // 'cache' => __DIR__ . '/../app/var/cache',
        ]);   
     }

    protected function view(string $template, array $params)
    {
        echo $this->twig->render($template, $params);

    }


}