<?php

namespace App\Model\Entities;

class Category extends Entity
{
    /**
     * id of category
     *
     * @var int|null
     */
    protected ?int $id = null;

    /**
     * name of category
     *
     * @var string|null
     */
    protected ?string $name = null;

    /**
     * slug of category
     *
     * @var string|null
     */
    protected ?string $slug = null;


    /**
     * getId
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * getName
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }


    /**
     * getSlug
     *
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }
}
