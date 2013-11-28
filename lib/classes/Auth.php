<?php

namespace Snilius;

use Snilius\Util\PDOHelper;

class Auth{
  public $username;
  private $pdo;
  
  function __construct() {
    $this->pdo=new PDOHelper($GLOBALS['db_conf']);
  }
  
  public function createSession() {
    $id=session_id();
    $token=uniqid('',true);
    $expiretime=time()+(60*60*20*7);

    //if($_SERVER['SERVER_NAME']=='localhost')
      //setcookie('key',$token,$expiretime,null);
    //else
      setcookie('key',$token,$expiretime,'/');
    
    $sql="INSERT INTO sessions (token,username,expire)VALUES(?,?,?)";
    $args=array($token,$this->username,$expiretime);
    if($this->pdo->prepExec($sql, $args)[0]==1)
        return true;
    else 
        return false;
  }
  
    /**
  * Check if session exist
  * @return bool session existensy
  */
  public function checkSession() {
    $key=@$_COOKIE['key'];
    if($key!=null){
      $sql="SELECT * FROM sessions WHERE `token`=?";
      $res=$this->pdo->prepQuery($sql,array($key));
      if($res[1]==1){//current session data fetched
        if(time()<$res[2][0]['expire']){//session active
          //update current session
          $newtime=time()+(60*60*20*7);
          setcookie('key',$key,$newtime);
          
          $sql="UPDATE sessions SET `expire`=? WHERE `id`=?";
          $args=array($newtime,$res[2][0]['id']);
          $this->pdo->prepExec($sql, $args);
          
          $this->username=$res[2][0]['username'];
          return true;
        }else
          return false;
      }else
        return false;
    }else
      return false;
  }
  
    /**
   * Logout current user
   */
  public function logout() {
      $key=@$_COOKIE['key'];
      if ($key!='') { //don't bather if no cookie :P
        setcookie('key','');
        $sql="DELETE FROM session WHERE `token`=?";
        $this->pdo->prepExec($sql, array($key));
      }
  }
  
  /* This function is copied from http://www.raspberrypi.org/phpBB3/viewtopic.php?f=36&t=10992
   * Since the method to get PAM working invlovs adding www-data to shadow i figured i just stick with this since php5-auth-pam is not inte Rasbian repos anyway
   * Appearently mkpasswd is not installed in Rasbian-2013-09-25 which this project is based on, this is solved by installing whois
   **/
  public function validateLogin($username, $password) {
      $shadow = explode("$", shell_exec("cat /etc/shadow | grep " . $username . " | awk -F : '{print $2}'"));
      print_r($shadow);
      if (count($shadow) < 3) {
          return 0;
      } else {
          $encription = "";
          switch ($shadow['1']) {
              case 1:
                  $encription = "MD5";  //nao testado
                  break;
              case 5:
                  $encription = "SHA-256";  //nao testado
                  break;
              case 6:
                  $encription = "SHA-512";
                  break;
              default:
                  return 0;
          }
          $pass = shell_exec("mkpasswd -m " . $encription . " " . $password . " " . $shadow['2'] . "");
          $this->username=$username;
          return (trim(implode("$", $shadow)) == trim($pass) ? 1 : 0);
      }
  }
}
?>
