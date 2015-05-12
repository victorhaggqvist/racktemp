<?php

use Snilius\Util\Bootstrap\Alert;
use Snilius\RackTemp\Mailer;
use Snilius\Sensor\SensorController;

if (isset($_POST['submit-notification'])) {
  $mgKey = @$_POST['mg-key'];
  $mgDomain = @$_POST['mg-domain'];
  $mgTo = @$_POST['mg-to'];


  // filter recipients field
  $to = explode(',', $mgTo);
  $to = array_map('trim', $to);
  $to = array_filter($to);
  $to = count($to)>1?implode(',', $to):$to[0];

  $s->setValue('mg-domain', $mgDomain);
  $s->setValue('mg-key', $mgKey);
  $s->setValue('mg-to', $mgTo);

  $sensorController = new SensorController();
  $sensors = $sensorController->getSensors();

  foreach ($sensors as $sensor) {
    $s->setValue('tempt-'.$sensor->name.'-max', $_POST['tempt-'.$sensor->name.'-max']);
    $s->setValue('tempt-'.$sensor->name.'-min', $_POST['tempt-'.$sensor->name.'-min']);
  }

  $s->setValue('notifications-enabled', (@$_POST['notifications-enabled']=="on")?1:0);
  $s->setValue('notifications-interval', $_POST['notifications-interval']);

  echo Alert::success('Settings updated');
}

if (isset($_POST['send-test'])) {
  $mailer = new Mailer();
  $test = $mailer->sendTest();

  if (strpos($test, 'Mailer Error')!==false) {
    echo Alert::warning($test, 'Message Error!');
  }else{
    echo Alert::success($test, 'Sweet!');
  }
}

$set = $s->getValues(array('mg-key', 'mg-domain', 'mg-to', 'notifications-enabled', 'notifications-interval'));

?>

<form class="form-horizontal" role="form" action="settings.php#notifications" method="post">
<div class="row">
  <div class="col-md-6">
    <h3>Notifications</h3>

    <div class="form-group">
      <label for="notifications-enabled" class="col-sm-4 control-label">Notifications enabled</label>
      <div class="col-sm-8 checkbox">
        <input type="checkbox" name="notifications-enabled" id="notifications-enabled" <?php echo ($set['notifications-enabled']=='1')?'checked':''; ?>>
        <span class="help-block">When box IS checked you will get notifications</span>
      </div>
    </div>

    <div class="form-group">
      <label for="notifications-interval" class="col-sm-2 control-label">Interval</label>
      <div class="col-sm-8">
        <input type="number" class="form-control" name="notifications-interval" id="notifications-interval" value="<?=$set['notifications-interval']?>" required>
        <span class="help-block">Even if there is more to report, this is the min interval anything sent. Interval in munites. Enter 0 to ignore this.</span>
      </div>
    </div>

    <h3>Temprature treshholds</h3>
    <p>If temp goes above max or bellow min, notifications will be sent.</p>
  <?php
  $sensorController = new SensorController();
  $sensors = $sensorController->getSensors();

  foreach ($sensors as $sensor) {
    ?>
    <div class="form-group">
      <label  class="col-sm-2 control-label"><?=$sensor->name?></label>
      <div class="col-sm-4">
        <div class="input-group">
          <span class="input-group-addon">MIN</span>
          <input type="number" class="form-control" name="tempt-<?=$sensor->name?>-min" value="<?=$s->getValue('tempt-'.$sensor->name.'-min')?>">
        </div>
      </div>
      <div class="col-sm-4">
        <div class="input-group">
          <input type="number" class="form-control" name="tempt-<?=$sensor->name?>-max" value="<?=$s->getValue('tempt-'.$sensor->name.'-max')?>">
          <span class="input-group-addon">MAX</span>
        </div>
      </div>
    </div>
    <?php } ?>
    <div class="form-group">
      <div class="col-sm-offset-2 col-sm-10">
        <input type="submit" class="btn btn-primary" name="submit-notification" value="Save">
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <h3>Email notifications</h3>

      <h4>Mailgun Settings</h4>
      <div class="form-group">
        <label for="mg-domain" class="col-sm-2 control-label">Domain</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" name="mg-domain" id="mg-domain" value="<?=$set['mg-domain']?>" placeholder="https://api.mailgun.net/v2/mg.example.com" required>
        </div>
      </div>

      <div class="form-group">
        <label for="mg-key" class="col-sm-2 control-label">API Key</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" name="mg-key" id="mg-key" value="<?=$set['mg-key']?>" required>
        </div>
      </div>

      <div class="form-group">
        <label for="mg-to" class="col-sm-2 control-label">To</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" name="mg-to" id="mg-to" value="<?=$set['mg-to']?>" required>
        </div>
      </div>

       <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
          <button id="smtp-test" class="btn btn-default" name="send-test">Send test</button>
          <input type="submit" class="btn btn-primary" name="submit-notification" value="Save">
         </div>
       </div>
  </div>
</div>
</form>

