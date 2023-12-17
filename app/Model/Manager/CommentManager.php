<?php

declare(strict_types=1);

namespace App\Model\Manager;

use App\Model\Entities\Comment;
use PDO;
use Safe\DateTime;

/**
 * @extends BaseManager <Comment>
 */
class CommentManager extends BaseManager
{
    private static ?CommentManager $commentInstance = null;

    /**
     * __construct
     *
     * @param array<string,string> $datasource Connection database informations
     */
    public function __construct(array $datasource)
    {
        parent::__construct('comment', Comment::class, $datasource);
    }
    // end _construct

    /**
     * Instance of manager
     *
     * @param array<string,string> $datasource
     */
    public static function getCommentInstance(array $datasource): CommentManager
    {
        if (!self::$commentInstance instanceof \App\Model\Manager\CommentManager || (!isset(self::$commentInstance))) {
            self::$commentInstance = new self($datasource);
        }

        return self::$commentInstance;
    }

    /**
     * getCommentsByPostId: get all comments od a post
     *
     * @param  int            $id Post Id
     * @return array<Comment>
     */
    public function getCommentsByPostId(int $id): array
    {
        $sql = <<<'SQL'
                 SELECT * FROM comment com
                WHERE com.post_id = ? and com.publish_state = true
            SQL;
        $statement = $this->dbConnect->prepare($sql);
        $statement->setFetchMode(PDO::FETCH_CLASS, $this->object);
        $statement->execute([$id]);

        return $statement->fetchAll();
    }

    /**
     * getCountCommentsByPostId : Number of comments of a post
     *
     * @param int $id Post id
     */
    public function getCountCommentsByPostId(int $id): int
    {
        $sql = <<<'SQL'
                SELECT com.id FROM comment com
                WHERE p.id = ? and com.publish_state = true
            SQL;
        $statement = $this->dbConnect->prepare($sql);
        $statement->setFetchMode(PDO::FETCH_DEFAULT);
        $statement->execute([$id]);

        return $statement->rowCount();
    }

    /**
     * getCommentUsername: get username of post
     *
     * @param int $id Post id
     */
    public function getCommentUsername(int $id): string
    {
        $sql = <<<'SQL'
                SELECT username FROM user
                WHERE user.id = ?
            SQL;
        $query = $this->dbConnect->prepare($sql);

        $query->execute([$id]);

        return (string) $query->fetchColumn();
    }

    /**
     * getCommentsByUserId : get comment by user
     *
     * @param  int            $id User id
     * @return array<Comment>
     */
    public function getCommentsByUserId(int $id): array
    {
        $sql = <<<SQL
                SELECT * FROM  {$this->table}
                WHERE user_id = :user_id
            SQL;
        $query = $this->dbConnect->prepare($sql);

        $query->setFetchMode(PDO::FETCH_CLASS, $this->object);
        $query->bindParam(':user_id', $id);
        $query->execute();

        return $query->fetchAll();
    }

    /**
     * insertNewComment ; inser new comment in database
     *
     * @param array<string, string> $params Data to insert
     */
    public function insertNewComment(array $params): void
    {
        $sql = <<<SQL
                INSERT INTO  {$this->table} (
                                                content,
                                                created_at,
                                                modified_at,
                                                post_id,
                                                user_id
                                                )
                VALUES (
                        :content,
                        :created_at,
                        :modified_at,
                        :post_id,
                        :user_id
                        )
            SQL;
        $query = $this->dbConnect->prepare($sql);

        $created_at = (new DateTime('now'))->format('Y-m-d H:i:s');

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
     * @param int $postId    Post id
     * @param int $commentId Comment id
     */
    public function verifyCouple(int $postId, int $commentId): int
    {
        $sql = <<<SQL
                SELECT id FROM  {$this->table}
                WHERE post_id = :postId AND id = :commentId
            SQL;
        $query = $this->dbConnect->prepare($sql);
        $query->setFetchMode(PDO::FETCH_DEFAULT);
        $query->bindParam(':postId', $postId);
        $query->bindParam(':commentId', $commentId);
        $query->execute();

        return $query->rowCount();
    }

    /**
     * getAllFilteredCat : get paged Posts about specifical category
     *
     * @param  array<string,string|int>|null $params Differents parameters for WHERE clause
     * @param  int|null                      $listId Id of List item to filter
     * @return array<Comment>
     */
    public function getAllFilteredCat(?array $params, ?int $listId): array
    {
        $sql = <<<SQL
                SELECT {$this->table}.* FROM {$this->table}
            SQL;
        if (isset($listId)) {
            switch ($this->table) {
                case 'post':
                    $sql .= <<<'SQL'
                            INNER JOIN post_category pc ON pc.post_id = post.id
                        SQL;
                    break;
                case 'user':
                    $sql .= <<<'SQL'
                            INNER JOIN role ON role.id = user.role_id
                        SQL;
                    break;
                case 'comment':
                    break;
            }// end switch
        }// end if

        if (null !== $params && [] !== $params) {
            $sql .= <<<'SQL'
                    WHERE
                SQL;
            $i = false;
            foreach ($params as $k => $value) {
                if ($i) {
                    $sql .= <<<'SQL'
                            AND
                        SQL;
                }
                $sql .= <<<SQL
                        {$k} = {$value}
                    SQL;
                $i = true;
            }
        }// end if

        if (isset($listId)) {
            switch ($this->table) {
                case 'post':
                    if (null !== $listId) {
                        $sql .= <<<SQL
                                AND pc.category_id = {$listId}
                            SQL;
                    }
                    break;
                case 'user':
                    if (null !== $listId) {
                        $sql .= <<<SQL
                                AND role.id = {$listId}
                            SQL;
                    }
                    break;
                case 'comment':
                    break;
            }// end switch
        }// end if

        $query = $this->dbConnect->prepare($sql);
        $query->execute();

        return $query->fetchAll(\PDO::FETCH_CLASS, $this->object);
    }

    /**
     * getAllOrderLimitCat : get paged Posts about specifical category
     *
     * @param  string|null                        $field  Name of field to order
     * @param  string|null                        $dir    Direction of order
     * @param  int|null                           $limit  Number of posts by page
     * @param  int|null                           $page   Current page
     * @param  array<string,string|bool|int>|null $params Differents parameters for WHERE clause
     * @return array<Comment>
     */
    public function getAllOrderLimit(?string $field, ?string $dir, ?int $limit, ?int $page, ?array $params): array
    {
        $sql = <<<SQL
                SELECT {$this->table}.* FROM {$this->table}
            SQL;

        if (null !== $params && [] !== $params) {
            $sql .= <<<'SQL'
                    WHERE
                SQL;
            $i = false;
            foreach ($params as $k => $value) {
                if ($i) {
                    $sql .= <<<'SQL'
                            AND
                        SQL;
                }
                $sql .= <<<SQL
                        {$k} = {$value}
                    SQL;
                $i = true;
            }
        }

        if (isset($field)) {
            $sql .= <<<SQL
                    ORDER BY {$field}
                SQL;
        }

        if (\in_array($dir, ['ASC', 'DESC'], true)) {
            $sql .= <<<SQL
                    {$dir}
                SQL;
        } else {
            $sql .= <<<'SQL'
                    DESC
                SQL;
        }

        if (isset($limit)) {
            $sql .= <<<SQL
                    LIMIT {$limit}
                SQL;
            if (isset($page) && 1 !== $page) {
                $offset = ($page - 1) * $limit;
                $sql .= <<<SQL
                        OFFSET {$offset}
                    SQL;
            }
        }

        $query = $this->dbConnect->prepare($sql);
        $query->execute();

        return $query->fetchAll(\PDO::FETCH_CLASS, $this->object);
    }
}
