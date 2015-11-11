<?php
/**
 * User: Victor Häggqvist
 * Date: 6/7/15
 * Time: 12:09 AM
 */

namespace AppBundle\Sensor;


use AppBundle\Util\PDOHelper;
use AppBundle\Util\Temperature;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use stdClass;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SensorTool {


    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var PDOHelper
     */
    private $pdo;

    function __construct(EntityManager $em, ContainerInterface $container) {
        $this->em = $em;

        $db_conf = new stdClass();
        $db_conf->host = $container->getParameter('database_host');
        $db_conf->db =  $container->getParameter('database_name');
        $db_conf->user =  $container->getParameter('database_user');
        $db_conf->pass =  $container->getParameter('database_password');

        $this->pdo = new PDOHelper($db_conf);
    }

    /**
     * Get latest temp
     * @param string $unit
     * @param string $mktemp
     * @return array
     */
    public function getTemp($name, $unit = 'c',$mktemp=true) {
        $sql = "SELECT temp,timestamp FROM sensor_".$name." ORDER BY timestamp DESC LIMIT 1";
        $res = $this->pdo->justQuery($sql);

        if($res[1] < 1)
            return null;

        $ret = $res[2][0];
        if ($mktemp)
            $ret['temp'] = Temperature::mktemp($ret['temp'],$unit);

        if (date('Y-m-d',strtotime($ret['timestamp'])) == date('Y-m-d'))  //if today
            $ret['timestamp'] = date('H:i',strtotime($ret['timestamp']));
        else
            $ret['timestamp'] = date('Y-m-d H:i',strtotime($ret['timestamp']));

        return $ret;
    }

    public function hasData($name) {
        return count($this->getList($name, 0, 1)) > 0? true : false;
    }

    public function getList($name, $start, $stop) {
        $sql = "SELECT id,temp,timestamp FROM sensor_".$name." ORDER BY timestamp DESC LIMIT ".$start.",".$stop;
        $res = $this->pdo->justQuery($sql);
        return $res[2];
    }

    public function getListSize($name) {
        $sql = "SELECT COUNT(temp) AS count FROM sensor_".$name;
        $res = $this->pdo->justQuery($sql);
        return $res[2][0]['count'];
    }

    /**
     * Register new temp
     * @param int $temp Temprature to add
     */
    public function addData($name, $temp) {
        $sql = "INSERT INTO sensor_".$name." (temp)VALUES(?)";
        $this->pdo->prepExec($sql,array($temp));
    }

    public function getHourStats($name) {
        $sql = 'SELECT temp, timestamp
            FROM sensor_'.$name.'
            WHERE timestamp >= NOW() - INTERVAL 1 HOUR';

        $ret = $this->pdo->justQuery($sql);

        if ($ret[1] > 0) {
            foreach ($ret[2] as &$row) {
                $row['temp'] = Temperature::mktemp($row['temp']);
            }
            return $ret[2];
        }
        return false;
    }

    public function getDayStats($name) {
        $timeTreshhold = strtotime('-12 hours');
        $recordedTime = $this->getFirstReportedDataTime($name);

        if (!$recordedTime || strtotime($recordedTime)>$timeTreshhold) {
            return array();
        }

        $sql = 'SELECT temp, timestamp FROM (
            SELECT
              ROUND(AVG(temp)) AS temp,
              timestamp,
              ROUND(UNIX_TIMESTAMP(timestamp) / (60 * 60)) AS timekey
            FROM sensor_'.$name.'
            WHERE timestamp >= NOW() - INTERVAL 1 DAY
            GROUP BY timekey
            ORDER BY timestamp ASC) as must';

        $ret = $this->pdo->justQuery($sql);

        if ($ret[1]>0) {
            foreach ($ret[2] as &$row) {
                $row['temp'] = $this->mktemp($row['temp']);
            }
            return $ret[2];
        }
        return false;
    }

    /**
     * Get week stats
     *
     * Stat is the avg temp for spans of two hours in a period of a week from now
     *
     * @return array|boolean Matrix of stats or false if there is nothing
     */
    public function getWeekStats($name) {
        $timeTreshhold = strtotime('-1 week');
        $recordedTime = $this->getFirstReportedDataTime($name);

        if (!$recordedTime || strtotime($recordedTime)>$timeTreshhold) {
            return array();
        }

        $sql = 'SELECT temp, timestamp FROM (
            SELECT
              ROUND(AVG(temp)) AS temp,
              DATE_ADD(
                DATE_FORMAT(timestamp, "%Y-%m-%d %H:00:00"),
                INTERVAL IF(HOUR(timestamp)%2<1,0,1) HOUR
              ) AS timestamp,
              ROUND(UNIX_TIMESTAMP(timestamp) / (120 * 60)) AS timekey
            FROM sensor_'.$name.'
            WHERE timestamp >= NOW() - INTERVAL 1 WEEK
            GROUP BY timekey) as musthav';

        $ret = $this->pdo->justQuery($sql);

        if ($ret[0] == 1) {
            $aWeekBack = strtotime(date('H')%2==0?'-1 week':'-1 week -1 hour');
            $expected = $this->getExpectedTimes('week', $aWeekBack);

            for ($i=0; $i < count($expected); $i++) {
                $find = $this->recursive_array_search($expected[$i]['timestamp'],$ret[2]);

                if (!$find) {
                    $expected[$i]['temp'] = null; // nulling makes a better chart then just skipping a time
                }else{
                    $expected[$i]['temp'] = Temperature::mktemp($ret[2][$find]['temp']);
                }
            }

            return $expected;
        }

        return false;
    }

    public function getMonthStats($name) {
        $timeTreshhold = strtotime('-1 month');
        $recordedTime = $this->getFirstReportedDataTime($name);

        if (!$recordedTime || strtotime($recordedTime)>$timeTreshhold) {
            return array();
        }

        $sql = 'SELECT temp, timestamp FROM (
            SELECT
              ROUND(AVG(temp)) AS temp,
              DATE_FORMAT(timestamp, "%Y-%m-%d %H:00:00") AS timestamp,
              ROUND(UNIX_TIMESTAMP(timestamp) / (300 * 60)) AS timekey
            FROM sensor_'.$name.'
            WHERE timestamp >= NOW() - INTERVAL 1 MONTH
            GROUP BY timekey) as must';

        $ret = $this->pdo->justQuery($sql);

        if ($ret[1] > 0) {
            foreach ($ret[2] as &$row) {
                $row['temp'] = Temperature::mktemp($row['temp']);
            }
            return $ret[2];
        }

        return false;
    }

    /**
     * Genarates the timestamp that are expected to be within a complete set of stats
     *
     * Range expectations
     * week, every 2 hours
     * month, every 4 hours
     *
     * @param  string $range Timerange, see range expencations
     * @param  int    $start Start unix timestamp
     * @return array         Array of timestamps
     */
    private function getExpectedTimes($range, $start) {
        $span['week'] = ' +2 hour';
        $span['month'] = ' +4 hour';
        $times = array();

        $t = $start;
        while ($t < time()){
            $times[]['timestamp'] = date("Y-m-d H:00:00", $t);
            $t = strtotime(date("Y-m-d H:i", $t).$span[$range]);
        }

        return $times;
    }

    /**
     * Get the time for the firts reported data
     * @return string|boolean   Timestamp string or false if no data
     */
    private function getFirstReportedDataTime($name) {
        $sql = 'SELECT timestamp FROM  sensor_'.$name.' ORDER BY timestamp ASC LIMIT 0,1';

        $ret = $this->pdo->justQuery($sql);
        if ($ret[1]>0) {
            return $ret[2][0]['timestamp'];
        }
        return false;
    }
}
