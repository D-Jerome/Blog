<?php

namespace App\Model\Manager;

use App\Model\Entities\Role;
use Framework\PDOConnection;
use PDO;

/**
 * @extends BaseManager <Role>
 */
class RoleManager extends BaseManager
{
    private static ?RoleManager $roleInstance;


    /**
     * __construct
     *
     * @param  array<string,string> $datasource Database connection informations from config file
     * @return void
     */
    private function __construct(array $datasource)
    {
        parent::__construct('role', Role::class, $datasource);

    }//end __construct


    /**
     * Instance of manager
     *
     * @param array<string,string> $datasource
     *
     * @return RoleManager
     */
    public static function getRoleInstance(array $datasource): RoleManager
    {
        if (empty(self::$roleInstance) || (!isset(self::$roleInstance))) {
            self::$roleInstance = new self($datasource);
        }

        return self::$roleInstance;
    }

}
