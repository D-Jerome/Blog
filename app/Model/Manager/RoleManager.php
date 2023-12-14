<?php

declare(strict_types=1);

namespace App\Model\Manager;

use App\Model\Entities\Role;

/**
 * @extends BaseManager <Role>
 */
class RoleManager extends BaseManager
{
    private static ?RoleManager $roleInstance = null;

    /**
     * __construct
     *
     * @param  array<string,string> $datasource Database connection informations from config file
     * @return void
     */
    private function __construct(array $datasource)
    {
        parent::__construct('role', Role::class, $datasource);
    }
    // end __construct

    /**
     * Instance of manager
     *
     * @param array<string,string> $datasource
     */
    public static function getRoleInstance(array $datasource): RoleManager
    {
        if (!self::$roleInstance instanceof \App\Model\Manager\RoleManager || (!isset(self::$roleInstance))) {
            self::$roleInstance = new self($datasource);
        }

        return self::$roleInstance;
    }
}
