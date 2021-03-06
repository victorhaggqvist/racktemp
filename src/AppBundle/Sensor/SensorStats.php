<?php
namespace AppBundle\Sensor;

use AppBundle\Entity\Sensor;
use AppBundle\Util\Temperature;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManager;


class SensorStats {

    /**
     * @var Connection
     */
    private $conn;

    /**
     * Create a stats object for given Sensor
     * @param Sensor $sensor Sensor to get stats for
     */
    public function __construct(EntityManager $em) {
        $this->conn = $em->getConnection();
    }

    /**
     * Get stats for today
     * @param string $stat is either max, min or avg
     * @return array stats array
     */
    public function getDailyStat($name, $stat, $format = true) {
        $sql = '';
        $ret = null;
        switch ($stat) {
            case 'max':
                $sql = "SELECT temp,timestamp  FROM `sensor_" . $name . "` WHERE `timestamp` >= CURDATE() ORDER BY `temp` DESC LIMIT 1";
                break;
            case 'min':
                $sql = "SELECT temp,timestamp  FROM `sensor_" . $name . "` WHERE `timestamp` >= CURDATE() ORDER BY `temp` ASC LIMIT 1";
                break;
            case 'avg':
                $sql = "SELECT AVG(temp) AS temp  FROM `sensor_" . $name . "` WHERE `timestamp` >= CURDATE() ORDER BY `temp` ASC LIMIT 1";
                break;
            default:
                throw new \Exception("Unexpected stat parameter: " . $stat, 1);
        }

        $res = $this->conn->fetchAll($sql);

        // if there are rows, otherwise no stats today
        if (count($res) > 0) {
            $ret = $res[0];
            if ($format)
                $ret['temp'] = Temperature::mktemp($ret['temp']);
        }

        return $ret;
    }

    /**
     * Get stats for last calendar week
     * @param string $stat is either max, min or avg
     * @return array stats array
     */
    public function getWeeklyStat($name, $stat, $format = true) {
        $startday = date("Y-m-d", strtotime("last Monday"));
        $sql = '';
        $ret = null;

        switch ($stat) {
            case 'max':
                $sql = "SELECT temp,timestamp  FROM `sensor_" . $name . "` WHERE `timestamp` >= '" . $startday . "' ORDER BY `temp` DESC LIMIT 1";
                break;
            case 'min':
                $sql = "SELECT temp,timestamp  FROM `sensor_" . $name . "` WHERE `timestamp` >= '" . $startday . "' ORDER BY `temp` ASC LIMIT 1";
                break;
            case 'avg':
                $sql = "SELECT AVG(temp) AS temp,timestamp FROM `sensor_" . $name . "` WHERE `timestamp` >='" . $startday . "'";
                break;
            default:
                throw new \Exception("Unexpected stat parameter: " . $stat, 1);
        }

        $res = $this->conn->fetchAll($sql);

        // if there are rows, otherwise no stats today
        if (count($res) > 0) {
            $ret = $res[0];
            if ($format)
                $ret['temp'] = Temperature::mktemp($ret['temp']);
        }

        return $ret;
    }


    public function getHourStats($name) {
        $sql = 'SELECT temp, timestamp
            FROM sensor_' . $name . '
            WHERE timestamp >= NOW() - INTERVAL 1 HOUR';

        $ret = $this->conn->fetchAll($sql);

        if (count($ret) > 0) {
            foreach ($ret as &$row) {
                $row['temp'] = Temperature::mktemp($row['temp']);
            }
            return $ret;
        }
        return false;
    }

    public function getDayStats($name) {
        $timeTreshhold = strtotime('-12 hours');
        $recordedTime = $this->getFirstReportedDataTime($name);

        if (!$recordedTime || strtotime($recordedTime) > $timeTreshhold) {
            return array();
        }

        $sql = 'SELECT temp, timestamp FROM (
            SELECT
              ROUND(AVG(temp)) AS temp,
              timestamp,
              ROUND(UNIX_TIMESTAMP(timestamp) / (60 * 60)) AS timekey
            FROM sensor_' . $name . '
            WHERE timestamp >= NOW() - INTERVAL 1 DAY
            GROUP BY timekey
            ORDER BY timestamp ASC) as must';

        $ret = $this->conn->fetchAll($sql);

        if (count($ret) > 0) {
            foreach ($ret as &$row) {
                $row['temp'] = Temperature::mktemp($row['temp']);
            }
            return $ret;
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

        if (!$recordedTime || strtotime($recordedTime) > $timeTreshhold) {
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
            FROM sensor_' . $name . '
            WHERE timestamp >= NOW() - INTERVAL 1 WEEK
            GROUP BY timekey) as musthav';

        $ret = $this->conn->fetchAll($sql);

        $aWeekBack = strtotime(date('H') % 2 == 0 ? '-1 week' : '-1 week -1 hour');
        $expected = $this->getExpectedTimes('week', $aWeekBack);

        for ($i = 0; $i < count($expected); $i++) {
            $find = $this->recursive_array_search($expected[$i]['timestamp'], $ret);

            if (!$find) {
                $expected[$i]['temp'] = null; // nulling makes a better chart then just skipping a time
            } else {
                $expected[$i]['temp'] = Temperature::mktemp($ret[$find]['temp']);
            }
        }

        return $expected;
    }

    public function getMonthStats($name) {
        $timeTreshhold = strtotime('-1 month');
        $recordedTime = $this->getFirstReportedDataTime($name);

        if (!$recordedTime || strtotime($recordedTime) > $timeTreshhold) {
            return array();
        }

        $sql = 'SELECT temp, timestamp FROM (
            SELECT
              ROUND(AVG(temp)) AS temp,
              DATE_FORMAT(timestamp, "%Y-%m-%d %H:00:00") AS timestamp,
              ROUND(UNIX_TIMESTAMP(timestamp) / (300 * 60)) AS timekey
            FROM sensor_' . $name . '
            WHERE timestamp >= NOW() - INTERVAL 1 MONTH
            GROUP BY timekey) as must';

        $ret = $this->conn->fetchAll($sql);

        if (count($ret) > 0) {
            foreach ($ret as &$row) {
                $row['temp'] = Temperature::mktemp($row['temp']);
            }
            return $ret;
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
     * @param  string   $range Timerange, see range expencations
     * @param  int      $start Start unix timestamp
     * @return array    Array of timestamps
     */
    private function getExpectedTimes($range, $start) {
        $span['week'] = ' +2 hour';
        $span['month'] = ' +4 hour';
        $times = array();

        $t = $start;
        while ($t < time()) {
            $times[]['timestamp'] = date("Y-m-d H:00:00", $t);
            $t = strtotime(date("Y-m-d H:i", $t) . $span[$range]);
        }

        return $times;
    }

    /**
     * Get the time for the firts reported data
     * @return string|boolean   Timestamp string or false if no data
     */
    private function getFirstReportedDataTime($name) {
        $sql = 'SELECT timestamp FROM  sensor_' . $name . ' ORDER BY timestamp ASC LIMIT 0,1';

        $ret = $this->conn->fetchAll($sql);
        if (count($ret) > 0) {
            return $ret[0]['timestamp'];
        }
        return false;
    }

    /**
     * Find the row key of a value in a matix
     *
     * From http://www.php.net/manual/en/function.array-search.php#91365
     * @param  mixed $needle    The searched value.
     * @param  array $haystack  The array
     * @return mixed            row index
     */
    private function recursive_array_search($needle, $haystack) {
        foreach ($haystack as $key => $value) {
            $current_key = $key;
            if ($needle === $value OR (is_array($value) && $this->recursive_array_search($needle, $value) !== false)) {
                return $current_key;
            }
        }
        return false;
    }
}

?>
