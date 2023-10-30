<?php

namespace Framework\Security;

class AuthUser
{
    protected int $id;
    protected int $role;
    protected string $pseudo;

    public function __construct(int $id, int $role, string $pseudo)
    {
        $this->id = $id;
        $this->role = $role;
        $this->pseudo = $pseudo;
    }

    public function getId(): int
    {
        return  $this->id;
    }

    public function getRole(): int
    {
        return  $this->role;
    }

    public function getPseudo(): string
    {
        return  $this->pseudo;
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
