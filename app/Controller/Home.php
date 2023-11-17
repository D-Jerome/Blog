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
    public function home()
    {
        //recherche des 3 derniers articles par catégories


        $user = $this->session->getUser();
        if (null !== $user) {
            $user = [
                        'name' => $user->getUsername(),
                        'id' => $user->getId()
                    ];
            return $this->view('frontoffice/home.html.twig', [  'authUser' => $user]);
        }

        if (null === $user) {
             return $this->view('frontoffice/home.html.twig', ['error' => false]);
        }

    }


    /**
     * homeContact
     *
     * @return void
     */
    public function homeContact(): void
    {
        $error = FALSE;
        $postdatas = (new Request('blog-project'))->getParams();
        foreach ($postdatas as $k => $data) {
            if (empty($data)) {
                $error = TRUE;
                throw new UnauthorizeValueException();
                //die("valeurs non authorisées");

            }
        }
        $mail = new Mail(Application::getEmailSource());
        $mail->sendMailToAdmin($postdatas);
        header('Location: /blog-project/');
    }


}
