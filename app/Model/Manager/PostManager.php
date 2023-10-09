<?php

namespace App\Model\Manager;

use App\Model\Entities\Post;
use Framework\Application;
use Framework\PDOConnection;


class PostManager extends BaseManager  
{
    public function __construct($datasource)
    {
        parent::__construct('post', Post::class, $datasource );
    }

    
    
}