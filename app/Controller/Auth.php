<?php

namespace App\Controller;

use Framework\BaseController;

class Auth extends BaseController
{
    
    public function getSession()
    {
       
        if (session_status() === PHP_SESSION_NONE){
                session_start();
        }
        
    }

    public function ()
    {

       
        $posts = new PostManager(Application::getDatasource());

        $statement = $posts->getAll();

        
        $this->view('posts.html.twig', ['posts'=> $statement]);
    }

    public function get()
    {

       
        $posts = new PostManager(Application::getDatasource());

        $statement = $posts->getAll();

        
        $this->view('posts.html.twig', ['posts'=> $statement]);
    }


}