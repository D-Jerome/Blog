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
        $users = UserManager::getUserInstance(Application::getDatasource());
        $user = $users->getByUsername($this->getRoute()->getParams()['login']);

        if (null === ($user)) {
            $user = [];
            $this->view(
                'frontoffice/login.html.twig',
                [
                'baseUrl' => Application::getBaseUrl(),
                'message' =>  '<strong>Erreur</strong><br>
                    Vérifiez votre Identifiant/Mot de passe.' ,
                'error' => true,
                'authUser' => $user]
            );
            exit;
        }

        if (false === ($user->getActive())) {
            $user = [];
            $this->view(
                'frontoffice/login.html.twig',
                [
                'baseUrl' => Application::getBaseUrl(),
                'message' => '<strong>Erreur</strong><br>
                    Vérifiez votre Identifiant/Mot de passe.',
                'error' => true,
                'authUser' => $user
                ]
            );
            exit;
        }

        if (password_verify($this->getRoute()->getParams()['password'], $user->getPassword())) {
            //     si ok : Mise en place de session de connexion pour l'utilisateur
            $user->setRoleName($users->getRoleById($user->getRoleId()));
            $this->session->connect($user);
            header('Location: '. Application::getBaseUrl() .'/admin/logged');

        } else {
            $this->view(
                'frontoffice/login.html.twig',
                [
                'baseUrl' => Application::getBaseUrl(),
                'message' => '<strong>Erreur</strong><br>
                    Vérifiez votre Identifiant/Mot de passe.',
                'error' => true,
                'authUser' => $user]
            );
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
        if ($user !== null) {
            header('Location: '. Application::getBaseUrl() .'/admin/logged');
        }
        //afficher page de connection

        $this->view(
            'frontoffice/login.html.twig',
            [
            'baseUrl' => Application::getBaseUrl(),
            'authUser' => $user]
        );
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
        $this->view(
            'frontoffice/signup.html.twig',
            [
            'baseUrl' => Application::getBaseUrl(),
            'authUser' => $user]
        );
    }


    /**
     * validationSignUp : Verify information of sign up
     *
     * @return void
     */
    public function validationSignUp()
    {

        try {
            $message = '';
            $error = false;
            $postdatas = (new Request('blog-project'))->getParams();
            foreach ($postdatas as $k => $data) {
                if (empty($data)) {
                    $message = "Formulaire Vide";
                    $error = true;

                }
                if (str_contains($k, "username") && !\Safe\preg_match("|^(\w){8,}$|", $data)) {
                    $message = "<strong>Identifiant impossible</strong><br>Votre identifiant doit comporter plus de 8 caractères(chiffres, minuscules , majuscules et _ uniquement). ";
                    $error = true;

                }

                if (str_contains($k, "password") && !\Safe\preg_match("|^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$|", $data)) {
                    $message = "Mot de passe non sécurisé";
                    $error = true;

                }

            }
            $users = UserManager::getUserInstance(Application::getDatasource());

            if ($users->getByUsername($postdatas['username'])) {
                $message = "Identifiant déjà utilisé";
                $error = true;
            }

            if ($postdatas['password'] !== $postdatas['confirmPassword']) {
                $message = "Les mots de passes ne sont pas identiques";
                $error = true;
            }

            if ($error === true) {
                unset($postdatas['password']);
                unset($postdatas['confirmPassword']);
                $this->view('frontoffice/signup.html.twig', ['message' => $message, 'error' => true, 'data' => $postdatas]);
            } else {
                $users->insertNewUser($postdatas);
                $mail = new Mail(Application::getEmailSource());
                $mail->sendMailToUser($users->getByUsername($postdatas['username']));
                header('Location: '. Application::getBaseUrl() .'/');
            }//end if

        } catch (UnauthorizeValueException $e) {

        }

    }


    /**
     * logout : Destroy session
     *
     * @return void
     */
    public function logout()
    {
        \Safe\session_destroy();
        header('Location: '. Application::getBaseUrl() .'/');
    }


    /**
     * forgetPwd : show for to obtain connection information
     *
     * @return void
     */
    public function forgetPwd()
    {
        $this->view(
            'frontoffice/forget.pwd.html.twig',
            [
            'baseUrl' => Application::getBaseUrl()
            ]
        );
    }


    /**
     * sendUserConnectionMail
     *
     * @return void
     */
    public function sendUserConnectionMail()
    {
        $postDatas = ((new Request(Application::getBaseUrl() .'/'))->getParams());
        $email = filter_var($postDatas['email'], FILTER_SANITIZE_EMAIL);
        $mail = new Mail(Application::getEmailSource());
        $users = UserManager::getUserInstance(Application::getDatasource());
        $userInfo = $users->getByUserEmail($email);
        if ($userInfo === false) {
            $this->view(
                'frontoffice/forget.pwd.html.twig',
                [
                'baseUrl' => Application::getBaseUrl(),
                'message' => '<strong>Utilisateur inconnu</strong><br>
                            Votre email nous est inconnu<br>
                            Merci de vous rapprocher de votre administrateur.',
                'error' => true,
                ]
            );
        } else {
            if ($mail->sendMailToUser($userInfo) === true) {
                $this->view(
                    'frontoffice/forget.pwd.html.twig',
                    [
                    'baseUrl' => Application::getBaseUrl(),
                    'message' => '<h5>Email envoyé</h5><br>
                                Un email de connexion vous a été envoyé.',
                    'error' => false,
                    ]
                );
            } else {
                $this->view(
                    'frontoffice/forget.pwd.html.twig',
                    [
                    'baseUrl' => Application::getBaseUrl(),
                    'message' => '<h5>Email non envoyé</h5><br>
                                Un problème est survenu. Rééssayez plus tard.',
                    'error' => true,
                    'forget' => true,
                    ]
                );
            }//endif

        }//endif

    }

}
