<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>RackTemp</title>
<meta name="description" content="">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="mobile-web-app-capable" content="yes">
<link rel="icon" href="favicon.ico" />
<link rel="apple-touch-icon-precomposed" href="/img/apple-touch-icon-precomposed.png" type="image/png">

<?php
if($s->getValue('use-cdn') == 1){
  echo '<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">';
  echo '<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-theme.min.css">';
}else{
  echo '<link rel="stylesheet" href="style/bootstrap.min.css">';
  // echo '<link rel="stylesheet" href="style/bootstrap-theme.min.css">';
}
?>

<link rel="stylesheet" href="style/racktemp.min.css">
