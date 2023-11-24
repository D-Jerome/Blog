<?php

namespace Framework;

use App\Model\Entities\User;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mail
{

    /**
     * smtp address
     *
     * @var string
     */
    private string $host;

    /**
     * authentification required(true)
     *
     * @var bool
     */
    private bool $smtpAuth;

    /**
     * login for smtp  connect
     *
     * @var string
     */
    private string $userName;

    /**
     * password for smtp connect
     *
     * @var string
     */
    private string $password;

    /**
     * secure type
     *
     * @var string
     */
    private string $smtpSecure;

    /**
     * port use for connection
     *
     * @var int
     */
    private int $port;

    /**
     * from email address
     *
     * @var string
     */
    private string $fromAddress;

    /**
     * from user name
     *
     * @var string|null
     */
    private ?string $fromName;

    /**
     * ReplyTo Address
     *
     * @var string
     */
    private string $replyToAddress;

    /**
     * ReplyTo Name
     *
     * @var string|null
     */
    private ?string $replyToName;

    /**
     * copy address
     *
     * @var string|null
     */
    private ?string $ccAddress;

    /**
     * blind copy address
     *
     * @var string|null
     */
    private ?string $bccAddress;

    /**
     * administrator address
     *
     * @var string
     */
    private string $adminAddress;

    /**
     * admin user name
     *
     * @var string|null
     */
    private ?string $adminName;


    /**
     * __construct : each data of email config
     *
     * @param  array<string, string> $config From the config file
     * @return void
     */
    public function __construct(array $config)
    {
        foreach ($config as $key => $value) {
            $this->$key = $value;
        }

    } //end __construct


    /**
     * sendMailToUser : send Email to User
     *
     * @param  User $user Receiver of email
     * @return bool
     */
    public function sendMailToUser(User $user): bool
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
        $mail->addAddress($user->getEmail(), $user->getFirstName().' '.$user->getLastName());
        $mail->addReplyTo($this->replyToAddress, $this->replyToName);
        $mail->addCC($this->ccAddress);
        $mail->addBCC($this->bccAddress);

        //Attachments
        // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Inscription à notre blog';
        $mail->Body    = 'Bienvenue, '.$user->getFirstname().' '.$user->getLastname().' <br> Merci de vous être inscrit sur notre blog.<br><br>Votre identifiant pour votre connexion est : <b>' . $user->getUsername() . '</b> correspondant à votre email .' . $user->getEmail();
        $mail->AltBody = 'Bienvenue, '.$user->getFirstname().' '.$user->getLastname().' Merci de vous être inscrit sur notre blog. Votre identifiant pour votre connexion est : ' . $user->getUsername() . ' correspondant à votre email . ' . $user->getEmail();

        try{
            $mail->send();
            return true;
        }catch (\Exception $e){
            return false;
        }

    }


    /**
     * sendMailToAdmin : send email to admin
     *
     * @param  array<string, string> $contact Informations of the contact
     * @return bool
     */
    public function sendMailToAdmin(array $contact): bool
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
        $mail->addAddress($this->adminAddress, $this->adminName);
        $mail->addReplyTo($this->replyToAddress, $this->replyToName);

        //Attachments
        // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Message de contact';
        $mail->Body    = '
                        Bonjour Admin, <br>
                        Voici un nouveau message d\'un utilisateur. <br>
                        <br>
                        Message de : '.$contact['name'].'<br>
                        Email : '.$contact['email'].'<br>
                        Message : '.$contact['content'].'<br>
                        ';
        $mail->AltBody = '
                        Bonjour Admin,
                        Voici un nouveau message d\'un utilisateur.
                        Message de : '.$contact['name'].'
                        Email : '.$contact['email'].'
                        Message : '.$contact['content'].'
                        ';

        try{
            $mail->send();
            return true;
        }catch (\Exception $e){
            return false;
        }

    }

}
