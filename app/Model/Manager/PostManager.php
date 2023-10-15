<?php

namespace App\Model\Manager;

use App\Model\Entities\Post;
use Framework\Application;
use Framework\PDOConnection;
use PDO;

class PostManager extends BaseManager  
{
    public function __construct($datasource)
    {
        parent::__construct('post', Post::class, $datasource );
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
   
}
