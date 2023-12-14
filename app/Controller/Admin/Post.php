<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Pagination;
use App\Model\Manager\CategoryManager;
use App\Model\Manager\CommentManager;
use App\Model\Manager\PostManager;
use Framework\BaseController;
use Framework\Config;
use Framework\Helpers\FilterBuilder;
use Framework\Helpers\Text;
use Framework\HttpParams;
use Framework\Security\AuthUser;
use Safe\DateTime;
use Webmozart\Assert\Assert;

class Post extends BaseController
{
    /**
     * posts: Show page with all published posts
     */
    public function posts(): void
    {
        $user = $this->session->getUser();
        Assert::isInstanceOf($user, AuthUser::class);

        $filter = new FilterBuilder(substr(strtolower($this->getRoute()->getcontroller()), strrpos($this->getRoute()->getcontroller(), '\\') + 1));
        $httpParams = $this->groupFilterDataUser();
        $sqlParams = [];
        $posts = PostManager::getPostInstance(Config::getDatasource());
        $pages = [];
        $sortBySQL = Text::camelCaseToSnakeCase((string) $httpParams['sort']);

        $count = 1;
        if ('admin' === $user->getRoleName()) {
            if (false !== $posts->getAllByParams([])) {
                $count = \count($posts->getAllByParams([]));
            }
        } else {
            $sqlParams = ['user_id' => $user->getId()];
            if (false !== $posts->getAllByParams($sqlParams)) {
                $count = \count($posts->getAllByParams($sqlParams));
            }
        }// end if

        if (null !== $httpParams['list']) {
            $count = \count($posts->getAllFilteredCat($sqlParams, (int) $httpParams['listSelect']));
        }// end if

        $pagination = new Pagination($this->getRoute(), $count);
        $pages = $pagination->pagesInformations();

        if (null === $httpParams['listSelect']) {
            $statementPosts = $posts->getAllOrderLimit($sortBySQL, (string) $httpParams['dir'], $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams);
        } else {
            $statementPosts = $posts->getAllOrderLimitCat($sortBySQL, (string) $httpParams['dir'], $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams, (int) $httpParams['listSelect']);
        }
        foreach ($statementPosts as $statementPost) {
            $statementPost->setCategories($posts->getCategoriesById($statementPost->getId()));
            $statementPost->setCountComments($posts->getCountCommentsByPostId($statementPost->getId()));
            $statementPost->setUsername($posts->getPostUsername($statementPost->getUserId()));
        }
        $this->session->generateToken();
        Assert::notNull($this->session->getToken());
        $user->setToken($this->session->getToken());

        $dataView = [
            'baseUrl'      => Config::getBaseUrl(),
            'posts'        => $statementPosts,
            'sort'         => $filter->getSort(),
            'dir'          => $filter->getDir(),
            'sortDir'      => $httpParams['dir'],
            'sortBy'       => $httpParams['sort'],
            'listSort'     => $httpParams['list'],
            'list'         => $filter->getList() ,
            'idListSelect' => $httpParams['listSelect'],
            'listSelect'   => $filter->getListSelect(),
            'listNames'    => $filter->getListNames(),
            'pages'        => $pages,
            'authUser'     => $user,
        ];
        $params = (new HttpParams())->getParamsGet();
        if (isset($params['delete']) && 'ok' === $params['delete']) {
            $dataView['message'] = '<strong>Suppression réussie</strong><br>
                l\'article a été supprimé.';
            $dataView['error'] = false;
        }

        $this->view('backoffice/admin.posts.html.twig', $dataView);
    }

    /**
     * deletePost
     */
    public function deletePost(int $id): void
    {
        PostManager::getPostInstance(Config::getDatasource())->delete($id);
        header('Location: '.Config::getBaseUrl().'/admin/posts?delete=ok');
    }

    /**
     * addPost
     */
    public function addPost(): void
    {
        $category = CategoryManager::getCategoryInstance(Config::getDatasource());
        $statementCategories = $category->getAllByParams([]);
        $user = $this->session->getUser();
        Assert::isInstanceOf($user, AuthUser::class);
        $this->session->generateToken();
        Assert::notNull($this->session->getToken());
        $user->setToken($this->session->getToken());
        $this->view('backoffice/add.post.html.twig', ['baseUrl' => Config::getBaseUrl(), 'categories' => $statementCategories, 'authUser' => $user]);
    }

    /**
     * addedPost
     */
    public function addedPost()
    {
        $post = PostManager::getPostInstance(Config::getDatasource());
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
        $newId = $post->insertNewPost($dataPost);
        $user = $this->session->getUser();
        Assert::isInstanceOf($user, AuthUser::class);
        $this->session->generateToken();
        Assert::notNull($this->session->getToken());
        $user->setToken($this->session->getToken());
        $statementPost = $post->getById($newId);
        $statementPost->setUsername($post->getPostUsername($statementPost->getUserId()));
        $statementPost->setCategories($post->getCategoriesById($statementPost->getId()));
        $this->view('backoffice/modify.post.html.twig', ['baseUrl' => Config::getBaseUrl(), 'post' => $statementPost, 'authUser' => $user]);
    }

    /**
     * modifyPost
     */
    public function modifyPost(int $id)
    {
        $post = PostManager::getPostInstance(Config::getDatasource());
        $statementPost = $post->getById($id);
        $statementPost->setUsername($post->getPostUsername($statementPost->getUserId()));
        $statementPost->setCategories($post->getCategoriesById($statementPost->getId()));
        $user = $this->session->getUser();
        Assert::isInstanceOf($user, AuthUser::class);
        $this->session->generateToken();
        Assert::notNull($this->session->getToken());
        $user->setToken($this->session->getToken());
        $this->view('backoffice/modify.post.html.twig', ['baseUrl' => Config::getBaseUrl(), 'post' => $statementPost , 'authUser' => $user]);
    }

    /**
     * modifiedPost
     */
    public function modifiedPost(int $id)
    {
        $post = PostManager::getPostInstance(Config::getDatasource());
        $params = [];
        $statement = $post->getById($id);
        $postDatas = (new HttpParams())->getParamsPost();
        Assert::isArray($postDatas);
        Assert::keyExists($postDatas, 'content');
        if ($postDatas['content'] !== $statement->getContent()) {
            Assert::stringNotEmpty($postDatas['content']);
            $params['content'] = (string) $postDatas['content'];
        }

        if ($postDatas['name'] !== $statement->getName()) {
            Assert::stringNotEmpty($postDatas['name']);
            $params['name'] = (string) $postDatas['name'];
        }
        if (null !== $params) {
            $params['modifiedAt'] = (new DateTime('now'))->format('Y-m-d H:i:s');
            $params['publishState'] = 0;

            $post->update($statement, $params);
        }

        $user = $this->session->getUser();
        Assert::isInstanceOf($user, AuthUser::class);
        $this->session->generateToken();
        Assert::notNull($this->session->getToken());
        $user->setToken($this->session->getToken());
        $post = PostManager::getPostInstance(Config::getDatasource());
        $statementPost = $post->getById($id);
        $statementPost->setUsername($post->getPostUsername($statementPost->getUserId()));
        $statementPost->setCategories($post->getCategoriesById($statementPost->getId()));

        $this->view('backoffice/modify.post.html.twig', ['baseUrl' => Config::getBaseUrl(), 'post' => $statementPost, 'authUser' => $user]);
    }

    /**
     * addComment
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

        $user = $this->session->getUser();
        $this->session->generateToken();
        Assert::isInstanceOf($user, AuthUser::class);
        Assert::notNull($this->session->getToken());
        $user->setToken($this->session->getToken());
        $this->view('backoffice/add.comment.html.twig', ['baseUrl' => Config::getBaseUrl(), 'post' => $statementPost, 'authUser' => $user, 'comments' => $statementComments]);
    }

    /**
     * addedComment
     */
    public function addedComment(int $id): void
    {
        $comment = CommentManager::getCommentInstance(Config::getDatasource());
        $commentData = (new HttpParams())->getParamsPost();
        $dataComment = [];
        Assert::isArray($commentData);
        foreach ($commentData as $key => $data) {
            Assert::notEmpty($data);
            Assert::string($key);
            Assert::notNull($data);
            Assert::string($data);
            $dataComment[$key] = $data;
        }
        $comment->insertNewComment($dataComment);
        // Message de prise en compte et de validation du commentaire par l'administrateur

        $post = PostManager::getPostInstance(Config::getDatasource());
        $statementPost = $post->getById($id);
        $slug = $statementPost->getSlug();
        header('Location: '.Config::getBaseUrl().'/post/'.$slug.'/'.$id);
    }

    /**
     * moderationPosts
     */
    public function moderationPosts(): void
    {
        $user = $this->session->getUser();
        Assert::isInstanceOf($user, AuthUser::class);
        $this->session->generateToken();
        Assert::notNull($this->session->getToken());
        $user->setToken($this->session->getToken());
        $filter = new FilterBuilder('admin.'.substr(strtolower($this->getRoute()->getcontroller()), strrpos($this->getRoute()->getcontroller(), '\\') + 1));

        $httpParams = $this->groupFilterDataUser();

        $sqlParams = [];

        $posts = PostManager::getPostInstance(Config::getDatasource());
        $pages = [];
        $sortBySQL = Text::camelCaseToSnakeCase((string) $httpParams['sort']);
        $count = 1;
        if (false !== $posts->getAllByParams([])) {
            $count = \count($posts->getAllByParams([]));
        }
        if (null !== $httpParams['list']) {
            $count = \count($posts->getAllFilteredCat($sqlParams, (int) $httpParams['listSelect']));
        }// end if

        $pagination = new Pagination($this->getRoute(), $count);
        $pages = $pagination->pagesInformations();

        if (null === $httpParams['listSelect']) {
            $statementPosts = $posts->getAllOrderLimit($sortBySQL, (string) $httpParams['dir'], $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams);
        } else {
            $statementPosts = $posts->getAllOrderLimitCat($sortBySQL, (string) $httpParams['dir'], $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams, (int) $httpParams['listSelect']);
        }

        foreach ($statementPosts as $statementPost) {
            $statementPost->setCategories($posts->getCategoriesById($statementPost->getId()));
            $statementPost->setCountComments($posts->getCountCommentsByPostId($statementPost->getId()));
            $statementPost->setUsername($posts->getPostUsername($statementPost->getUserId()));
        }

        $this->view(
            'backoffice/admin.moderation.posts.html.twig',
            [
                'baseUrl'      => Config::getBaseUrl(),
                'posts'        => $statementPosts,
                'sort'         => $filter->getSort(),
                'dir'          => $filter->getDir(),
                'sortDir'      => $httpParams['dir'],
                'sortBy'       => $httpParams['sort'],
                'listSort'     => $httpParams['list'],
                'list'         => $filter->getList() ,
                'idListSelect' => $httpParams['listSelect'],
                'listSelect'   => $filter->getListSelect(),
                'listNames'    => $filter->getListNames(),
                'pages'        => $pages,
                'authUser'     => $user,
            ]
        );
    }

    /**
     * unpublishPost
     *
     * @params array<string,int|string> page filter option
     */
    public function unpublishPost(int $id): void
    {
        $filterParams = (new HttpParams())->getParamsReferer();
        $filterParams = isset($filterParams) ? '?'.$filterParams : null;
        PostManager::getPostInstance(Config::getDatasource())->unpublish($id);
        header('Location: '.Config::getBaseUrl().'/admin/moderation/posts'.$filterParams.'#'.$id);
    }

    /**
     * publishPost
     */
    public function publishPost(int $id): void
    {
        $filterParams = (new HttpParams())->getParamsReferer();
        $filterParams = isset($filterParams) ? '?'.$filterParams : null;
        PostManager::getPostInstance(Config::getDatasource())->publish($id);
        header('Location: '.Config::getBaseUrl().'/admin/moderation/posts'.$filterParams.'#'.$id);
    }
}
