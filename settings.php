<?php
require_once('lib/config.inc');
require_once(LIB_PATH.'/head.inc');

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
    <div class="container" style="margin-top:40px;">
      <h1>Settings</h1>
      
      <ul class="nav nav-tabs">
        <li class="active"><a href="#sensors" data-toggle="tab">Sensors</a></li>
        <li><a href="#logging" data-toggle="tab">Logging</a></li>
        <li><a href="#general" data-toggle="tab">General</a></li>
        <li><a href="#api" data-toggle="tab">API</a></li>
      </ul>

      <div class="tab-content">
        <div class="tab-pane active" id="sensors">
          <?php require_once('settings-sensors.php'); ?>
        </div>
          
        <div class="tab-pane" id="logging">
          <?php require_once('settings-logging.php'); ?>
        </div>
        
        <div class="tab-pane" id="general">
          <?php require_once('settings-general.php'); ?>
        </div>
        
        <div class="tab-pane" id="api">
          <?php require_once('settings-api.php'); ?>
        </div>
      </div><!-- /.tab-content -->
      <?php require_once(TEMPLATES_PATH.'/footer.php'); ?>
    </div><!-- /.container -->
  </body>
  <script src="lib/js/loadtotab.js"></script>
  <script>
  $('.dropData').click(function(){
    console.log($(this));
    var sensor = $(this).attr('data-sensor');
    $('#drop-sensor').html(sensor);
    $('#dropName').val(sensor);
  });
  </script>
</html>
