<?php

namespace App\Controller;

use App\Model\Manager\CommentManager;

use Framework\Application;
use Framework\BaseController;

class Comment extends BaseController
{

    // public function postOfComment($id)
    // {
    //     $user = $this->session->getUser();
    //     if (null !== ($user)) {

    //         $user = [
    //             'name' => $user->getUsername(),
    //             'id' => $user->getId()
    //         ];
    //     }

    //     $comments = (new CommentManager(Application::getDatasource()));

    //     $statementComments = $comments->getCommentsByUserId($user['id']);

    //     foreach ($statementComments as $statementComment) {
    //         $statementComment->username = current($comments->getCommentUsername($user['id']));
    //     }

    //     $this->view('frontoffice/admin.comments.html.twig', ['comments' => $statementComments,  'authUser' => $user]);
    // }
}
