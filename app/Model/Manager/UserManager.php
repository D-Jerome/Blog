<?php

namespace App\Model\Manager;

use App\Model\Entities\Role;
use App\Model\Entities\User;
use PDO;
use Safe\DateTime;
use PhpParser\Node\Stmt\Else_;

/**
 * @extends BaseManager <User>
 */
class UserManager extends BaseManager
{
    /**
     * User Instance
     *
     * @var UserManager|null
     */
    private static ?UserManager $userInstance;


    /**
     * __construct
     *
     * @param  array<string,string> $datasource Database connection informations from config file
     * @return void
     */
    private function __construct(array $datasource)
    {
        parent::__construct('user', User::class, $datasource);

    }//end __construct


    /**
     * Instance of manager
     *
     * @param array<string,string> $datasource
     *
     * @return UserManager
     */
    public static function getUserInstance(array $datasource): UserManager
    {
        if (empty(self::$userInstance) || (!isset(self::$userInstance))) {
            self::$userInstance = new self($datasource);
        }

        return self::$userInstance;
    }


    /**
     * getByUsername : get User Object of the user
     *
     * @param  string $login Username passed in login form
     * @return User|null
     */
    public function getByUsername(string $login): ?User
    {
        $statement = $this->dbConnect->prepare("SELECT * FROM {$this->table} WHERE username = ?");
        $statement->setFetchMode(PDO::FETCH_CLASS, $this->object);
        $statement->execute([$login]);
        return $statement->fetch() ?? null;
    }


    /**
     * getByUseremail : get User Object of the user
     *
     * @param  string $email email of forget password form
     * @return User|null
     */
    public function getByUserEmail(string $email): ?User
    {
        $statement = $this->dbConnect->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $statement->setFetchMode(PDO::FETCH_CLASS, $this->object);
        $statement->execute([$email]);
        return $statement->fetch() ?? null;
    }


    /**
     * getRoleById : get Role object of the user-role-id
     *
     * @param  int $id Id of the Role of the user
     * @return string
     */
    public function getRoleById(int $id): string
    {
        $sql = <<<SQL
            SELECT r.name FROM role r
            WHERE r.id = ?
        SQL;
        $statement = $this->dbConnect->prepare($sql);
        $statement->execute([$id]);
        return (string)$statement->fetchColumn();
    }


    /**
     * insertNewUser : add new user in database
     *
     * @param  array<string,string> $params User information
     * @return int
     */
    public function insertNewUser(array $params): int
    {

        if (isset($params['roleId'])) {
            $sql = <<<SQL
                INSERT INTO $this->table (
                                        firstname,
                                        lastname,
                                        username,
                                        email,
                                        password,
                                        created_at,
                                        role_id
                                        )
                VALUES (
                        :firstname,
                        :lastname,
                        :username,
                        :email ,
                        :password,
                        :created_at,
                        :role_id
                        )
            SQL;
            $query = $this->dbConnect->prepare($sql);
        } else {
            $sql = <<<SQL
                INSERT INTO $this->table (
                                        firstname,
                                        lastname,
                                        username,
                                        email,
                                        password,
                                        created_at
                                        )
                VALUES (
                        :firstname,
                        :lastname,
                        :username,
                        :email,
                        :password,
                        :created_at
                        )
            SQL;
            $query = $this->dbConnect->prepare($sql);
        }//endif

        if (isset($params['password'])) {
            $password = password_hash($params['password'], PASSWORD_BCRYPT);
        } else {
            $password = password_hash('default', PASSWORD_BCRYPT);
        }

        $created_at = (new DateTime('now'))->format('Y-m-d H:i:s');


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
        return (int)$this->dbConnect->lastInsertId();
    }


    /**
     * updateUser : Update user information
     *
     * @param  array<string,string|int> $params New data user
     * @return int
     */
    public function updateUser(array $params): int
    {

        $actualUser = $this->getById((int)$params['id']);
        unset($params['token']);
        foreach ($params as $k => $param) {
            $getUser = 'get' . ucfirst($k);

            if ($param != $actualUser->$getUser()) {
                $field = $k;
                if (\Safe\preg_match('~[A-Z]~', $k, $matches)) {
                    foreach ($matches as $match) {
                        $field = str_replace($match, '_' . strtolower($match), $field);
                    }
                }
                $sql =  <<<SQL
                    UPDATE $this->table
                    SET $field = :value
                    WHERE id = :id
                SQL;
                $query = $this->dbConnect->prepare($sql);
                $query->bindParam(':value', $param);
                $query->bindParam(':id', $params['id']);
                $query->execute();
            }//end if

        }//end foreach

        return $actualUser->getId();
    }


    /**
     * verifyCouple : Valid link of items
     * verify the existance of a user with id and username pass in address
     *
     * @param  int    $id       User id pass in the address
     * @param  string $username Username in the address
     * @return int
     */
    public function verifyCouple(int $id, string $username): int
    {
        $sql = <<<SQL
            SELECT id FROM $this->table
            WHERE id = :id AND username = :username
        SQL;
        $query = $this->dbConnect->prepare($sql);
        $query->setFetchMode(PDO::FETCH_DEFAULT);
        $query->bindParam(':id', $id);
        $query->bindParam(':username', $username);
        $query->execute();
        return $query->rowCount();
    }


    /**
     * disable : disable an user
     *
     * @param  int $id Id of user to disable
     * @return void
     */
    public function disable(int $id): void
    {
        $sql = <<<SQL
            UPDATE $this->table SET active = false
            WHERE id = :id
        SQL;
        $query = $this->dbConnect->prepare($sql);
        $query->setFetchMode(PDO::FETCH_DEFAULT);
        $query->bindParam(':id', $id);
        $query->execute();
    }


    /**
     * enable : enable an user
     *
     * @param  int $id Id of user to enable
     * @return void
     */
    public function enable(int $id): void
    {

        $sql = <<<SQL
            UPDATE $this->table SET active = true
            WHERE id = :id
        SQL;
        $query = $this->dbConnect->prepare($sql);
        $query->setFetchMode(PDO::FETCH_DEFAULT);
        $query->bindParam(':id', $id);
        $query->execute();

    }

}
