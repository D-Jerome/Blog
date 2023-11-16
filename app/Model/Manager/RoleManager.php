<?php

namespace App\Model\Manager;

use App\Model\Entities\Role;
use Framework\PDOConnection;
use PDO;

class RoleManager extends BaseManager
{

    /**
     * __construct
     *
     * @param array<string, string> $datasource Database connection informations from config file
     * @return void
     */
    public function __construct(array $datasource)
    {
        parent::__construct('role', Role::class, $datasource);
    }//end __construct

}
