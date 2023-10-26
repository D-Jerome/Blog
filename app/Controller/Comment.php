<?php

namespace App\Controller;

use App\Model\Manager\CommentManager;
use BaseController;
use Framework\Application;
use Framework\Session;

class Comment extends BaseController
{

    public function postOfComment($id)
    {
        $user = [
            'name' => Session::getSessionByKey('authName'),
            'id' => Session::getSessionByKey('auth')
        ];
        
        $comments = (new CommentManager(Application::getDatasource()));
        
        $statementComments = $comments->getCommentsByUserId($user['id']);
        
        foreach ($statementComments as $statementComment) {
            $statementComment->username = current($comments->getCommentUsername($user['id']));

        }
        
        $this->view('admin.comments.html.twig', ['comments' => $statementComments,  'authUser' => $user]); 
        
    }

    


}