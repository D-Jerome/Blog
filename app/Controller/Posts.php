<?php

namespace App\Controller;

use Framework\BaseController;
use App\Model\Manager\PostsManager;
use Framework\Application;

class Posts extends BaseController
{
    public function posts()
    {

        echo "je suis la page de posts";
        $posts = new PostsManager("posts","Posts", Application::getDatasource());
        
        $statement = $posts->getAll();
        print_r($statement);
        $this->view('posts.html.twig', $statement);
    }

    public function login()
    {
        echo "je suis la page de login";
    }
}
