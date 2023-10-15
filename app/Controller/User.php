<?php

namespace App\Controller;


use App\Model\Manager\UserManager;
use Framework\{Application,Session};
use Framework\BaseController;

class User extends BaseController
{
   
    public function loginAuth()
    {
        
        $user = (new UserManager(Application::getDatasource()))->getByUsername($_POST['login']);
        if (isset($user)){
            
            // Verifier si le mot de passe correspond a l'utilisateur
            //     si ok : Mise en place de session de connexion pour l'utilisateur
            //     si nok : renvoi sur page de login avec message d'erreur
            // verification du role pour connexion à l'administration>

            
            
            
            // Verifier si le mot de passe correspond a l'utilisateur
            if (password_verify($_POST['password'],$user->password)){
             //     si ok : Mise en place de session de connexion pour l'utilisateur
                Session::getSession();
                
                    $_SESSION['auth'] = $user->getId();
                    $_SESSION['role'] = $user->getRole();
                   
                var_dump($_SESSION['auth']);
                $this->view('admin.html.twig', [$user->getUsername(), $user->getCreatedAt()] );
            }else{
                //     si nok : renvoi sur page de login avec message d'erreur
                $this->view('login.html.twig', [] );
            }
        }    
    }

    public function login()
    {
        
        $this->view('login.html.twig', [] );
    }

    public function logout()
    {
        session_destroy();
    }

}