<?php

namespace App\Model\Manager;

use Framework\Helpers\Text;
use App\Model\PDOConnection;
use Framework\Exception\PropertyNotFoundException;
use PDO;

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

        $query = $this->dbConnect->prepare('SELECT * FROM '. $this->table .' WHERE id = ?');
        $query->setFetchMode(\PDO::FETCH_CLASS, $this->object);
        $query->execute([$id]);
        return $query->fetch();
    }


    /**
     * getAll : get all data from called object
     *
     * @return array<object>
     */
    public function getAll(): array
    {
        $query = $this->dbConnect->prepare("SELECT * FROM ". $this->table);
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
        $query = $this->dbConnect->prepare('SELECT id, '.($field).' FROM '. ($this->table));
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
        $query = $this->dbConnect->prepare("SELECT * FROM ". $this->table ." WHERE publish_state = true");
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
        $sql = 'SELECT * FROM ' . $this->table;
        if (!empty($params)) {
            $sql .= ' WHERE ';
            $i = false;
            foreach ($params as $k => $value) {
                if ($i === true) {
                    $sql .= ' AND ';
                }
                $sql .= $k .' = '. $value;
                $i = true;
            }
        }//end if

        if (isset($field)) {
            $sql .= ' ORDER BY ' . $field;
        }
        if (in_array($dir, ['ASC', 'DESC'])) {
            $sql .= ' ' . $dir;
        } else {
            $sql .= ' DESC';
        }
        if (isset($limit)) {
            $sql .= ' LIMIT ' . $limit;
            if (isset($page) && $page !== 1) {
                $offset = ($page - 1) * $limit;
                $sql .= ' OFFSET ' .  $offset;
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

        $sql = 'SELECT '. $this->table .'.* FROM '. $this->table;
        switch ($this->table) {
        case 'post':
            $sql .= ' INNER JOIN post_category pc ON pc.post_id = post.id ';
            break;
        case 'user':
            $sql .= ' INNER JOIN role ON role.id = user.role_id ';
            break;
        case 'comment':
            break;
        }


        if (!empty($params) === true) {
            $sql .= ' WHERE ';
            $i = false;
            foreach ($params as $k => $value) {
                if ($i === true) {
                    $sql .= ' AND ';
                }
                $sql .= $k .' = '. $value;
                $i = true;
            }
        }
        switch ($this->table) {
        case 'post':
            if ($listId !== null) {
                $sql .= ' AND pc.category_id = '. $listId;
            }
            break;
        case 'user':
            if ($listId !== null) {
                $sql .= ' AND role.id = '. $listId;
            }
            break;
        case 'comment':
            break;
        }


        if (isset($field)) {
            $sql .= ' ORDER BY '. $field;
        }

        if (in_array($dir, ['ASC', 'DESC'])) {
            $sql .= ' '. $dir;
        } else {
            $sql .= ' DESC';
        }

        if (isset($limit)) {
            $sql .= ' LIMIT '. $limit;
            if (isset($page) && $page !== 1) {
                $offset = ($page - 1) * $limit;
                $sql .= ' OFFSET '.  $offset;
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
        $valueString = implode(", ", $valueArray);
        $sql = "INSERT INTO " . $this->table . "(" . implode(", ", $param) .") VALUES(". $valueString . ")";
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

        $sql = "UPDATE " . $this->table . " SET ";
        $countParam = count($param);
        $i = 0;
        foreach ($param as $paramName => $paramValue) {
            $i++;
            if ($paramName !== 'id') {
                $sql = $sql . Text::camelCaseToSnakeCase($paramName) ." = :". Text::camelCaseToSnakeCase($paramName);
            }

            if ($i !== $countParam) {
                $sql = $sql .", ";
            }

        }//end foreach

        $sql = $sql . " WHERE id = :id ";
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
            $query = $this->dbConnect->prepare("DELETE FROM ". $this->table ." WHERE id= ?");
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
        $query = $this->dbConnect->prepare(
            '
            UPDATE ' . $this->table . '
            SET
                publish_state = false
            WHERE id = :id
        '
        );
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
        $query = $this->dbConnect->prepare(
            '
            UPDATE ' . $this->table . '
            SET
                publish_state = true,
                publish_at = :publish_at
            WHERE id = :id
        '
        );
        $now = (new \DateTime('now'))->format('Y-m-d H:i:s');
        $query->setFetchMode(PDO::FETCH_DEFAULT);
        $query->bindParam(':id', $id);
        $query->bindParam(':publish_at', $now);
        $query->execute();
    }

}
