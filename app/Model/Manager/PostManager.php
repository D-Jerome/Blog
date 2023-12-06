<?php

namespace App\Model\Manager;

use App\Model\Entities\Category;
use App\Model\Entities\Post;
use Safe\DateTime;
use Framework\Application;
use Framework\Helpers\Text;
use Framework\PDOConnection;
use Framework\Request;
use PDO;

/**
 * Post Model
 *
 * @extends BaseManager <Post>
 */
class PostManager extends BaseManager
{
    private static ?PostManager $postInstance = null;
    /**
     * [ __construct]
     *
     * @param  array<string,string> $datasource Database connection informations from config file
     * @return void
     */
    public function __construct(array $datasource)
    {
        parent::__construct('post', Post::class, $datasource);

    }//end _construct


    /**
     * Instance of manager
     *
     * @param array<string,string> $datasource
     *
     * @return PostManager
     */
    public static function getPostInstance(array $datasource): PostManager
    {
        if (!self::$postInstance instanceof \App\Model\Manager\PostManager || (!isset(self::$postInstance))) {
            self::$postInstance = new self($datasource);
        }

        return self::$postInstance;
    }


    /**
     * GetCategoriesById : all Categories of post
     *
     * @param  int $id Id of post
     * @return array<Category>
     */
    public function getCategoriesById(int $id): array
    {
        $sql = <<<SQL
            SELECT c.* FROM category c
            INNER JOIN post_category pc ON pc.category_id = c.id
            INNER JOIN post p ON pc.post_id = p.id
            WHERE p.id = ?
        SQL;
        $statement = $this->dbConnect->prepare($sql);
        $statement->setFetchMode(PDO::FETCH_CLASS, Category::class);
        $statement->execute([$id]);
        return $statement->fetchAll();
    }


    /**
     * getAllFilteredByParam : get all datas of filtered of objects
     *
     * @param  string     $paramItem  Name of field to filter
     * @param  int|string $paramValue Value of field to filter
     * @return array<Post>
     */
    public function getAllFilteredByParam(string $paramItem, string|int $paramValue, null|bool $publish = false): array
    {
        $sql = <<<SQL
                SELECT *
                FROM $this->table
                INNER JOIN post_category pc ON pc.post_id = $this->table.id
                WHERE pc.category_id = $paramValue
        SQL;
        if ($publish === true) {
            $sql .= <<<SQL
                    AND publish_state = TRUE
            SQL;
        }

        $query = $this->dbConnect->prepare($sql);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_CLASS, $this->object);
    }


    /**
     * [getPostsbyCategory] : get all posts linked to Category selected
     *
     * @param  Category $category Category object
     * @return array<Post>
     */
    public function getPostsbyCategory(Category $category): array
    {
        $sql = <<<SQL
            SELECT p.* FROM post p
            INNER JOIN post_category pc ON pc.post_id = p.id
            WHERE pc.category_id = ? and p.publish_state = true
            ORDER BY p.created_at DESC
            LIMIT 3
        SQL;
        $query = $this->dbConnect->prepare($sql);
        $query->setFetchMode(PDO::FETCH_CLASS, $this->object);
        $query->execute([$category->getId()]);
        $statementByCategories = $query->fetchAll();
        foreach ($statementByCategories as $statementByCategory) {

            $statementByCategory->categories =  $this->getCategoriesById(
                $statementByCategory->getId()
            );
            $statementByCategory->countComments = (int)$this->getCountCommentsByPostId(
                $statementByCategory->getId()
            );
            $statementByCategory->username =  (
                $this->getPostUsername(
                    $statementByCategory->getUserId()
                )
            );

        }//end foreach

        return $statementByCategories;

    }


    /**
     * [getCountCommentsByPostId] : count comments of post
     *
     * @param  int $id Post id
     * @return int
     */
    public function getCountCommentsByPostId(int $id): int
    {
        $sql = <<<SQL
            SELECT (com.id) FROM comment com
            INNER JOIN post p ON com.post_id = p.id
            WHERE com.publish_state = true and p.id = ?
        SQL;
        $statement = $this->dbConnect->prepare($sql);
        $statement->execute([$id]);
        return $statement->rowcount();
    }


    /**
     * [getPostUsername] : get username from user_id
     *
     * @param  int $id User id
     * @return string
     */
    public function getPostUsername(int $id): string
    {
        $sql = <<<SQL
            SELECT username FROM user
            WHERE user.id = ?
        SQL;
        $query = $this->dbConnect->prepare($sql);
        $query->execute([$id]);
        return (string)$query->fetchColumn();
    }


    /**
     * [verifyCouple] : verify the existance of a post with id and slug pass in address
     *
     * @param  int    $id   id of post
     * @param  string $slug Slug of post
     * @return int
     */
    public function verifyCouple(int $id, string $slug): int
    {
        $sql = <<<SQL
            SELECT id FROM $this->table
            WHERE id = :id AND slug = :slug
        SQL;
        $query = $this->dbConnect->prepare($sql);
        $query->setFetchMode(PDO::FETCH_DEFAULT);
        $query->bindParam(':id', $id);
        $query->bindParam(':slug', $slug);
        $query->execute();
        return $query->rowCount();
    }


    /**
     * [insertNewPost] : Create a new post (unpublished post)
     *
     * @param  array<string, array<int,string>|string|int|null> $params Information to create a new post
     * @return int : Last PostId
     */
    public function insertNewPost(array $params): int
    {

        $sql = <<<SQL
            INSERT INTO $this->table (
                name,
                slug,
                content,
                created_at,
                user_id,
                modified_at
                )
            VALUES (:name , :slug , :content, :created_at, :user_id, :modified_at)
        SQL;
        $query = $this->dbConnect->prepare($sql);
        if (is_string($params['name'])) {
            $slug = Text::toSlug((string)$params['name']);
        }
        $created_at = (new DateTime('now'))->format('Y-m-d H:i:s');

        $query->bindParam(':name', $params['name']);
        $query->bindParam(':slug', $slug);
        $query->bindParam(':content', $params['content']);
        $query->bindParam(':created_at', $created_at);
        $query->bindParam(':user_id', $params['userId']);
        $query->bindParam(':modified_at', $created_at);
        $query->execute();

        $postId = (int)$this->dbConnect->lastInsertId();
        if (isset($params['categoryId']) && (is_array($params['categoryId']))) {
            $categories = $params['categoryId'];
            foreach ($categories as $category) {
                $sql = <<<SQL
                    INSERT INTO post_category (post_id, category_id)
                    VALUES (:post_id , :category_id)
                SQL;
                $query = $this->dbConnect->prepare($sql);
                $query->bindParam(':post_id', $postId);
                $query->bindParam(':category_id', $category);
                $query->execute();
            }//end foreach

        }//end if

        return $postId;

    }


}
