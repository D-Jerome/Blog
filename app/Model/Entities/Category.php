<?php

namespace App\Model\Entities;

class Category extends Entity
{
    /**
     * id of category
     *
     * @var int|null
     */
    protected ?int $id;

    /**
     * name of category
     *
     * @var string|null
     */
    protected ?string $name;

    /**
     * slug of category
     *
     * @var string|null
     */
    protected ?string $slug;


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
