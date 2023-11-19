<?php

namespace Framework\Security;

class AuthUser
{

    /**
     * user id of connected user
     *
     * @var int
     */
    protected int $id;

    /**
     * role name of connected user
     *
     * @var string
     */
    protected string $roleName;

    /**
     * username of connected user
     *
     * @var string
     */
    protected string $username;

    /**
     * __construct keep auth user information
     *
     * @param int $id
     * @param string $roleName
     * @param string $username
     * @return void
     */
    public function __construct(int $id, string $roleName, string $username)
    {
        $this->id = $id;
        $this->roleName = $roleName;
        $this->username = $username;
    }//end __construct


    /**
     * getId
     *
     * @return int
     */
    public function getId(): int
    {
        return  $this->id;
    }


    /**
     * getRoleName
     *
     * @return string
     */
    public function getRoleName(): string
    {
        return  $this->roleName;
    }


    /**
     * getUsername
     *
     * @return string
     */
    public function getUsername(): string
    {
        return  $this->username;
    }

    /**
     * get id, name, role of connected user
     *
     * @return array<string, string>
     *
     */
    public function getAllUserInfo(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->username,
            'roleName' => $this->roleName
        ];
    }
}
