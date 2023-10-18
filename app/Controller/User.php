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
       
            return $this->view('login.html.twig', ['error' => true, 'login' => false , 'user' => Session::getSessionByKey('authName')]);
            
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
            Session::setSessionValue('authName', $user->getUsername());
            Session::setSessionValue('roleName', ($users->getRoleById($user->getRole()))->getRole());

            var_dump(Session::getSessionByKey('authName'));
            $user->roleName = ($users->getRoleById($user->getRole()))->getRole();
            
            switch ($user->getRoleName()) {
                case 'admin':
                    return $this->view('admin.panel.html.twig', ['login' => true, 'user' => Session::getSessionByKey('authName')]);
                    
                case 'editor':
                    return $this->view('editor.panel.html.twig', ['login' => true , 'user' => Session::getSessionByKey('authName')]);
                  
                case 'visitor':
                    return $this->view('comment.panel.html.twig', ['login' => true ,  'user' => Session::getSessionByKey('authName')]);
                   
            }


            //     si nok : renvoi sur page de login avec message d'erreur
            
        }else{
            return $this->view('login.html.twig', ['error' => true, 'login' => false, 'user' => Session::getSessionByKey('authName')]);
        }  

        
       
    }

    public function login()
    {

        if (Session::checkSessionKey('auth')){
            header('Location: /blog-project/logged');
        }
            //afficher page de connection
        
        $this->view('login.html.twig', ['error' => false, 'user' => Session::getSessionByKey('authName')]);
    }

    public function loggedIn()
    {
        $this->view('admin.panel.html.twig', ['error' => false, 'login' => true, 'user' => Session::getSessionByKey('authName')]);
    }

    public function logout()
    {
        session_destroy();
    }
}
