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
        $categories = new CategoryManager(Application::getDatasource());
        $statementCategories = $categories->getAll();
        $posts = new PostManager(Application::getDatasource());
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
        if (null === $user) {
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

        $user = $userSession ? $userSession->getAllUserInfo() : null;

        $filter = new FilterBuilder(Application::getFilter(), substr(strtolower($this->getRoute()->getcontroller()), strrpos($this->getRoute()->getcontroller(), "\\") + 1));
        $httpParams = $this->groupFilterDataUser();
        $sqlParams = [ "publish_state" => TRUE];
        $posts = new PostManager(Application::getDatasource());
        $pages = [];
        $sortBySQL = Text::camelCaseToSnakeCase($httpParams['sortBy']);

        $count = count($posts->getAllPublish());

        $pagination = new Pagination($this->getRoute(), $count);
        $pages = $pagination->pagesInformations();

        if ($httpParams['listSortSelect'] === null) {
            $statementPosts = $posts->getAllOrderLimit($sortBySQL, $httpParams['sortDir'], $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams);
        } else {
            $statementPosts = $posts->getAllOrderLimitCat($sortBySQL, $httpParams['sortDir'], $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams, $httpParams['listSortSelect']);
        }


        foreach ($statementPosts as $statementPost) {
            $statementPost->categories = $posts->getCategoriesById($statementPost->id);
            $statementPost->countComments = $posts->getCountCommentsByPostId($statementPost->id);
            $statementPost->username =  ($posts->getPostUsername($statementPost->getUserId()));
        }

        $this->view(
            'frontoffice/posts.html.twig',
            [
                'baseUrl' => Application::getBaseUrl(),
                'posts' => $statementPosts,
                'sort' => $filter->getSort(),
                'dir' => $filter->getDir(),
                'sortDir' => $httpParams['sortDir'],
                'sortBy' => $httpParams['sortBy'],
                'listSort' => $httpParams['listSort'],
                'list' => $filter->getList() ,
                'idListSelect' => $httpParams['listSortSelect'],
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
     * @param  int $id
     * @return void
     */
    public function post(int $id): void
    {
        // $username=Session::getUsername();
        $post = new PostManager(Application::getDatasource());
        $comment = new CommentManager(Application::getDatasource());
        $statementPost = $post->getById($id);
        $statementComments = $comment->getCommentsByPostId($id);
        $statementPost->username =  ($post->getPostUsername($statementPost->getUserId()));
        $statementPost->categories = $post->getCategoriesById($statementPost->id);
        foreach ($statementComments as $statementComment) {
            $statementComment->setUsername($comment->getCommentUsername($statementComment->getUserId()));
        }
        $userSession = $this->session->getUser();
        $user = $userSession ? $userSession->getAllUserInfo() : null;

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
        $user = $userSession ? $userSession->getAllUserInfo() : null;

        $this->view('frontoffice/' . $user['roleName'] . '.panel.html.twig', ['baseUrl' => Application::getBaseUrl(), 'login' => true, 'authUser' => $user]);
    }


}
