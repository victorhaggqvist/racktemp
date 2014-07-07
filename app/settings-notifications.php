<?php

use Snilius\Util\Bootstrap\Alert;
use Snilius\RackTemp\Mailer;

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
?>
<div class="row">
  <div class="col-md-12">
    <h3>Email notifications</h3>
    <form class="form-horizontal" role="form" action="settings.php#notifications" method="post">

    <?php
    $set = $s->getValues(array('smtp-host', 'smtp-port', 'smtp-auth', 'smtp-user', 'smtp-password', 'smtp-encryption', 'smtp-to'));
     ?>

      <h4>SMTP Settings</h4>
      <div class="form-group">
        <label for="smtp-presets" class="col-sm-1 control-label">Presets</label>
        <div class="col-sm-5">
          <select class="form-control" id="smtp-presets">
            <option value="-1" selected>None</option>
            <option value="0">Gmail</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label for="smtp-host" class="col-sm-1 control-label">Host</label>
        <div class="col-sm-5">
          <input type="text" class="form-control" name="smtp-host" id="smtp-host" value="<?=$set['smtp-host']?>" placeholder="smtp.example.com" required>
        </div>
      </div>

      <div class="form-group">
        <label for="smtp-port" class="col-sm-1 control-label">Port</label>
        <div class="col-sm-5">
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
        <label class="col-sm-1 control-label" for="smtp-user">User</label>
        <div class="col-md-5">
          <input id="smtp-user" name="smtp-user" type="text" value="<?=$set['smtp-user']?>" placeholder="user@example.com" class="form-control" <?php echo ($set['smtp-auth']=='1')?'':'disabled'; ?>>
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-1 control-label" for="smtp-password">Password</label>
        <div class="col-md-5">
          <input id="smtp-password" name="smtp-password" type="password" value="<?=$set['smtp-password']?>" placeholder="**********" class="form-control" <?php echo ($set['smtp-auth']=='1')?'':'disabled'; ?>>
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-1 control-label" for="smtp-encryption">Encryption</label>
        <div class="col-md-5">
          <select id="smtp-encryption" name="smtp-encryption" class="form-control">
            <option value="1" <?php echo ($set['smtp-encryption']=='1')?'selected':''; ?>>None</option>
            <option value="2" <?php echo ($set['smtp-encryption']=='2')?'selected':''; echo (!$set['smtp-encryption'])?'selected':''; ?>>TLS</option>
            <option value="3" <?php echo ($set['smtp-encryption']=='3')?'selected':''; ?>>SSL</option>
          </select>
        </div>
      </div>

      <h4>Receivers</h4>
      <div class="form-group">
        <label class="col-sm-1 control-label" for="smtp-to">Recipient(s)</label>
        <div class="col-md-5">
          <input id="smtp-to" name="smtp-to" type="text" value="<?=$set['smtp-to']?>" placeholder="user@example.com" class="form-control" required>
          <span class="help-block">Multiple recipients are comma (,) separated</span>
        </div>
      </div>

      <div class="form-group">
        <div class="col-sm-offset-1 col-md-4">
          <button id="smtp-test" class="btn btn-default" name="send-test">Send test</button>
          <input type="submit" class="btn btn-primary" name="submit-notification" value="Save">
        </div>
      </div>
    </form>
  </div>
</div>

