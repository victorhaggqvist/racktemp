# week stats, Sensor->getWeekStats
SELECT temp, timestamp FROM (   # wrapper to strip of timekey
SELECT
  ROUND(AVG(temp)) AS temp,
  DATE_ADD(
    DATE_FORMAT(timestamp, "%Y-%m-%d %H:00:00"),
    INTERVAL IF(HOUR(timestamp)%2<1,0,1) HOUR  # push all timestamps to even to make them predictable, IF is true if statement <> 0 thefor MOD<1
  ) AS timestamp,
  ROUND(UNIX_TIMESTAMP(timestamp) / (120 * 60)) AS timekey  #group by every 2 hours ie 120 min
FROM sensor_mysen
WHERE timestamp >= NOW() - INTERVAL 1 WEEK
GROUP BY timekey) as must;   # must have alias apperently

# day stats
SELECT temp, timestamp FROM (
SELECT
  ROUND(AVG(temp)) AS temp,
  timestamp,
  ROUND(UNIX_TIMESTAMP(timestamp) / (60 * 60)) AS timekey
FROM sensor_mysen
WHERE timestamp >= NOW() - INTERVAL 1 DAY
GROUP BY timekey
ORDER BY timestamp ASC) as must;

# month stats
SELECT temp, timestamp FROM (
SELECT
  ROUND(AVG(temp)) AS temp,
  DATE_ADD(
    DATE_FORMAT(timestamp, "%Y-%m-%d %H:00:00"),
    INTERVAL IF(HOUR(timestamp)%2<1,0,1) HOUR
  ) AS timestamp,
  ROUND(UNIX_TIMESTAMP(timestamp) / (240 * 60)) AS timekey
FROM sensor_mysen
WHERE timestamp >= NOW() - INTERVAL 1 MONTH
GROUP BY timekey;) as must;
