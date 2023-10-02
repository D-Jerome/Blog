<?php

namespace Framework;

use Framework\PDOConnection;

class BaseManager
{

    protected object $dbConnect;
    private string $table;
    private string $objectName;

    public function __construct(string $table, string $objectName, $datasource)
    {
        $this->table = $table;
        $this->objectName = $objectName;        
        $this->dbConnect = PDOConnection::getInstance($datasource);
    }

    public function getById(  $id)
    {
        $query = $this->dbConnect->prepare("SELECT * FROM " . $this->table ." WHERE id =?");
        $query->execute(array($id));
        return $query->fetch();
    }

    public function GetAll()
    {
        $query = $this->dbConnect->prepare("SELECT * FROM  $this->table" );
        $query->execute();
        return $query->fetchAll();
    }

    public function create()
    {
    }

    public function insert()
    {
    }

    public function update()
    {
    }

    public function delete($obj)
    {
        if(property_exists($obj,"id")){
            $query= $this->dbConnect->prepare("DELETE FROM " . $this->table . " WHERE id=?");
			return $query->execute(array($obj->id));
        }else{
            throw new ObjectNotFoundException();
        }    

    }
}
