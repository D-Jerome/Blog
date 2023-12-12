<?php

namespace App\Controller;

use App\Model\Manager\UserManager;
use Exception;
use Framework\{Application, Config, Mail, Request, HttpParams};
use Framework\BaseController;
use Framework\Exception\PasswordPolicyException;
use Framework\Exception\UnauthorizeValueException;
use Framework\Security\AuthUser;
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
        $users = UserManager::getUserInstance(Config::getDatasource());
        $paramsPost = (new HttpParams())->getParamsPost();
        if (isset($paramsPost['login']) && is_string($paramsPost['login'])) {
            $user = $users->getByUsername($paramsPost['login']);
        }
        if (isset($user) === false || $user === false) {
            $user = [];
            $this->view(
                'frontoffice/login.html.twig',
                [
                'baseUrl' => Config::getBaseUrl(),
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
                'baseUrl' => Config::getBaseUrl(),
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
                header('Location: ' . Config::getBaseUrl() . '/admin/logged');
            } else {
                $this->view(
                    'frontoffice/login.html.twig',
                    [
                    'baseUrl' => Config::getBaseUrl(),
                    'message' => '<strong>Erreur</strong><br>
                        Vérifiez votre Identifiant/Mot de passe.',
                    'error' => true,
                    'authUser' => $user
                    ]
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
        $user = $this->session->getUser();
        if ($user instanceof \Framework\Security\AuthUser) {
            header('Location: ' . Config::getBaseUrl() . '/admin/logged');
        }
        //afficher page de connection

        $this->view(
            'frontoffice/login.html.twig',
            [
            'baseUrl' => Config::getBaseUrl(),
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
        $user = $this->session->getUser();
        if (!$user instanceof \Framework\Security\AuthUser) {
            $user = null;
        }
        $this->view(
            'frontoffice/signup.html.twig',
            [
            'baseUrl' => Config::getBaseUrl(),
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

            if (is_array($postdatas) === true && array_key_exists('re-email', $postdatas) === true && !empty($postdatas['re-email'])) {
                $error = true;
                $message = "<strong>Une erreur est survenue</strong><br>Veuillez vérifier votre email";
            }
            unset($postdatas['re-email']);
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
            $dataPost = [];
            foreach ($postdatas as $key => $data) {
                Assert::notEmpty($data);
                Assert::string($key);
                Assert::notNull($data);
                Assert::string($data);
                $dataPost[$key] = htmlentities($data);
            }

            $users = UserManager::getUserInstance(Config::getDatasource());
            if (is_string($dataPost['username'])) {
                if ($users->getByUsername($dataPost['username']) instanceof \App\Model\Entities\User) {
                    $message = "Identifiant déjà utilisé";
                    $error = true;
                }

                if ($dataPost['password'] !== $dataPost['confirmPassword']) {
                    $message = "Les mots de passes ne sont pas identiques";
                    $error = true;
                }
            }


            if ($error) {
                unset($dataPost['password']);
                unset($dataPost['confirmPassword']);
                $this->view('frontoffice/signup.html.twig', ['baseUrl' => Config::getBaseUrl(), 'message' => $message, 'error' => true, 'data' => $dataPost]);
            } else {
                $users->insertNewUser($dataPost);
                $mail = new Mail(Config::getEmailSource());
                Assert::isArray($dataPost);
                Assert::notNull($dataPost);
                Assert::notFalse($users->getByUsername($dataPost['username']));
                $mail->sendMailToUser($users->getByUsername($dataPost['username']));
                $message = "Votre compte est créé";
                $this->view('frontoffice/home.html.twig', ['baseUrl' => Config::getBaseUrl(), 'message' => $message, 'error' => false, 'data' => $dataPost]);
            }
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
        header('Location: ' . Config::getBaseUrl() . '/');
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
            'baseUrl' => Config::getBaseUrl()
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
            $mail = new Mail(Config::getEmailSource());
            $users = UserManager::getUserInstance(Config::getDatasource());

            $userInfo = $users->getByUserEmail($email);
            if ($userInfo === false) {
                $this->view(
                    'frontoffice/forget.pwd.html.twig',
                    [
                    'baseUrl' => Config::getBaseUrl(),
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
                        'baseUrl' => Config::getBaseUrl(),
                        'message' => '<strong>Email envoyé</strong><br>
                                    Un email de connexion vous a été envoyé.',
                        'error' => false,
                        ]
                    );
                } else {
                    $this->view(
                        'frontoffice/forget.pwd.html.twig',
                        [
                        'baseUrl' => Config::getBaseUrl(),
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
