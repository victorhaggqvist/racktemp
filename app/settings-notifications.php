<?php

use Snilius\Util\Bootstrap\Alert;
use Snilius\RackTemp\Mailer;
use Snilius\Sensor\SensorController;

if (isset($_POST['submit-notification'])) {
  $smtpHost = @$_POST['smtp-host'];
  $smtpPort = @$_POST['smtp-port'];
  $smtpAuth = @$_POST['smtp-auth'];
  $smtpUser = @$_POST['smtp-user'];
  $smtpPassword = @$_POST['smtp-password'];
  $smtpEncryption = @$_POST['smtp-encryption'];
  $smtpTo = @$_POST['smtp-to'];


  // filter recipients field
  $to = explode(',', $smtpTo);
  $to = array_map('trim', $to);
  $to = array_filter($to);
  $to = count($to)>1?implode(',', $to):$to[0];

  $s->setValue('smtp-host', $smtpHost);
  $s->setValue('smtp-port', $smtpPort);
  $s->setValue('smtp-auth', ($smtpAuth=="on")?1:0);
  $s->setValue('smtp-user', $smtpUser);
  $s->setValue('smtp-password', $smtpPassword);
  $s->setValue('smtp-encryption', $smtpEncryption);
  $s->setValue('smtp-to', $to);

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

$set = $s->getValues(array('smtp-host', 'smtp-port', 'smtp-auth', 'smtp-user', 'smtp-password', 'smtp-encryption', 'smtp-to', 'notifications-enabled', 'notifications-interval'));

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

      <h4>SMTP Settings</h4>
      <div class="form-group">
        <label for="smtp-presets" class="col-sm-2 control-label">Presets</label>
        <div class="col-sm-10">
          <select class="form-control" id="smtp-presets">
            <option value="-1" selected>None</option>
            <option value="0">Gmail</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label for="smtp-host" class="col-sm-2 control-label">Host</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" name="smtp-host" id="smtp-host" value="<?=$set['smtp-host']?>" placeholder="smtp.example.com" required>
        </div>
      </div>

      <div class="form-group">
        <label for="smtp-port" class="col-sm-2 control-label">Port</label>
        <div class="col-sm-10">
          <input type="number" class="form-control" name="smtp-port" id="smtp-port" value="<?=$set['smtp-port']?>" placeholder="465" required>
          <span class="help-block">Most likely 25, 465 or 587</span>
        </div>
      </div>

      <div class="form-group">
        <div class="col-sm-offset-1 col-sm-10">
          <div class="checkbox">
            <label>
              <input type="checkbox" name="smtp-auth" id="smtp-auth" <?php echo ($set['smtp-auth']=='1')?'checked':''; ?>> Requires authentication
            </label>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-2 control-label" for="smtp-user">User</label>
        <div class="col-sm-10">
          <input id="smtp-user" name="smtp-user" type="text" value="<?=$set['smtp-user']?>" placeholder="user@example.com" class="form-control" <?php echo ($set['smtp-auth']=='1')?'':'disabled'; ?>>
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-2 control-label" for="smtp-password">Password</label>
        <div class="col-sm-10">
          <input id="smtp-password" name="smtp-password" type="password" value="<?=$set['smtp-password']?>" placeholder="**********" class="form-control" <?php echo ($set['smtp-auth']=='1')?'':'disabled'; ?>>
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-2 control-label" for="smtp-encryption">Encryption</label>
        <div class="col-sm-10">
          <select id="smtp-encryption" name="smtp-encryption" class="form-control">
            <option value="1" <?php echo ($set['smtp-encryption']=='1')?'selected':''; ?>>None</option>
            <option value="2" <?php echo ($set['smtp-encryption']=='2')?'selected':''; echo (!$set['smtp-encryption'])?'selected':''; ?>>TLS</option>
            <option value="3" <?php echo ($set['smtp-encryption']=='3')?'selected':''; ?>>SSL</option>
          </select>
        </div>
      </div>

      <h4>Receivers</h4>
      <div class="form-group">
        <label class="col-sm-2 control-label" for="smtp-to">Recipient(s)</label>
        <div class="col-sm-10">
          <input id="smtp-to" name="smtp-to" type="text" value="<?=$set['smtp-to']?>" placeholder="user@example.com" class="form-control" required>
          <span class="help-block">Multiple recipients are comma (,) separated</span>
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

