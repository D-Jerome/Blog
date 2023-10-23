<?php

namespace App\Controller;


use App\Model\Manager\UserManager;
use Exception;
use Framework\{Application, Request, Session};
use Framework\BaseController;

class User extends BaseController
{

    public function loginAuth()
    {
        $users = (new UserManager(Application::getDatasource()));
        $user = $users->getByUsername($_POST['login']);
       
        if (null === ($user)) {
            $user = [
                'name'=> Session::getSessionByKey('authName'),
                'id'=> Session::getSessionByKey('auth')
            ];
            return $this->view('login.html.twig', ['error' => true, 'login' => false , 'authUser' => $user]);
            
        }
            // Verifier si le mot de passe correspond a l'utilisateur
            //     si ok : Mise en place de session de connexion pour l'utilisateur
            //     si nok : renvoi sur page de login avec message d'erreur
            // verification du role pour connexion à l'administration>


            // Verifier si le mot de passe correspond a l'utilisateur
         
        if (password_verify($_POST['password'], $user->password) ) {
            //     si ok : Mise en place de session de connexion pour l'utilisateur
            Session::setSessionValue('auth', $user->getId()) ;
            Session::setSessionValue('role', $user->getRoleId());
            Session::setSessionValue('authName', $user->getUsername());
            Session::setSessionValue('roleName', ($users->getRoleById($user->getRoleId()))->getRole());

            $user->roleName = ($users->getRoleById($user->getRoleId()))->getRole();
            
            header('Location: /blog-project/admin/logged');

            $user = [
                'name'=> Session::getSessionByKey('authName'),
                'id'=> Session::getSessionByKey('auth')
            ];
            //     si nok : renvoi sur page de login avec message d'erreur
            
        }else{
            return $this->view('login.html.twig', ['error' => true, 'login' => false, 'authUser' => $user ]);
        }  

        
       
    }

    public function login()
    {

        if (Session::checkSessionKey('auth')){
            header('Location: /blog-project/admin/logged');
        }
        $user = [
            'name'=> Session::getSessionByKey('authName'),
            'id'=> Session::getSessionByKey('auth')
        ];    //afficher page de connection
        
        $this->view('login.html.twig', ['error' => false, 'authUser' => $user]);
    }

    public function signUp()
    {
        $user = [
            'name'=> Session::getSessionByKey('authName'),
            'id'=> Session::getSessionByKey('auth')
        ];
        
        $this->view('signup.html.twig', ['error' => false, 'authUser' => $user]);
    }
    
    public function validationSignUp()
    {
        $error = false;
        $postdatas = (new Request('blog-project'))->getParams();
        foreach ($postdatas as $k => $data){
            if (null === $data){
                $error = true;
                //throw Exception;
            }
            
            if (str_contains($k,"password") && !preg_match("|^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[-+!*$@%_])([-+!*$@%_\w]{8,})$|", $data)){
                // erreur
                $error = true;
            }
        }
        $users = new UserManager(Application::getDatasource());
       
        if ($users->getByUsername($postdatas['username'])){
            $error = true;
        }
        
        if ($postdatas['password'] !==$postdatas['confirmPassword']){
            $error = true;

        }
        
        if ($error){
            unset($postdatas['password']);
            unset($postdatas['confirmPassword']);
            $this->view('signup.html.twig', ['error' => true, 'data' => $postdatas]);
        }else{
            $users->insertNewUser($postdatas);
            header('Location: /blog-project/');
        }
    }

    public function logout()
    {
        session_destroy();

        header('Location: /blog-project/');
    }
}
