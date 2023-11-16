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
     * @param  array $datasource : database connection informations from config file
     * @return void
     */
    public function __construct(array $datasource)
    {
        parent::__construct('role', Role::class, $datasource);
    }//end __construct

}
