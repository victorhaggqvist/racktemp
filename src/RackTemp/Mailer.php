<?php

namespace Snilius\RackTemp;

use \Snilius\Util\PDOHelper;
use \Snilius\Util\Settings;
use \Mailgun\Mailgun;


/**
*
*/
class Mailer {
  private $pdo;
  private $settings;
  private $mail;
  private $domain;

  function __construct() {
    $this->pdo = new PDOHelper($GLOBALS['db_conf']);
    $this->settings = new Settings();

    $key = $this->settings->getValue('mg-key');
    $this->domain = $this->settings->getValue('mg-domain');

    $this->mail = new Mailgun($key);
  }

  /**
   * Send a testmail with current settings
   * @return string Test result
   */
  public function sendTest() {
    $to = $this->settings->getValue('mg-to');
    $resp = $this->mail->sendMessage($this->domain, array(
      'from' => 'RackTemp Mailer <racktemp@example.com>',
      'to'=> $to,
      'subject' => 'RackTemp test mail',
      'text' => 'This is the message body, it works!'
      ));

    $ret = '';
    if($resp->http_response_code != 200) {
      $ret = 'Message could not be sent. Mailer Error: ' . $resp->http_response_body->message;
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
