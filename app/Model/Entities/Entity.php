<?php

namespace App\Model\Entities;

abstract class Entity
{
    

    /* avec le getter qui va bien */
    public function __get($name)
    {
        if (isset($this->$name)) {
            /* et puis là on peut bricoler les données */
            return $this->$name;
        }
    }

    /* après on fait pareil avec le setter */
    public function __set($name, $value)
    {
        
               
        if (property_exists($this,$name)) {
            /* et on peut aussi bricoler les données */
            $this->$name = $value;
        } else if (false !== strpos($name, '_')) {
            /* et traduire le snake_case en camelCase */
            
            $this->__set($this->snakeCaseToCamelCase($name), $value);
        }
    }

    /* et on implémente vite fait mal fait une méthode pour traduire le
       snake_case en camelCase (sérieusement, n’utilisez pas ça) */
    protected function snakeCaseToCamelCase($str)
    {
        $upperCamelCase = str_replace('_', '', ucwords($str, '_'));
        
        return strtolower(substr($upperCamelCase, 0, 1)) . substr($upperCamelCase, 1);
    }

    // public function __isset($property) {
    //     return isset($this->$property);
    //    }
}