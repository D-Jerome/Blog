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

        $posts = new PostManager(Application::getDatasource());
        $pages = [];

        $sortBySQL = Text::camelCaseToSnakeCase($httpParams['sortBy']);

        $count = count($posts->getAll());

        $pagination = new Pagination($this->getRoute(), $count);
        $pages = $pagination->pagesInformations();
        $comments = (new CommentManager(Application::getDatasource()));
        if ($user['roleName'] !== "admin") {
            $sqlParams = ['user_id' => $user['id']];
        }//end if

        $statementComments = $comments->getAllOrderLimit($sortBySQL, $httpParams['sortDir'], $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams);
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
     * modifyComment
     *
     * @param  int $id
     * @return void
     */
    public function modifyComment(int $id): void
    {

        $comments = new CommentManager(Application::getDatasource());
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
        $comments = new CommentManager(Application::getDatasource());
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

        $user = $this->session->getUser();
        $user = [
                 'name' => $user->getUsername(),
                 'id' => $user->getId(),
                 'roleName' => $user->getRoleName()
                ];

        $comments = new CommentManager(Application::getDatasource());
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

        $posts = new PostManager(Application::getDatasource());
        $pages = [];

        $userSession = $this->session->getUser();
        $user = $userSession->getAllUserInfo();

        $sortBySQL = Text::camelCaseToSnakeCase($httpParams['sortBy']);

        $count = count($posts->getAll());

        $pagination = new Pagination($this->getRoute(), $count);
        $pages = $pagination->pagesInformations();

        $comments = (new CommentManager(Application::getDatasource()));

        if ($httpParams['listSortSelect'] === null) {
            $statementComments = $comments->getAllOrderLimit($sortBySQL, $httpParams['sortDir'], $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams);
        } else {
            $statementComments = $comments->getAllOrderLimitCat($sortBySQL, $httpParams['sortDir'], $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams, $httpParams['listSortSelect']);
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
     * unpublishComment: action of unpublish comment
     *
     * @param  int $id
     * @return void
     */
    public function unpublishComment(int $id): void
    {
        (new CommentManager(Application::getDatasource()))->unpublish($id);
        header('Location: '. Application::getBaseUrl() .'/admin/moderation/comments#'.$id);
    }


    /**
     * publishComment: action of publish comment
     *
     * @param  int $id
     * @return void
     */
    public function publishComment(int $id): void
    {
        (new CommentManager(Application::getDatasource()))->publish($id);
        header('Location: '. Application::getBaseUrl() .'/admin/moderation/comments#'.$id);
    }


}
