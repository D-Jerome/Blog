<?php

namespace Framework\Security;

class AuthUser
{
    protected int $id;
    protected string $roleName;
    protected string $username;

    public function __construct(int $id, string $roleName, string $username )
    {
        $this->id = $id;
        $this->roleName = $roleName;
        $this->username = $username;
    }

    public function getId(): int
    {
        return  $this->id;
    }

    public function getRoleName(): string
    {
        return  $this->roleName;
    }

    public function getUsername(): string
    {
        return  $this->username;
    }

}
