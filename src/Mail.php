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
    }

    public function setDebug($debug = SMTP::DEBUG_OFF)
    {
        $this->mail->SMTPDebug = $debug;
    }

    public function send($from, $to, $subject, $body, $isHTML = true)
    {
        $this->isSend = true;

        try {
            $this->mail->setFrom($from);
            $this->mail->addAddress($to); // Add a recipient
            $this->mail->isHTML($isHTML); // Set email format to HTML
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;

            $this->mail->send();
            // echo 'Message has been sent';
            $this->isSendSuccess = true;

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
