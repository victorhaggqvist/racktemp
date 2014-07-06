# week stats, Sensor->getWeekStats
SELECT temp, timestamp FROM (   # wrapper to strip of timekey
SELECT
  ROUND(AVG(temp)) AS temp,
  DATE_ADD(
    DATE_FORMAT(timestamp, "%Y-%m-%d %H:00:00"),
    INTERVAL IF(HOUR(timestamp)%2<1,0,1) HOUR  # push all timestamps to even to make them preditable, IF is true if statement <> 0 thefor MOD<1
  ) AS timestamp,
  ROUND(UNIX_TIMESTAMP(timestamp) / (120 * 60)) AS timekey
FROM sensor_mysen
WHERE timestamp >= NOW() - INTERVAL 1 WEEK
GROUP BY timekey) as musthav;   # must have alias apperently
