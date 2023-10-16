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
        if (isset($user)) {

            // Verifier si le mot de passe correspond a l'utilisateur
            //     si ok : Mise en place de session de connexion pour l'utilisateur
            //     si nok : renvoi sur page de login avec message d'erreur
            // verification du role pour connexion à l'administration>




            // Verifier si le mot de passe correspond a l'utilisateur
            if (password_verify($_POST['password'], $user->password)) {
                //     si ok : Mise en place de session de connexion pour l'utilisateur

                $_SESSION['auth'] = $user->getId();
                $_SESSION['role'] = $user->getRole();
                
                $user->roleName = ($users->getRoleById($user->getRole()))->getRole();
                
                switch ($user->getRoleName()) {
                    case 'admin':
                        $this->view('admin.panel.html.twig', []);
                        break;
                    case 'editor':
                        $this->view('editor.panel.html.twig', []);
                        break;
                    case 'visitor':
                        $this->view('comment.panel.html.twig', []);
                        break;
                }
            } else {
                //     si nok : renvoi sur page de login avec message d'erreur
                $this->view('login.html.twig', []);
            }
        }
    }

    public function login()
    {

        $this->view('login.html.twig', []);
    }

    public function logout()
    {
        session_destroy();
    }
}
