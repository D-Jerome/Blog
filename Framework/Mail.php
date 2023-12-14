<?php

declare(strict_types=1);

namespace Framework;

use App\Model\Entities\User;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mail
{
    /**
     * smtp address
     */
    private string $host;

    /**
     * authentification required(true)
     */
    private bool $smtpAuth;

    /**
     * login for smtp  connect
     */
    private string $userName;

    /**
     * password for smtp connect
     */
    private string $password;

    /**
     * secure type
     */
    private string $smtpSecure;

    /**
     * port use for connection
     */
    private int $port;

    /**
     * from email address
     */
    private string $fromAddress;

    /**
     * from user name
     */
    private string $fromName = '';

    /**
     * ReplyTo Address
     */
    private string $replyToAddress;

    /**
     * ReplyTo Name
     */
    private string $replyToName = '';

    /**
     * copy address
     */
    private ?string $ccAddress;

    /**
     * blind copy address
     */
    private ?string $bccAddress;

    /**
     * administrator address
     */
    private string $adminAddress;

    /**
     * admin user name
     */
    private string $adminName = '';

    /**
     * __construct : each data of email config
     *
     * @param array<string,bool|int|string> $config From the config file
     */
    public function __construct(array $config)
    {
        foreach ($config as $key => $value) {
            $this->{$key} = $value;
        }
    }
    // end __construct

    /**
     * sendMailToUser : send Email to User
     *
     * @param User $user Receiver of email
     */
    public function sendMailToUser(User $user): bool
    {
        $mail = new PHPMailer(true);
        // Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host = $this->host;
        $mail->SMTPAuth = $this->smtpAuth;
        $mail->Username = $this->userName;
        $mail->Password = $this->password;
        $mail->SMTPSecure = $this->smtpSecure;
        $mail->Port = $this->port;

        // Recipients
        $mail->setFrom($this->fromAddress, $this->fromName);
        $mail->addAddress($user->getEmail(), $user->getFirstName() . ' ' . $user->getLastName());
        $mail->addReplyTo($this->replyToAddress, $this->replyToName);
        if (null !== $this->ccAddress) {
            $mail->addCC($this->ccAddress);
        }
        if (null !== $this->bccAddress) {
            $mail->addBCC($this->bccAddress);
        }
        // Attachments
        // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        // Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Inscription à notre blog';
        $mail->Body = 'Bienvenue, ' . $user->getFirstname() . ' ' . $user->getLastname() . ' <br> Merci de vous être inscrit sur notre blog.<br><br>Votre identifiant pour votre connexion est : <b>' . $user->getUsername() . '</b> correspondant à votre email .' . $user->getEmail();
        $mail->AltBody = 'Bienvenue, ' . $user->getFirstname() . ' ' . $user->getLastname() . ' Merci de vous être inscrit sur notre blog. Votre identifiant pour votre connexion est : ' . $user->getUsername() . ' correspondant à votre email . ' . $user->getEmail();

        try {
            $mail->send();

            return true;
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * sendMailToAdmin : send email to admin
     *
     * @param array<string, string> $contact Informations of the contact
     */
    public function sendMailToAdmin(array $contact): bool
    {
        $mail = new PHPMailer(true);
        // Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host = $this->host;
        $mail->SMTPAuth = $this->smtpAuth;
        $mail->Username = $this->userName;
        $mail->Password = $this->password;
        $mail->SMTPSecure = $this->smtpSecure;
        $mail->Port = $this->port;

        // Recipients
        $mail->setFrom($this->fromAddress, $this->fromName);
        $mail->addAddress($this->adminAddress, $this->adminName);
        $mail->addReplyTo($this->replyToAddress, $this->replyToName);

        // Attachments
        // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        // Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Message de contact';
        $mail->Body = '
                        Bonjour Admin, <br>
                        Voici un nouveau message d\'un utilisateur. <br>
                        <br>
                        Message de : ' . $contact['name'] . '<br>
                        Email : ' . $contact['email'] . '<br>
                        Message : ' . $contact['content'] . '<br>
                        ';
        $mail->AltBody = '
                        Bonjour Admin,
                        Voici un nouveau message d\'un utilisateur.
                        Message de : ' . $contact['name'] . '
                        Email : ' . $contact['email'] . '
                        Message : ' . $contact['content'] . '
                        ';

        try {
            $mail->send();

            return true;
        } catch (\Exception) {
            return false;
        }
    }
}
