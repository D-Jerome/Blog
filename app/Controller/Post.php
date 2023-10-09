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

   

    public function post($id)
    {
        $post = new PostManager(Application::getDatasource());

        $statement = $post->getById($id);

        
        $this->view('post.html.twig', ['post'=> $statement]);
        

    }
    
    public function postsPaged()
    {
        $orderBy = 'created_at'; 
        $dir = 'DESC';
        $perPage = $_GET['perPage']?? 8;
        $page = $_GET['page'] ?? 1;
        $posts = new PostManager(Application::getDatasource());
        $pages = [];
        $statement = $posts->getAllOrderLimit($orderBy, $dir, $perPage, $page) ;
        $count = count($posts->getAll());
       
        if ($page >= (ceil(($count/$perPage)))) {
            $pages['precActive'] = "aria-disabled='false'";
            $pages['suivActive'] = "aria-disabled='true'";
        }elseif ($page === 1 ) {
            $pages['precActive'] = "aria-disabled='true'";
            $pages['suivActive'] = "aria-disabled='false'";
        }else{
            $pages['suivActive'] = "aria-disabled='false'";
            $pages['precActive'] = "aria-disabled='false'";
        }


        
        $this->view('posts.html.twig', ['posts'=> $statement, 'pages'=> $pages]);
    }
}
