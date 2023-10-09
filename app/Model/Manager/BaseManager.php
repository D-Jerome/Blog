<?php

namespace App\Model\Manager;

use App\Model\PDOConnection;
use PropertyNotFoundException;

abstract class BaseManager
{

    protected object $dbConnect;
    private string $table;
    private  $object;

    public function __construct(string $table,  $objectName, $datasource)
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
       
        $query = $this->dbConnect->prepare('SELECT * FROM '. $this->table .' WHERE id = ' . end($id));
        $query->execute();
        $query->setFetchMode(\PDO::FETCH_CLASS, $this->object);
        return $query->fetch();
    }

    public function getAll()
    {      
        $query = $this->dbConnect->prepare("SELECT * FROM " . $this->table);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_CLASS, $this->object);
    }

    public function getAllOrderLimit(?string $field, ?string $dir, ?int $limit , ?int $page)
    {
        $sql= 'SELECT * FROM '. $this->table;
        if (isset($field)){
            $sql .= ' ORDER BY ' . $field;
        }
        if (in_array($dir,['ASC', 'DESC'])){
            $sql .= ' ' . $dir; 
        }else{
            $sql .= ' DESC';
        }
        if (isset($limit)){
            $sql .= ' LIMIT ' . $limit;
            if (isset($page) && $page !== 1 ){
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
        $valueArray = array_fill(1,$paramNumber,"?");
        $valueString = implode(", ",$valueArray);
        $sql = "INSERT INTO " . $this->table . "(" . implode(", ",$param) . ") VALUES(" . $valueString . ")";
        $req = $this->dbConnect->prepare($sql);
        $boundParam = array();
			foreach($param as $paramName)
			{
				if(property_exists($obj,$paramName))
				{
					$boundParam[$paramName] = $obj->$paramName;	
				}
				else
				{
					throw new PropertyNotFoundException($this->object,$paramName);	
				}
			}
			$req->execute($boundParam);
    
    }

    public function update($obj, $param)
    {
        $sql = "UPDATE " . $this->table . " SET ";
        foreach($param as $paramName)
        {
            $sql = $sql . $paramName . " = ?, ";
        }
        $sql = $sql . " WHERE id = ? ";
        $req = $this->dbConnect->prepare($sql);
        
        $param[] = 'id';
        $boundParam = array();
        foreach($param as $paramName)
        {
            if(property_exists($obj,$paramName))
            {
                $boundParam[$paramName] = $obj->$paramName;	
            }
            else
            {
                throw new PropertyNotFoundException($this->object,$paramName);	
            }
        }
        
        $req->execute($boundParam);
    }

    public function delete($obj):bool
    {
        if (property_exists($obj, "id")) {
            $query = $this->dbConnect->prepare("DELETE FROM " . $this->table . " WHERE id=?");
            $query->execute(array($obj->id));
            return true;
        } else {
            return false;
        }
    }
}
