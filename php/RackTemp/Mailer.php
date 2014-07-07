<?php

namespace Snilius\RackTemp;

use \Snilius\Util\PDOHelper;
use \Snilius\Util\Settings;


/**
*
*/
class Mailer {
  private $pdo;
  private $settings;

  function __construct() {
    $this->pdo = new PDOHelper($GLOBALS['db_conf']);
    $this->settings = new Settings();
  }

  /**
   * Send a testmail with current settings
   * @return string Test result
   */
  public function sendTest() {
    $s = $this->settings;
    $mail = new \PHPMailer;

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = $s->getValue('smtp-host');  // Specify main and backup SMTP servers

    if ($s->getValue('smtp-auth') == '1') {
      $mail->SMTPAuth = true;                               // Enable SMTP authentication
      $mail->Username = $s->getValue('smtp-user');                 // SMTP username
      $mail->Password = $s->getValue('smtp-password');                           // SMTP password
    }

    $encryption = '';
    switch ($s->getValue('smtp-encryption')) {
      case '1':
        $encryption = false;
        break;
      case '2':
        $encryption = 'tls';
        break;
      case '3':
        $encryption = 'ssl';
        break;
    }

    if ($encryption)
      $mail->SMTPSecure = $encryption;                            // Enable encryption, 'ssl' also accepted

    $mail->From = 'racktemp@example.com';
    $mail->FromName = 'RackTemp Mailer';
    $mail->addAddress($s->getValue('smtp-to'));               // Name is optional
    $mail->addReplyTo('info@example.com', 'Information');

    $mail->WordWrap = 50;                                 // Set word wrap to 50 characters

    $mail->Subject = 'RackTemp test mail';
    $mail->Body    = 'This is the message body, it works!';

    $ret = '';
    if(!$mail->send()) {
        $ret = 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
    } else {
        $ret = 'Message has been sent';
    }

    return $ret;
  }
}

 ?>
