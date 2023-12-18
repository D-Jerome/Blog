<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Manager\CategoryManager;
use App\Model\Manager\CommentManager;
use App\Model\Manager\PostManager;
use Framework\BaseController;
use Framework\Config;
use Framework\Helpers\FilterBuilder;
use Framework\ParamsGetFilter;
use Webmozart\Assert\Assert;

class Post extends BaseController
{
    /**
     * postsByCategory : 3 most recent Posts by category
     */
    public function postsByCategory(): void
    {
        $user = $this->session->getUser();
        if (!$user instanceof \Framework\Security\AuthUser) {
            $user = null;
        }
        // recherche des 3 derniers articles par catégories
        $categories = CategoryManager::getCategoryInstance(Config::getDatasource());
        $statementCategories = $categories->getAllByParams([]);
        $posts = PostManager::getPostInstance(Config::getDatasource());
        $postsByCategories = null;
        Assert::notFalse($statementCategories);
        foreach ($statementCategories as $statementCategory) {
            $statementPostsByCategory = $posts->getPostsbyCategory($statementCategory);
            foreach ($statementPostsByCategory as $statementPost) {
                $statementPost->setCategories([$statementCategory]);
                $statementPost->setCountComments($posts->getCountCommentsByPostId($statementPost->getId()));
                $statementPost->setUsername($posts->getPostUsername($statementPost->getUserId()));
            }
            $postsByCategories = array_merge((array) $statementPostsByCategory, (array) $postsByCategories);
        }

        $this->view('frontoffice/posts.category.html.twig', ['baseUrl' => Config::getBaseUrl(), 'categories' => $statementCategories, 'posts' => $postsByCategories,  'authUser' => $user]);
    }

    /**
     * posts : recovers all informations for each publish article for display with paging
     */
    public function posts(): void
    {
        $user = $this->session->getUser();
        if (!$user instanceof \Framework\Security\AuthUser) {
            $user = null;
        }
        $filter = new FilterBuilder(substr(strtolower($this->getRoute()->getcontroller()), strrpos($this->getRoute()->getcontroller(), '\\') + 1));
        $httpParams = new ParamsGetFilter();
        $sqlParams = ['publish_state' => true];
        $posts = PostManager::getPostInstance(Config::getDatasource());
        $pages = [];

        if (null === $httpParams->getList()) {
            $count = \count($posts->getAllPublish());
        } else {
            $count = \count($posts->getAllFilteredByParam((string) $httpParams->getList(), (int) $httpParams->getListSelect(), true));
        }

        $pagination = new Pagination($this->getRoute(), $count);
        $pages = $pagination->pagesInformations();

        $statementPosts = $posts->getAllOrderLimitCat($httpParams, $pagination->getPerPage(), $pagination->getCurrentPage(), $sqlParams);

        foreach ($statementPosts as $statementPost) {
            $statementPost->setCategories($posts->getCategoriesById($statementPost->getId()));
            $statementPost->setCountComments($posts->getCountCommentsByPostId($statementPost->getId()));
            $statementPost->setUsername($posts->getPostUsername($statementPost->getUserId()));
        }

        $this->view(
            'frontoffice/posts.html.twig',
            [
                'baseUrl'      => Config::getBaseUrl(),
                'posts'        => $statementPosts,
                'filter'       => $filter,
                'httpFilter'   => $httpParams,
                'pages'        => $pages,
                'authUser'     => $user,
            ]
        );
    }

    /**
     * post : recovers article's informations (in @param) for display
     */
    public function post(int $id): void
    {
        $post = PostManager::getPostInstance(Config::getDatasource());
        $comment = CommentManager::getCommentInstance(Config::getDatasource());
        $statementPost = $post->getById($id);
        $statementComments = $comment->getCommentsByPostId($id);
        $statementPost->setUsername($post->getPostUsername($statementPost->getUserId()));
        $statementPost->setCategories($post->getCategoriesById($statementPost->getId()));
        foreach ($statementComments as $statementComment) {
            $statementComment->setUsername((string) $comment->getCommentUsername($statementComment->getUserId()));
        }
        $user = $this->session->getUser();
        if (!$user instanceof \Framework\Security\AuthUser) {
            $user = null;
        }
        $this->view('frontoffice/post.html.twig', ['baseUrl' => Config::getBaseUrl(), 'post' => $statementPost, 'authUser' => $user, 'comments' => $statementComments]);
    }
}
