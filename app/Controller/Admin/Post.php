<?php

namespace App\Controller\Admin;

use App\Model\Manager\CategoryManager;
use App\Model\Manager\{PostManager, CommentManager};
use Framework\Application;
use Framework\BaseController;
use Framework\Helpers\{Text, FilterBuilder};
use Framework\Request;
use Framework\Session;

class Post extends BaseController
{
    /**
     * posts: Show page with all published posts
     *
     * @return void
     */
    public function posts(): void
    {
        $filter = new FilterBuilder(Application::getFilter(), substr(strtolower($this->getRoute()->getcontroller()), strrpos($this->getRoute()->getcontroller(), "\\") + 1));
        $sortList = $filter->getSort();
        $dirList = $filter->getDir();
        $list = $filter->getList();
        $listNames = $filter->getListNames();


        $sortBy = isset(($this->getRoute()->getParams())['sort']) ? ($this->getRoute()->getParams())['sort'] : 'createdAt';
        $sortDir = ($this->getRoute()->getParams())['dir'] ?? 'DESC';
        $perPage = ($this->getRoute()->getParams())['perPage'] ?? 8;

        $currentPage = ($this->getRoute()->getParams())['page'] ?? 1;
        $currentPage = (int)$currentPage;
        $sqlParams = [];
        $posts = new PostManager(Application::getDatasource());
        $pages = [];
        $sortBySQL = Text::camelCaseToSnakeCase($sortBy);
        $statementPosts = $posts->getAllOrderLimit($sortBySQL, $sortDir, $perPage, $currentPage, $sqlParams);
        foreach ($statementPosts as $statementPost) {
            $statementPost->categories = $posts->getCategoriesById($statementPost->id);
            $statementPost->countComments = $posts->getCountCommentsByPostId($statementPost->id);
            $statementPost->username =  current($posts->getPostUsername($statementPost->getUserId()));
        }
        if (array_search('publish_state', $sqlParams) && $sqlParams['publish_state']) {
            $count = count($posts->getAllPublish());
        } else {
            $count = count($posts->getAll());
        }//enf id

        if ((int)(ceil(($count / $perPage))) === 1) {
            $pages['nextActive'] = false;
            $pages['previousActive'] = false;
        } elseif ($currentPage >= (ceil(($count / $perPage)))) {
            $pages['previousActive'] = true;
            $pages['nextActive'] = false;
        } elseif ($currentPage === 1) {
            $pages['previousActive'] = false;
            $pages['nextActive'] = true;
        } else {
            $pages['nextActive'] = true;
            $pages['previousActive'] = true;
        }//end if

        //pagination
        $temp = ($this->getRoute()->getParams());
        unset($temp['page']);
        $this->getRoute()->setParams($temp);
        $query = http_build_query($this->getRoute()->getParams());
        if (!empty($query)) {
            $query = "&$query";
        }
        $pages['previousUri'] = Application::getBaseUrl(). $this->getRoute()->getPath() . '?page=' . ($currentPage - 1) . $query;
        $pages['nextUri'] = Application::getBaseUrl(). $this->getRoute()->getPath() . '?page=' . ($currentPage + 1) . $query;


        $user = $this->session->getUser();
        $user = [
                 'name' => $user->getUsername(),
                 'id' => $user->getId(),
                 'roleName' => $user->getRoleName()
                ];

        $this->view('backoffice/admin.posts.html.twig', [
            'posts' => $statementPosts,
            'sort' => $sortList,
            'dir' => $dirList,
            'sortDir' => $sortDir,
            'sortBy' => $sortBy,
            'list' => $list ,
            'listNames' => $listNames,
            'pages' => $pages,
            'authUser' => $user
        ]);
    }


    /**
     * deletePost
     *
     * @param  int $id
     * @return void
     */
    public function deletePost(int $id): void
    {
        (new PostManager(Application::getDatasource()))->delete($id);
        header('Location: /blog-project/admin');
    }


    /**
     * addPost
     *
     * @return void
     */
    public function addPost(): void
    {
        $category = new CategoryManager(Application::getDatasource());
        $statementCategories = $category->getAll();
        $user = $this->session->getUser();
        $user = [
                 'name' => $user->getUsername(),
                 'id' => $user->getId(),
                 'roleName' => $user->getRoleName()
                ];

        $this->view('backoffice/add.post.html.twig', ['categories' => $statementCategories, 'authUser' => $user]);
    }


    /**
     * addedPost
     *
     * @return void
     */
    public function addedPost()
    {
        $post = new PostManager(Application::getDatasource());
        $request = new Request("/blog-project/");

        $post->insertNewPost($request->getParams());
        $user = $this->session->getUser();
        $user = [
                 'name' => $user->getUsername(),
                 'id' => $user->getId(),
                 'roleName' => $user->getRoleName()
                ];


        $statement = '';
        $this->view('backoffice/modify.post.html.twig', ['post' => $statement, 'authUser' => $user]);
    }


    /**
     * modifyPost
     *
     * @param  int $id
     * @return void
     */
    public function modifyPost(int $id)
    {
        $post = new PostManager(Application::getDatasource());
        $statementPost = $post->getById($id);
        $statementPost->username =  current($post->getPostUsername($statementPost->getUserId()));
        $statementPost->categories = $post->getCategoriesById($statementPost->id);
        $user = $this->session->getUser();
        $user = [
                 'name' => $user->getUsername(),
                 'id' => $user->getId(),
                 'roleName' => $user->getRoleName()
                ];


        $this->view('backoffice/modify.post.html.twig', ['post' => $statementPost , 'authUser' => $user]);
    }

    /**
     * modifiedPost
     *
     * @param  int $id
     * @return void
     */
    public function modifiedPost(int $id)
    {
        $post = new PostManager(Application::getDatasource());
        $params = [];
        $statement = $post->getById($id);

        // dd($_POST, $statement);
        if ($this->getRoute()->getParams()['content'] !== $statement->getContent()) {
            $params['content'] = $this->getRoute()->getParams()['content'];
        }
        // dd($_POST['name'], $statement->getName());
        if ($this->getRoute()->getParams()['name'] !== $statement->getName()) {
            $params['name'] = $this->getRoute()->getParams()['name'];
        }
        if (null !== $params) {
            $params['modifiedAt'] = (new \DateTime('now'))->format('Y-m-d H:i:s');
            $params['publishState'] = 0;

            $post->update($statement, $params);
        }

        $user = $this->session->getUser();
        $user = [
                 'name' => $user->getUsername(),
                 'id' => $user->getId(),
                 'roleName' => $user->getRoleName()
                ];

        $post = new PostManager(Application::getDatasource());
        $statement = $post->getById($id);
        $this->view('backoffice/modify.post.html.twig', ['post' => $statement, 'authUser' => $user]);
    }


    /**
     * addComment
     *
     * @param  int $id
     * @return void
     */
    public function addComment(int $id): void
    {
        // $username=Session::getUsername();
        $post = new PostManager(Application::getDatasource());
        $comment = new CommentManager(Application::getDatasource());
        $statementPost = $post->getById($id);
        $statementComments = $comment->getCommentsByPostId($id);
        $statementPost->username =  current($post->getPostUsername($statementPost->getUserId()));
        foreach ($statementComments as $statementComment) {
            //dd($statementComment->getUserId());
            $statementComment->username = current($comment->getCommentUsername($statementComment->getUserId()));
        }
        $statementPost->categories = $post->getCategoriesById($statementPost->id);
        $statementPost->countComments = $post->getCountCommentsByPostId($statementPost->id);
        $statementPost->username =  current($post->getPostUsername($statementPost->getUserId()));

        $user = $this->session->getUser();
        $user = [
                 'name' => $user->getUsername(),
                 'id' => $user->getId(),
                 'roleName' => $user->getRoleName()
                ];

        $this->view('backoffice/add.comment.html.twig', ['post' => $statementPost, 'authUser' => $user, 'comments' => $statementComments]);
    }


    /**
     * addedComment
     *
     * @param  int $id
     * @return void
     */
    public function addedComment(int $id): void
    {

        $comment = new CommentManager(Application::getDatasource());
        $request = new Request("/blog-project/");

        $comment->insertNewComment($request->getParams());
        //Message de prise en compte et de validation du commentaire par l'administrateur

        $user = $this->session->getUser();
        $user = [
                 'name' => $user->getUsername(),
                 'id' => $user->getId(),
                 'roleName' => $user->getRoleName()
                ];

        $post = new PostManager(Application::getDatasource());
        $statementPost = $post->getById($id);
        $slug = $statementPost->getSlug();
        Header("Location: /blog-project/post/$slug/$id");
    }


    /**
     * moderationPosts
     *
     * @return void
     */
    public function moderationPosts(): void
    {

        $filter = new FilterBuilder(Application::getFilter(), substr(strtolower($this->getRoute()->getcontroller()), strrpos($this->getRoute()->getcontroller(), "\\") + 1));
        $sortList = $filter->getSort();
        $dirList = $filter->getDir();
        $list = $filter->getList();
        $listNames = $filter->getListNames();


        $sortBy = isset(($this->getRoute()->getParams())['sort']) ? ($this->getRoute()->getParams())['sort'] : 'createdAt';
        $sortDir = ($this->getRoute()->getParams())['dir'] ?? 'DESC';
        $perPage = ($this->getRoute()->getParams())['perPage'] ?? 8;

        $currentPage = ($this->getRoute()->getParams())['page'] ?? 1;
        $currentPage = (int)$currentPage;
        $sqlParams = [];
        $posts = new PostManager(Application::getDatasource());
        $pages = [];
        $sortBySQL = Text::camelCaseToSnakeCase($sortBy);
        $statementPosts = $posts->getAllOrderLimit($sortBySQL, $sortDir, $perPage, $currentPage, $sqlParams);
        foreach ($statementPosts as $statementPost) {
            $statementPost->categories = $posts->getCategoriesById($statementPost->id);
            $statementPost->countComments = $posts->getCountCommentsByPostId($statementPost->id);
            $statementPost->username =  current($posts->getPostUsername($statementPost->getUserId()));
        }
        if (array_search('publish_state', $sqlParams) && $sqlParams['publish_state']) {
            $count = count($posts->getAllPublish());
        } else {
            $count = count($posts->getAll());
        }//enf id

        if ((int)(ceil(($count / $perPage))) === 1) {
            $pages['nextActive'] = false;
            $pages['previousActive'] = false;
        } elseif ($currentPage >= (ceil(($count / $perPage)))) {
            $pages['previousActive'] = true;
            $pages['nextActive'] = false;
        } elseif ($currentPage === 1) {
            $pages['previousActive'] = false;
            $pages['nextActive'] = true;
        } else {
            $pages['nextActive'] = true;
            $pages['previousActive'] = true;
        }//end if

        //pagination
        $temp = ($this->getRoute()->getParams());
        unset($temp['page']);
        $this->getRoute()->setParams($temp);
        $query = http_build_query($this->getRoute()->getParams());
        if (!empty($query)) {
            $query = "&$query";
        }
        $pages['previousUri'] = Application::getBaseUrl(). $this->getRoute()->getPath() . '?page=' . ($currentPage - 1) . $query;
        $pages['nextUri'] = Application::getBaseUrl(). $this->getRoute()->getPath() . '?page=' . ($currentPage + 1) . $query;


        $user = $this->session->getUser();
        $user = [
                 'name' => $user->getUsername(),
                 'id' => $user->getId(),
                 'roleName' => $user->getRoleName()
                ];

        $this->view('backoffice/admin.moderation.posts.html.twig', [
            'posts' => $statementPosts,
            'sort' => $sortList,
            'dir' => $dirList,
            'sortDir' => $sortDir,
            'sortBy' => $sortBy,
            'list' => $list ,
            'listNames' => $listNames,
            'pages' => $pages,
            'authUser' => $user
        ]);

    }


    /**
     * unpublishPost
     *
     * @param  int $id
     * @return void
     */
    public function unpublishPost(int $id): void
    {

        (new PostManager(Application::getDatasource()))->unpublish($id);
        header('Location: /blog-project/admin/moderation/posts');
    }


    /**
     * publishPost
     *
     * @param  int $id
     * @return void
     */
    public function publishPost(int $id): void
    {

        (new PostManager(Application::getDatasource()))->publish($id);
        header('Location: /blog-project/admin/moderation/posts');
    }


}
