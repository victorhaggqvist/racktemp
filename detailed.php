<?php
require_once('lib/config.inc');
require_once(LIB_PATH.'/head.inc');

use Snilius\SensorController;
use Snilius\Sensor;
use Snilius\Util\ArrayTool;
use Snilius\Util\Paginator;
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
      <h1>Detailed metrics</h1>
      
      <?php 
      $sensCont = new SensorController();
      $sensors = $sensCont->getSensors();
      
      $itemsPerPage=50;
      $page=(@$_GET['page'])?@$_GET['page']:1;
      $sensorName=@$_GET['sensor'];
      $pagesToDisplay=20;
      
      
      //tabs
      echo '<ul class="nav nav-tabs">';
      foreach ($sensors as $key => $sensor) {
        echo '<li class="'.((ArrayTool::first($sensors,$key))?"active":"").'"><a href="#'.$sensor['name'].'" data-toggle="tab">'.$sensor['name'].'</a></li>';
      }
      echo '</ul><div class="tab-content">';
      
      foreach ($sensors as $key => $sensor) {
        $sens = new Sensor($sensor['name']);
        $total = $sens->getListSize();
        
        echo '<div class="tab-pane'.((ArrayTool::first($sensors,$key))?" active":"").'" id="'.$sensor['name'].'">';
        
        //if there is data collected
        if ($total<1) {
          echo "No data collected yet, just hang on for a cupple of minutes";
        }else{
          $paginator = new Paginator($itemsPerPage,$pagesToDisplay,$total);
          
          $list = $sens->getList((($page*$itemsPerPage)-$itemsPerPage),$itemsPerPage);
          
          $listStart=(($page*$itemsPerPage)-$itemsPerPage);
          $listEnd=($page*$itemsPerPage>$total)?$total:($page*$itemsPerPage);
          echo 'Listing '.$listStart.' - '.$listEnd.' of '.$total.' from '.$sensor['name'].'<br>';
          
          echo $paginator->getPagination($page,$sensor['name']);
          echo '<table class="table table-striped" style="margin-top:20px;">'
              .'<tr><th>#</th><th>Temp</th><th>Time</th></tr>';
          foreach ($list as $row) {
            echo '<tr><td>'.$row['id'].'</td><td>'.$row['temp'].'</td><td>'.$row['timestamp'].'</td></tr>';
          }
          echo '</table>';
          
          echo $paginator->getPagination($page,$sensor['name']);
        }
        echo '</div>';
      }
      
      echo '</div>';
      ?>
      
      <?php require_once(TEMPLATES_PATH.'/footer.php'); ?>
    </div>
  </body>
  <script src="lib/js/loadtotab.js"></script>
</html>
