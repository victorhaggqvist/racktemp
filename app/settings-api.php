<?php 
use Snilius\Util\Bootstrap\Alert;
$api = new Api();
if(isset($_GET['delkey'])){
  $id=$_GET['delkey'];
  if($api->deleteKey($id))
    echo Alert::success('Key removed');
  else 
    echo Alert::warning('Some thing whent wrong.. give it another try');
}

if (isset($_POST['submit-api'])) {
  $name=@$_POST['name'];
  $api = new Api();
  if($api->newKey($name))
    echo Alert::success('API key generated');
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
$list = $api->listKey();
foreach ($list as $l) {
  $access=(isset($l['last_access']))?$l['last_access']:'<span class="text-muted">Never Accessed</span>';
  echo '<tr><td>'.$l['id'].'</td><td>'.$l['name'].'</td><td>'.$l['key'].'</td><td>'.$access.'</td><td style="padding:2px;"><a href="settings.php?delkey='.$l['id'].'#api" class="btn btn-danger">Delete</a></td></tr>';
}
?>
</table>