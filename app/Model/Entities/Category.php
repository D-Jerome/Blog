<?php

namespace App\Model\Entities;

class Category extends Entity
{
	protected ?int $id;

	protected ?string $name;

	protected ?string $slug;


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
	 * getName
	 *
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}


	/**
	 * getSlug
	 *
	 * @return string
	 */
	public function getSlug(): string
	{
		return $this->slug;
	}


}
