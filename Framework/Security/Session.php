<?php

namespace Framework\Security;

use App\Model\Entities\User;

class Session
{
    
    /**
     * __construct : Start session if not already strated
     *
     * @return void
     */
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }//end __construct()


    /**
     * getUser : get User connected
     *
     * @return AuthUser|null
     */
    public function getUser(): ?AuthUser
    {
        return isset($_SESSION['id']) ? new AuthUser($_SESSION['id'], $_SESSION['roleName'], $_SESSION['username']) : null;
    }


    /**
     * connect : keep information of user un session
     *
     * @param  User $user User connected
     * @return void
     */
    public function connect(User $user)
    {
        $_SESSION['id'] = $user->getId();
        $_SESSION['roleName'] = $user->getRoleName();
        $_SESSION['username'] = $user->getUsername();

    }

    /**
     * Generate new token when call
     *
     * @return void
     *
     */
    public function generateToken(): void
    {
        $_SESSION['token'] =  md5(bin2hex(openssl_random_pseudo_bytes(6)));

    }


    /**
     * getToken
     *
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $_SESSION['token'];
    }

}
