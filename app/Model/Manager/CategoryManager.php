<?php

namespace App\Model\Manager;

use App\Model\Entities\Category;
use Framework\PDOConnection;
use PDO;

class CategoryManager extends BaseManager  
{
    public function __construct($datasource)
    {
        parent::__construct('category', Category::class, $datasource );
    }

    
    
}
