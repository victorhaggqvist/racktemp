<?php require_once('head.inc'); ?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
  <head>
    <?php require_once('header.inc'); ?>
  </head>
  <body>
    <?php require_once('menu.php'); ?>
    <div class="container" id="wrapper">
      <div class="row">
        <div class="col-sm-4">
          <h2>Current Temperature</h2>
          
          <p>
          <?php
          $pdo= new PDOHelper($db_conf);
          $sensors=$pdo->justQuery('SELECT * FROM sensors')[2];
          
          if(count($sensors)<1)
            echo "There are no sensors yet";
          
          foreach($sensors as $sn){
            $ret=$pdo->justQuery("SELECT temp,timestamp FROM sensor_".$sn['name']." ORDER BY timestamp DESC LIMIT 1");
            
            $date=(date('Y-m-d',strtotime($ret[2][0]['timestamp']))==date('Y-m-d'))?date('H:i',strtotime($ret[2][0]['timestamp'])):date('Y-m-d H:i',strtotime($ret[2][0]['timestamp']));
            echo '<strong>'.$sn['name'].'</strong>: '.mktemp($ret[2][0]['temp']).'C <span class="text-muted">('.$date.')</span><br>';
          }
          ?>
          </p>
          
          <a href="#" class="btn btn-default" id="refresh"><span class="glyphicon glyphicon-refresh"></span> Refresh</a>
        </div>
        <div class="col-sm-4">
          <h2>Daily Stats</h2>
          <p>
            <?php
            
            foreach($sensors as $sn){
              $sqlMin="SELECT temp,timestamp  FROM `sensor_".$sn['name']."` WHERE `timestamp` >= CURDATE() ORDER BY `temp` ASC LIMIT 1";
              $sqlMax="SELECT temp,timestamp  FROM `sensor_".$sn['name']."` WHERE `timestamp` >= CURDATE() ORDER BY `temp` DESC LIMIT 1";
              
              $min=$pdo->justQuery($sqlMin);
              $max=$pdo->justQuery($sqlMax);
              
              echo '<strong>'.$sn['name'].'</strong><br>';
              //if($min[1]>0)
                echo 'Min: '.mktemp($min[2][0]['temp']).'C <span class="text-muted">('.date('H:i',strtotime($min[2][0]['timestamp'])).')</span><br>';
              //if($max[1]>0)
                echo 'Max: '.mktemp($max[2][0]['temp']).'C <span class="text-muted">('.date('H:i',strtotime($max[2][0]['timestamp'])).')</span><br>';
            }
            ?>
          </p>
          <a href="#" class="btn btn-default">Today &raquo;</a>
        </div>
        <div class="col-sm-4">
          <h2>Weekly Stats</h2>
          <p>
          <?php
          $startday=date("Y-m-d",strtotime("last Monday"));
          $sqlAvg="SELECT AVG(temp) AS temp,timestamp FROM `sensor_".$sn['name']."` WHERE `timestamp` >='".$startday."'";
          $sqlMin="SELECT temp,timestamp  FROM `sensor_".$sn['name']."` WHERE `timestamp` >= '".$startday."' ORDER BY `temp` ASC LIMIT 1";
          $sqlMax="SELECT temp,timestamp  FROM `sensor_".$sn['name']."` WHERE `timestamp` >= '".$startday."' ORDER BY `temp` DESC LIMIT 1";
          
          $avg=$pdo->justQuery($sqlAvg);
          $max=$pdo->justQuery($sqlMax);
          $min=$pdo->justQuery($sqlMin);
          
          echo '<strong>Min</strong>: '.mktemp($min[2][0]['temp']).'C<br>';
          echo '<strong>Max</strong>: '.mktemp($max[2][0]['temp']).'C<br>';
          echo '<strong>Avg</strong>: '.mktemp($avg[2][0]['temp']).'C';
          ?>
          </p>
          <a href="#" class="btn btn-default">More &raquo;</a>
        </div>
      </div>
      <?php require_once('footer.inc'); ?>
    </div>
  </body>
  <script>
  $('#refresh').click(function(){
    $.get( "lib/readtemp.php", function( data ) {
      console.log(data);
    });
  });
  </script>
</html>
