<?php

namespace App\Controller\Admin;

use App\Model\Manager\CommentManager;
use App\Model\Manager\PostManager;
use Framework\Application;
use Framework\BaseController;
use Framework\Session;

class Comment extends BaseController
{

    public function comments()
    {
        $user = $this->session->getUser();
            $user = [
                'name' => $user->getUsername(),
                'id' => $user->getId(),
                'roleName' => $user->getRoleName()
            ];

        $comments = (new CommentManager(Application::getDatasource()));

        if ($user['roleName'] === "admin"){
            $statementComments = $comments->getAll();
            
        }else{
            $statementComments = $comments->getCommentsByUserId($user['id']);
        }
        
        foreach ($statementComments as $statementComment) {
            $statementComment->username = current($comments->getCommentUsername($statementComment->getUserId()));
        }

        $this->view('admin.comments.html.twig', ['comments' => $statementComments,  'authUser' => $user]);
    }
}
