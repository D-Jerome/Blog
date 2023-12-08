<?php

namespace App\Controller\Admin;

use App\Controller\Pagination;
use App\Model\Manager\CommentManager;
use App\Model\Manager\PostManager;
use Framework\{Application,Config};
use Framework\BaseController;
use Framework\Helpers\FilterBuilder;
use Framework\Helpers\Text;
use Framework\HttpParams;
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
        $user = $userSession instanceof \Framework\Security\AuthUser ? $userSession->getAllUserInfo() : null;
        $filter = new FilterBuilder('admin.' . substr(strtolower($this->getRoute()->getcontroller()), strrpos($this->getRoute()->getcontroller(), "\\") + 1));
        $httpParams = $this->groupFilterDataUser();
        $sqlParams = [];
        $comments = CommentManager::getCommentInstance(Config::getDatasource());
        $posts = PostManager::getPostInstance(Config::getDatasource());
        $pages = [];

        $sortBySQL = Text::camelCaseToSnakeCase((string)$httpParams['sort']);
        if ($user['roleName'] === "admin") {
            $count = count($comments->getAllByParams([]));
        } else {
            $sqlParams = ['user_id' => $user['id']];
            $count = count($comments->getAllByParams($sqlParams));
        }//end if

        $pagination = new Pagination($this->getRoute(), $count);
        $pages = $pagination->pagesInformations();


        $statementComments = $comments->getAllOrderLimit($sortBySQL, (string)$httpParams['dir'], $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams);
        foreach ($statementComments as $statementComment) {
            $statementComment->setUsername($comments->getCommentUsername($statementComment->getUserId()));
        }

        $statementPosts = $posts->getAllByParams([]);
        foreach ($statementPosts as $statementPost) {
            $statementPost->setCategories($posts->getCategoriesById($statementPost->getId()));
            $statementPost->setCountComments($posts->getCountCommentsByPostId($statementPost->getId()));
            $statementPost->setUsername($posts->getPostUsername($statementPost->getUserId()));
        }
        $this->view(
            'backoffice/admin.comments.html.twig',
            [
            'baseUrl' => Config::getBaseUrl(),
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
     * @return void
     */
    public function modifyComment(int $id): void
    {

        $comments = CommentManager::getCommentInstance(Config::getDatasource());
        $statement = $comments->getById($id);
        $statement->setUsername($comments->getCommentUsername($statement->getUserId()));

        $userSession = $this->session->getUser();
        $user = $userSession->getAllUserInfo();
        $this->session->generateToken();
        $user['token'] = $this->session->getToken();
        $this->view('backoffice/modify.comment.html.twig', ['baseUrl' => Config::getBaseUrl(), 'comment' => $statement, 'authUser' => $user]);
    }


    /**
     * modifiedComment: action after modification of comment
     *
     * @return void
     */
    public function modifiedComment(int $id): void
    {
        $comments = CommentManager::getCommentInstance(Config::getDatasource());
        $params = [];
        $statement = $comments->getById($id);

        if ((new HttpParams())->getParamsPost()['content'] !== $statement->getContent()) {
            $params['content'] = (new HttpParams())->getParamsPost()['content'];
        }
        if (null !== $params) {
            $params['modifiedAt'] = (new DateTime('now'))->format('Y-m-d H:i:s');
            $params['publishState'] = 0;

            $comments->update($statement, $params);
        }

        $userSession = $this->session->getUser();
        $user = $userSession->getAllUserInfo();
        $this->session->generateToken();
        $user['token'] = $this->session->getToken();
        $comments = CommentManager::getCommentInstance(Config::getDatasource());
        $statement = $comments->getById($id);
        $statement->setUsername($comments->getCommentUsername($statement->getUserId()));

        $this->view('backoffice/modify.comment.html.twig', ['baseUrl' => Config::getBaseUrl(), 'comment' => $statement, 'authUser' => $user]);
    }


    /**
     * moderationComments; prepare view to moderate comments
     *
     * @return void
     */
    public function moderationComments()
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

        $pagination = new Pagination($this->getRoute(), $count);
        $pages = $pagination->pagesInformations();

        $comments = (CommentManager::getCommentInstance(Config::getDatasource()));

        if ($httpParams['listSelect'] === null) {
            $statementComments = $comments->getAllOrderLimit($sortBySQL, (string)$httpParams['dir'], $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams);
        } else {
            $statementComments = $comments->getAllOrderLimitCat($sortBySQL, (string)$httpParams['dir'], $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams, (int)$httpParams['listSelect']);
        }
        foreach ($statementComments as $statementComment) {
            $statementComment->setUsername($comments->getCommentUsername($statementComment->getUserId()));
        }


        $statementPosts = $posts->getAllByParams([]);
        foreach ($statementPosts as $statementPost) {
            $statementPost->setCategories($posts->getCategoriesById($statementPost->getId()));
            $statementPost->setCountComments($posts->getCountCommentsByPostId($statementPost->getId()));
            $statementPost->setusername($posts->getPostUsername($statementPost->getUserId()));
        }

        $this->view(
            'backoffice/admin.moderation.comments.html.twig',
            [
            'baseUrl' => Config::getBaseUrl(),
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
     * @return void
     */
    public function unpublishComment(int $id): void
    {
        $filterParams = (new HttpParams())->getParamsReferer();
        $filterParams = isset($filterParams) ? '?' . $filterParams : null;
        (CommentManager::getCommentInstance(Config::getDatasource()))->unpublish($id);
        header('Location: ' . Config::getBaseUrl() . '/admin/moderation/comments' . $filterParams . '#' . $id);
    }


    /**
     * publishComment: action of publish comment
     *
     * @return void
     */
    public function publishComment(int $id): void
    {
        $filterParams = (new HttpParams())->getParamsReferer();
        $filterParams = isset($filterParams) ? '?' . $filterParams : null;
        (CommentManager::getCommentInstance(Config::getDatasource()))->publish($id);
        header('Location: ' . Config::getBaseUrl() . '/admin/moderation/comments' . $filterParams . '#' . $id);
    }
}
