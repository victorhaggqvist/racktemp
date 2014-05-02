<?php 
use Snilius\Util\Bootstrap\Alert;

if (isset($_POST['submit-general'])) {
  $auth=@$_POST['auth'];
  $useCdn=@$_POST['use-cdn'];
  $manualSensorAdd=@$_POST['manual-sensor-add'];
  $s->setValue('auth',($auth=="on")?1:0);
  $s->setValue('use-cdn',($useCdn=="on")?1:0);
  $s->setValue('manual-sensor-add',($manualSensorAdd=="on")?1:0);
  echo Alert::success('Settings updated');
}
?>
<form class="form-horizontal" role="form" action="settings.php#general" method="post">
  <div class="checkbox">
    <label>
      <input type="checkbox" name="auth" <?php echo ($s->getValue('auth')==1)?'checked':'';?>> Require Authentication
    </label>
    <span class="help-block">Require users to login in order to access racktemp</span>
  </div>
  
  <div class="checkbox">
    <label>
      <input type="checkbox" name="use-cdn" <?php echo ($s->getValue('use-cdn')==1)?'checked':'';?>> Use CDN for Bootstrap
    </label>
    <span class="help-block">Load Bootstrap resources from CDN rather than from local machine. A internet connection are obviously required to do this</span>
  </div>
  
  <div class="checkbox">
    <label>
      <input type="checkbox" name="manual-sensor-add" <?php echo ($s->getValue('manual-sensor-add')==1)?'checked':'';?>> Manualy add sensors
    </label>
    <span class="help-block">Enable the ability to manualy add sensors</span>
  </div>
  
  <input type="submit" class="btn btn-primary" name="submit-general" value="Save">
</form>