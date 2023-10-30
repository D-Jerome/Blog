<?php

namespace App\Model\Entities;

class Role extends Entity
{
	protected ?int $id;
	protected ?string $role;



	public function getId()
	{
		return $this->id;
	}

	public function getRole()
	{
		return $this->role;
	}
}
