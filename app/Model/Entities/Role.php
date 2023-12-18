<?php

declare(strict_types=1);

namespace App\Model\Entities;

class Role extends Entity
{
    /**
     * id of role
     */
    protected ?int $id = null;

    /**
     * name of role
     */
    protected ?string $name = null;

    /**
     * getId
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * getRole
     */
    public function getName(): ?string
    {
        return $this->name;
    }
}
