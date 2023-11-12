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

        $this->view('backoffice/admin.comments.html.twig', ['comments' => $statementComments,  'authUser' => $user]);
    }

    public function modifyComment($id)
    {
        
        $comments = new CommentManager(Application::getDatasource());

        $statement = $comments->getById($id);
 
        $statement->username = current($comments->getCommentUsername($statement->getUserId()));

        $user = $this->session->getUser();
            $user = [
                'name' => $user->getUsername(),
                'id' => $user->getId(),
                'roleName' => $user->getRoleName()
            ];

        $this->view('backoffice/modify.comment.html.twig', ['comment' => $statement, 'authUser' => $user]);
    }

    public function modifiedComment($id)
    {
        $comments = new CommentManager(Application::getDatasource());
        $params=[];
        $statement = $comments->getById($id);
        
        // dd($_POST, $statement);
        if ($this->getRoute()->getParams()['content'] !== $statement->getContent()) {   
           
            $params['content']= $this->getRoute()->getParams()['content'];
        }
        if (null !== $params) {
            
            $params['modifiedAt'] = (new \DateTime('now'))->format('Y-m-d H:i:s');
            $params['publishState'] = 0;
           
            $comments->update($statement, $params); 
        }

        $user = $this->session->getUser();
        $user = [
            'name' => $user->getUsername(),
            'id' => $user->getId(),
            'roleName' => $user->getRoleName()
        ];

        $comments = new CommentManager(Application::getDatasource());
        $statement = $comments->getById($id);
        $statement->username = current($comments->getCommentUsername($statement->getUserId()));
        
        $this->view('backoffice/modify.comment.html.twig', ['comment' => $statement, 'authUser' => $user]);
    }

    public function moderationComments()
    {
        $comments = new CommentManager(Application::getDatasource());

        $statementComments = $comments->getAll();
        foreach ($statementComments as $statementComment) {
            $statementComment->username = current($comments->getCommentUsername($statementComment->getUserId()));
        }
        $user = $this->session->getUser();
            $user = [
                'name' => $user->getUsername(),
                'id' => $user->getId(),
                'roleName' => $user->getRoleName()
            ];

          
        $this->view('backoffice/admin.moderation.comments.html.twig', ['comments' => $statementComments, 'authUser' => $user]);
    }

    public function moderateComment($id)
    {
        $comment = new CommentManager(Application::getDatasource());

        $statement = $comment->getById($id);
        $user = $this->session->getUser();
            $user = [
                'name' => $user->getUsername(),
                'id' => $user->getId(),
                'roleName' => $user->getRoleName()
            ];


        $this->view('backoffice/modify.comment.html.twig', ['comment' => $statement, 'authUser' => $user]);
    }

    public function moderatedComment($id)
    {
        $comment = new CommentManager(Application::getDatasource());
        $params=[];
        $statement = $comment->getById($id);

        // dd($_POST, $statement);
        if ($this->getRoute()->getParams()['content'] !== $statement->getContent()) {   
            $params['content']= $this->getRoute()->getParams()['content'];
        }
        
        if (null !== $params) {
            $params['modifiedAt'] = (new \DateTime('now'))->format('Y-m-d H:i:s');
            $params['publishState'] = 0;
           
            $comment->update($statement, $params); 
        }

        $user = $this->session->getUser();
        $user = [
            'name' => $user->getUsername(),
            'id' => $user->getId(),
            'roleName' => $user->getRoleName()
        ];

        $comment = new PostManager(Application::getDatasource());
        $statement = $comment->getById($id);
        $this->view('backoffice/modify.comment.html.twig', ['comment' => $statement, 'authUser' => $user]);
    }

    public function unpublishComment(int $id)
    {

        (new CommentManager(Application::getDatasource()))->unpublish($id);
        header('Location: /blog-project/admin/moderation/comments');
    }

    public function publishComment(int $id)
    {
        (new CommentManager(Application::getDatasource()))->publish($id);
        header('Location: /blog-project/admin/moderation/comments');
    }
}
