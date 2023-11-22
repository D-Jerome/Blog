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
    /**
     * loginAuth : Verifiy password match
     *
     * @return void
     */
    public function loginAuth(): void
    {
        $users = (new UserManager(Application::getDatasource()));
        $user = $users->getByUsername($this->getRoute()->getParams()['login']);

        if (null === ($user)) {
            $user = [];
            $this->view('frontoffice/login.html.twig', ['baseUrl' => Application::getBaseUrl(), 'message' => true, 'error' => true, 'login' => false, 'authUser' => $user]);
            exit;
        }

        if (false === ($user->getActive())) {
            $user = [];
            $this->view('frontoffice/login.html.twig', ['baseUrl' => Application::getBaseUrl(), 'message' => true, 'error' => true, 'login' => true, 'authUser' => $user]);
            exit;
        }

        if (password_verify($this->getRoute()->getParams()['password'], $user->getPassword())) {
            //     si ok : Mise en place de session de connexion pour l'utilisateur
            $user->setRoleName($users->getRoleById($user->getRoleId()));
            $this->session->connect($user);
            header('Location: '. Application::getBaseUrl() .'/admin/logged');

        } else {
            $this->view('frontoffice/login.html.twig', ['baseUrl' => Application::getBaseUrl(), 'message' => true, 'error' => true, 'login' => false, 'authUser' => $user]);
        }//end if

    }


    /**
     * login: show login form
     *
     * @return void
     */
    public function login()
    {
        $userSession = $this->session->getUser();

        $user = $userSession ? $userSession->getAllUserInfo() : null;
        if ($user !== null){
            header('Location: '. Application::getBaseUrl() .'/admin/logged');
        }
        //afficher page de connection

        $this->view('frontoffice/login.html.twig', ['baseUrl' => Application::getBaseUrl(), 'message' => false, 'error' => false, 'authUser' => $user]);
    }


    /**
     * signUp : show sign up form
     *
     * @return void
     */
    public function signUp()
    {
        $user = null;
        $userSession = $this->session->getUser();
        if (null !== $userSession) {
            $user = $userSession->getAllUserInfo();
        }
        $this->view('frontoffice/signup.html.twig', ['baseUrl' => Application::getBaseUrl(), 'error' => false, 'authUser' => $user]);
    }


    /**
     * validationSignUp : Verify information of sign up
     *
     * @return void
     */
    public function validationSignUp()
    {

        try
        {
            $error = FALSE;
            $postdatas = (new Request('blog-project'))->getParams();
            foreach ($postdatas as $k => $data) {
                if (empty($data)) {
                    $error = TRUE;
                    throw new UnauthorizeValueException();
                    // die("valeurs non authorisées");
                    //throw Exception;
                }

                if (str_contains($k, "password") && !preg_match("|^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$|", $data)) {
                    // erreur
                    $error = TRUE;
                    throw new PasswordPolicyException();
                    // die("le mot de passe ne correspond pas à la politique de mot de passe ");
                }

            }
            $users = new UserManager(Application::getDatasource());

            if ($users->getByUsername($postdatas['username'])) {
                $error = TRUE;
                // die("l'identifiant est indisponible");
            }

            if ($postdatas['password'] !== $postdatas['confirmPassword']) {
                $error = TRUE;
                // die("les mots de passe sont différents ");
            }

            if ($error == TRUE) {
                unset($postdatas['password']);
                unset($postdatas['confirmPassword']);
                $this->view('frontoffice/signup.html.twig', ['error' => true, 'data' => $postdatas]);
            } else {
                $users->insertNewUser($postdatas);
                $mail = new Mail(Application::getEmailSource());
                $mail->sendMailToUser($users->getByUsername($postdatas['username']));
                header('Location: '. Application::getBaseUrl() .'/');
            }//end if

        }catch (UnauthorizeValueException $e){

        }

    }


    /**
     * logout : Destroy session
     *
     * @return void
     */
    public function logout()
    {
        session_destroy();
        header('Location: '. Application::getBaseUrl() .'/');
    }


    /**
     * forgetPwd : show for to obtain connection information
     *
     * @return void
     */
    public function forgetPwd()
    {
        $this->view('frontoffice/forget.pwd.html.twig', ['baseUrl' => Application::getBaseUrl(), 'error' => false]);
    }


    /**
     * sendUserConnectionMail
     *
     * @return void
     */
    public function sendUserConnectionMail()
    {
        $this->view('frontoffice/home.html.twig', ['baseUrl' => Application::getBaseUrl(), 'error' => false]);
    }

}
