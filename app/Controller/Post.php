<?php

namespace App\Controller;

use App\Model\Entities\Post as EntitiesPost;
use Framework\BaseController;
use App\Model\Manager\{CategoryManager, PostManager, CommentManager, UserManager};
use Framework\Application;
use Framework\Helpers\FilterBuilder;
use Framework\Helpers\Text;
use App\Controller\Pagination;
use PDO;

class Post extends BaseController
{
    /**
     * postsByCategory : 3 most recent Posts by category
     *
     * @return void
     */
    public function postsByCategory(): void
    {
        //recherche des 3 derniers articles par catégories
        $categories = CategoryManager::getCategoryInstance(Application::getDatasource());
        $statementCategories = $categories->getAllByParams([]);
        $posts = PostManager::getPostInstance(Application::getDatasource());
        $postsByCategories = null;
        foreach ($statementCategories as $statementCategory) {
            $statementPostsByCategory = $posts->getPostsbyCategory($statementCategory);
            foreach ($statementPostsByCategory as $statementPost) {
                $statementPost->setCategories([$statementCategory]);
                $statementPost->setCountComments($posts->getCountCommentsByPostId($statementPost->getId()));
                $statementPost->setUsername($posts->getPostUsername($statementPost->getUserId()));
            }
            $postsByCategories =  array_merge((array) $statementPostsByCategory, (array) $postsByCategories);
        }

        $user = $this->session->getUser();
        if (!$user instanceof \Framework\Security\AuthUser) {
            $this->view('frontoffice/posts.category.html.twig', ['baseUrl' => Application::getBaseUrl(), 'categories' => $statementCategories, 'posts' => $postsByCategories, 'error' => false]);
            exit;
        }

        $user = [
            'name' => $user->getUsername(),
            'id' => $user->getId(),
            'roleName' => $user->getRoleName()
        ];
        $this->view('frontoffice/posts.category.html.twig', ['baseUrl' => Application::getBaseUrl(), 'categories' => $statementCategories, 'posts' => $postsByCategories,  'authUser' => $user]);
    }


    /**
     * posts : recovers all informations for each publish article for display with paging
     *
     * @return void
     */
    public function posts(): void
    {
        $userSession = $this->session->getUser();

        $user = $userSession instanceof \Framework\Security\AuthUser ? $userSession->getAllUserInfo() : null;

        $filter = new FilterBuilder(Application::getFilter(), substr(strtolower($this->getRoute()->getcontroller()), strrpos($this->getRoute()->getcontroller(), "\\") + 1));
        $httpParams = $this->groupFilterDataUser();
        $sqlParams = [ "publish_state" => true];
        $posts = PostManager::getPostInstance(Application::getDatasource());
        $pages = [];
        $sortBySQL = Text::camelCaseToSnakeCase((string)$httpParams['sort']);

        if ($httpParams['list'] === null) {
            $count = count($posts->getAllPublish());
        } else {
            $count = count($posts->getAllFilteredByParam((string)$httpParams['list'], (int)$httpParams['listSelect'], true));
        }

        $pagination = new Pagination($this->getRoute(), $count);
        $pages = $pagination->pagesInformations();

        if ($httpParams['listSelect'] === null) {
            $statementPosts = $posts->getAllOrderLimit($sortBySQL, (string)$httpParams['dir'], $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams);
        } else {
            $statementPosts = $posts->getAllOrderLimitCat($sortBySQL, (string)$httpParams['dir'], $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams, (int)$httpParams['listSelect']);
        }


        foreach ($statementPosts as $statementPost) {
            $statementPost->setCategories($posts->getCategoriesById($statementPost->getId()));
            $statementPost->setCountComments($posts->getCountCommentsByPostId($statementPost->getId()));
            $statementPost->setUsername($posts->getPostUsername($statementPost->getUserId()));
        }

        $this->view(
            'frontoffice/posts.html.twig',
            [
                'baseUrl' => Application::getBaseUrl(),
                'posts' => $statementPosts,
                'sort' => $filter->getSort(),
                'dir' => $filter->getDir(),
                'sortDir' => $httpParams['dir'],
                'sortBy' => $httpParams['sort'],
                'listSort' => $httpParams['list'],
                'list' => $filter->getList() ,
                'idListSelect' => $httpParams['listSelect'],
                'listSelect' => $filter->getListSelect(),
                'listNames' => $filter->getListNames(),
                'pages' => $pages,
                'authUser' => $user
                ]
        );
    }



    /**
     * post : recovers article's informations (in @param) for display
     *
     * @return void
     */
    public function post(int $id): void
    {
        $post = PostManager::getPostInstance(Application::getDatasource());
        $comment = CommentManager::getCommentInstance(Application::getDatasource());
        $statementPost = $post->getById($id);
        $statementComments = $comment->getCommentsByPostId($id);
        $statementPost->setUsername($post->getPostUsername($statementPost->getUserId()));
        $statementPost->setCategories($post->getCategoriesById($statementPost->getId()));
        foreach ($statementComments as $statementComment) {
            $statementComment->setUsername((string)$comment->getCommentUsername($statementComment->getUserId()));
        }
        $userSession = $this->session->getUser();
        $user = $userSession instanceof \Framework\Security\AuthUser ? $userSession->getAllUserInfo() : null;

        $this->view('frontoffice/post.html.twig', ['baseUrl' => Application::getBaseUrl(), 'post' => $statementPost, 'authUser' => $user, 'comments' => $statementComments]);
    }


    /**
     * admin : administration role panel for user
     *
     * @return void
     */
    public function admin(): void
    {
        $userSession = $this->session->getUser();
        $user = $userSession instanceof \Framework\Security\AuthUser ? $userSession->getAllUserInfo() : null;

        $this->view('frontoffice/' . $user['roleName'] . '.panel.html.twig', ['baseUrl' => Application::getBaseUrl(), 'login' => true, 'authUser' => $user]);
    }
}
