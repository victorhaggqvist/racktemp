<?php
namespace Snilius\Util\Bootstrap;

/*
 * Alert helper for Twitter Boostrap alerts
 * Designed for Bootstrat v3
 */
class Alert {
  /**
   * Create a Bootstrap 3 Danger Alert dialog
   * @param  string  $msg    Message to put in alert
   * @param  boolean $strong Include catch word
   * @return string          HTML to display
   */
  public static function danger($msg, $strong=true) {
    return Alert::makeAlert('danger', $msg, $strong);
  }

  /**
   * Create a Bootstrap 3 Warning Alert dialog
   * @param  string  $msg    Message to put in alert
   * @param  boolean $strong Include catch word
   * @return string          HTML to display
   */
  public static function warning($msg, $strong=true) {
    return Alert::makeAlert('warning', $msg, $strong);
  }

  /**
   * Create a Bootstrap 3 Success Alert dialog
   * @param  string  $msg    Message to put in alert
   * @param  boolean $strong Include catch word
   * @return string          HTML to display
   */
  public static function success($msg, $strong=true) {
    return Alert::makeAlert('success', $msg, $strong);
  }

  /**
   * Create a Bootstrap 3 Info Alert dialog
   * @param  string  $msg    Message to put in alert
   * @param  boolean $strong Include catch word
   * @return string          HTML to display
   */
  public static function info($msg, $strong=true) {
    return Alert::makeAlert('info', $msg, $strong);
  }

  /**
   * Construct HTML for Alert
   * @param  string  $type   Type of alert
   * @param  string  $msg    Message to put in alert
   * @param  boolean $strong Include strong
   * @return string          Constructed HTML
   */
  private static function makeAlert($type, $msg, $strong) {
    return '<div class="alert alert-'.$type.'">'.(($strong)?"<strong>Sweet!</strong> ":"").$msg.'</div>';
  }
}
?>
