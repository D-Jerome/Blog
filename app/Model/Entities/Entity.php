<?php

namespace App\Model\Entities;

abstract class Entity
{
    /**
     * __get
     *
     * @param  string $name
     * @return string
     */
    public function __get(string $name): string
    {
        if (isset($this->$name)) {
            /* et puis là on peut bricoler les données */
            return $this->$name;
        }
    }


    /**
     * __set
     *
     * @param  string $name
     * @param  mixed  $value Multiple types of values
     * @return void
     */
    public function __set(string $name, mixed $value): void
    {
        if (property_exists($this, $name)) {
            /* et on peut aussi bricoler les données */
            $this->$name = $value;
        } else if (false !== strpos($name, '_')) {
            /* et traduire le snake_case en camelCase */

            $this->__set($this->snakeCaseToCamelCase($name), $value);
        }
    }


    /**
     * snakeCaseToCamelCase
     *
     * @param  string $str
     * @return string
     */
    protected function snakeCaseToCamelCase(string $str): string
    {
        $upperCamelCase = str_replace('_', '', ucwords($str, '_'));

        return strtolower(substr($upperCamelCase, 0, 1)) . substr($upperCamelCase, 1);
    }


}
