<?php

namespace App\Controller;

use App\Model\Category;
use App\Model\Manager\CategoryManager;
use App\Model\Manager\PostManager;
use Framework\Application;
use Framework\BaseController;
use Framework\Exception\{UnauthorizeValueException,InvalidUserException};
use Framework\Mail;
use Framework\{Request,HttpParams};
use Framework\Session;

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
        $user = $userSession ? $userSession->getAllUserInfo() : null;
        $err = (new HttpParams())->getParamsGet();

        if(!isset($err['auth'])) {
            if (null === $user) {
                $this->view('frontoffice/home.html.twig', ['baseUrl' => Application::getBaseUrl(), 'error' => false]);
                exit;
            }

            $this->view('frontoffice/home.html.twig', [  'baseUrl' => Application::getBaseUrl(), 'authUser' => $user]);
        } else {
            $this->view(
                'frontoffice/home.html.twig',
                ['baseUrl' => Application::getBaseUrl(), 'message' =>  '<strong>Opération non authorisée</strong><br>
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
        $postdatas = (new HttpParams())->getParamsPost();
        foreach ($postdatas as $data) {
            if (is_string($data)) {
                $data = htmlentities($data);
            }
        }
        foreach ($postdatas as $k => $data) {
            if (empty($data)) {
                $error = true;
                throw new UnauthorizeValueException();

            }
        }

        $mail = new Mail(Application::getEmailSource());
        if ($mail->sendMailToAdmin($postdatas)) {
            $this->view(
                'frontoffice/home.html.twig',
                [  'baseUrl' => Application::getBaseUrl(), 'message' => '<strong>Envoi réussi</strong><br>
            L\'envoi du message a été éffectué.', 'error' => false ]
            );
        } else {
            $this->view(
                'frontoffice/home.html.twig',
                [  'baseUrl' => Application::getBaseUrl(), 'message' => '<strong>Envoi a echoué</strong><br>
            L\'envoi du message a échoué.<br>Rééssayez plus tard.', 'error' => true ]
            );
        }

    }


}
