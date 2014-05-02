<?php
namespace Snilius\Util;

use \PDO;

/*
 * A helper class for PDO
 * @author Victor Häggqvist
 * @version 0.1
 * */
class PDOHelper extends PDO{
   private $cfg;
   public $dsn;

   function __construct($cfg) {
      try {
        $this->dsn = "mysql:host=$cfg->host;dbname=$cfg->db";
        parent::__construct($this->dsn, $cfg->user, $cfg->pass);
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // throw exceptions
        $this->cfg = $cfg;
      }
      catch(Exception $e) {
         print "Error: " . $e->getMessage();
      }
  }

  public function simpleExec($sql) {
    try{
      $sth = $this->exec($sql);
      return array(
          1
      );
    }
    catch(Exception $e){
      return $this->_err($e);
    }
  }
  /**
   * A simple query
   * @param SQL Statement $sql
   * @return multitype:number NULL
   */
  public function justQuery($sql){
    try{
      $sth = $this->prepare($sql);
      $sth->execute();
      return array(
            1,
            $sth->rowCount(),
            $sth->fetchAll(PDO::FETCH_ASSOC)
      );
    }
    catch(Exception $e){
      return $this->_err($e);
    }
  }

  /**
   * Bulk action w/o return value
   * @param SQL Statement $sql
   * @param Arguments $args
   * @return multitype:number |multitype:number NULL
   */
  public function prepExec($sql,$args){
    try{
      $sth = $this->prepare($sql);
      $sth->execute($args);
      return array(
            1,
            $sth->rowCount()
      );
    }
    catch(Exception $e){
      return $this->_err($e);
    }
  }
  
  /**
   * A prepered insert statemen that returns the generated PK for inserted row
   * @param SQL Statement $sql
   * @param Arguments $args
   * @return multitype:number string |multitype:number NULL
   */
  function prepInsert($sql,$args) {
    try{
      $sth = $this->prepare($sql);
      $sth->execute($args);
      return array(
          1,
          $this->lastInsertId()
      );
    }
    catch(Exception $e){
      return $this->_err($e);
    }
  }
  
  /**
   * Bulk action with return value
   * @param SQL Statement $sql
   * @param Arguments $args
   * @return multitype:number NULL
   */
  public function prepQuery($sql,$args){
    try{
      $sth = $this->prepare($sql);
      $sth->execute($args);
      return array(
            1,
            $sth->rowCount(),
            $sth->fetchAll(PDO::FETCH_ASSOC)
        );
    }
    catch(Exception $e){
      return $this->_err($e);
    }
  }

  /**
   * Return the expected error structure
   * @param Exeption $e
   * @return multitype:number NULL
   */
  private function _err($e) {
    return array(
      0,
      $e->getMessage()
    );
  }
}
?>
