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
     * @return string
     */
    public function postsByCategory()
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
            return $this->view('frontoffice/posts.category.html.twig', ['categories' => $statementCategories, 'posts' => $postsByCategories, 'error' => false]);
        }

        $user = [
            'name' => $user->getUsername(),
            'id' => $user->getId(),
            'roleName' => $user->getRoleName()
        ];
        return $this->view('frontoffice/posts.category.html.twig', ['categories' => $statementCategories, 'posts' => $postsByCategories,  'authUser' => $user]);
    }


    /**
     * posts : recovers all informations for each publish article for display with paging
     *
     * @return void
     */
    public function posts()
    {
        $user = $this->session->getUser();
        if (null !== $user) {
            $user = [
                 'name' => $user->getUsername(),
                 'id' => $user->getId(),
                 'roleName' => $user->getRoleName()
                ];
         }

        $filter = new FilterBuilder(Application::getFilter(), substr(strtolower($this->getRoute()->getcontroller()), strrpos($this->getRoute()->getcontroller(), "\\") + 1));
        $listSort = !empty(($this->getRoute()->getParams())['list']) ? ($this->getRoute()->getParams())['list'] : null;
        $listSortSelect = (($this->getRoute()->getParams())['listSelect']) !== '---'? ($this->getRoute()->getParams())['listSelect'] : null;
        if (($listSortSelect) === null){
            $listSort = null;
        }

        $sortBy = isset(($this->getRoute()->getParams())['sort']) ? ($this->getRoute()->getParams())['sort'] : 'createdAt';
        $sortDir = ($this->getRoute()->getParams())['dir'] ?? 'DESC';
        $sqlParams = [ "publish_state" => TRUE];
        $posts = new PostManager(Application::getDatasource());
        $pages = [];
        $sortBySQL = Text::camelCaseToSnakeCase($sortBy);


        if ($sqlParams["publish_state"] == FALSE) {
            $count = count($posts->getAllPublish());
        } else {
            $count = count($posts->getAll());
        }//enf id

        $pagination = new Pagination($this->getRoute(), $count);
        $pages = $pagination->pagesInformations();

        if (!$listSortSelect) {
            $statementPosts = $posts->getAllOrderLimit($sortBySQL, $sortDir, $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams);
        } else {
            $statementPosts = $posts->getAllOrderLimitCat($sortBySQL, $sortDir, $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams, $listSortSelect);
        }


        foreach ($statementPosts as $statementPost) {
            $statementPost->categories = $posts->getCategoriesById($statementPost->id);
            $statementPost->countComments = $posts->getCountCommentsByPostId($statementPost->id);
            $statementPost->username =  ($posts->getPostUsername($statementPost->getUserId()));
        }

        return $this->view(
            'frontoffice/posts.html.twig',
            [
                'posts' => $statementPosts,
                'sort' => $filter->getSort(),
                'dir' => $filter->getDir(),
                'sortDir' => $sortDir,
                'sortBy' => $sortBy,
                'listSort' => $listSort,
                'list' => $filter->getList() ,
                'idListSelect' => $listSortSelect,
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
    public function post(int $id)
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
        $user = $this->session->getUser();
        if (null !== $user) {
            $user = [
                'name' => $user->getUsername(),
                'id' => $user->getId()
            ];
        }
        return $this->view('frontoffice/post.html.twig', ['post' => $statementPost, 'authUser' => $user, 'comments' => $statementComments]);
    }


    /**
     * admin : administration role panel for user
     *
     * @return void
     */
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
        return $this->view('frontoffice/' . $user['roleName'] . '.panel.html.twig', ['login' => true, 'authUser' => $user]);
    }


}
