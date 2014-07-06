<?php
require_once 'lib/head.inc' ;
?>
<!DOCTYPE html>
<html>
  <head>
    <?php require_once TEMPLATES_PATH.'/header.php'; ?>
  </head>
  <body>
    <?php require_once TEMPLATES_PATH.'/menu.php'; ?>
    <div class="container wrapper">

      <div class="row">
        <div class="col-md-6">
          <h3>Today</h3>
          <div id="today"></div>
        </div>
        <div class="col-md-6">
          <h3>Week</h3>
          <!-- <div id="today2" style="width: 500px; height:200px;"></div> -->
          <div id="week"></div>
        </div>
      </div>

      <?php require_once TEMPLATES_PATH.'/footer.php'; ?>
    </div><!-- /.container -->
  </body>

  <?php
  $api = new \Snilius\RackTemp\Api\Api();
   ?>

  <script>
  RackTemp.setApiInfo(<?php echo $api->getWebKey(); ?>); // jshint ignore:line
  RackTemp.createChartToday();
  RackTemp.createChartWeek();

  </script>
</html>

