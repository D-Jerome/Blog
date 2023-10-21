<?php

namespace App\Model\Manager;

use App\Model\Entities\Role;
use Framework\PDOConnection;
use PDO;

class RoleManager extends BaseManager  
{
    public function __construct($datasource)
    {
        parent::__construct('role', Role::class, $datasource );
    }

    
    
}
