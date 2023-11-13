<?php

namespace Framework\Security;

class AuthUser
{

    protected int $id;

    protected string $roleName;

    protected string $username;
    
    /**
     * __construct keep auth user information
     *
     * @param  int $id
     * @param  string $roleName
     * @param  string $username
     * @return void
     */
    public function __construct(int $id, string $roleName, string $username )
    {
        $this->id = $id;
        $this->roleName = $roleName;
        $this->username = $username;
    } //end __construct

        
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

}
