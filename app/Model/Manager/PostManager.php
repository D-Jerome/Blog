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
 * @package App\Model\Manager
 * @author  Jdubus <dubus.jerome@gmail.com>
 */
class PostManager extends BaseManager
{

    private static ?BaseManager $postInstance;
    /**
     * [ __construct]
     *
     * @param  array<string, string> $datasource Database connection informations from config file
     * @return void
     */
    public function __construct(array $datasource)
    {
        parent::__construct('post', Post::class, $datasource);

    }//end _construct


    /**
     * Instance of manager
     *
     * @param array<string, array<string>|string> $datasource
     *
     * @return object
     */
    public static function getPostInstance(array $datasource): object
    {
        if (empty(self::$postInstance) || (!isset(self::$postInstance))) {
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
     * @return array<object>
     */
    public function getAllFilteredByParam(string $paramItem, string|int $paramValue, null|bool $publish = false ): array
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
     * @return array<Post>|string
     */
    public function getPostsbyCategory(Category $category): array|string
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
            $statementByCategory->username =  ($this->getPostUsername(
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
     * @return int|string|false|null
     */
    public function getPostUsername(int $id): int|string|false|null
    {
        $sql = <<<SQL
            SELECT username FROM user
            WHERE user.id = ?
        SQL;
        $query = $this->dbConnect->prepare($sql);
        $query->execute([$id]);
        return $query->fetchColumn();
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
     * @param  array<string, string|array<int, string>> $params Information to create a new post
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

        $slug = Text::toSlug($params['name']);
        $created_at = (new DateTime('now'))->format('Y-m-d H:i:s');

        $query->bindParam(':name', $params['name']);
        $query->bindParam(':slug', $slug);
        $query->bindParam(':content', $params['content']);
        $query->bindParam(':created_at', $created_at);
        $query->bindParam(':user_id', $params['userId']);
        $query->bindParam(':modified_at', $created_at);
        $query->execute();

        $postId = $this->dbConnect->lastInsertId();
        if (isset($params['categoryId'])) {
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
