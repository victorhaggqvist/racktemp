<?php

/**
 * Settings
 */
class Settings {
	private $pdo;
	
	public function __construct() {
		$this->pdo=new PDOHelper($GLOBALS['db_conf']);
	}
	
	public function getValue($key)
	{
		$sql="SELECT * FROM settings WHERE `key`=:key";
		$args=array('key'=>$key);
		$res=$this->pdo->prepQuery($sql,$args);
		if($res[0]==1){
		  return $res[2][0]['value'];
		}else 
			return $res;
	}
	
	public function setValue($key,$value)
	{
		$sql="INSERT INTO settings (key,value) VALUES (:key,:value)
          ON DUPLICATE KEY UPDATE
          key = VALUES(:key),
          value = VALUES(:value)";
		$args=array('key'=>$key,'value'=>$value);
		$res=$this->pdo->prepQuery($sql,$args);
		print_r($res);
		if($res[0]==1){
		  return true;
		}else 
			return false;
	}
}


?>
