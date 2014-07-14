<?php
require_once 'lib/head.inc';

use Snilius\Sensor\SensorController;
use Snilius\Sensor\Sensor;
use Snilius\Util\ArrayTool;
use Snilius\Util\Paginator;
?>
<!DOCTYPE html>
<html>
  <head>
    <?php require_once(TEMPLATES_PATH.'/header.php'); ?>
  </head>
  <body>
    <?php require_once(TEMPLATES_PATH.'/menu.php'); ?>
    <div class="container wrapper">
      <h1>Detailed metrics</h1>

      <?php
      $sensorController = new SensorController();
      $sensors = $sensorController->getSensors();

      $itemsPerPage = 50;
      $page = (@$_GET['page'])?@$_GET['page']:1;
      $sensorName = @$_GET['sensor'];
      $pagesToDisplay = 20;


      //tabs
      echo '<ul class="nav nav-tabs">';
      foreach ($sensors as $key => $sensor) {
        echo '<li class="'.((ArrayTool::first($sensors,$key))?"active":"").'"><a href="#'.$sensor->name.'" data-toggle="tab">'.$sensor->name.'</a></li>';
      }
      echo '</ul><div class="tab-content">';

      foreach ($sensors as $key => $sensor) {
        $sens = new Sensor($sensor->name);
        $total = $sens->getListSize();

        echo '<div class="tab-pane'.((ArrayTool::first($sensors,$key))?" active":"").'" id="'.$sensor->name.'">';

        //if there is data collected
        if ($total<1) {
          echo "No data collected yet, just hang on for a while";
        }else{
          $paginator = new Paginator($itemsPerPage, $pagesToDisplay, $total);

          $list = $sens->getList((($page*$itemsPerPage)-$itemsPerPage), $itemsPerPage);

          $listStart=(($page*$itemsPerPage)-$itemsPerPage);
          $listEnd=($page*$itemsPerPage>$total)?$total:($page*$itemsPerPage);
          echo '<p>Listing '.$listStart.' - '.$listEnd.' of '.$total.' from '.$sensor->name.'</p>';

          echo $paginator->getPagination($page, $sensor->name);
          echo '<table class="table table-striped" style="margin-top:20px;">'
              .'<tr><th>#</th><th>Temp</th><th>Time</th></tr>';
          foreach ($list as $row) {
            echo '<tr><td>'.$row['id'].'</td><td>'.$row['temp'].'</td><td>'.$row['timestamp'].'</td></tr>';
          }
          echo '</table>';

          echo $paginator->getPagination($page, $sensor->name);
        }
        echo '</div>';
      }

      echo '</div>';

      require_once TEMPLATES_PATH.'/footer.php';
      ?>
    </div><!-- /.container -->
  </body>
  <script>
    RackTemp.loadToTab();
  </script>
</html>
