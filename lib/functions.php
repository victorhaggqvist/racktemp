<?php
function mktemp($input,$round=true,$unit="c"){
  $ret=0;
  $temp=substr($input,0,2).'.'.substr($input,2,5);
  if($unit=="f"){
    $t=$temp*1.8+32;
    $temp=$t;
  }
  
  if($round)
    $ret=round($temp,1);
  else
    $ret=$temp;
  return $ret;
}

function alertDanger($msg){
  return '<div class="alert alert-danger"><strong>Oh snap!</strong> '.$msg.'</div>';
}

function alertSuccess($msg){
  return '<div class="alert alert-success"><strong>Sweet!</strong> '.$msg.'</div>';
}

 ?>
