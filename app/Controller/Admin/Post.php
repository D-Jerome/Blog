<?php

namespace App\Controller\Admin;

use App\Model\Manager\CategoryManager;
use App\Model\Manager\{PostManager, CommentManager};
use Framework\Application;
use Framework\BaseController;
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

        $posts = (new PostManager(Application::getDatasource()));
        $statementPosts = $posts->getAll();
        foreach ($statementPosts as $statementPost) {
            $statementPost->username = current($posts->getPostUsername($statementPost->getUserId()));
        }
        $user = $this->session->getUser();
            $user = [
                     'name' => $user->getUsername(),
                     'id' => $user->getId(),
                     'roleName' => $user->getRoleName()
                    ];
        $this->view('backoffice/admin.posts.html.twig', ['posts' => $statementPosts, 'authUser' => $user]);
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
        $params=[];
        $statement = $post->getById($id);

        // dd($_POST, $statement);
        if ($this->getRoute()->getParams()['content'] !== $statement->getContent()) {   
            $params['content']= $this->getRoute()->getParams()['content'];
        }
        // dd($_POST['name'], $statement->getName());
        if ($this->getRoute()->getParams()['name'] !== $statement->getName()) {
            $params['name']= $this->getRoute()->getParams()['name'];
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
        $posts = new PostManager(Application::getDatasource());

        $statementPosts = $posts->getAll();
        foreach ($statementPosts as $statementPost) {
            $statementPost->username = current($posts->getPostUsername($statementPost->getUserId()));
        }
        $user = $this->session->getUser();
            $user = [
                     'name' => $user->getUsername(),
                     'id' => $user->getId(),
                     'roleName' => $user->getRoleName()
                    ];


        $this->view('backoffice/admin.moderation.posts.html.twig', ['posts' => $statementPosts, 'authUser' => $user]);
    }

    
        
    /**
     * moderatePost
     *
     * @param  int $id
     * @return void
     */
    public function moderatePost(int $id): void
    {
        $post = new PostManager(Application::getDatasource());

        $statement = $post->getById($id);
        $user = $this->session->getUser();
            $user = [
                     'name' => $user->getUsername(),
                     'id' => $user->getId(),
                     'roleName' => $user->getRoleName()
                    ];


        $this->view('backoffice/modify.post.html.twig', ['post' => $statement, 'authUser' => $user]);
    }

        
    /**
     * moderatedPost
     *
     * @param  int $id
     * @return void
     */
    public function moderatedPost(int $id): void
    {
        $post = new PostManager(Application::getDatasource());
        $params=[];
        $statement = $post->getById($id);

        // dd($_POST, $statement);
        if ($this->getRoute()->getParams()['content'] !== $statement->getContent()) {   
            $params['content']= $this->getRoute()->getParams()['content'];
        }
        // dd($_POST['name'], $statement->getName());
        if ($this->getRoute()->getParams()['name'] !== $statement->getName()) {
            $params['name']= $this->getRoute()->getParams()['name'];
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
