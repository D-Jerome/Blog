<?php

namespace App\Model\Manager;

use App\Model\Entities\Category;
use Framework\PDOConnection;
use PDO;

class CategoryManager extends BaseManager
{

    private static ?BaseManager $categoryInstance;


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



    /**
     * Instance of manager
     *
     * @param array<string, array<string>|string> $datasource
     *
     * @return object
     */
    public static function getCategoryInstance(array $datasource): object
    {
        if (empty(self::$categoryInstance) || (!isset(self::$categoryInstance))) {
            self::$categoryInstance = new self($datasource);
        }

        return self::$categoryInstance;
    }
}
