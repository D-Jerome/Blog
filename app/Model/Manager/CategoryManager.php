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
     * @param  array $datasource : connection datas
     * @return void
     */
    public function __construct(array $datasource)
    {
        parent::__construct('category', Category::class, $datasource);
    }//end __construct

}
