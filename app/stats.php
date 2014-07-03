<?php
require_once 'lib/config.inc' ;
require_once LIB_PATH.'/head.inc' ;
?>
<!DOCTYPE html>
<html>
  <head>
    <?php require_once TEMPLATES_PATH.'/header.php'; ?>
    <link rel="stylesheet" href="style/c3.css">
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
          <div id="today" style="width: 500px; height:200px;"></div>
        </div>
      </div>

      <?php require_once TEMPLATES_PATH.'/footer.php'; ?>
    </div><!-- /.container -->
  </body>
  <script src="js/loadtotab.js"></script>
  <script src="js/d3.min.js"></script>
  <script src="js/c3.min.js"></script>
  <script>
  // var dataAsColumn = [
  //   ['x', '2013-12-03 15:25:03', '2013-12-03 15:30:03', '2013-12-03 15:35:03', '2013-12-03 15:40:03', '2013-12-03 15:45:03', '2013-12-03 15:50:03', '2013-12-03 15:55:03', '2013-12-03 16:00:03', '2013-12-03 16:05:03', '2013-12-03 16:10:03', '2013-12-03 16:15:03'],
  //   ['top', 20.375, 20.312, 20.437, 20.375, 20.375, 20.500, 20.500, 20.500, 20.437, 20.375, 20.437],
  //   ['bottom', 17.687, 17.37, 18.125, 18.125, 18.000, 18.000, 17.875, 17.750, 17.750, 17.875, 18.125]
  // ];

  c3.generate({
    bindto: '#today',
    data: {
      url: 'api/graph/span/hour',
      x: 'x',
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
</html>

