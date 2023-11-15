<?php

namespace App\Controller\Admin;

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
        if (array_search('publish_state',$sqlParams) && $sqlParams['publish_state']) {
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

        $comments = (new CommentManager(Application::getDatasource()));
        if ($user['roleName'] === "admin") {
            $statementComments = $comments->getAll();
        } else {
            $statementComments = $comments->getCommentsByUserId($user['id']);
        }//end if
        foreach ($statementComments as $statementComment) {
            $statementComment->username = current($comments->getCommentUsername($statementComment->getUserId()));
        }

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
        
        
        
        
        
        
        
        
        
        
        
        
        
        

        $this->view('backoffice/admin.comments.html.twig', ['comments' => $statementComments, 'posts' => $statementPosts, 'authUser' => $user]);
    
    
    
    
    
    
    
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
        $comments = new CommentManager(Application::getDatasource());

        $statementComments = $comments->getAll();
        foreach ($statementComments as $statementComment) {
            $statementComment->username = current($comments->getCommentUsername($statementComment->getUserId()));
        }
        $user = $this->session->getUser();
        $user = [
                 'name' => $user->getUsername(),
                 'id' => $user->getId(),
                 'roleName' => $user->getRoleName()
                ];

        $this->view('backoffice/admin.moderation.comments.html.twig', ['comments' => $statementComments, 'authUser' => $user]);
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
