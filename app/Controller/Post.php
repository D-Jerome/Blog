<?php

namespace App\Controller;

use App\Model\Entities\Post as EntitiesPost;
use Framework\BaseController;
use App\Model\Manager\{CategoryManager, PostManager};
use Framework\Application;
use Framework\Session;
use \PDO;


class Post extends BaseController
{

    public function posts()
    {
        $username=Session::getUsername();

        $posts = new PostManager(Application::getDatasource());

        $statementPosts = $posts->getAll();
        
        foreach ($statementPosts as $statementPost){
            
            $statementPost->categories = $posts->getCategoriesById($statementPost->id) ;
        }
           
       
        $this->view('posts.html.twig', ['posts' => $statementPosts, 'name' => $username ]);
    }



    public function post($id)
    {
        $username=Session::getUsername();
        $post = new PostManager(Application::getDatasource());

        $statement = $post->getById($id);


        $this->view('post.html.twig', ['post' => $statement, 'name' => $username]);
    }

    public function postsPaged()
    {
        $username=Session::getUsername();
        $orderBy = 'created_at';
        $dir = 'DESC';
        $perPage = $_GET['perPage'] ?? 8;
        $currentPage = $_GET['page'] ?? 1;
        $currentPage = (int)$currentPage;
        $posts = new PostManager(Application::getDatasource());
        $pages = [];
        $statementPosts = $posts->getAllOrderLimit($orderBy, $dir, $perPage, $currentPage);
        foreach ($statementPosts as $statementPost){
            
            $statementPost->categories = $posts->getCategoriesById($statementPost->id) ;
        }
        
        $count = count($posts->getAll());

        if ($currentPage >= (ceil(($count / $perPage)))) {
            $pages['previousActive'] = true;
            $pages['nextActive'] = false;
        } elseif ($currentPage === 1) {

            $pages['previousActive'] = false;
            $pages['nextActive'] = true;
        } else {
            $pages['nextActive'] = true;
            $pages['previousActive'] = true;
        }

        $uri = explode('?', $_SERVER['REQUEST_URI'])[0];
        $get = $_GET;
        unset($get['page']);

        $query = http_build_query($get);
        if (!empty($query)) {
            $uri = $uri . '?' . $query;
        }

        //pagination

        $uri = explode('?', $_SERVER['REQUEST_URI'])[0];
        $get = $_GET;
        unset($get['page']);
        $query = http_build_query($get);
        $pages['previousUri'] = $uri . '?page=' . ($currentPage - 1) . $query;
        $pages['nextUri'] = $uri . '?page=' . ($currentPage + 1) . $query;


        $this->view('posts.html.twig', ['posts' => $statementPosts, 'pages' => $pages , 'name' => $username]);
    }
}
