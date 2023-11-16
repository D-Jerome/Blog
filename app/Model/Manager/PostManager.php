<?php

namespace App\Model\Manager;

use App\Model\Entities\Category;
use App\Model\Entities\Post;
use DateTime;
use Framework\Application;
use Framework\Helpers\Text;
use Framework\PDOConnection;
use Framework\Request;
use PDO;

class PostManager extends BaseManager
{

    /**
     * __construct
     *
     * @param array<string, string> $datasource Database connection informations from config file
     * @return void
     */
    public function __construct(array $datasource)
    {
        parent::__construct('post', Post::class, $datasource);
    }


    /**
     * getCategoriesById : all Categories of post
     *
     * @param int $id Id of post
     * @return array<string, string>
     */
    public function getCategoriesById(int $id): array
    {
        $statement = $this->dbConnect->prepare(
            '
            SELECT c.* FROM category c
            INNER JOIN post_category pc ON pc.category_id = c.id
            INNER JOIN post p ON pc.post_id = p.id
            WHERE p.id = ?
            '
        );
        $statement->setFetchMode(PDO::FETCH_CLASS, Category::class);
        $statement->execute([$id]);
        return $statement->fetchAll();
    }


    /**
     * getPostsbyCategory : get all posts linked to Category selected
     *
     * @param Category $category Category object
     * @return array<string, string>
     */
    public function getPostsbyCategory(Category $category): array
    {
        $query = $this->dbConnect->prepare(
            '
            SELECT p.* FROM post p
            INNER JOIN post_category pc ON pc.post_id = p.id
            WHERE pc.category_id = ? and p.publish_state = true
            ORDER BY p.created_at DESC
            LIMIT 3
            '
        );
        $query->setFetchMode(PDO::FETCH_CLASS, $this->object);
        $query->execute([$category->getId()]);
        $statementByCategories = $query->fetchAll();
        foreach ($statementByCategories as $statementByCategory) {

            $statementByCategory->categories =  $this->getCategoriesById($statementByCategory->id);
            $statementByCategory->countComments = (int)$this->getCountCommentsByPostId($statementByCategory->id);
            $statementByCategory->username =  ($this->getPostUsername($statementByCategory->getUserId()));

        }//end foreach

        return $statementByCategories;

    }


    /**
     * getCountCommentsByPostId : count comments of post
     *
     * @param int $id Post id
     * @return int
     */
    public function getCountCommentsByPostId(int $id): int
    {
        $statement = $this->dbConnect->prepare(
            '
            SELECT (com.id) FROM comment com
            INNER JOIN post p ON com.post_id = p.id
            WHERE com.publish_state = true and p.id = ?
            '
        );
        $statement->execute([$id]);
        return $statement->rowcount();
    }


    /**
     * getPostUsername : get username from user_id
     *
     * @param int $id User id
     * @return string
     */
    public function getPostUsername(int $id): string
    {
        $query = $this->dbConnect->prepare(
            '
            SELECT username FROM user
            WHERE user.id = ?
        '
        );
        $query->setFetchMode(PDO::FETCH_DEFAULT);
        $query->execute([$id]);
        return $query->fetch();
    }


    /**
     * verifyCouple : verify the existance of a post with id and slug pass in address
     *
     * @param int $id id of post
     * @param string $slug Slug of post
     * @return int
     */
    public function verifyCouple(int $id, string $slug): int
    {

        $query = $this->dbConnect->prepare(
            '
            SELECT id FROM ' . $this->table . '
            WHERE id = :id AND slug = :slug
        '
        );
        $query->setFetchMode(PDO::FETCH_DEFAULT);
        $query->bindParam(':id', $id);
        $query->bindParam(':slug', $slug);
        $query->execute();
        return $query->rowCount();
    }


    /**
     * insertNewPost : Create a new post (unpublished post)
     *
     * @param array<string, string|array<int, string>> $params Information to create a new post
     * @return void
     */
    public function insertNewPost(array $params): void
    {
        $query = $this->dbConnect->prepare(
            '
            INSERT INTO ' . $this->table . '(name , slug, content, created_at, user_id)
            VALUES (:name , :slug , :content, :created_at, :user_id)
            '
        );

        $slug = Text::toSlug($params['name']);
        $created_at = (new \DateTime('now'))->format('Y-m-d H:i:s');

        $query->bindParam(':name', $params['name']);
        $query->bindParam(':slug', $slug);
        $query->bindParam(':content', $params['content']);
        $query->bindParam(':created_at', $created_at);
        $query->bindParam(':user_id', $params['userId']);
        $query->execute();

        $postId = $this->dbConnect->lastInsertId();
        if (isset($params['categoryId'])) {
            $categories = $params['categoryId'];
            foreach ($categories as $category) {
                $query = $this->dbConnect->prepare(
                    '
                    INSERT INTO post_category (post_id, category_id)
                    VALUES (:post_id , :category_id)
                    '
                );
                $query->bindParam(':post_id', $postId);
                $query->bindParam(':category_id', $category);
                $query->execute();
            }//end foreach

        }//end if

    }


    /**
     * getAllOrderLimitCat : get paged Posts about specifical category
     *
     * @param  null|string $field Name of field to order
     * @param  null|string $dir Direction of order
     * @param  null|int $limit Number of posts by page
     * @param  null|int $page Current page
     * @param  array<string, string> $params Differents parameters for WHERE clause
     * @param  int $catId Id of category to filter
     * @return array<string,string>
     */
    public function getAllOrderLimitCat(?string $field, ?string $dir, ?int $limit, ?int $page, ?array $params, int $catId) : array
    {
        $sql = 'SELECT post.* FROM ' . $this->table;
        $sql .= ' INNER JOIN post_category pc ON pc.post_id = post.id ';

        if (!empty($params)) {
            $sql .= ' WHERE ';
            $i = FALSE;
            foreach ($params as $k => $value) {
                if ($i === FALSE) {
                    $sql .= ' AND ';
                }
                $sql .= $k .' = '. $value;
            }
        }
        $sql .= ' AND pc.category_id = ' . $catId;
        if (isset($field)) {
            $sql .= ' ORDER BY ' . $field;
        }
        if (in_array($dir, ['ASC', 'DESC'])) {
            $sql .= ' ' . $dir;
        } else {
            $sql .= ' DESC';
        }
        if (isset($limit)) {
            $sql .= ' LIMIT ' . $limit;
            if (isset($page) && $page !== 1) {
                $offset = ($page - 1) * $limit;
                $sql .= ' OFFSET ' .  $offset;
            }
        }

        $query = $this->dbConnect->prepare($sql);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_CLASS, $this->object);
    }


    /**
     * unpublish : unpublish post
     *
     * @param  int $id Post id
     * @return void
     */
    public function unpublish(int $id): void
    {
        $query = $this->dbConnect->prepare(
            '
            UPDATE ' . $this->table . '
            SET
                publish_state = false
            WHERE id = :id
        '
        );
        $query->setFetchMode(PDO::FETCH_DEFAULT);
        $query->bindParam(':id', $id);
        $query->execute();
    }


    /**
     * publish : publish post
     *
     * @param  int $id Post id
     * @return void
     */
    public function publish(int $id): void
    {
        $query = $this->dbConnect->prepare(
            '
            UPDATE ' . $this->table . '
            SET
                publish_state = true
                publish_at = :publish_at
            WHERE id = :id
        '
        );
        $now = (new \DateTime('now'))->format('Y-m-d H:i:s');
        $query->setFetchMode(PDO::FETCH_DEFAULT);
        $query->bindParam(':id', $id);
        $query->bindParam(':publish_at', $now);
        $query->execute();
    }

}
