<?php

namespace App\Controller\Admin;

use App\Controller\Pagination;
use App\Model\Manager\CategoryManager;
use App\Model\Manager\{PostManager, CommentManager};
use Framework\{Application,Config};
use Framework\BaseController;
use Framework\Helpers\{Text, FilterBuilder};
use Framework\{Request, HttpParams};
use Framework\Session;
use Safe\DateTime;

use function Safe\parse_url;

class Post extends BaseController
{
    /**
     * posts: Show page with all published posts
     *
     * @return void
     */
    public function posts(): void
    {
        $userSession = $this->session->getUser();

        $user = $userSession instanceof \Framework\Security\AuthUser ? $userSession->getAllUserInfo() : null;

        $filter = new FilterBuilder(substr(strtolower($this->getRoute()->getcontroller()), strrpos($this->getRoute()->getcontroller(), "\\") + 1));
        $httpParams = $this->groupFilterDataUser();
        $sqlParams = [] ;
        $posts = PostManager::getPostInstance(Config::getDatasource());
        $pages = [];
        $sortBySQL = Text::camelCaseToSnakeCase((string)$httpParams['sort']);

        if ($user['roleName'] === "admin") {
            $count = count($posts->getAllByParams([]));
        } else {
            $sqlParams = ['user_id' => $user['id']];
            $count = count($posts->getAllByParams($sqlParams));
        }//end if

        if ($httpParams['list'] !== null) {
            $count = count($posts->getAllFilteredCat($sqlParams, (int)$httpParams['listSelect']));
        }//end if

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

        $dataView = [
            'baseUrl' => Config::getBaseUrl(),
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
        ];
        $params = (new HttpParams())->getParamsGet();
        if (isset($params['delete']) && $params['delete'] == 'ok') {
            $dataView['message'] = '<strong>Suppression réussie</strong><br>
                l\'article a été supprimé.';
            $dataView['error'] = false;
        }

        $this->view('backoffice/admin.posts.html.twig', $dataView);
    }


    /**
     * deletePost
     *
     * @return void
     */
    public function deletePost(int $id): void
    {
        (PostManager::getPostInstance(Config::getDatasource()))->delete($id);
        header('Location: ' . Config::getBaseUrl() . '/admin/posts?delete=ok');
    }


    /**
     * addPost
     *
     * @return void
     */
    public function addPost(): void
    {
        $category = CategoryManager::getCategoryInstance(Config::getDatasource());
        $statementCategories = $category->getAllByParams([]);
        $userSession = $this->session->getUser();
        $user = $userSession->getAllUserInfo();
        $this->session->generateToken();
        $user['token'] = $this->session->getToken();
        $this->view('backoffice/add.post.html.twig', ['baseUrl' => Config::getBaseUrl(), 'categories' => $statementCategories, 'authUser' => $user]);
    }


    /**
     * addedPost
     *
     * @return void
     */
    public function addedPost()
    {
        $post = PostManager::getPostInstance(Config::getDatasource());

        $newId = $post->insertNewPost((new HttpParams())->getParamsPost());
        $userSession = $this->session->getUser();
        $user = $userSession->getAllUserInfo();
        $statementPost = $post->getById($newId);
        $statementPost->setUsername($post->getPostUsername($statementPost->getUserId()));
        $statementPost->setCategories($post->getCategoriesById($statementPost->getId()));
        $this->view('backoffice/modify.post.html.twig', ['baseUrl' => Config::getBaseUrl(),'post' => $statementPost, 'authUser' => $user]);
    }


    /**
     * modifyPost
     *
     * @return void
     */
    public function modifyPost(int $id)
    {
        $post = PostManager::getPostInstance(Config::getDatasource());
        $statementPost = $post->getById($id);
        $statementPost->setUsername($post->getPostUsername($statementPost->getUserId()));
        $statementPost->setCategories($post->getCategoriesById($statementPost->getId()));
        $userSession = $this->session->getUser();
        $user = $userSession->getAllUserInfo();
        $this->session->generateToken();
        $user['token'] = $this->session->getToken();
        $this->view('backoffice/modify.post.html.twig', ['baseUrl' => Config::getBaseUrl(), 'post' => $statementPost , 'authUser' => $user]);
    }

    /**
     * modifiedPost
     *
     * @return void
     */
    public function modifiedPost(int $id)
    {
        $post = PostManager::getPostInstance(Config::getDatasource());
        $params = [];
        $statement = $post->getById($id);
        $postDatas = (new HttpParams())->getParamsPost();

        if ($postDatas['content'] !== $statement->getContent()) {
            $params['content'] =  $postDatas['content'];
        }

        if ($postDatas['name'] !== $statement->getName()) {
            $params['name'] =  $postDatas['name'];
        }
        if (null !== $params) {
            $params['modifiedAt'] = (new DateTime('now'))->format('Y-m-d H:i:s');
            $params['publishState'] = 0;

            $post->update($statement, $params);
        }

        $userSession = $this->session->getUser();
        $user = $userSession->getAllUserInfo();
        $this->session->generateToken();
        $user['token'] = $this->session->getToken();
        $post = PostManager::getPostInstance(Config::getDatasource());
        $statementPost = $post->getById($id);
        $statementPost->setUsername($post->getPostUsername($statementPost->getUserId()));
        $statementPost->setCategories($post->getCategoriesById($statementPost->getId()));

        $this->view('backoffice/modify.post.html.twig', ['baseUrl' => Config::getBaseUrl(), 'post' => $statementPost, 'authUser' => $user]);
    }


    /**
     * addComment
     *
     * @return void
     */
    public function addComment(int $id): void
    {

        $post = PostManager::getPostInstance(Config::getDatasource());
        $comment = CommentManager::getCommentInstance(Config::getDatasource());
        $statementPost = $post->getById($id);
        $statementComments = $comment->getCommentsByPostId($id);
        $statementPost->setUsername($post->getPostUsername($statementPost->getUserId()));
        foreach ($statementComments as $statementComment) {
            $statementComment->setUsername($comment->getCommentUsername($statementComment->getUserId()));
        }
        $statementPost->setCategories($post->getCategoriesById($statementPost->getId()));
        $statementPost->setcountComments($post->getCountCommentsByPostId($statementPost->getId()));
        // $statementPost->username = ($post->getPostUsername($statementPost->getUserId()));

        $userSession = $this->session->getUser();
        $user = $userSession->getAllUserInfo();
        $this->session->generateToken();
        $user['token'] = $this->session->getToken();
        $this->view('backoffice/add.comment.html.twig', ['baseUrl' => Config::getBaseUrl(), 'post' => $statementPost, 'authUser' => $user, 'comments' => $statementComments]);
    }


    /**
     * addedComment
     *
     * @return void
     */
    public function addedComment(int $id): void
    {

        $comment = CommentManager::getCommentInstance(Config::getDatasource());

        $comment->insertNewComment((new HttpParams())->getParamsPost());
        //Message de prise en compte et de validation du commentaire par l'administrateur

        $userSession = $this->session->getUser();
        $user = $userSession->getAllUserInfo();

        $post = PostManager::getPostInstance(Config::getDatasource());
        $statementPost = $post->getById($id);
        $slug = $statementPost->getSlug();
        Header('Location: ' . Config::getBaseUrl() . '/post/' . $slug . '/' . $id);
    }


    /**
     * moderationPosts
     *
     * @return void
     */
    public function moderationPosts(): void
    {

        $userSession = $this->session->getUser();

        $user = $userSession instanceof \Framework\Security\AuthUser ? $userSession->getAllUserInfo() : null;
        $this->session->generateToken();
        $user['token'] = $this->session->getToken();
        $filter = new FilterBuilder('admin.' . substr(strtolower($this->getRoute()->getcontroller()), strrpos($this->getRoute()->getcontroller(), "\\") + 1));

        $httpParams = $this->groupFilterDataUser();

        $sqlParams = [];

        $posts = PostManager::getPostInstance(Config::getDatasource());
        $pages = [];
        $sortBySQL = Text::camelCaseToSnakeCase((string)$httpParams['sort']);

        $count = count($posts->getAllByParams([]));

        if ($httpParams['list'] !== null) {
            $count = count($posts->getAllFilteredCat($sqlParams, (int)$httpParams['listSelect']));
        }//end if

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
            'backoffice/admin.moderation.posts.html.twig',
            [
            'baseUrl' => Config::getBaseUrl(),
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
     * unpublishPost
     *
     * @params array<string,int|string> page filter option
     * @return void
     */
    public function unpublishPost(int $id): void
    {
        $filterParams = (new HttpParams())->getParamsReferer();
        $filterParams = isset($filterParams) ? '?' . $filterParams : null;
        (PostManager::getPostInstance(Config::getDatasource()))->unpublish($id);
        header('Location: ' . Config::getBaseUrl() . '/admin/moderation/posts' . $filterParams . '#' . $id);
    }


    /**
     * publishPost
     *
     * @return void
     */
    public function publishPost(int $id): void
    {
        $filterParams = (new HttpParams())->getParamsReferer();
        $filterParams = isset($filterParams) ? '?' . $filterParams : null;
        (PostManager::getPostInstance(Config::getDatasource()))->publish($id);
        header('Location: ' . Config::getBaseUrl() . '/admin/moderation/posts' . $filterParams . '#' . $id);
    }
}
