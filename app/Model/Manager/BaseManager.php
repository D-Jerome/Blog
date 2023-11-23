<?php

namespace App\Model\Manager;

use Framework\Helpers\Text;
use App\Model\PDOConnection;
use Framework\Exception\PropertyNotFoundException;
use PDO;
use Safe\DateTime;

abstract class BaseManager
{
    /**
     * Database connector
     *
     * @var PDO
     */
    public PDO $dbConnect;

    /**
     * table name
     *
     * @var string
     */
    protected string $table;

    /**
     * name of object
     *
     * @var string
     */
    public string $object;


    /**
     * __construct
     *
     * @param  string                $table      Name of the table to query database
     * @param  string                $objectName Name of object to return information
     * @param  array<string, string> $datasource Database connection informations
     * @return void
     */
    public function __construct(string $table, string $objectName, array $datasource)
    {
        $this->table = $table;
        $this->object = $objectName;
        $this->dbConnect = PDOConnection::getInstance($datasource);

    }//end __construct


    /**
     * getById : get all datas of specified id of object
     *
     * @param  int $id Id of object to search
     * @return object
     */
    public function getById($id): object
    {
        $sql = <<<SQL
                SELECT *
                FROM $this->table
                WHERE id = ?
        SQL;
        $query = $this->dbConnect->prepare($sql);
        $query->setFetchMode(\PDO::FETCH_CLASS, $this->object);
        $query->execute([$id]);
        return $query->fetch();
    }


    /**
     * getAllFilteredByParam : get all datas of filtered of objects
     *
     * @param  string     $paramItem  Name of field to filter
     * @param  int|string $paramValue Value of field to filter
     * @return array<object>
     */
    public function getAllFilteredByParam(string $paramItem, string|int $paramValue ): array
    {
        $sql = <<<SQL
                SELECT *
                FROM $this->table
                WHERE $paramItem = $paramValue
        SQL;
        $query = $this->dbConnect->prepare($sql);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_CLASS, $this->object);
    }


    /**
     * getAll : get all data from called object
     *
     * @return array<object>
     */
    public function getAll(): array
    {
        $sql = <<<SQL
                SELECT * FROM $this->table
        SQL;
        $query = $this->dbConnect->prepare($sql);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_CLASS, $this->object);
    }


    /**
     * getAllOneField : collect all information on the field
     *
     * @param  string $field Name of field to list
     * @return array<string, string>
     */
    public function getAllToList(string $field): array
    {
        $sql = <<<SQL
                SELECT id, $field FROM $this->table
        SQL;
        $query = $this->dbConnect->prepare($sql);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }


    /**
     * getAllPublish: get all published object
     *
     * @return array<object>
     */
    public function getAllPublish(): array
    {
        $sql = <<<SQL
                SELECT * FROM $this->table
                WHERE publish_state = true
        SQL;
        $query = $this->dbConnect->prepare($sql);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_CLASS, $this->object);
    }


    /**
     * getAllOrderLimit : get paged Posts
     *
     * @param null|string                          $field  Name of field to order
     * @param null|string                          $dir    Direction of order
     * @param null|int                             $limit  Number of posts by page
     * @param null|int                             $page   Current page
     * @param null|array<string, int|string|bool > $params Differents parameters for WHERE clause
     *
     * @return array<object>
     */
    public function getAllOrderLimit(?string $field, ?string $dir, ?int $limit, ?int $page, ?array $params): array
    {
        $sql = <<<SQL
            SELECT * FROM  $this->table
        SQL;
        if (!empty($params)) {
            $sql .= <<<SQL
                WHERE
            SQL;
            $i = false;
            foreach ($params as $k => $value) {
                if ($i === true) {
                    $sql .= <<<SQL
                        AND
                    SQL;
                }
                $sql .= <<<SQL
                    $k = $value
                SQL;
                $i = true;
            }
        }//end if

        if (isset($field)) {
            $sql .= <<<SQL
                ORDER BY $field
            SQL;
        }
        if (in_array($dir, ['ASC', 'DESC'])) {
            $sql .= <<<SQL
                $dir
            SQL;
        } else {
            $sql .= <<<SQL
                DESC
            SQL;
        }
        if (isset($limit)) {
            $sql .= <<<SQL
                LIMIT $limit
            SQL;
            if (isset($page) && $page !== 1) {
                $offset = ($page - 1) * $limit;
                $sql .= <<<SQL
                    OFFSET $offset
                SQL;
            }
        }//end if
       
        $query = $this->dbConnect->prepare($sql);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_CLASS, $this->object);
    }


    /**
     * getAllOrderLimitCat : get paged Posts about specifical category
     *
     * @param  null|string                 $field  Name of field to order
     * @param  null|string                 $dir    Direction of order
     * @param  null|int                    $limit  Number of posts by page
     * @param  null|int                    $page   Current page
     * @param  array<string, string|bool > $params Differents parameters for WHERE clause
     * @param  null|int                    $listId Id of List item to filter
     * @return array<object>
     */
    public function getAllOrderLimitCat(?string $field, ?string $dir, ?int $limit, ?int $page, ?array $params, ?int $listId): array
    {

        $sql = <<<SQL
            SELECT $this->table.* FROM $this->table
        SQL;
        switch ($this->table) {
        case 'post':
            $sql .= <<<SQL
                INNER JOIN post_category pc ON pc.post_id = post.id
            SQL;
            break;
        case 'user':
            $sql .= <<<SQL
                INNER JOIN role ON role.id = user.role_id
            SQL;
            break;
        case 'comment':
            break;
        }


        if (!empty($params) === true) {
            $sql .= <<<SQL
                WHERE
            SQL;
            $i = false;
            foreach ($params as $k => $value) {
                if ($i === true) {
                    $sql .= <<<SQL
                        AND
                    SQL;
                }
                $sql .= <<<SQL
                    $k = $value
                SQL;
                $i = true;
            }
        }
        switch ($this->table) {
        case 'post':
            if ($listId !== null) {
                $sql .= <<<SQL
                    AND pc.category_id = $listId
                SQL;
            }
            break;
        case 'user':
            if ($listId !== null) {
                $sql .= <<<SQL
                    AND role.id = $listId
                SQL;
            }
            break;
        case 'comment':
            break;
        }


        if (isset($field)) {
            $sql .= <<<SQL
                ORDER BY $field
            SQL;
        }

        if (in_array($dir, ['ASC', 'DESC'])) {
            $sql .= <<<SQL
                $dir
            SQL;
        } else {
            $sql .= <<<SQL
                DESC
            SQL;
        }

        if (isset($limit)) {
            $sql .= <<<SQL
                LIMIT $limit
            SQL;
            if (isset($page) && $page !== 1) {
                $offset = ($page - 1) * $limit;
                $sql .= <<<SQL
                    OFFSET $offset
                SQL;
            }

        }

        $query = $this->dbConnect->prepare($sql);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_CLASS, $this->object);
    }


    /**
     * insert : insert data in database
     *
     * @param  object                $obj
     * @param  array<string, string> $param
     * @return void
     * @throws PropertyNotFoundException
     */
    public function insert(object $obj, array $param): void
    {
        $paramNumber = count($param);
        $valueArray = array_fill(1, $paramNumber, "?");
        $paramValue = implode(", ", $param);
        $valueString = implode(", ", $valueArray);
        $sql = <<<SQL
            INSERT INTO $this->table ($paramValue) VALUES($valueString)
        SQL;
        $req = $this->dbConnect->prepare($sql);
        $boundParam = array();
        foreach ($param as $paramName) {
            if (property_exists($obj, $paramName)) {
                $boundParam[$paramName] = $obj->$paramName;
            } else {
                throw new PropertyNotFoundException($this->object);
            }
        }
        $req->execute($boundParam);
    }


    /**
     * update : update data of an object
     *
     * @param  object               $obj
     * @param  array<string,string> $param
     * @return void
     * @throws PropertyNotFoundException
     */
    public function update(object $obj, array $param): void
    {

        $sql = <<<SQL
            UPDATE $this->table SET
        SQL;
        $countParam = count($param);
        $i = 0;
        foreach ($param as $paramName => $paramValue) {
            $i++;
            if ($paramName !== 'id') {
                $sql .= <<<SQL
                    Text::camelCaseToSnakeCase($paramName) = :Text::camelCaseToSnakeCase($paramName)
                SQL;
            }

            if ($i !== $countParam) {
                $sql = $sql .", ";
            }

        }//end foreach

        $sql .= <<<SQL
            WHERE id = :id
        SQL;
        $req = $this->dbConnect->prepare($sql);
        $param['id'] = $obj->getId();
        $boundParam = [];
        foreach ($param as $paramName => $paramValue) {
            if (property_exists($obj, $paramName)) {
                $boundParam[Text::camelCaseToSnakeCase($paramName)] = $paramValue;
            } else {
                throw new PropertyNotFoundException($this->object);
            }

        }//end foreach

        $req->execute($boundParam);
    }


    /**
     * delete : delete data of id
     *
     * @param  int $id Id of item to delete
     * @return bool
     */
    public function delete(int $id): bool
    {
        try {
            $sql = <<<SQL
                DELETE FROM $this->table WHERE id= ?
            SQL;
            $query = $this->dbConnect->prepare($sql);
            $query->execute([$id]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }


    /**
     * [unpublish] : unpublish post
     *
     * @param  int $id Post id
     * @return void
     */
    public function unpublish(int $id): void
    {
        $sql = <<<SQL
             UPDATE $this->table
            SET publish_state = false
            WHERE id = :id
        SQL;
        $query = $this->dbConnect->prepare($sql);
        $query->setFetchMode(PDO::FETCH_DEFAULT);
        $query->bindParam(':id', $id);
        $query->execute();
    }


    /**
     * [publish] : publish post
     *
     * @param  int $id Post id
     * @return void
     */
    public function publish(int $id): void
    {
        $sql = <<<SQL
            UPDATE $this->table
            SET publish_state = true,
                publish_at = :publish_at
            WHERE id = :id
        SQL;
        $query = $this->dbConnect->prepare($sql);
        $now = (new DateTime('now'))->format('Y-m-d H:i:s');
        $query->setFetchMode(PDO::FETCH_DEFAULT);
        $query->bindParam(':id', $id);
        $query->bindParam(':publish_at', $now);
        $query->execute();
    }

}
