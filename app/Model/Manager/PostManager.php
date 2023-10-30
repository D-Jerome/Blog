<?php

namespace App\Model\Manager;


use App\Model\Entities\Post;
use DateTime;
use Framework\Application;
use Framework\Helpers\Text;
use Framework\PDOConnection;
use Framework\Request;
use PDO;

class PostManager extends BaseManager
{
    public function __construct($datasource)
    {
        parent::__construct('post', Post::class, $datasource);
    }

    public function getCategoriesById(int $id)
    {
        $statement = $this->dbConnect->prepare('
            SELECT c.* FROM category c 
            INNER JOIN post_category pc ON pc.category_id = c.id 
            INNER JOIN post p ON pc.post_id = p.id 
            WHERE p.id = ?
            ');
        $statement->setFetchMode(PDO::FETCH_CLASS, $this->object);
        $statement->execute([$id]);
        return $statement->fetchAll();
    }

    public function getCountCommentsByPostId(int $id)
    {
        $statement = $this->dbConnect->prepare('
            SELECT(com.id) FROM comment com 
            INNER JOIN post p ON com.post_id = p.id 
            WHERE p.id = ? 
            ');
        $statement->execute([$id]);
        return $statement->rowcount();
    }

    public function getPostUsername(int $id)
    {
        $query = $this->dbConnect->prepare('
            SELECT username FROM user
            WHERE user.id = ?
        ');
        $query->setFetchMode(PDO::FETCH_DEFAULT);
        $query->execute([$id]);
        return $query->fetch();
    }

    public function verifyCoupleIdSlug(int $id, string $slug): int
    {

        $query = $this->dbConnect->prepare('
            SELECT id FROM ' . $this->table . '
            WHERE id = :id AND slug = :slug
        ');
        $query->setFetchMode(PDO::FETCH_DEFAULT);
        $query->bindParam(':id', $id);
        $query->bindParam(':slug', $slug);
        $query->execute();
        return $query->rowCount();
    }

    public function insertNewPost(array $params)
    {
        $query = $this->dbConnect->prepare('
            INSERT INTO ' . $this->table . '(name , slug, content, created_at, user_id) 
            VALUES (:name , :slug , :content, :created_at, :user_id)
        ');

        $slug = Text::toSlug($params['name']);
        $created_at = (new \DateTime('now'))->format('Y-m-d H:i:s');

        $query->bindParam(':name', $params['name']);
        $query->bindParam(':slug', $slug);
        $query->bindParam(':content', $params['content']);
        $query->bindParam(':created_at', $created_at);
        $query->bindParam(':user_id', $params['userId']);
        $query->execute();

        $postId = $this->dbConnect->lastInsertId();
        $categories = $params['categoryId'];
        foreach ($categories as $category) {
            $query = $this->dbConnect->prepare('
                INSERT INTO post_category (post_id, category_id) 
                VALUES (:post_id , :category_id)
                ');
            $query->bindParam(':post_id', $postId);
            $query->bindParam(':category_id', $category);
            $query->execute();
        }
    }
}
