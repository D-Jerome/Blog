<?php

namespace App\Model\Manager;

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
            SELECT com.* FROM comment com 
            INNER JOIN post p ON com.post_id = p.id 
            WHERE p.id = ?
            ');
            
        $statement->setFetchMode(PDO::FETCH_CLASS, $this->object);
        $statement->execute([(int)$id]);
        return $statement->fetchAll();
   }


   public function getCountCommentsByPostId(int $id)
   {
        $statement = $this->dbConnect->prepare('
            SELECT count(com.id) FROM comment com 
            INNER JOIN post p ON com.post_id = p.id 
            WHERE p.id = ?
            ');
        $statement->setFetchMode(PDO::FETCH_DEFAULT);
        $statement->execute([$id]);
        return $statement->rowCount();
   }
   
}
