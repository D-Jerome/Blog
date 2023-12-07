<?php

namespace App\Controller;

use App\Model\Manager\UserManager;
use Exception;
use Framework\{Application, Mail, Request, HttpParams};
use Framework\BaseController;
use Framework\Exception\PasswordPolicyException;
use Framework\Exception\UnauthorizeValueException;
use Webmozart\Assert\Assert;

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
        $paramsPost = (new HttpParams())->getParamsPost();
        if (isset($paramsPost['login']) && is_string($paramsPost['login'])) {
            $user = $users->getByUsername($paramsPost['login']);
        }
        if (isset($user) === false) {
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
        if (isset($paramsPost['password']) && is_string($paramsPost['password'])) {
            if (password_verify($paramsPost['password'], $user->getPassword())) {
                //     si ok : Mise en place de session de connexion pour l'utilisateur
                $user->setRoleName($users->getRoleById($user->getRoleId()));
                $this->session->connect($user);
                header('Location: ' . Application::getBaseUrl() . '/admin/logged');
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
    }


    /**
     * login: show login form
     *
     * @return void
     */
    public function login()
    {
        $userSession = $this->session->getUser();

        $user = $userSession instanceof \Framework\Security\AuthUser ? $userSession->getAllUserInfo() : null;
        if ($user !== null) {
            header('Location: ' . Application::getBaseUrl() . '/admin/logged');
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
        if ($userSession instanceof \Framework\Security\AuthUser) {
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
            $postdatas = (new HttpParams())->getParamsPost();
            Assert::isArray($postdatas);
            foreach ($postdatas as $k => $data) {
                if (empty($data)) {
                    $message = "Formulaire Vide";
                    $error = true;
                }
                Assert::string($data);
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
            if (is_string($postdatas['username'])) {
                if ($users->getByUsername($postdatas['username']) instanceof \App\Model\Entities\User) {
                    $message = "Identifiant déjà utilisé";
                    $error = true;
                }

                if ($postdatas['password'] !== $postdatas['confirmPassword']) {
                    $message = "Les mots de passes ne sont pas identiques";
                    $error = true;
                }

                if ($error) {
                    unset($postdatas['password']);
                    unset($postdatas['confirmPassword']);
                    $this->view('frontoffice/signup.html.twig', ['message' => $message, 'error' => true, 'data' => $postdatas]);
                } else {
                    $users->insertNewUser($postdatas);
                    $mail = new Mail(Application::getEmailSource());
                    $mail->sendMailToUser($users->getByUsername($postdatas['username']));
                    header('Location: ' . Application::getBaseUrl() . '/');
                }//end if
            }// end if
        } catch (UnauthorizeValueException) {
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
        header('Location: ' . Application::getBaseUrl() . '/');
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
        $postDatas = (new HttpParams())->getParamsPost();
        if (isset($postDatas['email']) && is_string($postDatas['email'])) {
            $email = (string)filter_var($postDatas['email'], FILTER_SANITIZE_EMAIL);
            $mail = new Mail(Application::getEmailSource());
            $users = UserManager::getUserInstance(Application::getDatasource());
            $userInfo = $users->getByUserEmail($email);
            if (isset(($userInfo)['email']) === false) {
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
                if ($mail->sendMailToUser($userInfo)) {
                    $this->view(
                        'frontoffice/forget.pwd.html.twig',
                        [
                        'baseUrl' => Application::getBaseUrl(),
                        'message' => '<strong>Email envoyé</strong><br>
                                    Un email de connexion vous a été envoyé.',
                        'error' => false,
                        ]
                    );
                } else {
                    $this->view(
                        'frontoffice/forget.pwd.html.twig',
                        [
                        'baseUrl' => Application::getBaseUrl(),
                        'message' => '<strong>Email non envoyé</strong><br>
                                    Un problème est survenu. Rééssayez plus tard.',
                        'error' => true,
                        ]
                    );
                }//endif
            }//endif
        }// end if
    }
}
