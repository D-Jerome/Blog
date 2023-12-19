<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Pagination;
use App\Model\Manager\CommentManager;
use App\Model\Manager\PostManager;
use Framework\BaseController;
use Framework\Config;
use Framework\Helpers\FilterBuilder;
use Framework\HttpParams;
use Framework\ParamsGetFilter;
use Framework\Security\AuthUser;
use Safe\DateTime;
use Webmozart\Assert\Assert;

class Comment extends BaseController
{
    /**
     * comments: show comments of user
     *      or show all comments for admin
     * return void
     */
    public function comments(): void
    {
        $user = $this->session->getUser();
        Assert::isInstanceOf($user, AuthUser::class);
        $filter = new FilterBuilder('admin.'.substr(strtolower($this->getRoute()->getcontroller()), strrpos($this->getRoute()->getcontroller(), '\\') + 1));
        $httpParams = new ParamsGetFilter();
        $sqlParams = [];
        $comments = CommentManager::getCommentInstance(Config::getDatasource());
        $posts = PostManager::getPostInstance(Config::getDatasource());
        $pages = [];

        $count = 1;

        if ('admin' === $user->getRoleName()) {
            if (false !== $comments->getAllByParams([])) {
                $count = \count($comments->getAllByParams([]));
            }
        } else {
            $sqlParams = ['user_id' => $user->getId()];
            if (false !== $comments->getAllByParams($sqlParams)) {
                $count = \count($comments->getAllByParams($sqlParams));
            }
        }// end if

        $pagination = new Pagination($this->getRoute(), $count);
        $pages = $pagination->pagesInformations();

        $statementComments = $comments->getAllOrderLimit($httpParams, $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams);
        foreach ($statementComments as $statementComment) {
            $statementComment->setUsername($comments->getCommentUsername($statementComment->getUserId()));
        }

        $statementPosts = $posts->getAllByParams([]);
        Assert::isArray($statementPosts);
        Assert::notNull($statementPosts);
        foreach ($statementPosts as $statementPost) {
            $statementPost->setCategories($posts->getCategoriesById($statementPost->getId()));
            $statementPost->setCountComments($posts->getCountCommentsByPostId($statementPost->getId()));
            $statementPost->setUsername($posts->getPostUsername($statementPost->getUserId()));
        }
        $this->view(
            'backoffice/admin.comments.html.twig',
            [
                'baseUrl'      => Config::getBaseUrl(),
                'comments'     => $statementComments,
                'posts'        => $statementPosts,
                'filter'       => $filter,
                'httpFilter'   => $httpParams,
                'pages'        => $pages,
                'authUser'     => $user,
            ]
        );
    }

    /**
     * modifyComment
     */
    public function modifyComment(int $id): void
    {
        $comments = CommentManager::getCommentInstance(Config::getDatasource());
        $statement = $comments->getById($id);
        $statement->setUsername($comments->getCommentUsername($statement->getUserId()));

        $user = $this->session->getUser();
        Assert::isInstanceOf($user, AuthUser::class);
        $this->session->generateToken();
        Assert::notNull($this->session->getToken());
        $user->setToken($this->session->getToken());
        $this->view('backoffice/modify.comment.html.twig', ['baseUrl' => Config::getBaseUrl(), 'comment' => $statement, 'authUser' => $user]);
    }

    /**
     * modifiedComment: action after modification of comment
     */
    public function modifiedComment(int $id): void
    {
        $comments = CommentManager::getCommentInstance(Config::getDatasource());
        $params = [];
        $statement = $comments->getById($id);
        $postData = (new HttpParams())->getParamsPost();
        $dataPost = [];
        Assert::isArray($postData);
        foreach ($postData as $key => $data) {
            Assert::notEmpty($data);
            Assert::string($key);
            Assert::notNull($data);
            if (true === \is_string($data) && 'content' !== $key) {
                $dataPost[$key] = htmlentities($data);
            } else {
                $dataPost[$key] = $data;
            }
        }
        if ($dataPost['content'] !== $statement->getContent()) {
            $params['content'] = $dataPost['content'];
        }
        if (null !== $params) {
            $params['modifiedAt'] = (new DateTime('now'))->format('Y-m-d H:i:s');
            $params['publishState'] = 0;
            $comments->update($statement, $params);
        }

        $user = $this->session->getUser();
        Assert::isInstanceOf($user, AuthUser::class);
        $this->session->generateToken();
        Assert::notNull($this->session->getToken());
        $user->setToken($this->session->getToken());
        $comments = CommentManager::getCommentInstance(Config::getDatasource());
        $statement = $comments->getById($id);
        $statement->setUsername($comments->getCommentUsername($statement->getUserId()));

        $this->view('backoffice/modify.comment.html.twig', ['baseUrl' => Config::getBaseUrl(), 'comment' => $statement, 'authUser' => $user]);
    }

    /**
     * moderationComments; prepare view to moderate comments
     * return void
     */
    public function moderationComments(): void
    {
        $user = $this->session->getUser();
        Assert::isInstanceOf($user, AuthUser::class);
        $this->session->generateToken();
        Assert::notNull($this->session->getToken());
        $user->setToken($this->session->getToken());

        $filter = new FilterBuilder('admin.'.substr(strtolower($this->getRoute()->getcontroller()), strrpos($this->getRoute()->getcontroller(), '\\') + 1));

        $httpParams = new ParamsGetFilter();

        $sqlParams = [];

        $posts = PostManager::getPostInstance(Config::getDatasource());
        $pages = [];

        $count = 1;
        if (false !== $posts->getAllByParams([])) {
            $count = \count($posts->getAllByParams([]));
        }
        $pagination = new Pagination($this->getRoute(), $count);
        $pages = $pagination->pagesInformations();

        $comments = CommentManager::getCommentInstance(Config::getDatasource());

        $statementComments = $comments->getAllOrderLimit($httpParams, $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams);

        foreach ($statementComments as $statementComment) {
            $statementComment->setUsername($comments->getCommentUsername($statementComment->getUserId()));
        }

        $statementPosts = $posts->getAllByParams([]);
        Assert::isArray($statementPosts);
        foreach ($statementPosts as $statementPost) {
            $statementPost->setCategories($posts->getCategoriesById($statementPost->getId()));
            $statementPost->setCountComments($posts->getCountCommentsByPostId($statementPost->getId()));
            $statementPost->setusername($posts->getPostUsername($statementPost->getUserId()));
        }

        $this->view(
            'backoffice/admin.moderation.comments.html.twig',
            [
                'baseUrl'      => Config::getBaseUrl(),
                'comments'     => $statementComments,
                'posts'        => $statementPosts,
                'filter'       => $filter,
                'httpFilter'   => $httpParams,
                'pages'        => $pages,
                'authUser'     => $user,
            ]
        );
    }

    /**
     * unpublishComment: action of unpublish comment
     */
    public function unpublishComment(int $id): void
    {
        $filterParams = (new HttpParams())->getParamsReferer();
        $filterParams = isset($filterParams) ? '?'.$filterParams : null;
        CommentManager::getCommentInstance(Config::getDatasource())->unpublish($id);
        header('Location: '.Config::getBaseUrl().'/admin/moderation/comments'.$filterParams.'#'.$id);
    }

    /**
     * publishComment: action of publish comment
     */
    public function publishComment(int $id): void
    {
        $filterParams = (new HttpParams())->getParamsReferer();
        $filterParams = isset($filterParams) ? '?'.$filterParams : null;
        CommentManager::getCommentInstance(Config::getDatasource())->publish($id);
        header('Location: '.Config::getBaseUrl().'/admin/moderation/comments'.$filterParams.'#'.$id);
    }
}
