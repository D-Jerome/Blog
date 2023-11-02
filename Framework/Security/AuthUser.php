<?php

namespace Framework\Security;

class AuthUser
{
    protected int $id;
    protected string $role;
    protected string $username;

    public function __construct(int $id, string $role, string $username )
    {
        $this->id = $id;
        $this->role = $role;
        $this->username = $username;
    }

    public function getId(): int
    {
        return  $this->id;
    }

    public function getRole(): string
    {
        return  $this->role;
    }

    public function getUsername(): string
    {
        return  $this->username;
    }

    public function getRoleName(): string
    {
        return match ($this->role) {
            1 => 'admin',
            2 => 'editor',
            3 => 'visitor'
        };
    }
}
