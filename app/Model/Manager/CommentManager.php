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
   
   public function getCommentUserName(int $id)
   {
        $query = $this->dbConnect->prepare('
            SELECT username FROM user
            WHERE user.id = ?
        ');
        $query->setFetchMode(PDO::FETCH_DEFAULT);
        $query->execute([$id]);
        return $query->fetch();

   }
}
