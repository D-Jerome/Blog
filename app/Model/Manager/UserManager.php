<?php

namespace App\Model\Manager;

use App\Model\Entities\Role;
use App\Model\Entities\User;
use PDO;
use PhpParser\Node\Stmt\Else_;

class UserManager extends BaseManager
{

    /**
     * __construct
     *
     * @param  array $datasource : database connection informations from config file
     * @return void
     */
    public function __construct(array $datasource)
    {
        parent::__construct('user', User::class, $datasource);
    }//end __construct


    /**
     * getByUsername : get User Object of the user
     *
     * @param  string $login : username passed in login form
     * @return User
     */
    public function getByUsername(string $login): ?User
    {
        $statement = $this->dbConnect->prepare("SELECT * FROM {$this->table} WHERE username = ?");
        $statement->setFetchMode(PDO::FETCH_CLASS, $this->object);
        $statement->execute([$login]);
        return $statement->fetch() ?: null;
    }


    /**
     * getRoleById : get Role object of the user-role-id
     *
     * @param  int $id : id of the Role of the user
     * @return Role
     */
    public function getRoleById(int $id): Role
    {
        $statement = $this->dbConnect->prepare(
            '
            SELECT r.* FROM role r
            WHERE r.id = ?
            '
        );
        $statement->setFetchMode(PDO::FETCH_CLASS, Role::class);
        $statement->execute([$id]);
        return $statement->fetch();
    }


    /**
     * insertNewUser : add new user in database
     *
     * @param  array $params : user information 
     * @return void
     */
    public function insertNewUser(array $params)
    {

        if (isset($params['roleId'])) {
            $query = $this->dbConnect->prepare(
                '
                INSERT INTO ' . $this->table . '(firstname,lastname,username, email , file, created_at, role_id )
                VALUES (:firstname, :lastname, :username, :email , :password, :created_at, :role_id)
            '
            );
        } else {
            $query = $this->dbConnect->prepare(
                '
                INSERT INTO ' . $this->table . '(firstname,lastname,username, email , password, created_at )
                VALUES (:firstname, :lastname, :username, :email , :password, :created_at)
            '
            );
        }//endif

        if (isset($params['password'])) {
            $password = password_hash($params['password'], PASSWORD_BCRYPT);
        } else {
            $password = password_hash('default', PASSWORD_BCRYPT);
        }

        $created_at = (new \DateTime('now'))->format('Y-m-d H:i:s');


        $query->bindParam(':firstname', $params['firstname']);
        $query->bindParam(':lastname', $params['lastname']);
        $query->bindParam(':username', $params['username']);
        $query->bindParam(':email', $params['email']);
        $query->bindParam(':password', $password);
        $query->bindParam(':created_at', $created_at);
        if (isset($params['roleId'])) {
            $query->bindParam(':role_id', $params['roleId']);
        }

        $query->execute();

        return $this->dbConnect->lastInsertId();
    }


    /**
     * updateUser ; Update user information
     *
     * @param  array $params : new data user
     * @return void
     */
    public function updateUser(array $params)
    {

        $actualUser = $this->getById($params['id']);

        foreach ($params as $k => $param) {
            $getUser = 'get' . ucfirst($k);
            //dd($actualUser->$getUser(), $param, $k);
            if ($param != $actualUser->$getUser()) {
                $field = $k;
                if (preg_match('~[A-Z]~', $k, $matches)) {
                    foreach ($matches as $match) {
                        $field = str_replace($match, '_' . strtolower($match), $field);
                    }
                }
                $query = $this->dbConnect->prepare("UPDATE $this->table SET  $field = :value WHERE id = :id");
                $query->bindParam(':value', $param);
                $query->bindParam(':id', $params['id']);
                $query->execute();
            }//indif

        }//endforeach
        return $actualUser->getId();
    }


    /**
     * verifyCouple : verify the existance of a user with id and username pass in address
     *
     * @param  int $id : user id pass in the address
     * @param  string $username : username in the address
     * @return int
     */
    public function verifyCouple(int $id, string $username): int
    {

        $query = $this->dbConnect->prepare(
            '
            SELECT id FROM ' . $this->table . '
            WHERE id = :id AND username = :username
        '
        );
        $query->setFetchMode(PDO::FETCH_DEFAULT);
        $query->bindParam(':id', $id);
        $query->bindParam(':username', $username);
        $query->execute();
        return $query->rowCount();
    }


    /**
     * disable : disable an user
     *
     * @param  int $id : id of user to disable
     * @return void
     */
    public function disable(int $id): void
    {
        $query = $this->dbConnect->prepare(
            '
            UPDATE ' . $this->table . ' SET active = false
            WHERE id = :id
        '
        );
        $query->setFetchMode(PDO::FETCH_DEFAULT);
        $query->bindParam(':id', $id);
        $query->execute();
    }


    /**
     * enable : enable an user
     *
     * @param  int $id : id of user to enable
     * @return void
     */
    public function enable(int $id): void
    {
        $query = $this->dbConnect->prepare(
            '
            UPDATE ' . $this->table . ' SET active = true
            WHERE id = :id
        '
        );
        $query->setFetchMode(PDO::FETCH_DEFAULT);
        $query->bindParam(':id', $id);
        $query->execute();
    }

}
