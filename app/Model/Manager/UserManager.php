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
        
        if(isset($params['roleId'])){
            $query = $this->dbConnect->prepare('
                INSERT INTO ' . $this->table . '(username, email , password, created_at, role_id ) 
                VALUES (:username, :email , :password, :created_at, :role_id)
            ');
        }else{
            $query = $this->dbConnect->prepare('
                INSERT INTO ' . $this->table . '(username, email , password, created_at ) 
                VALUES (:username, :email , :password, :created_at)
            ');
        }    

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
        if(isset($params['roleId'])){
            $query->bindParam(':role_id', $params['roleId']);
        }
                
        $query->execute();

        return $this->dbConnect->lastInsertId();
    }

    public function updateUser(array $params)
    {

        $actualUser = $this->getById($params['id']);
        
        foreach ($params as $k => $param){
            $getUser = 'get' . ucfirst($k);
            //dd($actualUser->$getUser(), $param, $k);
            if ($param != $actualUser->$getUser()){
                $query = $this->dbConnect->prepare("UPDATE $this->table SET  $k = :value WHERE id = :id" ); 
                $query->bindParam(':value', $param);
                $query->bindParam(':id', $params['id']);
                $query->execute();
            }
        }
        return $actualUser->getId();
    }

    public function verifyCoupleUsernameUserId(int $id, string $string): int
    {
       
        $query = $this->dbConnect->prepare('
            SELECT id FROM ' . $this->table . '
            WHERE id = :id AND username = :username
        ');
        $query->setFetchMode(PDO::FETCH_DEFAULT);
        $query->bindParam(':id', $id);
        $query->bindParam(':username', $string); 
        $query->execute();
        return $query->rowCount();
    }  
}
