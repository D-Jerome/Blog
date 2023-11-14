<?php

namespace App\Controller\Admin;

use App\Model\Manager\CommentManager;
use App\Model\Manager\PostManager;
use Framework\Application;
use Framework\BaseController;
use Framework\Session;

class Comment extends BaseController
{
    /**
     * comments: show comments of user
     *      or show all comments for admin
     *
     * @return void
     */
    public function comments()
    {
        $user = $this->session->getUser();
        $user = [
                 'name' => $user->getUsername(),
                 'id' => $user->getId(),
                 'roleName' => $user->getRoleName()
                ];
        $comments = (new CommentManager(Application::getDatasource()));
        if ($user['roleName'] === "admin") {
            $statementComments = $comments->getAll();
        } else {
            $statementComments = $comments->getCommentsByUserId($user['id']);
        }//end if
        foreach ($statementComments as $statementComment) {
            $statementComment->username = current($comments->getCommentUsername($statementComment->getUserId()));
        }
        $statementPosts = (new PostManager(Application::getDatasource()))->getAll();
        $this->view('backoffice/admin.comments.html.twig', ['comments' => $statementComments, 'posts' => $statementPosts, 'authUser' => $user]);
    }


    /**
     * modifyComment
     *
     * @param  int $id
     * @return void
     */
    public function modifyComment(int $id): void
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


    /**
     * modifiedComment: action after modification of comment
     *
     * @param  int $id
     * @return void
     */
    public function modifiedComment(int $id): void
    {
        $comments = new CommentManager(Application::getDatasource());
        $params = [];
        $statement = $comments->getById($id);

        // dd($_POST, $statement);
        if ($this->getRoute()->getParams()['content'] !== $statement->getContent()) {

            $params['content'] = $this->getRoute()->getParams()['content'];
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


    /**
     * moderationComments; prepare view to moderate comments
     *
     * @return void
     */
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


    /**
     * unpublishComment: action of unpublish comment
     *
     * @param  int $id
     * @return void
     */
    public function unpublishComment(int $id): void
    {
        (new CommentManager(Application::getDatasource()))->unpublish($id);
        header('Location: /blog-project/admin/moderation/comments');
    }


    /**
     * publishComment: action of publish comment
     *
     * @param  int $id
     * @return void
     */
    public function publishComment(int $id): void
    {
        (new CommentManager(Application::getDatasource()))->publish($id);
        header('Location: /blog-project/admin/moderation/comments');
    }


}
