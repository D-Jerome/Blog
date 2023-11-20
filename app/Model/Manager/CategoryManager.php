<?php

namespace App\Model\Manager;

use App\Model\Entities\Category;
use Framework\PDOConnection;
use PDO;

class CategoryManager extends BaseManager
{

    /**
     * __construct
     *
     * @param  array<string, string> $datasource Connection datas
     * @return void
     */
    public function __construct(array $datasource)
    {
        parent::__construct('category', Category::class, $datasource);

    }//end __construct

}
