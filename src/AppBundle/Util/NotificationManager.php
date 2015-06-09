<?php
/**
 * User: Victor Häggqvist
 * Date: 6/10/15
 * Time: 12:27 AM
 */

namespace AppBundle\Util;


use AppBundle\Entity\Sensor;
use AppBundle\Sensor\SensorController;
use AppBundle\Sensor\SensorTool;
use Symfony\Bridge\Monolog\Logger;

class NotificationManager {

    /**
     * @var Settings
     */
    private $settings;
    /**
     * @var SensorController
     */
    private $sensorController;
    /**
     * @var SensorTool
     */
    private $sensorTool;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(Settings $settings,
                                SensorController $sensorController,
                                SensorTool $sensorTool,
                                Logger $logger,
                                Mailer $mailer) {

        $this->settings = $settings;
        $this->sensorController = $sensorController;
        $this->sensorTool = $sensorTool;
        $this->logger = $logger;
        $this->mailer = $mailer;
    }

    public function sendNotifications() {
        $enabled = $this->settings->get('notifications-enabled');

        if ($enabled == '1') {
            $this->logger->info('Sending notifications');
            $interval = intval($this->settings->get('notifications-interval')) * 60 * 60;
            $lastnote = $this->settings->get('notifications-last');
            $lastnote = (!$lastnote) ? strtotime('-'.($interval/60/60).' minutes') : $lastnote;

            if (time()-$interval < $lastnote) {   // if interval has past
                $this->settings->set('notifications-last', $lastnote);
                $this->logger->info('within notification interval');
                $sensors = $this->sensorController->getSensors();
                $message = '';

                foreach ($sensors as $sensor) {
                    /* @var Sensor $sensor  */
                    $temp = $this->sensorTool->getList($sensor->getName(), 0, 1)[0];
                    if (strtotime($temp['timestamp']) > strtotime('-10 minutes')) { // if temp is new
                        $min = $this->settings->get('tempt-'.$sensor->getName().'-min');
                        $max = $this->settings->get('tempt-'.$sensor->getName().'-max');

                        if (intval($temp['temp']) <= intval($min)) {
                            $message .= sprintf('%s is bellow defined minimum a %s\n', $sensor->getName(), Temperature::mktemp($temp['temp']));
                            $this->logger->info(sprintf('%s triggered min', $sensor->getName()));
                        } elseif (intval($temp['temp']) >= intval($max)) {
                            $message .= sprintf('%s is above defined maximum a %s\n', $sensor->getName(), Temperature::mktemp($temp['temp']));
                            $this->logger->info(sprintf('%s triggered max', $sensor->getName()));
                        }
                    }
                }

                if (strlen($message) > 0) {
                    $msg = "RackTemp Temperature Notification\n\n";
                    $msg .= $message;

                    $this->mailer->sendNotification($msg);
                }
            } else {
                $this->logger->info('not in interval, skipping notifications');
            }
        }
    }
}
