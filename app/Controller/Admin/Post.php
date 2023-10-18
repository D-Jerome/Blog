<?php

namespace App\Controller\Admin;

use App\Controller\Post as ControllerPost;
use App\Model\Manager\PostManager;
use Framework\Application;
use Framework\BaseController;

class Post extends BaseController
{

    public function posts()
    {
        
        $posts = (new PostManager(Application::getDatasource()))->getAll();

        $this->view('admin.posts.html.twig', ['posts' => $posts ]);
        

    }

    public function deletePost($id)
    {
        
        (new PostManager(Application::getDatasource()))->delete($id);
        header('Location: /blog-project/admin');
    }

    public function modifyPost($id)
    {
        $post = new PostManager(Application::getDatasource());

        $statement = $post->getById($id);
        
       

        $this->view('modify.post.html.twig', ['post' => $statement]);

    }

    public function modifyedPost($id)
    {      
        $post = new PostManager(Application::getDatasource());

        $statement = $post->getById($id);
        $modification = false;
        // dd($_POST, $statement);
        if ($_POST['content'] != $statement->getContent()){
            echo 'corps';//update du content
            $modification = true;
        }
        // dd($_POST['name'], $statement->getName());
        if ($_POST['name'] !== $statement->getName()){
            echo 'titre';
            
            //update du name
            $modification = true;
        }
        if ($modification){
            //update date
            echo 'modification';
        }

        $this->view('modify.post.html.twig', ['post' => $statement]);

    }

}