<?php

namespace Framework\Security;

class AuthUser
{
    /**
     * instant Token
     *
     * @var string
     */
    private string $token;


    /**
     * __construct keep auth user information
     *
     *
     * @return void
     */
    public function __construct(
        /**
         * user id of connected user
         */
        protected int $id,
        /**
         * role name of connected user
         */
        protected string $roleName,
        /**
         * username of connected user
         */
        protected string $username
    ) {
    }
    //end __construct


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
     * generate new token
     * get id, name, role, token of connected user
     *
     * @return array<string, int|string>
     */
    public function getAllUserInfo(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->username,
            'roleName' => $this->roleName,
        ];
    }


    /**
     * set Token value
     *
     * @param string $token
     *
     * @return void
     *
     */
    public function setToken(string $token)
    {
        $this->token = $token;
    }

    /**
     * get Token value
     *
     *
     *
     * @return string
     *
     */
    public function getToken()
    {
        return $this->token;
    }
}
