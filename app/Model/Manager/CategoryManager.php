<?php

namespace App\Model\Manager;

use App\Model\Entities\Category;
use Framework\PDOConnection;
use PDO;
/**
 * @extends BaseManager <Category>
 */
class CategoryManager extends BaseManager
{

    private static ?CategoryManager $categoryInstance = null;


    /**
     * __construct
     *
     * @param  array<string,string> $datasource Connection datas
     * @return void
     */
    public function __construct(array $datasource)
    {
        parent::__construct('category', Category::class, $datasource);

    }//end __construct



    /**
     * Instance of manager
     *
     * @param array<string,string> $datasource
     *
     * @return CategoryManager
     */
    public static function getCategoryInstance(array $datasource): CategoryManager
    {
        if (!self::$categoryInstance instanceof \App\Model\Manager\CategoryManager || (!isset(self::$categoryInstance))) {
            self::$categoryInstance = new self($datasource);
        }

        return self::$categoryInstance;
    }
}
