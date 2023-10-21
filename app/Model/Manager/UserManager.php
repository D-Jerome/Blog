<?php

namespace App\Model\Manager;

use App\Model\Entities\Role;
use App\Model\Entities\User;
use PDO;

class UserManager extends BaseManager
{
    public function __construct($datasource)
    {
        parent::__construct('user', User::class, $datasource);
    }

    public function getByUsername(string $login): ?User
    {
        $statement = $this->dbConnect->prepare("SELECT * FROM {$this->table} WHERE username = ?");
        $statement->setFetchMode(PDO::FETCH_CLASS, $this->object);
        $statement->execute([$login]);
        return $statement->fetch() ?: null;
    }

    public function getRoleById(int $id): Role
    {
        $statement = $this->dbConnect->prepare('
            SELECT r.* FROM role r 
            WHERE r.id = ?
            ');
        $statement->setFetchMode(PDO::FETCH_CLASS, Role::class);
        $statement->execute([$id]);
        return $statement->fetch();
    }
    
    public function insertNewUser(array $params)
    {
        $query = $this->dbConnect->prepare('
            INSERT INTO ' . $this->table . '(username, email , password, created_at, role_id ) 
            VALUES (:username, :email , :password, :created_at, :role_id)
        ');
        
        if (isset($params['password'])){
            $password = password_hash($params['password'], PASSWORD_BCRYPT);
        }else{
            $password = password_hash('default',PASSWORD_BCRYPT);
        }
        
        $created_at = (new \DateTime('now'))->format('Y-m-d H:i:s');

        $query->bindParam(':username', $params['username']);
        $query->bindParam(':email', $params['email']);
        $query->bindParam(':password', $password);
        $query->bindParam(':created_at', $created_at);
        $query->bindParam(':role_id', $params['roleId']);
        $query->execute();

        return $this->dbConnect->lastInsertId();
    }
}
