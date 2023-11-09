<?php

namespace Framework;

use App\Model\Entities\User;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mail
{
    
    private string $host;                     
    private bool $smtpAuth;                       
    private string $userName;                    
    private string $password;                            
    private string $smtpSecure;     
    private string $port;                            
    private string $fromAddress;
    private ?string $fromName;
    private string $replyToAddress;
    private ?string $replyToName;
    private ?string $ccAddress;
    private ?string $bccAddress;
    

    public function __construct($config)
    {
        foreach ($config as $key => $value){
            $this->$key = $value;
        }

    }
    
    public function sendMail(User $user)
    {
        $mail = new PHPMailer(true);
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      
        $mail->isSMTP();                                            
        $mail->Host       = $this->host;                     
        $mail->SMTPAuth   = $this->smtpAuth;                 
        $mail->Username   = $this->userName;                 
        $mail->Password   = $this->password;                
        $mail->SMTPSecure = $this->smtpSecure;            
        $mail->Port       = $this->port;                     

        //Recipients
        $mail->setFrom($this->fromAddress, $this->fromName);
        $mail->addAddress($user->getEmail(), "$user->getFirstName() $user->lastName()");     
        $mail->addReplyTo($this->replyToAddress, $this->replyToName);
        $mail->addCC($this->ccAddress);
        $mail->addBCC($this->bccAddress);

        //Attachments
        // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Inscription à notre blog';
        $mail->Body    = 'Bienvenue, ' . $user->getFirstname() . ' ' . $user->getLastname() . ' <br> Merci de vous être inscrit sur notre blog.<br><br>Votre identifiant pour votre connexion est : <b>' . $user->getUsername() . '</b> correspondant à votre email .' . $user->getEmail() ;
        $mail->AltBody = 'Bienvenue, ' . $user->getFirstname() . ' ' . $user->getLastname() . ' Merci de vous être inscrit sur notre blog. Votre identifiant pour votre connexion est : ' . $user->getUsername() . ' correspondant à votre email . ' . $user->getEmail() ;
        
        if (!$mail->send()) {
            echo 'Email not sent an error was encountered: ' . $mail->ErrorInfo;
        } else {
            echo 'Message has been sent.';
        }
    }
}
