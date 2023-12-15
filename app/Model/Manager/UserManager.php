<?php

declare(strict_types=1);

namespace App\Model\Manager;

use App\Model\Entities\Role;
use App\Model\Entities\User;
use PDO;
use Safe\DateTime;

/**
 * @extends BaseManager <User>
 */
class UserManager extends BaseManager
{
    /**
     * User Instance
     */
    private static ?UserManager $userInstance = null;

    /**
     * __construct
     *
     * @param array<string,string> $datasource Database connection informations from config file
     */
    private function __construct(array $datasource)
    {
        parent::__construct('user', User::class, $datasource);
    }
    // end __construct

    /**
     * Instance of manager
     *
     * @param array<string,string> $datasource
     */
    public static function getUserInstance(array $datasource): UserManager
    {
        if (!self::$userInstance instanceof \App\Model\Manager\UserManager || (!isset(self::$userInstance))) {
            self::$userInstance = new self($datasource);
        }

        return self::$userInstance;
    }

    /**
     * getByUsername : get User Object of the user
     *
     * @param string $login Username passed in login form
     */
    public function getByUsername(string $login): false | User
    {
        $statement = $this->dbConnect->prepare("SELECT * FROM {$this->table} WHERE username = ?");
        $statement->setFetchMode(PDO::FETCH_CLASS, $this->object);
        $statement->execute([$login]);
        $result = $statement->fetch();
        if ($result instanceof \App\Model\Entities\User) {
            return $result;
        }

        return false;
    }

    /**
     * getByUseremail : get User Object of the user
     *
     * @param string $email email of forget password form
     */
    public function getByUserEmail(string $email): false | User
    {
        $statement = $this->dbConnect->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $statement->setFetchMode(PDO::FETCH_CLASS, $this->object);
        $statement->execute([$email]);
        $result = $statement->fetch();
        if ($result instanceof \App\Model\Entities\User) {
            return $result;
        }

        return false;
    }

    /**
     * getRoleById : get Role object of the user-role-id
     *
     * @param int $id Id of the Role of the user
     */
    public function getRoleById(int $id): string
    {
        $sql = <<<'SQL'
                SELECT r.name FROM role r
                WHERE r.id = ?
            SQL;
        $statement = $this->dbConnect->prepare($sql);
        $statement->execute([$id]);

        return (string) $statement->fetchColumn();
    }

    /**
     * insertNewUser : add new user in database
     *
     * @param array<string,string> $params User information
     */
    public function insertNewUser(array $params): int
    {
        if (isset($params['roleId'])) {
            $sql = <<<SQL
                    INSERT INTO {$this->table} (
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
                    INSERT INTO {$this->table} (
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
        }// endif

        if (isset($params['password'])) {
            $password = password_hash($params['password'], \PASSWORD_BCRYPT);
        } else {
            $password = password_hash('default', \PASSWORD_BCRYPT);
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

        return (int) $this->dbConnect->lastInsertId();
    }

    /**
     * updateUser : Update user information
     *
     * @param array<string,string|int> $params New data user
     */
    public function updateUser(array $params): int
    {
        $actualUser = $this->getById((int) $params['id']);
        unset($params['token']);
        foreach ($params as $k => $param) {
            $getUser = 'get' . ucfirst($k);

            if ($actualUser->{$getUser}() !== $param) {
                $field = $k;
                if (0 !== \Safe\preg_match('~[A-Z]~', $k, $matches)) {
                    foreach ($matches as $match) {
                        $field = str_replace($match, '_' . strtolower((string) $match), $field);
                    }
                }
                $sql = <<<SQL
                        UPDATE {$this->table}
                        SET {$field} = :value
                        WHERE id = :id
                    SQL;
                $query = $this->dbConnect->prepare($sql);
                $query->bindParam(':value', $param);
                $query->bindParam(':id', $params['id']);
                $query->execute();
            }// end if
        }// end foreach

        return $actualUser->getId();
    }

    /**
     * verifyCouple : Valid link of items
     * verify the existance of a user with id and username pass in address
     *
     * @param int    $id       User id pass in the address
     * @param string $username Username in the address
     */
    public function verifyCouple(int $id, string $username): int
    {
        $sql = <<<SQL
                SELECT id FROM {$this->table}
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
     * @param int $id Id of user to disable
     */
    public function disable(int $id): void
    {
        $sql = <<<SQL
                UPDATE {$this->table} SET active = false
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
     * @param int $id Id of user to enable
     */
    public function enable(int $id): void
    {
        $sql = <<<SQL
                UPDATE {$this->table} SET active = true
                WHERE id = :id
            SQL;
        $query = $this->dbConnect->prepare($sql);
        $query->setFetchMode(PDO::FETCH_DEFAULT);
        $query->bindParam(':id', $id);
        $query->execute();
    }

    /**
     * getAllOrderLimitCat : get paged Posts about specifical category
     *
     * @param  array<string,string|int>|null $params Differents parameters for WHERE clause
     * @param  int|null                      $listId Id of List item to filter
     * @return array<User>
     */
    public function getAllFilteredCat(?array $params, ?int $listId): array
    {
        $sql = <<<SQL
                SELECT {$this->table}.* FROM {$this->table}
            SQL;
        if (isset($listId)) {
            $sql .= <<<'SQL'
                    INNER JOIN role ON role.id = user.role_id
                SQL;
        }// end if

        if (null !== $params && [] !== $params) {
            $sql .= <<<'SQL'
                    WHERE
                SQL;
            $i = false;
            foreach ($params as $k => $value) {
                if ($i) {
                    $sql .= <<<'SQL'
                            AND
                        SQL;
                }
                $sql .= <<<SQL
                        {$k} = {$value}
                    SQL;
                $i = true;
            }
        }// end if

        if (isset($listId)) {
            if (null !== $listId) {
                $sql .= <<<SQL
                        AND role.id = {$listId}
                    SQL;
            }
        }// end if

        $query = $this->dbConnect->prepare($sql);
        $query->execute();

        return $query->fetchAll(\PDO::FETCH_CLASS, $this->object);
    }

    /**
     * getAllOrderLimitCat : get paged Posts about specifical category
     *
     * @param  string|null                        $field  Name of field to order
     * @param  string|null                        $dir    Direction of order
     * @param  int|null                           $limit  Number of posts by page
     * @param  int|null                           $page   Current page
     * @param  array<string,string|bool|int>|null $params Differents parameters for WHERE clause
     * @param  int|null                           $listId Id of List item to filter(optionnal)
     * @return array<User>
     */
    public function getAllOrderLimitCat(?string $field, ?string $dir, ?int $limit, ?int $page, ?array $params, ?int $listId): array
    {
        $sql = <<<SQL
                SELECT {$this->table}.* FROM {$this->table}
            SQL;
        if (isset($listId)) {
            $sql .= <<<'SQL'
                    INNER JOIN role ON role.id = user.role_id
                SQL;
        }

        if (null !== $params && [] !== $params) {
            $sql .= <<<'SQL'
                    WHERE
                SQL;
            $i = false;
            foreach ($params as $k => $value) {
                if ($i) {
                    $sql .= <<<'SQL'
                            AND
                        SQL;
                }
                $sql .= <<<SQL
                        {$k} = {$value}
                    SQL;
                $i = true;
            }
        }
        if (isset($listId)) {
            if (null !== $listId) {
                $sql .= <<<SQL
                        AND role.id = {$listId}
                    SQL;
            }
        }

        if (isset($field)) {
            $sql .= <<<SQL
                    ORDER BY {$field}
                SQL;
        }

        if (\in_array($dir, ['ASC', 'DESC'], true)) {
            $sql .= <<<SQL
                    {$dir}
                SQL;
        } else {
            $sql .= <<<'SQL'
                    DESC
                SQL;
        }

        if (isset($limit)) {
            $sql .= <<<SQL
                    LIMIT {$limit}
                SQL;
            if (isset($page) && 1 !== $page) {
                $offset = ($page - 1) * $limit;
                $sql .= <<<SQL
                        OFFSET {$offset}
                    SQL;
            }
        }

        $query = $this->dbConnect->prepare($sql);
        $query->execute();

        return $query->fetchAll(\PDO::FETCH_CLASS, $this->object);
    }
}
