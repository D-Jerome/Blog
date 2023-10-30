<?php

namespace App\Controller\Admin;

use App\Controller\Post as ControllerPost;
use App\Model\Manager\CategoryManager;
use App\Model\Manager\{PostManager, CommentManager};
use Framework\Application;
use Framework\BaseController;
use Framework\Request;
use Framework\Session;


class Post extends BaseController
{

    public function posts()
    {

        $posts = (new PostManager(Application::getDatasource()));
        $statementPosts = $posts->getAll();
        foreach ($statementPosts as $statementPost) {
            $statementPost->username = current($posts->getPostUsername($statementPost->getUserId()));
        }
        $user = $this->session->getUser();
            $user = [
                'name' => $user->getUsername(),
                'id' => $user->getId(),
                'roleName' => $user->getRoleName()
            ];
        $this->view('admin.posts.html.twig', ['posts' => $statementPosts, 'authUser' => $user]);
    }

    public function deletePost($id)
    {

        (new PostManager(Application::getDatasource()))->delete($id);
        header('Location: /blog-project/admin');
    }

    public function addPost()
    {
        $category = new CategoryManager(Application::getDatasource());
        $statementCategories = $category->getAll();
        $user = $this->session->getUser();
            $user = [
                'name' => $user->getUsername(),
                'id' => $user->getId(),
                'roleName' => $user->getRoleName()
            ];

        $this->view('add.post.html.twig', ['categories' => $statementCategories, 'authUser' => $user]);
    }

    public function addedPost()
    {
        $post = new PostManager(Application::getDatasource());
        $request = new Request("/blog-project/");

        $post->insertNewPost($request->getParams());
        $user = $this->session->getUser();
            $user = [
                'name' => $user->getUsername(),
                'id' => $user->getId(),
                'roleName' => $user->getRoleName()
            ];


        $statement = '';
        $this->view('modify.post.html.twig', ['post' => $statement, 'authUser' => $user]);
    }

    public function modifyPost($id)
    {
        $post = new PostManager(Application::getDatasource());

        $statement = $post->getById($id);
        $user = $this->session->getUser();
            $user = [
                'name' => $user->getUsername(),
                'id' => $user->getId(),
                'roleName' => $user->getRoleName()
            ];


        $this->view('modify.post.html.twig', ['post' => $statement, 'authUser' => $user]);
    }

    public function modifiedPost($id)
    {
        $post = new PostManager(Application::getDatasource());

        $statement = $post->getById($id);
        $modification = false;
        // dd($_POST, $statement);
        if ($_POST['content'] != $statement->getContent()) {
            echo 'corps'; //update du content
            $modification = true;
        }
        // dd($_POST['name'], $statement->getName());
        if ($_POST['name'] !== $statement->getName()) {
            echo 'titre';

            //update du name
            $modification = true;
        }
        if ($modification) {
            //update date
            echo 'modification';
        }
        $user = $this->session->getUser();
        $user = [
            'name' => $user->getUsername(),
            'id' => $user->getId(),
            'roleName' => $user->getRoleName()
        ];
        $this->view('modify.post.html.twig', ['post' => $statement, 'authUser' => $user]);
    }

    public function addComment($id)
    {
        // $username=Session::getUsername();
        $post = new PostManager(Application::getDatasource());
        $comment = new CommentManager(Application::getDatasource());
        $statementPost = $post->getById($id);
        $statementComments = $comment->getCommentsByPostId($id);
        $statementPost->username =  current($post->getPostUsername($statementPost->getUserId()));
        foreach ($statementComments as $statementComment) {
            //dd($statementComment->getUserId());
            $statementComment->username = current($comment->getCommentUsername($statementComment->getUserId()));
        }
        $user = $this->session->getUser();
        $user = [
            'name' => $user->getUsername(),
            'id' => $user->getId(),
            'roleName' => $user->getRoleName()
        ];

        $this->view('add.comment.html.twig', ['post' => $statementPost, 'authUser' => $user, 'comments' => $statementComments]);
    }

    public function addedComment($id)
    {

        $comment = new CommentManager(Application::getDatasource());
        $request = new Request("/blog-project/");

        $comment->insertNewComment($request->getParams());
        $user = $this->session->getUser();
            $user = [
                'name' => $user->getUsername(),
                'id' => $user->getId(),
                'roleName' => $user->getRoleName()
            ];

        $post = new PostManager(Application::getDatasource());
        $statementPost = $post->getById($id);
        $slug = $statementPost->getSlug();
        Header("Location: /blog-project/post/$slug-$id");
    }
}
