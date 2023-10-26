<?php

namespace App\Model\Manager;

use App\Helpers\Text;
use App\Model\Entities\Comment;
use Framework\Application;
use Framework\PDOConnection;
use PDO;

class CommentManager extends BaseManager  
{
    public function __construct($datasource)
    {
        parent::__construct('comment', Comment::class, $datasource );
    }
	
   public function getCommentsByPostId($id)
   {
        $statement = $this->dbConnect->prepare('
            SELECT * FROM comment com 
            WHERE com.post_id = ?
            ');
            
        $statement->setFetchMode(PDO::FETCH_CLASS, $this->object);
        $statement->execute([$id]);
        return $statement->fetchAll();
   }


   public function getCountCommentsByPostId(int $id)
   {
        $statement = $this->dbConnect->prepare('
            SELECT com.id FROM comment com 
            WHERE p.id = ?
            ');
        $statement->setFetchMode(PDO::FETCH_DEFAULT);
        $statement->execute([$id]);
        return $statement->rowCount();
   }
   
   public function getCommentUsername(int $id)
   {
        $query = $this->dbConnect->prepare('
            SELECT username FROM user
            WHERE user.id = ?
        ');
        $query->setFetchMode(PDO::FETCH_DEFAULT);
        $query->execute([$id]);
        return $query->fetch();

   }

   public function getCommentsByUserId(int $id)
   {
        $query = $this->dbConnect->prepare("
            SELECT * FROM  $this->table 
            WHERE user_id = :user_id
        ");
        
        $query->setFetchMode(PDO::FETCH_CLASS, $this->object);
        $query->bindParam(':user_id', $id);
        $query->execute();

        return $query->fetchAll() ;

   }

   public function insertNewComment(array $params)
   {
        $query = $this->dbConnect->prepare('
            INSERT INTO ' . $this->table . '(content, created_at, post_id, user_id) 
            VALUES (:content, :created_at, :post_id, :user_id)
        ');
        
        $created_at = (new \DateTime('now'))->format('Y-m-d H:i:s');

        $query->bindParam(':content', $params['content']);
        $query->bindParam(':created_at', $created_at);
        $query->bindParam(':post_id', $params['postId']);
        $query->bindParam(':user_id', $params['userId']);
        $query->execute();

   }
   
   public function verifyCoupleCommentIdPostId(int $postId , int $commentId): int
   {
        $query = $this->dbConnect->prepare('
            SELECT id FROM ' . $this->table . '
            WHERE post_id = :postId AND id = :commentId
        ');
        $query->setFetchMode(PDO::FETCH_DEFAULT);
        $query->bindParam(':postId', $postId);
        $query->bindParam(':commentId', $commentId); 
        $query->execute();
        return $query->rowCount();
    }  
}
