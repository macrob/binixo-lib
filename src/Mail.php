<?php

namespace BinixoLib;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Mail
{
    private $mail;
    private $isSend = false;
    private $isSendSuccess = false;

    private $hash;

    private function isAlreadySend($hash) {
        if ($this->hash === $hash) {
            return true;
        }

        return false;
    }

    public function __construct($host, $username, $password, $port = 587, $smtpSecure = PHPMailer::ENCRYPTION_STARTTLS)
    {
        $this->mail = new PHPMailer(true);

        $this->mail->SMTPDebug = SMTP::DEBUG_OFF;
        $this->mail->isSMTP(); // Send using SMTP
        $this->mail->Host       = $host; // Set the SMTP server to send through
        $this->mail->SMTPAuth   = true; // Enable SMTP authentication
        $this->mail->Username   = $username; // SMTP username
        $this->mail->Password   = $password; // SMTP password
        $this->mail->SMTPSecure = $smtpSecure; // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
        $this->mail->Port       = $port; // TCP port to connect to

        $this->hash = $_SESSION['mail_contact_us'];
    }

    public function setDebug($debug = SMTP::DEBUG_OFF)
    {
        $this->mail->SMTPDebug = $debug;
    }

    public function send($from, $to, $replyTo, $subject, $body, $isHTML = true)
    {
        $hash = md5(implode(".", [$from, $to, $replyTo, $subject, $body]));
        
        if ($this->isAlreadySend($hash)) {
            $this->isSend = true;   
            $this->isSendSuccess = true;
            return true;
        }

        $this->isSend = true;

        try {
            $this->mail->addReplyTo($replyTo);
            $this->mail->setFrom($from);
            $this->mail->addAddress($to); // Add a recipient
            $this->mail->isHTML($isHTML); // Set email format to HTML
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;

            $this->mail->send();
            // echo 'Message has been sent';
            $this->isSendSuccess = true;

            $_SESSION['mail_contact_us'] = $hash;
            return true;
        } catch (Exception $e) {
            $this->isSendSuccess = false;
            // echo "Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}";
            return false;
        }
    }

    public function isSend() {
        return $this->isSend;
    }

    public function isSendSucceeded() {
        return $this->isSendSuccess;
    }
}
