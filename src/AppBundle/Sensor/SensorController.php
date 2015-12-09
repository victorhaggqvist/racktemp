<?php
/**
 * User: Victor Häggqvist
 * Date: 6/6/15
 * Time: 10:05 PM
 */

namespace AppBundle\Sensor;


use AppBundle\Entity\Sensor;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Exception;
use Guzzle\Plugin\Backoff\ReasonPhraseBackoffStrategy;
use Symfony\Component\Process\Process;

class SensorController {

    /**
     * @var EntityManager
     */
    private $em;

    function __construct(EntityManager $em) {
        $this->em = $em;
    }

    /**
     * Get registered sensors
     * @return Sensor[]
     */
    public function getSensors() {
        return $this->em->getRepository('AppBundle:Sensor')->findAll();
    }

    /**
     * Check on sensor hardware
     * @param array $sensors Array of sensors
     * @return boolean If sensors are ok
     */
    public function checkSensors($sensors) {
        if (!$sensors)
            return false;

        $attached = $this->getAttachedSensors();
        if (is_array($attached)) { // if this is false, you are probably not on a rpi and just testing stuff
            foreach ($sensors as $sensor) {
                /** @var Sensor $sensor */
                if (!in_array($sensor->getUid(), $attached))
                    return false;
            }
            return true;
        }

        return false;
    }

    /**
     * Add new sensor to system
     * @param Sensor $sensor A Sensor object
     * @return boolean if addition was successful
     */
    public function addSensor(Sensor $sensor) {
        $this->em->getConnection()->beginTransaction();
        try {
            $this->em->persist($sensor);
            $this->em->flush();

            $conn = $this->em->getConnection();
            $sql = sprintf("CREATE TABLE IF NOT EXISTS `sensor_%s` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `temp` int(11) NOT NULL,
              `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`))", preg_replace('/\s/', '_', $sensor->getName()));
            $conn->exec($sql);


        } catch (Exception $e) {
            $this->em->getConnection()->rollback();
            throw $e;
        }
    }

    /**
     * Get array of attached sensors
     * @return array|boolean Array of hardware id's
     */
    public function getAttachedSensors() {
        $exc = shell_exec("ls /sys/bus/w1/devices/ | grep 28");
        if (strpos($exc, '28') === false) // ie, grep empty
            return false;
        $sensors = preg_split("/[\s]/",trim($exc));      //put them in an array
        return $sensors;
    }

    /**
     * Drops all metrics form a collection
     * @param string Sensor name
     */
    public function dropSensorData($name) {
        $rsm = new ResultSetMapping();
        $this->em->createNativeQuery(sprintf("TRUNCATE `sensor_%s`", $name), $rsm)->execute();
        $this->em->flush();
    }

    /**
     * @param Sensor $sensor
     * @return bool|string
     */
    public function readSensor(Sensor $sensor) {
        $process = new Process(sprintf('ls /sys/bus/w1/devices | grep %s', $sensor->getUid()));
        $process->run();
        $output = $process->getOutput();

        if (strlen($output) > 0) {
            $temp = $this->readSensorByUid($sensor->getUid());
            if (strlen($temp) > 0)
                return $temp;
            return false;
        }
        return false;
    }

    /**
     * @param $uid
     * @return bool|string
     */
    public function readSensorByUid($uid) {

        $process = new Process(sprintf('cat /sys/bus/w1/devices/%s/w1_slave', $uid));
        $process->run();
        $read = $process->getOutput();

        $lines = explode("\n", $read);

        //get status
        $parts = explode(" ", $lines[0]);
        $ok = $parts[count($parts)-1];
        if ($ok == "YES"){
            //get temp
            $parts = explode("=", $lines[1]);
            $temp = $parts[count($parts)-1];
            return $temp;
        }else{
            return false;
        }
    }
}
