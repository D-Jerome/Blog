<?php

namespace App\Model\Manager;

use App\Helpers\Text;
use App\Model\Entities\Comment;
use Framework\Application;
use Framework\PDOConnection;
use PDO;

class CommentManager extends BaseManager
{

    /**
     * __construct
     *
     * @param  array<string, string> $datasource Connection database informations
     * @return void
     */
    public function __construct(array $datasource)
    {
        parent::__construct('comment', Comment::class, $datasource);

    }//end _construct


    /**
     * getCommentsByPostId: get all comments od a post
     *
     * @param  int $id Post Id
     * @return array<Comment>
     */
    public function getCommentsByPostId(int $id): array
    {
        $statement = $this->dbConnect->prepare(
            '
            SELECT * FROM comment com
            WHERE com.post_id = ? and com.publish_state = true
            '
        );

        $statement->setFetchMode(PDO::FETCH_CLASS, $this->object);
        $statement->execute([$id]);
        return $statement->fetchAll();
    }


    /**
     * getCountCommentsByPostId : Number of comments of a post
     *
     * @param  int $id Post id
     * @return int
     */
    public function getCountCommentsByPostId(int $id): int
    {
        $statement = $this->dbConnect->prepare(
            '
            SELECT com.id FROM comment com
            WHERE p.id = ? and com.publish_state = true
            '
        );
        $statement->setFetchMode(PDO::FETCH_DEFAULT);
        $statement->execute([$id]);
        return $statement->rowCount();
    }


    /**
     * getCommentUsername: get username of post
     *
     * @param  int $id Post id
     * @return string
     */
    public function getCommentUsername(int $id): string
    {
        $query = $this->dbConnect->prepare(
            '
            SELECT username FROM user
            WHERE user.id = ?
        '
        );

        $query->execute([$id]);
        return $query->fetchColumn();
    }


    /**
     * getCommentsByUserId : get comment by user
     *
     * @param  int $id User id
     * @return array<Comment>
     */
    public function getCommentsByUserId(int $id): array
    {
        $query = $this->dbConnect->prepare(
            "
            SELECT * FROM  $this->table
            WHERE user_id = :user_id
        "
        );

        $query->setFetchMode(PDO::FETCH_CLASS, $this->object);
        $query->bindParam(':user_id', $id);
        $query->execute();

        return $query->fetchAll();
    }


    /**
     * insertNewComment ; inser new comment in database
     *
     * @param  array<string, string> $params Data to insert
     * @return void
     */
    public function insertNewComment(array $params)
    {
        $query = $this->dbConnect->prepare(
            '
            INSERT INTO ' . $this->table . '(content, created_at, modified_at, post_id, user_id)
            VALUES (:content, :created_at, :modified_at, :post_id, :user_id)
        '
        );

        $created_at = (new \DateTime('now'))->format('Y-m-d H:i:s');

        $query->bindParam(':content', $params['content']);
        $query->bindParam(':created_at', $created_at);
        $query->bindParam(':modified_at', $created_at);
        $query->bindParam(':post_id', $params['postId']);
        $query->bindParam(':user_id', $params['userId']);
        $query->execute();
    }


    /**
     * verifyCouple : verify the existance of a couple post and comment pass in address
     *
     * @param  int $postId    Post id
     * @param  int $commentId Comment id
     * @return int
     */
    public function verifyCouple(int $postId, int $commentId): int
    {
        $query = $this->dbConnect->prepare(
            '
            SELECT id FROM ' . $this->table . '
            WHERE post_id = :postId AND id = :commentId
            '
        );
        $query->setFetchMode(PDO::FETCH_DEFAULT);
        $query->bindParam(':postId', $postId);
        $query->bindParam(':commentId', $commentId);
        $query->execute();
        return $query->rowCount();
    }

}
