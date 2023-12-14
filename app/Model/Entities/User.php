<?php

declare(strict_types=1);

namespace App\Model\Entities;

class User extends Entity
{
    /**
     * user id
     */
    protected int $id;

    /**
     * firstname information
     */
    protected string $firstname;

    /**
     * lastname information
     */
    protected string $lastname;

    /**
     * username = login
     */
    protected string $username;

    /**
     * email information for forgot password
     */
    protected string $email;

    /**
     * crypted password
     */
    protected string $password;

    /**
     * date of creation of user
     */
    protected string $createdAt;

    /**
     * roleID (link to role.id)
     */
    protected int $roleId;

    /**
     * name of the role
     */
    protected string $roleName;

    /**
     * true = active , false = inactive
     */
    protected bool $active;

    /**
     * getId
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * getFirstname
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * getLastname
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * getUsername
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * getEmail
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * getPassword
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * getCreatedAt
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * getRoleId
     */
    public function getRoleId(): int
    {
        return $this->roleId;
    }

    /**
     * getRoleName
     */
    public function getRoleName(): string
    {
        return $this->roleName;
    }

    /**
     * getActive
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * set Rolename
     */
    public function setRoleName(string $name): void
    {
        $this->roleName = $name;
    }
}
