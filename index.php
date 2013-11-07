<?php
require_once('lib/config.inc');
require_once(LIB_PATH.'/head.inc'); 

use Snilius\Util\Bootstrap\Alert;
use Snilius\SensorController;
use Snilius\Sensor;
use Snilius\SensorStats;
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
  <head>
    <?php require_once(TEMPLATES_PATH.'/header.php'); ?>
  </head>
  <body>
    <?php require_once(TEMPLATES_PATH.'/menu.php'); ?>
    <div class="container" id="wrapper">
      
      <?php 
      $sensCont = new SensorController();
      $sensors = $sensCont->getSensors();
      if (!$sensCont->checkSensors($sensors)) {
        echo Alert::danger("Something is messed up with your sensors, you better check on them!");
      }
      
      ?>
      
      <div class="row">
        <div class="col-sm-4">
          <h2>Current Temperature</h2>
          <div class="pull-right" id="clock" style="font-size: 2em;"></div>
          <p>
          <?php
          if(count($sensors)<1)
            echo "There are no sensors yet";
          
          foreach($sensors as $snensor){
            $sn = new Sensor($snensor['name']);
            $current=$sn->getTemp();
            
            echo '<strong>'.ucfirst($sn->name).'</strong>: '.$current['temp'].'C <span class="text-muted">('.$current['timestamp'].')</span><br>';
          }
          ?>
          </p>
          
          <!-- <a href="#" class="btn btn-default" id="refresh"><span class="glyphicon glyphicon-refresh"></span> Refresh</a>-->
          <strong>Past Hour</strong><br>
          <img src="img.current.php" style="min-width:290px; min-height: 170px; border: 1px #000 solid; background: 136px url('img/chartload.gif') no-repeat;" />
        </div>
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
          <a href="#" class="btn btn-default">Today &raquo;</a>
        </div>
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
          
          $avg=$avg/$i;
          
          echo '<strong>Min</strong>: '.$min.'C<br>';
          echo '<strong>Max</strong>: '.$max.'C<br>';
          echo '<strong>Avg</strong>: '.$avg.'C';
          ?>
          </p>
          <a href="#" class="btn btn-default">More &raquo;</a>
        </div>
      </div>
      <?php require_once(TEMPLATES_PATH.'/footer.php'); ?>
    </div>
  </body>
  <script>
  $('#refresh').click(function(){
    $.get( "lib/readtempcron.php", function( data ) {
      console.log(data);
      window.location="../";
    });
  });

  $(clock);

  function clock(){
    var today=new Date();
    var h=today.getHours();
    var m=today.getMinutes();
    
    h=checkTime(h);
    m=checkTime(m);
    
    document.getElementById('clock').innerHTML=h+":"+m;

    setTimeout(function(){
      clock()
      },500);
  
    function checkTime(i){
      if (i<10)
        i="0" + i;
      return i;
    }
  }
  </script>
</html>
