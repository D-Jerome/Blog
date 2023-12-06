<?php

namespace App\Model\Entities;

use DateTime;

class User extends Entity
{
    /**
     * user id
     *
     * @var int
     */
    protected int $id;

    /**
     * firstname information
     *
     * @var string
     */
    protected string $firstname;

    /**
     * lastname information
     *
     * @var string
     */
    protected string $lastname;

    /**
     * username = login
     *
     * @var string
     */
    protected string $username;

    /**
     * email information for forgot password
     *
     * @var string
     */
    protected string $email;

    /**
     * crypted password
     *
     * @var string
     */
    protected string $password;

    /**
     * date of creation of user
     *
     * @var string
     */
    protected string $createdAt;

    /**
     * roleID (link to role.id)
     *
     * @var int
     */
    protected int $roleId;

    /**
     * name of the role
     *
     * @var string
     */
    protected string $roleName;

    /**
     * true = active , false = inactive
     *
     * @var bool
     */
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

    /**
     * set Rolename
     *
     *
     * @return void
     */
    public function setRoleName(string $name):void
    {
        $this->roleName = $name;
    }

}
