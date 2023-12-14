<?php

declare(strict_types=1);

namespace App\Model\Entities;

class Category extends Entity
{
    /**
     * id of category
     */
    protected ?int $id = null;

    /**
     * name of category
     */
    protected ?string $name = null;

    /**
     * slug of category
     */
    protected ?string $slug = null;

    /**
     * getId
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * getName
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * getSlug
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }
}
