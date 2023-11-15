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

    public function __construct(string $table, $objectName, $datasource)
    {

        $this->table = $table;
        $this->object = $objectName;
        $this->dbConnect = PDOConnection::getInstance($datasource);
    }

    public function getById($id)
    {
        //don't work
        // $query = $this->dbConnect->prepare('SELECT * FROM  $this->table WHERE id =?');
        // $query->execute(array($id));

        $query = $this->dbConnect->prepare('SELECT * FROM ' . $this->table . ' WHERE id = ?');
        $query->setFetchMode(\PDO::FETCH_CLASS, $this->object);
        $query->execute([$id]);
        return $query->fetch();
    }

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
        $query = $this->dbConnect->prepare("SELECT $field FROM " . $this->table);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }


    public function getAllPublish()
    {
        $query = $this->dbConnect->prepare("SELECT * FROM " . $this->table ." WHERE publish_state = true");
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_CLASS, $this->object);
    }

    public function getAllOrderLimit(?string $field, ?string $dir, ?int $limit, ?int $page, ?array $params)
    {
        $sql = 'SELECT * FROM ' . $this->table;
        if (!empty($params)) {
            $sql .= ' WHERE ';
            $i = 0;
            foreach ($params as $k => $value){
                if ($i !== 0 ){
                    $sql .= ' AND ';
                }
                $sql .= $k .' = '. $value;
            }
        }
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
        }

        $query = $this->dbConnect->prepare($sql);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_CLASS, $this->object);
    }

    public function insert($obj, $param)
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


    public function update($obj, $param)
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
        }
        $sql = $sql . " WHERE id = :id ";
        $req = $this->dbConnect->prepare($sql);
        $param['id'] = $obj->getId() ;
        $boundParam = [];
        foreach ($param as $paramName => $paramValue) {
            if (property_exists($obj, $paramName)) {
                $boundParam[Text::camelCaseToSnakeCase($paramName)] = $paramValue;
            } else {
                throw new PropertyNotFoundException($this->object, $paramName);
            }
        }

        // dd($sql,$boundParam);
        $req->execute($boundParam);
    }

    public function delete($id): bool
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
