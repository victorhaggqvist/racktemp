<footer>Copyrigt &copy; 2013 Snilus.com <div class="pull-right">Licensed under GPL v2</div></footer>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="lib/js/vendor/jquery-1.10.2.min.js"><\/script>')</script>
<script src="lib/js/plugins.js"></script>
<script src="lib/js/main.js"></script>

<?php 
if($s->getValue('use-cdn')==1){
  echo '<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>';
}else{
  echo '<script src="lib/js/bootstrap.min.js"></script>';
}
?>

<!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
<script>
    (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
    function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
    e=o.createElement(i);r=o.getElementsByTagName(i)[0];
    e.src='//www.google-analytics.com/analytics.js';
    r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
    ga('create','UA-XXXXX-X');ga('send','pageview');
</script>
