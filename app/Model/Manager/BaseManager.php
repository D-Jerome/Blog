<?php

namespace App\Model\Manager;

use Framework\Helpers\Text;
use App\Model\PDOConnection;
use Framework\Exception\PropertyNotFoundException;

abstract class BaseManager
{
    public object $dbConnect;
    protected string $table;
    public $object;


    /**
     * __construct
     *
     * @param  string $table : name of the table to query database
     * @param  string $objectName : name of object to return information
     * @param  array $datasource : database connection informations
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
     * @param  int $id : id of object to search
     * @return object
     */
    public function getById($id): object
    {

        $query = $this->dbConnect->prepare('SELECT * FROM ' . $this->table . ' WHERE id = ?');
        $query->setFetchMode(\PDO::FETCH_CLASS, $this->object);
        $query->execute([$id]);
        return $query->fetch();
    }


    /**
     * getAll : get all data from called object
     *
     * @return void
     */
    public function getAll()
    {
        $query = $this->dbConnect->prepare("SELECT * FROM " . $this->table);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_CLASS, $this->object);
    }


    /**
     * getAllOneField : collect all information on the field
     *
     * @param  string $field : name of field to list
     * @return array
     */
    public function getAllToList(string $field): array
    {
        $query = $this->dbConnect->prepare("SELECT id, $field FROM " . $this->table);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }


    /**
     * getAllPublish: get all published object
     *
     * @return object
     */
    public function getAllPublish(): object
    {
        $query = $this->dbConnect->prepare("SELECT * FROM " . $this->table ." WHERE publish_state = true");
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_CLASS, $this->object);
    }


    /**
     * getAllOrderLimit : get paged Posts
     *
     * @param  string $field : name of field to order
     * @param  string $dir : direction of order
     * @param  int $limit : number of posts by page
     * @param  int $page : current page
     * @param  array $params : differents parameters for WHERE clause
     *
     * @return Post
     */
    public function getAllOrderLimit(?string $field, ?string $dir, ?int $limit, ?int $page, ?array $params)
    {
        $sql = 'SELECT * FROM ' . $this->table;
        if (!empty($params)) {
            $sql .= ' WHERE ';
            $i = 0;
            foreach ($params as $k => $value) {
                if ($i !== 0) {
                    $sql .= ' AND ';
                }
                $sql .= $k .' = '. $value;
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
     * insert : insert data in database
     *
     * @param  object $obj
     * @param  array $param
     * @return void
     */
    public function insert(object $obj, array $param): void
    {
        $paramNumber = count($param);
        $valueArray = array_fill(1, $paramNumber, "?");
        $valueString = implode(", ", $valueArray);
        $sql = "INSERT INTO " . $this->table . "(" . implode(", ", $param) . ") VALUES(" . $valueString . ")";
        $req = $this->dbConnect->prepare($sql);
        $boundParam = array();
        foreach ($param as $paramName) {
            if (property_exists($obj, $paramName)) {
                $boundParam[$paramName] = $obj->$paramName;
            } else {
                throw new PropertyNotFoundException($this->object, $paramName);
            }
        }
        $req->execute($boundParam);
    }


    /**
     * update : update data of an object
     *
     * @param  object $obj
     * @param  array $param
     * @return void
     */
    public function update(object $obj, array $param): void
    {

        $sql = "UPDATE " . $this->table . " SET ";
        $countParam = count($param);
        $i = 0;
        foreach ($param as $paramName => $paramValue) {
            $i++;
            if ($paramName !== 'id') {
                $sql = $sql . Text::camelCaseToSnakeCase($paramName) . " = :" . Text::camelCaseToSnakeCase($paramName);
            }

            if ($i !== $countParam) {
                $sql = $sql . ", ";
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
                throw new PropertyNotFoundException($this->object, $paramName);
            }

        }//end foreach

        $req->execute($boundParam);
    }


    /**
     * delete : delete data of id
     *
     * @param  int $id : id of item to delete
     * @return bool
     */
    public function delete(int $id): bool
    {
        try {
            $query = $this->dbConnect->prepare("DELETE FROM " . $this->table . " WHERE id= ?");
            $query->execute([$id]);
            return true;
        } catch (\Exception $e) {
            dd($e->getMessage());
            return false;
        }
    }

}
