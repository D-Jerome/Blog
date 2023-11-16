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
        $currentPage = null;
        $perPage = null;

        if (isset(($this->getRoute()->getParams())['page'])) {
            $currentPage = ($this->getRoute()->getParams())['page'];
        }
        if (isset(($this->getRoute()->getParams())['perPage'])) {
            $perPage = ($this->getRoute()->getParams())['perPage'];
        }
        $filter = new FilterBuilder(Application::getFilter(), 'admin.' . substr(strtolower($this->getRoute()->getcontroller()), strrpos($this->getRoute()->getcontroller(), "\\") + 1));
        

        $sortBy = isset(($this->getRoute()->getParams())['sort']) ? ($this->getRoute()->getParams())['sort'] : 'createdAt';
        $sortDir = ($this->getRoute()->getParams())['dir'] ?? 'DESC';

        $sqlParams = [];

        $posts = new PostManager(Application::getDatasource());
        $pages = [];

        $user = $this->session->getUser();
        $user = [
                 'name' => $user->getUsername(),
                 'id' => $user->getId(),
                 'roleName' => $user->getRoleName()
                ];

        $sortBySQL = Text::camelCaseToSnakeCase($sortBy);

        if (array_search('publish_state', $sqlParams) && $sqlParams['publish_state']) {
            $count = count($posts->getAllPublish());
        } else {
            $count = count($posts->getAll());
        }//enf id

        $pagination = new Pagination($this->getRoute(), $count, $currentPage, $perPage);
        $pages = $pagination->pagesInformations();
        $comments = (new CommentManager(Application::getDatasource()));
        if ($user['roleName'] !== "admin") {
            $sqlParams = ['user_id' => $user['id']];
        }//end if

        $statementComments = $comments->getAllOrderLimit($sortBySQL, $sortDir, $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams);
        foreach ($statementComments as $statementComment) {
            $statementComment->username = current($comments->getCommentUsername($statementComment->getUserId()));
        }

        $statementPosts = $posts->getAll();
        foreach ($statementPosts as $statementPost) {
            $statementPost->categories = $posts->getCategoriesById($statementPost->id);
            $statementPost->countComments = $posts->getCountCommentsByPostId($statementPost->id);
            $statementPost->username =  current($posts->getPostUsername($statementPost->getUserId()));
        }
        $this->view(
            'backoffice/admin.comments.html.twig',
            [
            'comments' => $statementComments,
            'posts' => $statementPosts,
            'sort' => $filter->getSort(),
            'dir' => $filter->getDir(),
            'sortDir' => $sortDir,
            'sortBy' => $sortBy,
            'list' => $filter->getList() ,
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
        $statement->username = current($comments->getCommentUsername($statement->getUserId()));

        $user = $this->session->getUser();
        $user = [
                 'name' => $user->getUsername(),
                 'id' => $user->getId(),
                 'roleName' => $user->getRoleName()
                ];

        $this->view('backoffice/modify.comment.html.twig', ['comment' => $statement, 'authUser' => $user]);
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

        // dd($_POST, $statement);
        if ($this->getRoute()->getParams()['content'] !== $statement->getContent()) {

            $params['content'] = $this->getRoute()->getParams()['content'];
        }
        if (null !== $params) {

            $params['modifiedAt'] = (new \DateTime('now'))->format('Y-m-d H:i:s');
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
        $statement->username = current($comments->getCommentUsername($statement->getUserId()));

        $this->view('backoffice/modify.comment.html.twig', ['comment' => $statement, 'authUser' => $user]);
    }


    /**
     * moderationComments; prepare view to moderate comments
     *
     * @return void
     */
    public function moderationComments()
    {
        $filter = new FilterBuilder(Application::getFilter(), 'admin.'.substr(strtolower($this->getRoute()->getcontroller()), strrpos($this->getRoute()->getcontroller(), "\\") + 1));
        $currentPage = null;
        $perPage = null;

        if (isset(($this->getRoute()->getParams())['page'])) {
            $currentPage = ($this->getRoute()->getParams())['page'];
        }

        if (isset(($this->getRoute()->getParams())['perPage'])) {
            $perPage = ($this->getRoute()->getParams())['perPage'];
        }

        $sortBy = isset(($this->getRoute()->getParams())['sort']) ? ($this->getRoute()->getParams())['sort'] : 'createdAt';
        $sortDir = ($this->getRoute()->getParams())['dir'] ?? 'DESC';

        $sqlParams = [];

        $posts = new PostManager(Application::getDatasource());
        $pages = [];

        $user = $this->session->getUser();
        $user = [
                    'name' => $user->getUsername(),
                    'id' => $user->getId(),
                    'roleName' => $user->getRoleName()
                ];

        $sortBySQL = Text::camelCaseToSnakeCase($sortBy);

        if (array_search('publish_state', $sqlParams) && $sqlParams['publish_state']) {
            $count = count($posts->getAllPublish());
        } else {
            $count = count($posts->getAll());
        }//enf id

        $pagination = new Pagination($this->getRoute(), $count, $currentPage, $perPage);
        $pages = $pagination->pagesInformations();

        $comments = (new CommentManager(Application::getDatasource()));

        $statementComments = $comments->getAllOrderLimit($sortBySQL, $sortDir, $pagination->getperPage(), $pagination->getcurrentPage(), $sqlParams);
        foreach ($statementComments as $statementComment) {
            $statementComment->username = current($comments->getCommentUsername($statementComment->getUserId()));
        }


        $statementPosts = $posts->getAll();
        foreach ($statementPosts as $statementPost) {
            $statementPost->categories = $posts->getCategoriesById($statementPost->id);
            $statementPost->countComments = $posts->getCountCommentsByPostId($statementPost->id);
            $statementPost->username =  current($posts->getPostUsername($statementPost->getUserId()));
        }

        $this->view(
            'backoffice/admin.moderation.comments.html.twig',
            [
            'comments' => $statementComments,
            'posts' => $statementPosts,
            'sort' => $filter->getSort(),
            'dir' => $filter->getDir(),
            'sortDir' => $sortDir,
            'sortBy' => $sortBy,
            'list' => $filter->getList() ,
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
        header('Location: /blog-project/admin/moderation/comments');
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
        header('Location: /blog-project/admin/moderation/comments');
    }


}
