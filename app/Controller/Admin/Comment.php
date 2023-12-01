<?php

namespace App\Controller\Admin;

use App\Controller\Pagination;
use App\Model\Manager\CommentManager;
use App\Model\Manager\PostManager;
use Framework\Application;
use Framework\BaseController;
use Framework\Helpers\FilterBuilder;
use Framework\Helpers\Text;
use Framework\Session;
use Safe\DateTime;
use function Safe\parse_url;

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

        $userSession = $this->session->getUser();

        $user = $userSession ? $userSession->getAllUserInfo() : null;
        $filter = new FilterBuilder(Application::getFilter(), 'admin.' . substr(strtolower($this->getRoute()->getcontroller()), strrpos($this->getRoute()->getcontroller(), "\\") + 1));
        $httpParams = $this->groupFilterDataUser();
        $sqlParams = [];
        $comments = CommentManager::getCommentInstance(Application::getDatasource());
        $posts = PostManager::getPostInstance(Application::getDatasource());
        $pages = [];

        $sortBySQL = Text::camelCaseToSnakeCase($httpParams['sort']);
        if ($user['roleName'] === "admin") {
            $count = count($comments->getAll());
        }else{
            $sqlParams = ['user_id' => $user['id']];
            $count = count($comments->getAllFilteredByParams($sqlParams));
        }//end if

        $pagination = new Pagination($this->getRoute(), $count);
        $pages = $pagination->pagesInformations();


        $statementComments = $comments->getAllOrderLimit($sortBySQL, $httpParams['dir'], $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams);
        foreach ($statementComments as $statementComment) {
            $statementComment->username = $comments->getCommentUsername($statementComment->getUserId());
        }

        $statementPosts = $posts->getAll();
        foreach ($statementPosts as $statementPost) {
            $statementPost->categories = $posts->getCategoriesById($statementPost->id);
            $statementPost->countComments = $posts->getCountCommentsByPostId($statementPost->id);
            $statementPost->username = $posts->getPostUsername($statementPost->getUserId());
        }
        $this->view(
            'backoffice/admin.comments.html.twig',
            [
            'baseUrl' => Application::getBaseUrl(),
            'comments' => $statementComments,
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
     * modifyComment
     *
     * @param  int $id
     * @return void
     */
    public function modifyComment(int $id): void
    {

        $comments = CommentManager::getCommentInstance(Application::getDatasource());
        $statement = $comments->getById($id);
        $statement->username = $comments->getCommentUsername($statement->getUserId());

        $userSession = $this->session->getUser();
        $user = $userSession->getAllUserInfo();

        $this->view('backoffice/modify.comment.html.twig', ['baseUrl' => Application::getBaseUrl(), 'comment' => $statement, 'authUser' => $user]);
    }


    /**
     * modifiedComment: action after modification of comment
     *
     * @param  int $id
     * @return void
     */
    public function modifiedComment(int $id): void
    {
        $comments = CommentManager::getCommentInstance(Application::getDatasource());
        $params = [];
        $statement = $comments->getById($id);

        if ($this->getRoute()->getParams()['content'] !== $statement->getContent()) {

            $params['content'] = $this->getRoute()->getParams()['content'];
        }
        if (null !== $params) {

            $params['modifiedAt'] = (new DateTime('now'))->format('Y-m-d H:i:s');
            $params['publishState'] = 0;

            $comments->update($statement, $params);
        }

        $userSession = $this->session->getUser();
        $user = $userSession->getAllUserInfo();

        $comments = CommentManager::getCommentInstance(Application::getDatasource());
        $statement = $comments->getById($id);
        $statement->username = $comments->getCommentUsername($statement->getUserId());

        $this->view('backoffice/modify.comment.html.twig', ['baseUrl' => Application::getBaseUrl(), 'comment' => $statement, 'authUser' => $user]);
    }


    /**
     * moderationComments; prepare view to moderate comments
     *
     * @return void
     */
    public function moderationComments()
    {

        $userSession = $this->session->getUser();

        $user = $userSession ? $userSession->getAllUserInfo() : null;

        $filter = new FilterBuilder(Application::getFilter(), 'admin.'.substr(strtolower($this->getRoute()->getcontroller()), strrpos($this->getRoute()->getcontroller(), "\\") + 1));

        $httpParams = $this->groupFilterDataUser();

        $sqlParams = [];

        $posts = PostManager::getPostInstance(Application::getDatasource());
        $pages = [];

        $sortBySQL = Text::camelCaseToSnakeCase($httpParams['sort']);

        $count = count($posts->getAll());

        $pagination = new Pagination($this->getRoute(), $count);
        $pages = $pagination->pagesInformations();

        $comments = (CommentManager::getCommentInstance(Application::getDatasource()));

        if ($httpParams['listSelect'] === null) {
            $statementComments = $comments->getAllOrderLimit($sortBySQL, $httpParams['dir'], $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams);
        } else {
            $statementComments = $comments->getAllOrderLimitCat($sortBySQL, $httpParams['dir'], $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams, $httpParams['listSelect']);
        }
        foreach ($statementComments as $statementComment) {
            $statementComment->username = $comments->getCommentUsername($statementComment->getUserId());
        }


        $statementPosts = $posts->getAll();
        foreach ($statementPosts as $statementPost) {
            $statementPost->categories = $posts->getCategoriesById($statementPost->id);
            $statementPost->countComments = $posts->getCountCommentsByPostId($statementPost->id);
            $statementPost->username = $posts->getPostUsername($statementPost->getUserId());
        }

        $this->view(
            'backoffice/admin.moderation.comments.html.twig',
            [
            'baseUrl' => Application::getBaseUrl(),
            'comments' => $statementComments,
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
     * unpublishComment: action of unpublish comment
     *
     * @param  int $id
     * @return void
     */
    public function unpublishComment(int $id): void
    {
        $filterParams = parse_url(\Safe\filter_input_array(INPUT_SERVER, FILTER_SANITIZE_URL)['HTTP_REFERER'], PHP_URL_QUERY);
        if ($filterParams !== null) {
            $filterParams = '?'.$filterParams;
        }
        (CommentManager::getCommentInstance(Application::getDatasource()))->unpublish($id);
        header('Location: '. Application::getBaseUrl() .'/admin/moderation/comments'.$filterParams.'#'.$id);
    }


    /**
     * publishComment: action of publish comment
     *
     * @param  int $id
     * @return void
     */
    public function publishComment(int $id): void
    {
        $filterParams = parse_url(\Safe\filter_input_array(INPUT_SERVER, FILTER_SANITIZE_URL)['HTTP_REFERER'], PHP_URL_QUERY);
        if ($filterParams !== null) {
            $filterParams = '?'.$filterParams;
        }
        (CommentManager::getCommentInstance(Application::getDatasource()))->publish($id);
        header('Location: '. Application::getBaseUrl() .'/admin/moderation/comments'.$filterParams.'#'.$id);
    }


}
