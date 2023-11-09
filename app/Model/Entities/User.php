<?php

namespace App\Model\Entities;

class User extends Entity
{
	protected int $id;
	protected string $firstname;
	protected string $lastname;
	protected string $username;
	protected ?string $description;
	protected string $email;
	protected string $password;
	protected $createdAt;
	protected ?string $picture;
	protected ?string $file;
	protected int $roleId;
	protected string $roleName;
	protected bool $active;

	public function getId()
	{
		return $this->id;
	}

	public function getFirstname()
	{
		return $this->firstname;
	}

	public function getLastname()
	{
		return $this->lastname;
	}

	public function getUsername()
	{
		return $this->username;
	}

	public function getDescription()
	{
		return $this->description;
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

	public function picture()
	{
		return $this->picture;
	}

	public function file()
	{
		return $this->file;
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
