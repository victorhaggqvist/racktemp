<hr>

<?php if ($_SERVER["DOCUMENT_URI"]=='/login.php'): ?>
<footer><div style="margin-top: 10px; color: #666;">RackTemp v<?php echo file_get_contents('../VERSION'); ?> &copy; 2013-2014 Snilus</div></footer>
<?php else: ?>
<footer>
  <div class="row">
    <div class="col-md-4">
      <p>RackTemp v<?php echo file_get_contents('../VERSION'); ?> <a href="https://github.com/victorhaggqvist/racktemp" class="text-muted">Source</a></p>
    </div>
    <div class="col-md-4"><p>Copyright &copy; 2013-2014 Snilus</p></div>
    <div class="col-md-4"><p>Licensed under GPLv3</p></div>
  </div>
</footer>
<?php endif; ?>
<script src="js/jquery.min.js"></script>

<?php
if($s->getValue('use-cdn') == 1)
  echo '<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>';
else
  echo '<script src="js/bootstrap.min.js"></script>';

if ($s->getValue('send-stats') == 1)
  require_once 'lib/analytics.php';

?>

<script src="js/d3.min.js"></script>
<script src="js/c3.min.js"></script>
<script src="js/racktemp.min.js"></script>

