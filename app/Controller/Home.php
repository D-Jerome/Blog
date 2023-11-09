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
    public function home()
    {
        //recherche des 3 derniers articles par catégories


        $user = $this->session->getUser();
        if (null !== $user) {
            $user = [
                'name' => $user->getUsername(),
                'id' => $user->getId()
            ];
        }

        $user = $this->session->getUser();
        if (null === $user) {
            return $this->view('home.html.twig', ['error' => false]);
        }

        $user = [
            'name' => $user->getUsername(),
            'id' => $user->getId()
        ];


        return $this->view('home.html.twig', [  'authUser' => $user]);
    }

    public function homeContact()
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
        }
        $mail = new Mail(Application::getEmailSource());
        $mail->sendMailToAdmin($postdatas);
        header('Location: /blog-project/');



    }


    public function login()
    {
        echo "je suis la page de login";
    }
}
