<?php

namespace App\Controller;


use App\Model\Manager\UserManager;
use Framework\{Application, Session};
use Framework\BaseController;

class User extends BaseController
{

    public function loginAuth()
    {
   
        
        $users = (new UserManager(Application::getDatasource()));
        $user = $users->getByUsername($_POST['login']);
        
        if (null === ($user)) {
            echo 'toto';
            return $this->view('login.html.twig', ['error' => true, 'login' => false]);
            
        }
            // Verifier si le mot de passe correspond a l'utilisateur
            //     si ok : Mise en place de session de connexion pour l'utilisateur
            //     si nok : renvoi sur page de login avec message d'erreur
            // verification du role pour connexion à l'administration>


            // Verifier si le mot de passe correspond a l'utilisateur
         
        if (password_verify($_POST['password'], $user->password) ) {
            //     si ok : Mise en place de session de connexion pour l'utilisateur
            Session::setSessionValue('auth', $user->getId()) ;
            Session::setSessionValue('role', $user->getRole());
            
            $user->roleName = ($users->getRoleById($user->getRole()))->getRole();
            dd($_SESSION);
            switch ($user->getRoleName()) {
                case 'admin':
                    return $this->view('admin.panel.html.twig', ['login' => true, 'user' => $user]);
                    
                case 'editor':
                    return $this->view('editor.panel.html.twig', ['login' => true , 'user' => $user]);
                  
                case 'visitor':
                    return $this->view('comment.panel.html.twig', ['login' => true , 'user' => $user]);
                   
            }


            //     si nok : renvoi sur page de login avec message d'erreur
            
        }else{
            return $this->view('login.html.twig', ['error' => true, 'login' => false]);
        }  

        
       
    }

    public function login()
    {

        if (Session::checkSessionKey('auth')){
            header('Location: /blog-project/admin?auth=1');
        }
            //afficher page de connection
        
        $this->view('login.html.twig', ['error' => false]);
    }

    public function logout()
    {
        session_destroy();
    }
}
