<footer>Copyright &copy; 2013 Snilus.com <div class="pull-right">Licensed by GPLv3</div></footer>
<script src="js/jquery.min.js"></script>


<?php
if($s->getValue('use-cdn')==1){
  echo '<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>';
}else{
  echo '<script src="js/bootstrap.min.js"></script>';
}

if ($s->getValue('send-stats') == 1)
  require_once 'lib/analytics.php';

?>
