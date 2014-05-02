<?php
require_once('lib/config.inc');
require_once(LIB_PATH.'/head.inc');

use Snilius\Util\Bootstrap\Alert;
use Snilius\SensorController;
use Snilius\Sensor;
use Snilius\SensorStats;
?>
<!DOCTYPE html>
<html>
  <head>
    <?php require_once(TEMPLATES_PATH.'/header.php'); ?>
  </head>
  <body>
    <?php require_once(TEMPLATES_PATH.'/menu.php'); ?>
    <div class="container" id="wrapper">

      <?php

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "https://raw.github.com/victorhaggqvist/racktemp/master/VERSION");
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $version = curl_exec($ch);

      curl_close($ch);
      $localVersion = shell_exec("cat ./VERSION");

      if ($localVersion < $version) {
        echo Alert::info('There is a new version of RackTemp available, head over Github for more <a href="https://github.com/victorhaggqvist/racktemp/" style="color:#000;">info</a>');
      }

      $sensorController = new SensorController();
      $sensorsDef = $sensorController->getSensors();
      if (!$sensorController->checkSensors($sensorsDef)) {
        echo Alert::danger("Something is messed up with your sensors, you better check on them!");
      }

      $startTemplate = '';
      if(count($sensorsDef) < 1)
        $startTemplate = 'firsttime.php';

      $sensors = array();
      foreach ($sensorsDef as $sensor) {
        $sn = new Sensor($sensor);
        // var_dump($sn);
        if($sn->isData())
          $sensors[] = $sn;
      }
      var_dump($sensors);

      if (count($sensors)<1)
        $startTemplate = 'nodata.php';

      // should be good to go for showing stats
      if (strlen($startTemplate)<1) {
      ?>

      <div class="row">
        <div class="col-sm-4">
          <h2>Current Temperature</h2>
          <div class="pull-right" id="clock" style="font-size: 2em;"></div>
          <p>
          <?php

          foreach($sensors as $sensor){
            $current = $sensor->getTemp();

            echo '<strong>'.ucfirst($sensor->name).'</strong>: '.$current['temp'].'C <span class="text-muted">('.$current['timestamp'].')</span><br>';
          }
          ?>
          </p>

          <!-- <a href="#" class="btn btn-default" id="refresh"><span class="glyphicon glyphicon-refresh"></span> Refresh</a>-->
          <strong>Past Hour</strong><br>
          <img src="img.current.php" style="min-width:290px; min-height: 170px; border: 1px #000 solid; background: 136px url('img/chartload.gif') no-repeat;" />
        </div><!-- /.col-sm-4 Current Temperature -->

        <div class="col-sm-4">
          <h2>Daily Stats</h2>
          <p>
            <?php

            foreach($sensors as $sn){
              $stat = new SensorStats($sn['name']);
              $min=$stat->getDailyStat('min');
              $max=$stat->getDailyStat('max');
              $avg=$stat->getDailyStat('avg');

              echo '<strong>'.ucfirst($sn['name']).'</strong><br>';
              echo 'Min: '.$min['temp'].'C <span class="text-muted">('.date('H:i',strtotime($min['timestamp'])).')</span><br>';
              echo 'Max: '.$max['temp'].'C <span class="text-muted">('.date('H:i',strtotime($max['timestamp'])).')</span><br>';
              echo 'Avg: '.$avg['temp'].'C<br>';
            }
            ?>
          </p>
          <!--<a href="#" class="btn btn-default">Today &raquo;</a>-->
        </div><!-- /.col-sm-4 Daily Stats -->

        <div class="col-sm-4">
          <h2>Weekly Stats</h2>
          <p>
          <?php

          $min = '';
          $max = '';
          $avg = 0;

          $i = 0;
          foreach ($sensors as $sn) {
            $stat = new SensorStats($sn['name']);
            if ($i==0) {
              $min = $stat->getWeeklyStat('min')['temp'];
              $max = $stat->getWeeklyStat('max')['temp'];
            }else{
              if($stat->getWeeklyStat('min')['temp']<$min)
                $min = $stat->getWeeklyStat('min')['temp'];
              if($stat->getWeeklyStat('max')['temp']<$max)
                $max = $stat->getWeeklyStat('max')['temp'];
            }
            $avg+= $stat->getWeeklyStat('avg')['temp'];
            $i++;
          }

          $avg=($avg==0)?0:$avg/$i;
          if (count($sensors)<1){
            echo "no data yet QWE";
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
  </body>
  <!-- <script src="js/racktemp.js"></script>
  <script>
  $('#refresh').click(function(){
    $.get( "lib/readtempcron.php", function( data ) {
      console.log(data);
      window.location="../";
    });
  });

  clock();
  </script> -->

</html>
