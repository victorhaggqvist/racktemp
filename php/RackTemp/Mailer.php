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
  private $mail;

  function __construct() {
    $this->pdo = new PDOHelper($GLOBALS['db_conf']);
    $this->settings = new Settings();

    $this->mail = new \PHPMailer();

    $this->mail->isSMTP();
    $this->mail->Host = $this->settings->getValue('smtp-host');

    if ($this->settings->getValue('smtp-auth') == '1') {
      $this->mail->SMTPAuth = true;
      $this->mail->Username = $this->settings->getValue('smtp-user');
      $this->mail->Password = $this->settings->getValue('smtp-password');
    }

    $encryption = '';
    switch ($this->settings->getValue('smtp-encryption')) {
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
      $this->mail->SMTPSecure = $encryption;

    $this->mail->From = 'racktemp@example.com';
    $this->mail->FromName = 'RackTemp Mailer';
    $this->mail->addAddress($this->settings->getValue('smtp-to'));

    $this->mail->WordWrap = 50;
  }

  /**
   * Send a testmail with current settings
   * @return string Test result
   */
  public function sendTest() {
    $this->mail->Subject = 'RackTemp test mail';
    $this->mail->Body    = 'This is the message body, it works!';

    $ret = '';
    if(!$this->mail->send()) {
      $ret = 'Message could not be sent. Mailer Error: ' . $this->mail->ErrorInfo;
    } else {
      $ret = 'Message has been sent';
    }

    return $ret;
  }

  /**
   * Send notification email
   * @param  string $message The notification message
   * @return string          Mail result
   */
  public function sendNotification($message) {
    $this->mail->Subject = 'RackTemp Notification';
    $this->mail->Body = $message;

    if(!$this->mail->send()) {
      return 'Notification could not be sent. Mailer Error: ' . $this->mail->ErrorInfo;
    } else {
      return 'Notification has been sent';
    }
  }
}

 ?>
