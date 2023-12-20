<?php

declare(strict_types=1);

namespace App\Controller;

use Framework\BaseController;
use Framework\Config;
use Framework\HttpParams;
use Framework\Mail;
use Webmozart\Assert\Assert;

class Home extends BaseController
{
    /**
     * home
     */
    public function home(): void
    {
        // recherche des 3 derniers articles par catégories
        $user = $this->session->getUser();
        if (!$user instanceof \Framework\Security\AuthUser) {
            $user = null;
        }
        $err = (new HttpParams())->getParamsGet();

        if (!isset($err['auth'])) {
            if (null === $user) {
                $this->view('frontoffice/home.html.twig', ['baseUrl' => Config::getBaseUrl(), 'error' => false]);
            } else {
                $this->view('frontoffice/home.html.twig', ['baseUrl' => Config::getBaseUrl(), 'authUser' => $user]);
            }
        } else {
            $this->view(
                'frontoffice/home.html.twig',
                ['baseUrl' => Config::getBaseUrl(), 'message' =>  '<strong>Opération non authorisée</strong><br>
                Vos droits n\'authorisent pas cette action.' , 'error' => true, 'authUser' => $user]
            );
        }
    }

    /**
     * homeContact
     */
    public function homeContact(): void
    {
        $error = false;
        $message = '';
        $dataPost = [];
        $postdatas = (new HttpParams())->getParamsPost();
        if (true === \is_array($postdatas) && \array_key_exists('re-email', $postdatas) && !empty($postdatas['re-email'])) {
            $error = true;
            $message = '<strong>Une erreur est survenue</strong><br>Veuillez vérifier votre email';
        }
        unset($postdatas['re-email']);
        Assert::isArray($postdatas);
        foreach ($postdatas as $key => $data) {
            Assert::notEmpty($data);
            Assert::string($key);
            Assert::notNull($data);
            Assert::string($data);
            $dataPost[$key] = htmlentities($data);
        }
        $mail = new Mail(Config::getEmailSource());
        if (!$mail->sendMailToAdmin($dataPost)) {
            $error = true;
            $message = "<strong>Envoi a echoué</strong><br>L'envoi du message a échoué.<br>Rééssayez plus tard.";
        }

        if (false === $error) {
            $this->view(
                'frontoffice/home.html.twig',
                ['baseUrl'                                => Config::getBaseUrl(), 'message' => '<strong>Envoi réussi</strong><br>
            L\'envoi du message a été éffectué.', 'error' => false]
            );
        } else {
            $this->view(
                'frontoffice/home.html.twig',
                ['baseUrl' => Config::getBaseUrl(), 'message' => $message, 'error' => true]
            );
        }
    }
}
