<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title></title>
<meta name="description" content="">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="mobile-web-app-capable" content="yes">

<!-- Place favicon.ico and apple-touch-icon.png in the root directory -->

<link rel="stylesheet" href="lib/css/normalize.css">
<link rel="stylesheet" href="lib/css/main.css">
<script data-main="js/main" src="js/require.js"></script>
<script src="lib/js/vendor/modernizr-2.6.2.min.js"></script>

<?php 
if($s->getValue('use-cdn')==1){
  echo '<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-theme.min.css">';
}else{
  echo '<link rel="stylesheet" href="lib/css/bootstrap.min.css">
        <link rel="stylesheet" href="lib/css/bootstrap-theme.min.css">';
}
?>

<link rel="stylesheet" href="lib/css/racktemp.css">
