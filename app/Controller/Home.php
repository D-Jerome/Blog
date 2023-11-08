<?php

namespace App\Controller;

use App\Model\Category;
use App\Model\Manager\CategoryManager;
use App\Model\Manager\PostManager;
use Framework\Application;
use Framework\BaseController;
use Framework\Request;
use Framework\Session;

class Home extends BaseController
{
    public function home()
    {
        //recherche des 3 derniers articles par catégories

        $categories = new CategoryManager(Application::getDatasource());
        $statementCategories = $categories->getAll();
        $posts = new PostManager(Application::getDatasource());
        $postsByCategories = null;
        foreach ($statementCategories as $statementCategory) {
            $statementPostsByCategory = $posts->getPostsbyCategory($statementCategory);

            foreach ($statementPostsByCategory as $statementPost) {

                $statementPost->categories =  [$statementCategory];
                $statementPost->countComments = (int)$posts->getCountCommentsByPostId($statementPost->id);
                $statementPost->username =  current($posts->getPostUsername($statementPost->getUserId()));
            }
            $postsByCategories =  array_merge((array) $statementPostsByCategory, (array) $postsByCategories);
        }


        $user = $this->session->getUser();
        if (null !== $user) {
            $user = [
                'name' => $user->getUsername(),
                'id' => $user->getId()
            ];
        }

        $user = $this->session->getUser();
        if (null === $user) {
            return $this->view('home.html.twig', ['categories' => $statementCategories, 'posts' => $postsByCategories, 'error' => false]);
        }

        $user = [
            'name' => $user->getUsername(),
            'id' => $user->getId()
        ];


        return $this->view('home.html.twig', ['categories' => $statementCategories, 'posts' => $postsByCategories,  'authUser' => $user]);
    }

    public function login()
    {
        echo "je suis la page de login";
    }
}
