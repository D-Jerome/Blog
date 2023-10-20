<?php

namespace App\Model\Entities;

class Category extends Entity
{
	protected ?int $id;
	protected ?string $name;
	protected ?string $slug;
	

	public function getId()
	{
		return $this->id;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getSlug()
	{
		return $this->slug;
	}

}