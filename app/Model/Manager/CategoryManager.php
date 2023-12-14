<?php

declare(strict_types=1);

namespace App\Model\Manager;

use App\Model\Entities\Category;

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
    }
    // end __construct

    /**
     * Instance of manager
     *
     * @param array<string,string> $datasource
     */
    public static function getCategoryInstance(array $datasource): CategoryManager
    {
        if (!self::$categoryInstance instanceof \App\Model\Manager\CategoryManager || (!isset(self::$categoryInstance))) {
            self::$categoryInstance = new self($datasource);
        }

        return self::$categoryInstance;
    }
}
