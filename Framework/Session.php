<?php

namespace Framework;

class Session 
{
   public static function getSession()
   {
        if (session_status() === PHP_SESSION_NONE){
            session_start();
        }else{
            echo 'déjà connecté';
            header('Location: /blog-project/');
        }
   }


}