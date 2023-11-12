<?php

namespace App\Controller;

use App\Model\Manager\UserManager;
use Exception;
use Framework\{Application, Mail, Request};
use Framework\BaseController;
use Framework\Exception\PasswordPolicyException;
use Framework\Exception\UnauthorizeValueException;

class User extends BaseController
{

    public function loginAuth()
    {
        $users = (new UserManager(Application::getDatasource()));
        $user = $users->getByUsername($_POST['login']);

        if (null === ($user)) {
            $user = [];

            return $this->view('frontoffice/login.html.twig', ['error' => true, 'login' => false, 'authUser' => $user]);
        }
        if (false === ($user->getActive())) {
            $user = [];
            return $this->view('frontoffice/login.html.twig', ['error' => true, 'login' => true, 'authUser' => $user]);
        }
        // Verifier si le mot de passe correspond a l'utilisateur
        //     si ok : Mise en place de session de connexion pour l'utilisateur
        //     si nok : renvoi sur page de login avec message d'erreur
        // verification du role pour connexion à l'administration>


        // Verifier si le mot de passe correspond a l'utilisateur

        if (password_verify($_POST['password'], $user->password)) {
            //     si ok : Mise en place de session de connexion pour l'utilisateur
            $user->roleName = ($users->getRoleById($user->getRoleId()))->getRole();
            $this->session->connect($user);
            // Session::setSessionValue('auth', $user->getId()) ;
            // Session::setSessionValue('role', $user->getRoleId());
            // Session::setSessionValue('authName', $user->getUsername());
            // Session::setSessionValue('roleName', ($users->getRoleById($user->getRoleId()))->getRole());



            header('Location: /blog-project/admin/logged');

            $user = [
                'name' => $user->getUsername(),
                'id' => $user->getId(),
                'role' => $user->getRoleName()
            ];
            //     si nok : renvoi sur page de login avec message d'erreur

        } else {
            return $this->view('frontoffice/login.html.twig', ['error' => true, 'login' => false, 'authUser' => $user]);
        }
    }

    public function login()
    {
        $user = $this->session->getUser();
        if (null !== $user) {
            header('Location: /blog-project/admin/logged');
        }
        //afficher page de connection

        $this->view('frontoffice/login.html.twig', ['error' => false, 'authUser' => $user]);
    }

    public function signUp()
    {
        $user = $this->session->getUser();
        if (null !== $user) {
            $user = [

                'username' => $user->getUsername(),
                'id' => $user->getId(),
                'role' => $user->getRoleName()
            ];
        }
        $this->view('frontoffice/signup.html.twig', ['error' => false, 'authUser' => $user]);
    }

    public function validationSignUp()
    {
        $error = false;
        $postdatas = (new Request('blog-project'))->getParams();
        foreach ($postdatas as $k => $data) {
            if (null === $data) {
                throw new UnauthorizeValueException();
                die("valeurs non authorisées");
                $error = true;
                //throw Exception;
            }

            if (str_contains($k, "password") && !preg_match("|^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$|", $data)) {
                // erreur
                throw new PasswordPolicyException();
                die("le mot de passe ne correspond pas à la politique de mot de passe ");
                $error = true;
            }
        }
        $users = new UserManager(Application::getDatasource());

        if ($users->getByUsername($postdatas['username'])) {
            die("l'identifiant est indisponible");
            $error = true;
        }

        if ($postdatas['password'] !== $postdatas['confirmPassword']) {
            die("les mots de passe sont différents ");
            $error = true;
        }

        if ($error) {
            unset($postdatas['password']);
            unset($postdatas['confirmPassword']);
            $this->view('frontoffice/signup.html.twig', ['error' => true, 'data' => $postdatas]);
        } else {
            $users->insertNewUser($postdatas);
            $mail = new Mail(Application::getEmailSource());
            $mail->sendMailToUser($users->getByUsername($postdatas['username']));
            header('Location: /blog-project/');
        }
    }

    public function logout()
    {
        session_destroy();

        header('Location: /blog-project/');
    }

    public function forgetPwd()
    {
        $this->view('frontoffice/forget.pwd.html.twig', ['error' => false]);
    }
    public function sendUserConnectionMail()
    {
        

        $this->view('frontoffice/home.html.twig', ['error' => false]);
    }

}
