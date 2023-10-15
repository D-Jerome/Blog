<?php

namespace App\Model\Entities;

class User extends Entity
{
	protected int $id;
	protected string $username;
	protected string $password;
	protected $createdAt;
	protected int $roleId;

	public function getId()
	{
		return $this->id;
	}

	public function getUsername()
	{
		return $this->username;
	}

	public function getPassword()
	{
		return $this->password;
	}

	public function getCreatedAt()
	{
		return $this->createdAt;
	}

	public function getRole()
	{
		return $this->roleId;
	}
	
}
