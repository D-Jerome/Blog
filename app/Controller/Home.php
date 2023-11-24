<?php

namespace App\Controller;

use App\Model\Category;
use App\Model\Manager\CategoryManager;
use App\Model\Manager\PostManager;
use Framework\Application;
use Framework\BaseController;
use Framework\Exception\UnauthorizeValueException;
use Framework\Mail;
use Framework\Request;
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
        if (null === $user) {
            $this->view('frontoffice/home.html.twig', ['baseUrl' => Application::getBaseUrl(), 'error' => false]);
            exit;
        }

        $this->view('frontoffice/home.html.twig', [  'baseUrl' => Application::getBaseUrl(), 'authUser' => $user]);

    }


    /**
     * homeContact
     *
     * @return void
     */
    public function homeContact(): void
    {
        $error = false;
        $postdatas = (new Request(Application::getBaseUrl() .'/'))->getParams();
        foreach ($postdatas as $data){
            $data = htmlentities($data);
        }
        foreach ($postdatas as $k => $data) {
            if (empty($data)) {
                $error = true;
                throw new UnauthorizeValueException();

            }
        }

        $mail = new Mail(Application::getEmailSource());
        if ($mail->sendMailToAdmin($postdatas)) {
            $this->view('frontoffice/home.html.twig', [  'baseUrl' => Application::getBaseUrl(), 'message' => true, 'error' => false ]);
        }else{
            $this->view('frontoffice/home.html.twig', [  'baseUrl' => Application::getBaseUrl(), 'message' => true, 'error' => true ]);
        }

    }


}
