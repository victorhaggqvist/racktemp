<?php

namespace AppBundle\Util;


use \Mailgun\Mailgun;
use Monolog\Logger;


/**
 *
 */
class Mailer {
    /** @var Settings */
    private $settings;

    /** @var Mailgun  */
    private $mail;

    private $domain;
    /**
     * @var Logger
     */
    private $logger;

    function __construct(Settings $settings, Logger $logger) {
        $this->settings = $settings;

        $key = $this->settings->get('mg-key');
        $this->domain = $this->settings->get('mg-domain');

        $this->mail = new Mailgun($key);
        $this->logger = $logger;
    }

    /**
     * Send a testmail with current settings
     * @return string Test result
     */
    public function sendTest() {
        $to = $this->settings->get('mg-to');
        $resp = $this->mail->sendMessage($this->domain, array(
            'from'    => 'RackTemp Mailer <racktemp@example.com>',
            'to'      => $to,
            'subject' => 'RackTemp test mail',
            'text'    => 'This is the message body, it works!'
        ));


        if ($resp->http_response_code != 200) {
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
        $to = $this->settings->get('mg-to');
        $resp = $this->mail->sendMessage($this->domain, array(
            'from'    => 'RackTemp Mailer <noreply@racktemp.com>',
            'to'      => $to,
            'subject' => 'RackTemp Notification',
            'text'    => $message
        ));

        if ($resp->http_response_code != 200) {
            $this->logger->info('notification sent');
        } else {
            $this->logger->error(sprintf('failed to send notification: %s', $resp->http_response_code));
        }
    }

}

?>
