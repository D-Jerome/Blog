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
        $currentPage = (int)$_GET['page'] ?? 1;
       
        $posts = new PostManager(Application::getDatasource());
        $pages = [];
        $statement = $posts->getAllOrderLimit($orderBy, $dir, $perPage, $currentPage) ;
        $count = count($posts->getAll());
        
        if ($currentPage >= (ceil(($count/$perPage)))) {
            $pages['previousActive'] = true;
            $pages['nextActive'] = false;

        }elseif ($currentPage === 1 ) {
            
            $pages['previousActive'] = false;
            $pages['nextActive'] = true;
            
        }else{
            $pages['nextActive'] = true;
            $pages['previousActive'] = true;
           
        }

        $uri = explode('?',$_SERVER['REQUEST_URI'])[0];
        $get = $_GET;
        unset($get['page']);
        
        $queryP=http_build_query($get);
        if (!empty($query)){
                $uri = $uri . '?' . $query;
        }
        
        //pagination
            
            $uri = explode('?',$_SERVER['REQUEST_URI'])[0];
            $get = $_GET;
            unset($get['page']);
            $query=http_build_query($get);
            $pages['previousUri'] = $uri . '?page='. ($currentPage - 1) . $query; 
            $pages['nextUri'] = $uri . '?page='. ($currentPage + 1) . $query; 

        
        $this->view('posts.html.twig', ['posts'=> $statement, 'pages'=> $pages]);
    }
}
