<?php

namespace App\Model\Entities;

use DateTime;

class User extends Entity
{

	protected int $id;

	protected string $firstname;

	protected string $lastname;

	protected string $username;

	protected string $email;

	protected string $password;

	protected string $createdAt;

	protected int $roleId;

	protected string $roleName;

	protected bool $active;


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
	 * getFirstname
	 *
	 * @return string
	 */
	public function getFirstname(): string
	{
		return $this->firstname;
	}


	/**
	 * getLastname
	 *
	 * @return string
	 */
	public function getLastname(): string
	{
		return $this->lastname;
	}


	/**
	 * getUsername
	 *
	 * @return string
	 */
	public function getUsername(): string
	{
		return $this->username;
	}


	/**
	 * getEmail
	 *
	 * @return string
	 */
	public function getEmail(): string
	{
		return $this->email;
	}


	/**
	 * getPassword
	 *
	 * @return string
	 */
	public function getPassword(): string
	{
		return $this->password;
	}


	/**
	 * getCreatedAt
	 *
	 * @return string
	 */
	public function getCreatedAt(): string
	{
		return $this->createdAt;
	}


	/**
	 * getRoleId
	 *
	 * @return int
	 */
	public function getRoleId(): int
	{
		return $this->roleId;
	}


	/**
	 * getRoleName
	 *
	 * @return string
	 */
	public function getRoleName(): string
	{
		return $this->roleName;
	}


	/**
	 * getActive
	 *
	 * @return bool
	 */
	public function getActive(): bool
	{
		return $this->active;
	}


}
