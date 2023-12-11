<?php

namespace App\Controller;

use App\Model\Category;
use App\Model\Manager\CategoryManager;
use App\Model\Manager\PostManager;
use Framework\{Application,Config};
use Framework\BaseController;
use Framework\Exception\{UnauthorizeValueException,InvalidUserException};
use Framework\Mail;
use Framework\{Request,HttpParams};
use Framework\Session;
use Webmozart\Assert\Assert;

class Home extends BaseController
{
    /**
     * home
     *
     * @return void
     */
    public function home(): void
    {
        //recherche des 3 derniers articles par catégories
        $userSession = $this->session->getUser();
        $user = $userSession instanceof \Framework\Security\AuthUser ? $userSession->getAllUserInfo() : null;
        $err = (new HttpParams())->getParamsGet();

        if (!isset($err['auth'])) {
            if (null === $user) {
                $this->view('frontoffice/home.html.twig', ['baseUrl' => Config::getBaseUrl(), 'error' => false]);
                exit;
            }

            $this->view('frontoffice/home.html.twig', [  'baseUrl' => Config::getBaseUrl(), 'authUser' => $user]);
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
     *
     * @return void
     */
    public function homeContact(): void
    {
        $error = false;
        $dataPost = [];
        $postdatas = (new HttpParams())->getParamsPost();
        Assert::isArray($postdatas);
        foreach ($postdatas as $key => $data) {
            Assert::notEmpty($data);
            Assert::string($key);
            Assert::notNull($data);
            Assert::string($data);
            $dataPost[$key] = htmlentities($data);
        }

        $mail = new Mail(Config::getEmailSource());
        if ($mail->sendMailToAdmin($dataPost)) {
            $this->view(
                'frontoffice/home.html.twig',
                [  'baseUrl' => Config::getBaseUrl(), 'message' => '<strong>Envoi réussi</strong><br>
            L\'envoi du message a été éffectué.', 'error' => false ]
            );
        } else {
            $this->view(
                'frontoffice/home.html.twig',
                [  'baseUrl' => Config::getBaseUrl(), 'message' => '<strong>Envoi a echoué</strong><br>
            L\'envoi du message a échoué.<br>Rééssayez plus tard.', 'error' => true ]
            );
        }
    }
}
