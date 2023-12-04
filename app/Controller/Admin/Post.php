<?php

namespace App\Controller\Admin;

use App\Controller\Pagination;
use App\Model\Manager\CategoryManager;
use App\Model\Manager\{PostManager, CommentManager};
use Framework\Application;
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

        $user = $userSession ? $userSession->getAllUserInfo() : null;

        $filter = new FilterBuilder(Application::getFilter(), substr(strtolower($this->getRoute()->getcontroller()), strrpos($this->getRoute()->getcontroller(), "\\") + 1));
        $httpParams = $this->groupFilterDataUser();
        $sqlParams = [];
        $posts = PostManager::getPostInstance(Application::getDatasource());
        $pages = [];


        $sortBySQL = Text::camelCaseToSnakeCase($httpParams['sort']);
        if ($user['roleName'] === "admin") {
            $count = count($posts->getAllByParams([]));
        } else {
            $sqlParams = ['user_id' => $user['id']];
            $count = count($posts->getAllByParams($sqlParams));
        }//end if

        if ($httpParams['list'] !== null) {
            $count = count($posts->getAllFilteredCat($sqlParams, $httpParams['listSelect']));
        }//end if

        $pagination = new Pagination($this->getRoute(), $count);
        $pages = $pagination->pagesInformations();

        if ($httpParams['listSelect'] === null) {
            $statementPosts = $posts->getAllOrderLimit($sortBySQL, $httpParams['dir'], $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams);
        } else {
            $statementPosts = $posts->getAllOrderLimitCat($sortBySQL, $httpParams['dir'], $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams, $httpParams['listSelect']);
        }
        foreach ($statementPosts as $statementPost) {
            $statementPost->categories = $posts->getCategoriesById($statementPost->id);
            $statementPost->countComments = $posts->getCountCommentsByPostId($statementPost->id);
            $statementPost->username =  ($posts->getPostUsername($statementPost->getUserId()));
        }

        $dataView = [
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
        ];
        $params=(new HttpParams())->getParamsGet();
        if (isset($params['delete'])) {
            if ($params['delete'] == 'ok') {
                $dataView['message'] = '<strong>Suppression réussie</strong><br>
                l\'article a été supprimé.';
                $dataView['error'] = false;
            }
        }

        $this->view('backoffice/admin.posts.html.twig', $dataView);
    }


    /**
     * deletePost
     *
     * @param  int $id
     * @return void
     */
    public function deletePost(int $id): void
    {
        (PostManager::getPostInstance(Application::getDatasource()))->delete($id);
        header('Location: '. Application::getBaseUrl() .'/admin/posts?delete=ok');
    }


    /**
     * addPost
     *
     * @return void
     */
    public function addPost(): void
    {
        $category = CategoryManager::getCategoryInstance(Application::getDatasource());
        $statementCategories = $category->getAllByParams([]);
        $userSession = $this->session->getUser();
        $user = $userSession->getAllUserInfo();
        $this->session->generateToken();
        $user['token']= $this->session->getToken();
        $this->view('backoffice/add.post.html.twig', ['baseUrl' => Application::getBaseUrl(), 'categories' => $statementCategories, 'authUser' => $user]);
    }


    /**
     * addedPost
     *
     * @return void
     */
    public function addedPost()
    {
        $post = PostManager::getPostInstance(Application::getDatasource());
        $request = new Request(Application::getBaseUrl() .'/');

        $newId = $post->insertNewPost((new HttpParams())->getParamsPost());
        $userSession = $this->session->getUser();
        $user = $userSession->getAllUserInfo();
        $statementPost = $post->getById($newId);
        $statementPost->username = ($post->getPostUsername($statementPost->getUserId()));
        $statementPost->categories = $post->getCategoriesById($statementPost->id);
        $this->view('backoffice/modify.post.html.twig', ['baseUrl' => Application::getBaseUrl(),'post' => $statementPost, 'authUser' => $user]);
    }


    /**
     * modifyPost
     *
     * @param  int $id
     * @return void
     */
    public function modifyPost(int $id)
    {
        $post = PostManager::getPostInstance(Application::getDatasource());
        $statementPost = $post->getById($id);
        $statementPost->username = ($post->getPostUsername($statementPost->getUserId()));
        $statementPost->categories = $post->getCategoriesById($statementPost->id);
        $userSession = $this->session->getUser();
        $user = $userSession->getAllUserInfo();
        $this->session->generateToken();
        $user['token']= $this->session->getToken();
        $this->view('backoffice/modify.post.html.twig', ['baseUrl' => Application::getBaseUrl(), 'post' => $statementPost , 'authUser' => $user]);
    }

    /**
     * modifiedPost
     *
     * @param  int $id
     * @return void
     */
    public function modifiedPost(int $id)
    {
        $post = PostManager::getPostInstance(Application::getDatasource());
        $params = [];
        $statement = $post->getById($id);


        if ($this->getRoute()->getParams()['content'] !== $statement->getContent()) {
            $params['content'] = $this->getRoute()->getParams()['content'];
        }

        if ($this->getRoute()->getParams()['name'] !== $statement->getName()) {
            $params['name'] = $this->getRoute()->getParams()['name'];
        }
        if (null !== $params) {
            $params['modifiedAt'] = (new DateTime('now'))->format('Y-m-d H:i:s');
            $params['publishState'] = 0;

            $post->update($statement, $params);
        }

        $userSession = $this->session->getUser();
        $user = $userSession->getAllUserInfo();
        $this->session->generateToken();
        $user['token']= $this->session->getToken();
        $post = PostManager::getPostInstance(Application::getDatasource());
        $statementPost = $post->getById($id);
        $statementPost->username = ($post->getPostUsername($statementPost->getUserId()));
        $statementPost->categories = $post->getCategoriesById($statementPost->id);

        $this->view('backoffice/modify.post.html.twig', ['baseUrl' => Application::getBaseUrl(), 'post' => $statementPost, 'authUser' => $user]);
    }


    /**
     * addComment
     *
     * @param  int $id
     * @return void
     */
    public function addComment(int $id): void
    {

        $post = PostManager::getPostInstance(Application::getDatasource());
        $comment = CommentManager::getCommentInstance(Application::getDatasource());
        $statementPost = $post->getById($id);
        $statementComments = $comment->getCommentsByPostId($id);
        $statementPost->username =  ($post->getPostUsername($statementPost->getUserId()));
        foreach ($statementComments as $statementComment) {
            $statementComment->setUsername($comment->getCommentUsername($statementComment->getUserId()));
        }
        $statementPost->categories = $post->getCategoriesById($statementPost->id);
        $statementPost->countComments = $post->getCountCommentsByPostId($statementPost->id);
        $statementPost->username = ($post->getPostUsername($statementPost->getUserId()));

        $userSession = $this->session->getUser();
        $user = $userSession->getAllUserInfo();
        $this->session->generateToken();
        $user['token']= $this->session->getToken();
        $this->view('backoffice/add.comment.html.twig', ['baseUrl' => Application::getBaseUrl(), 'post' => $statementPost, 'authUser' => $user, 'comments' => $statementComments]);
    }


    /**
     * addedComment
     *
     * @param  int $id
     * @return void
     */
    public function addedComment(int $id): void
    {

        $comment = CommentManager::getCommentInstance(Application::getDatasource());
        // $request = new Request(Application::getBaseUrl() .'/');

        $comment->insertNewComment((new HttpParams())->getParamsPost());
        //Message de prise en compte et de validation du commentaire par l'administrateur

        $userSession = $this->session->getUser();
        $user = $userSession->getAllUserInfo();

        $post = PostManager::getPostInstance(Application::getDatasource());
        $statementPost = $post->getById($id);
        $slug = $statementPost->getSlug();
        Header('Location: '. Application::getBaseUrl() .'/post/'. $slug .'/'. $id);
    }


    /**
     * moderationPosts
     *
     * @return void
     */
    public function moderationPosts(): void
    {

        $userSession = $this->session->getUser();

        $user = $userSession ? $userSession->getAllUserInfo() : null;
        $this->session->generateToken();
        $user['token']= $this->session->getToken();
        $filter = new FilterBuilder(Application::getFilter(), 'admin.' . substr(strtolower($this->getRoute()->getcontroller()), strrpos($this->getRoute()->getcontroller(), "\\") + 1));

        $httpParams = $this->groupFilterDataUser();

        $sqlParams = [];

        $posts = PostManager::getPostInstance(Application::getDatasource());
        $pages = [];
        $sortBySQL = Text::camelCaseToSnakeCase($httpParams['sort']);

        $count = count($posts->getAllByParams([]));

        if ($httpParams['list'] !== null) {
            $count = count($posts->getAllFilteredCat($sqlParams, $httpParams['listSelect']));
        }//end if

        $pagination = new Pagination($this->getRoute(), $count);
        $pages = $pagination->pagesInformations();

        if ($httpParams['listSelect'] === null) {
            $statementPosts = $posts->getAllOrderLimit($sortBySQL, $httpParams['dir'], $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams);
        } else {
            $statementPosts = $posts->getAllOrderLimitCat($sortBySQL, $httpParams['dir'], $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams, $httpParams['listSelect']);
        }

        foreach ($statementPosts as $statementPost) {
            $statementPost->categories = $posts->getCategoriesById($statementPost->id);
            $statementPost->countComments = $posts->getCountCommentsByPostId($statementPost->id);
            $statementPost->username = ($posts->getPostUsername($statementPost->getUserId()));
        }


        $this->view(
            'backoffice/admin.moderation.posts.html.twig',
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
     * unpublishPost
     *
     * @param  int $id
     * @params array<string,int|string> page filter option
     * @return void
     */
    public function unpublishPost(int $id): void
    {
        $filterParams = (new HttpParams())->getParamsReferer();
        $filterParams = isset($filterParams)? '?'.$filterParams : null;
        (PostManager::getPostInstance(Application::getDatasource()))->unpublish($id);
        header('Location: '. Application::getBaseUrl() .'/admin/moderation/posts'.$filterParams.'#'.$id);
    }


    /**
     * publishPost
     *
     * @param  int $id
     * @return void
     */
    public function publishPost(int $id): void
    {
        $filterParams = (new HttpParams())->getParamsReferer();
        $filterParams = isset($filterParams)? '?'.$filterParams : null;
        (PostManager::getPostInstance(Application::getDatasource()))->publish($id);
        header('Location: '. Application::getBaseUrl() .'/admin/moderation/posts'.$filterParams.'#'.$id);
    }


}
