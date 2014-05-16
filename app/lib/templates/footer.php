<hr>
<footer>
  <div class="row">
    <div class="col-md-4">
      <p>RackTemp v<?php echo shell_exec('cat ./VERSION'); ?> <a href="https://github.com/victorhaggqvist/racktemp">Source</a></p>
    </div>
    <div class="col-md-4"><p>Copyright &copy; 2013-2014 Snilus</p></div>
    <div class="col-md-4"><p>Licensed by GPLv3</p></div>
  </div>
</footer>
<script src="js/jquery.min.js"></script>


<?php
if($s->getValue('use-cdn') == 1)
  echo '<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>';
else
  echo '<script src="js/bootstrap.min.js"></script>';

if ($s->getValue('send-stats') == 1)
  require_once 'lib/analytics.php';

?>
