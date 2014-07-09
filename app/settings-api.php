<?php
use Snilius\Util\Bootstrap\Alert;
use Snilius\RackTemp\Api\Api;

$api = new Api();
if(isset($_GET['delkey'])){
  $id = $_GET['delkey'];
  if($api->deleteKey($id))
    echo Alert::success('Key removed');
  else
    echo Alert::warning('Some thing whent wrong.. give it another try');
}

if (isset($_POST['submit-api'])) {
  $name = @$_POST['name'];
  if($api->newKey($name))
    echo Alert::success('API key <strong>'.$name.'</strong> created');
  else
    echo Alert::warning('Some thing whent wrong.. give it another try');
}
?>
<h3>Add key</h3>
<form class="form-horizontal" role="form" action="settings.php#api" method="post">
  <div class="form-group">
    <label for="name" class="col-lg-1 control-label">Name</label>
    <div class="col-lg-3">
      <input type="text" class="form-control" id="name" name="name" placeholder="Just so you can identify it">
    </div>
    <input type="submit" class="btn btn-primary" name="submit-api" value="Generate">
  </div>
</form>

<h3>Keys</h3>
<table class="table">
<tr><th>#</th><th>Name</th><th>Key</th><th>Last Access</th><th>Actions</th></tr>
<?php
$list = $api->getKeys();
foreach ($list as $l) {
  $access=(isset($l['last_access']))?$l['last_access']:'<span class="text-muted">Never Accessed</span>';
  echo '<tr><td>'.$l['id'].'</td><td>'.$l['name'].'</td><td>'.$l['key'].'</td><td>'.$access.'</td><td style="padding:2px;"><a href="settings.php?delkey='.$l['id'].'#api" class="btn btn-danger">Delete</a></td></tr>';
}
?>
</table>
<h3>Usage</h3>

<?php
$apikey = 'UtUSD3gIKFz8BsPSQSJ2dmYc73b3Mu42uG1c8YibCEetgDj4g';
$timestamp = time();
$token = hash('sha512', $timestamp . $apikey);
 ?>

<pre class="prettyprint">
&lt;?php
$apikey = 'a_sample_key';
// value: a_sample_key
$timestamp = time();
// value: <?php echo $timestamp."\n" ?>
$token = hash('sha512', $timestamp . $apikey);
// value: '<?php echo $token; ?>'
?&gt;
</pre>
This is the used in HTTP Basic auth with the timestamp as user and token as password.
<script src="https://google-code-prettify.googlecode.com/svn/loader/run_prettify.js"></script>
