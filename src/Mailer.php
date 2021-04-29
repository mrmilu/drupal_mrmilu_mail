<?php

namespace Drupal\mrmilu_mail;

use Drupal\Core\Site\Settings;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

class Mailer {

  public $fromName;
  public $fromEmail;
  public $toEmail;
  public $mailer;

  public function __construct() {
    $username = Settings::get('smtp_username');
    $password = Settings::get('smtp_password');

    $transport = (new Swift_SmtpTransport('smtp.sendgrid.net', 587, 'tls'))
      ->setUsername($username)
      ->setPassword($password);

    $this->mailer = new Swift_Mailer($transport);
    $this->fromEmail = Settings::get('smtp_from');
    $this->fromName = Settings::get('smtp_name');
    $this->toEmail = Settings::get('smtp_to');
  }

  public function createMessage($subject, $body, $to = NULL) {
    $to = $to ? $to : $this->toEmail;
    return (new Swift_Message($subject))
      ->setFrom([$this->fromEmail => $this->fromName])
      ->setTo($to)
      ->setBody($body, 'text/html');
  }

  public function sendMail($subject, $body, $to = NULL) {
    $message = $this->createMessage($subject, $body, $to);
    return $this->send($message);
  }

  public function send(Swift_Message $message) {
    return $this->mailer->send($message);
  }
}
