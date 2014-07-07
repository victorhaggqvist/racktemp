<?php
require_once 'lib/head.inc';
?>
<!DOCTYPE html>
<html>
  <head>
    <?php require_once TEMPLATES_PATH.'/header.php'; ?>
  </head>
  <body>
    <?php require_once TEMPLATES_PATH.'/menu.php'; ?>
    <div class="container wrapper">

      <ul class="nav nav-tabs">
        <li class="active"><a href="#sensors" data-toggle="tab">Sensors</a></li>
        <li><a href="#logging" data-toggle="tab">Logging</a></li>
        <li><a href="#general" data-toggle="tab">General</a></li>
        <li><a href="#api" data-toggle="tab">API</a></li>
        <li><a href="#notifications" data-toggle="tab">Notifications</a></li>
      </ul>

      <div class="tab-content">
        <div class="tab-pane active" id="sensors">
          <?php require_once 'settings-sensors.php'; ?>
        </div>

        <div class="tab-pane" id="logging">
          <?php require_once 'settings-logging.php'; ?>
        </div>

        <div class="tab-pane" id="general">
          <?php require_once 'settings-general.php'; ?>
        </div>

        <div class="tab-pane" id="api">
          <?php require_once 'settings-api.php'; ?>
        </div>

        <div class="tab-pane" id="notifications">
          <?php require_once 'settings-notifications.php'; ?>
        </div>
      </div><!-- /.tab-content -->

      <?php require_once TEMPLATES_PATH.'/footer.php'; ?>
    </div><!-- /.container -->

    <script>
    RackTemp.loadToTab();
    RackTemp.smtpSettingsSetup();
    $('.dropData').click(function(){
      console.log($(this));
      var sensor = $(this).attr('data-sensor');
      $('#drop-sensor').html(sensor);
      $('#dropName').val(sensor);
    });
    </script>
  </body>
</html>
