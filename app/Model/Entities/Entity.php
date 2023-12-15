<?php

declare(strict_types=1);

namespace App\Model\Entities;

abstract class Entity
{
    /**
     * __get
     */
    public function __get(int | string $name): null | int | string
    {
        return $this->{$name} ?? null;
    }

    /**
     * __set
     *
     * @param mixed $value Multiple types of values
     */
    public function __set(string $name, mixed $value): void
    {
        if (property_exists($this, $name)) {
            /* et on peut aussi bricoler les données */
            if (!str_contains($name, 'Id') && !str_contains($name, 'count') && (1 === $value || 0 === $value)) {
                $this->{$name} = (bool) $value;
            } else {
                $this->{$name} = $value;
            }
        } elseif (str_contains($name, '_')) {
            /* et traduire le snake_case en camelCase */
            $this->__set($this->snakeCaseToCamelCase($name), $value);
        }
    }

    /**
     * snakeCaseToCamelCase
     */
    protected function snakeCaseToCamelCase(string $str): string
    {
        $upperCamelCase = str_replace('_', '', ucwords($str, '_'));

        return strtolower(substr($upperCamelCase, 0, 1)) . substr($upperCamelCase, 1);
    }
}
