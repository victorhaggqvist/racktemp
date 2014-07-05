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
      <h3>Today</h3>
      <div class="row">
        <div class="col-md-6">
          <div id="today"></div>
        </div>
        <div class="col-md-6">
          <!-- <div id="today2" style="width: 500px; height:200px;"></div> -->
          <div id="today2"></div>
        </div>
      </div>

      <?php require_once TEMPLATES_PATH.'/footer.php'; ?>
    </div><!-- /.container -->
  </body>

  <?php
  $api = new \Snilius\RackTemp\Api();
   ?>

  <script>
  RackTemp.setApiAuth(<?php echo $api->getWebKey(); ?>); // jshint ignore:line
  function fetchData(url, callback){
    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.onreadystatechange = function () {
      if (xhr.readyState !== 4 || xhr.status !== 200){ return; }
      callback(xhr.responseText);
    };
    xhr.send();
  }

  var chart = c3.generate(RackTemp.chart.today);

  // fetch today
  fetchData(makeApiUrl('graph/span/day'), function(resp){
    var chartData = JSON.parse(resp);
    if (RackTemp.isChartEmpty(chartData)) {
      document.getElementById('today').innerHTML = 'No chart data';
    }else{
      chart.load({
        columns: chartData
      });
    }
  });

  </script>
</html>

