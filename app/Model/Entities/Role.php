<?php

namespace App\Model\Entities;

class Role extends Entity
{

	protected ?int $id;

	protected ?string $role;


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
	public function getRole(): string
	{
		return $this->role;
	}


}
