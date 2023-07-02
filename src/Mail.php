<?php
namespace BinixoLib;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Mail
{
    private $mail;
    
    public function __construct($host, $username, $password, $port = 587, $isDebug = SMTP::DEBUG_OFF)
    {
        $this->mail = new PHPMailer(true);

        $this->mail->SMTPDebug = $isDebug; // Enable verbose debug output
        $this->mail->isSMTP(); // Send using SMTP
        $this->mail->Host       = $host; // Set the SMTP server to send through
        $this->mail->SMTPAuth   = true; // Enable SMTP authentication
        $this->mail->Username   = $username; // SMTP username
        $this->mail->Password   = $password; // SMTP password
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
        $this->mail->Port       = $port; // TCP port to connect to
    }
    
    public function send($from, $to, $subject, $body, $isHTML = true)
    {
        try {
            $this->mail->setFrom($from);
            $this->mail->addAddress($to); // Add a recipient
            $this->mail->isHTML($isHTML); // Set email format to HTML
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;

            $this->mail->send();
            // echo 'Message has been sent';
            return true;
        } catch (Exception $e) {
            // echo "Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}";
            return false;
        }
    }
}