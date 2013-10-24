<?php
namespace Snilius\Util\Bootstrap;

/*
 * Alert helper for Twitter Boostrap alerts
 * Designed for Bootstrat v3.0
 */
class Alert {
  public static function danger($msg,$strong=true) {
    return '<div class="alert alert-danger">'.(($strong)?"<strong>Oh snap!</strong> ":"").$msg.'</div>';
  }
  
  public static function warning($msg,$strong=true){
    return '<div class="alert alert-warning">'.(($strong)?"<strong>Warning!</strong> ":"").$msg.'</div>';
  }
  
  public static function success($msg,$strong=true){
    return '<div class="alert alert-success">'.(($strong)?"<strong>Sweet!</strong> ":"").$msg.'</div>';
  }
  
  public static function info($msg,$strong=true){
    return '<div class="alert alert-info">'.(($strong)?"<strong>Sweet!</strong> ":"").$msg.'</div>';
  }
}
?>