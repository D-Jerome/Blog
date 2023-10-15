<?php

namespace App\Model\Manager;

use App\Model\Entities\User;
use PDO;

class UserManager extends BaseManager
{
    public function __construct($datasource)
    {
        parent::__construct('user', User::class, $datasource);
    }

    public function getByUsername(string $login) : ?User
    {
        $statement = $this->dbConnect->prepare("SELECT * FROM {$this->table} WHERE username = ?");
        $statement->setFetchMode(PDO::FETCH_CLASS, $this->object);
        $statement->execute([$login]);
        return $statement->fetch()?: null;
    
    }
}
