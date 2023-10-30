<?php

namespace App\Model\Entities;

class User extends Entity
{
	protected int $id;
	protected string $username;
	protected string $email;
	protected string $password;
	protected $createdAt;
	protected int $roleId;
	protected string $roleName;
	protected bool $active;

	public function getId()
	{
		return $this->id;
	}

	public function getUsername()
	{
		return $this->username;
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function getPassword()
	{
		return $this->password;
	}

	public function getCreatedAt()
	{
		return $this->createdAt;
	}

	public function getRoleId()
	{
		return $this->roleId;
	}

	public function getRoleName()
	{
		return $this->roleName;
	}

	public function getActive()
	{
		return $this->active;
	}
}
