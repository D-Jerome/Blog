<?php

namespace App\Controller;

use App\Model\Entities\Post as EntitiesPost;
use Framework\BaseController;
use App\Model\Manager\{CategoryManager, PostManager, CommentManager, UserManager};
use Framework\Application;
use \PDO;


class Post extends BaseController
{

    public function posts()
    {
        //$username=Session::getUsername();

        $posts = new PostManager(Application::getDatasource());

        $statementPosts = $posts->getAll();

        foreach ($statementPosts as $statementPost) {

            $statementPost->categories =  $posts->getCategoriesById($statementPost->id);
            $statementPost->countComments = (int)$posts->getCountCommentsByPostId($statementPost->id);
            $statementPost->username =  current($posts->getPostUsername($statementPost->getUserId()));
        }
        $user = $this->session->getUser();
        if (null !== $user) {
            $user = [
                'name' => $user->getUsername(),
                'id' => $user->getId()
            ];
        }

        $this->view('posts.html.twig', ['posts' => $statementPosts, 'authUser' => $user]);
    }



    public function post(int $id)
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
        if (null !== $user) {
            $user = [
                'name' => $user->getUsername(),
                'id' => $user->getId()
            ];
        }
        $this->view('post.html.twig', ['post' => $statementPost, 'authUser' => $user, 'comments' => $statementComments]);
    }

    public function postsPaged()
    {
        //$username=Session::getUsername();
        $orderBy = 'created_at';
        $dir = 'DESC';
        $perPage = $_GET['perPage'] ?? 8;
        $currentPage = $_GET['page'] ?? 1;
        $currentPage = (int)$currentPage;
        $posts = new PostManager(Application::getDatasource());
        $pages = [];
        $statementPosts = $posts->getAllOrderLimit($orderBy, $dir, $perPage, $currentPage);
        foreach ($statementPosts as $statementPost) {

            $statementPost->categories = $posts->getCategoriesById($statementPost->id);
            $statementPost->countComments = $posts->getCountCommentsByPostId($statementPost->id);
            $statementPost->username =  current($posts->getPostUsername($statementPost->getUserId()));
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
        $user = $this->session->getUser();
        if (null !== $user) {
            $user = [
                'name' => $user->getUsername(),
                'id' => $user->getId()
            ];
        }

        $this->view('posts.html.twig', ['posts' => $statementPosts, 'pages' => $pages, 'authUser' => $user]);
    }
    public function admin()
    {
        $user = $this->session->getUser();
        if (null !== $user) {
            $user = [
                'name' => $user->getUsername(),
                'id' => $user->getId(),
                'roleName' => $user->getRoleName()
            ];
        }


        return $this->view('' . $user['roleName'] . '.panel.html.twig', ['login' => true, 'authUser' => $user]);
    }
}
