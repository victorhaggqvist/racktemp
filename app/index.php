<?php
require_once 'lib/head.inc';

use Snilius\Util\Bootstrap\Alert;
use Snilius\Sensor\SensorController;
use Snilius\Sensor\Sensor;
use Snilius\Sensor\SensorStats;
?>
<!DOCTYPE html>
<html>
  <head>
    <?php require_once TEMPLATES_PATH.'/header.php'; ?>
  </head>
  <body>
    <?php require_once TEMPLATES_PATH.'/menu.php'; ?>
    <div class="container wrapper">

      <?php

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "https://raw.githubusercontent.com/victorhaggqvist/racktemp/master/VERSION");
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $version = curl_exec($ch);
      curl_close($ch);

      $localVersion = file_get_contents('../VERSION');

      if ($localVersion < $version) {
        $message = 'There is a new version of RackTemp available, head over Github for more '.
                   '<a href="https://github.com/victorhaggqvist/racktemp/" style="color:#000;">info</a>';
        echo Alert::info($message);
      }

      $sensorController = new SensorController();
      $sensors = $sensorController->getSensors();


      if (!$sensorController->checkSensors($sensors) &&
        $s->getValue('dev-ignore-no-sensors') != 1 &&
        count($sensors)>0) {
        echo Alert::danger("Something is messed up with your sensors, you better check on them!");
      }

      $startTemplate = '';
      $activeSensors = array();
      if (count($sensors)>0) {
        foreach ($sensors as $sensor) {
          if($sensor->isData())
            $activeSensors[] = $sensor;
        }
      }else{
        $startTemplate = 'firsttime.php';
      }


      if (count($activeSensors)<1 && strlen($startTemplate)<1)
        $startTemplate = 'nodata.php';

      // should be good to go for showing stats
      if (strlen($startTemplate)<1) {
      ?>

      <div class="row">
        <div class="col-sm-4">
          <h2>Latest Temperature</h2>
          <div class="pull-right" id="clock" style="font-size: 2em;"></div>
          <p>
          <?php

          foreach($activeSensors as $sensor){
            $current = $sensor->getTemp();

            echo '<strong>'.ucfirst($sensor->name).'</strong>: '.$current['temp'].
                 'C <span class="text-muted">'.$current['timestamp'].'</span><br>';
          }
          ?>
          </p>

          <!-- <a href="#" class="btn btn-default" id="refresh"><span class="glyphicon glyphicon-refresh"></span> Refresh</a>-->
          <strong>Past Hour</strong><br>
          <div id="today"></div>
          <!-- <img src="img.current.php" style="min-width:290px; min-height: 170px; border: 1px #000 solid; background: 136px url('img/chartload.gif') no-repeat;" /> -->
        </div><!-- /.col-sm-4 Latest Temperature -->

        <div class="col-sm-4">
          <h2>Daily Stats</h2>
          <p>
            <?php

            foreach($activeSensors as $sensor){
              $stat = new SensorStats($sensor);
              echo '<strong>'.ucfirst($sensor->name).'</strong><br>';

              $min = $stat->getDailyStat('min');
              if (!is_null($min)) {
                $max = $stat->getDailyStat('max');
                $avg = $stat->getDailyStat('avg');

                echo 'Min: '.$min['temp'].'C <span class="text-muted">'.date('H:i',strtotime($min['timestamp'])).'</span><br>';
                echo 'Max: '.$max['temp'].'C <span class="text-muted">'.date('H:i',strtotime($max['timestamp'])).'</span><br>';
                echo 'Avg: '.$avg['temp'].'C<br>';
              }else
                echo 'No fresh stats for today <br>';

            }
            ?>
          </p>
          <!--<a href="#" class="btn btn-default">Today &raquo;</a>-->
        </div><!-- /.col-sm-4 Daily Stats -->

        <div class="col-sm-4">
          <h2>Weekly Stats</h2>
          <p>
          <?php

          $min = null;
          $max = null;
          $avg = 0;

          // calc weekly stats for all sensors that has been avtice

          $weeklyActiveSensors = 0;
          for ($i=0; $i < count($activeSensors); $i++) {
            $stat = new SensorStats($sensors[$i]);

            $tempMin = $stat->getWeeklyStat('min');

            if (!is_null($tempMin)) { // if there is any data for sensor
              $tempMax = $stat->getWeeklyStat('max');
              if ($i == 0) {
                $min = $tempMin['temp'];
                $max = $tempMax['temp'];
              }else{
                if($tempMin['temp'] < $min)
                  $min = $tempMin['temp'];
                if($tempMax['temp'] < $max)
                  $max = $tempMax['temp'];
              }
              $avg+= $stat->getWeeklyStat('avg')['temp'];
              $weeklyActiveSensors++;
            }
          }

          $avg=($avg==0)?0:$avg/$weeklyActiveSensors;
          if ($weeklyActiveSensors<1){
            echo "There were no active sensors this week";
          }else {
            echo '<strong>Min</strong>: '.$min.'C<br>';
            echo '<strong>Max</strong>: '.$max.'C<br>';
            echo '<strong>Avg</strong>: '.$avg.'C';
          }

          ?>
          </p>
          <!-- <a href="#" class="btn btn-default">More &raquo;</a>-->
        </div><!-- /.col-sm-4 Weekly Stats -->

      </div><!-- /.row -->

      <?php
      }else { // show appropriate template
        require_once  TEMPLATES_PATH.'/'.$startTemplate;
      }

      require_once TEMPLATES_PATH.'/footer.php';
      ?>
    </div><!-- /.container -->

  <?php if (strlen($startTemplate)<1): ?>

  <script src="js/d3.min.js"></script>
  <script src="js/c3.min.js"></script>
  <script src="js/clock.js"></script>
  <script>
  // $('#refresh').click(function(){
  //   $.get( "lib/readtempcron.php", function( data ) {
  //     console.log(data);
  //     window.location="../";
  //   });
  // });

  clock();
  </script>
  <script>
  var dataAsColumn = [
    ['x', '2013-12-03 15:25:03', '2013-12-03 15:30:03', '2013-12-03 15:35:03', '2013-12-03 15:40:03', '2013-12-03 15:45:03', '2013-12-03 15:50:03', '2013-12-03 15:55:03', '2013-12-03 16:00:03', '2013-12-03 16:05:03', '2013-12-03 16:10:03', '2013-12-03 16:15:03'],
    ['top', 20.375, 20.312, 20.437, 20.375, 20.375, 20.500, 20.500, 20.500, 20.437, 20.375, 20.437],
    ['bottom', 17.687, 17.37, 18.125, 18.125, 18.000, 18.000, 17.875, 17.750, 17.750, 17.875, 18.125]
  ];

  c3.generate({
    bindto: '#today',
    data: {
      x: 'x',
      url: 'api/graph/span/hour',
      type: 'spline'
    },
    axis: {
      x: {
        // label: 'Minute',
        type: 'timeseries',
        tick: {
          format: '%M'
        }
      },
      y: {
        // label: 'Temprature'
      }
    }
  });
  </script>
<?php endif; ?>
</body>

</html>
