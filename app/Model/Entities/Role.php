<?php

namespace App\Model\Entities;

class Role extends Entity
{
    /**
     * id of role
     *
     * @var int|null
     */
    protected ?int $id;

    /**
     * name of role
     *
     * @var string|null
     */
    protected ?string $name;


    /**
     * getId
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }


    /**
     * getRole
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }


}
