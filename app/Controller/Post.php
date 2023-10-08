<?php

namespace App\Controller;

use Framework\BaseController;
use App\Model\Manager\PostManager;
use Framework\Application;

class Post extends BaseController
{
    public function posts()
    {

       
        $posts = new PostManager(Application::getDatasource());

        $statement = $posts->getAll();

        
        $this->view('posts.html.twig', ['posts'=> $statement]);
    }

    public function login()
    {
        echo "je suis la page de login";
    }
}
