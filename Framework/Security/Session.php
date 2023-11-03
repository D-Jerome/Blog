<?php

namespace Framework\Security;

use App\Model\Entities\User;

class Session
{

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function getUser(): ?AuthUser
    {
        return isset($_SESSION['id']) ? new AuthUser($_SESSION['id'], $_SESSION['roleName'], $_SESSION['username']) : null;
    }

    public function connect(User $user)
    {
        
        
        $_SESSION['id'] = $user->getId();
        $_SESSION['roleName'] = $user->getRoleName();
        $_SESSION['username'] = $user->getUsername();

       
    }
}
