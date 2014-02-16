<?php
class ABMemory {
  const TRADES_DATETIME = 'date';
  const TRADES_AMOUNT = 'amount';
  const TRADES_PRICE = 'price';
  const TRADES_TRADE_TYPE = 'trade_type';
  const TRADES_BID = 'bid';
  const TRADES_ASK = 'ask';
  const TICKS_BUY = 'buy' ;
  const TICKS_SELL = 'sell';
  const TICKS_HIGH_PRICE ='high_price';
  const TICKS_LOW_PRICE= 'low_price';
  protected $db_host;
  protected $db_name;
  protected $db_user;
  protected $db_pass;
  protected $CONNECT_FAILED = "Unable to connect to DB: ";
  protected $DB_FAILED = "Unable to select DB: ";
  protected $QUERY_FAILED = "Could not successfully run query from DB: ";
  protected $Tables = array("ticks" => array("dt" => "int", "vAsk" => "int", "vBid"=>"int", "pOpen" => "int","pHigh" => "int", "pLow" => "int", "pClose" => "int"));
  
  public function __construct ( $db_host , $db_name , $db_user, $db_pass) {
    $this ->db_host = $db_host;
    $this ->db_name = $db_name;
    $this ->db_user = $db_user;
    $this ->db_pass = $db_pass;
  }
  
  public function connectMySQL() {
    if(!mysql_connect($this->db_host, $this->db_user, $this->db_pass)) throw new Exception($CONNECT_FAILED.mysql_error());
    if (!mysql_select_db($this->db_name)) throw new Exception($DB_FAILED.mysql_error());
    $sql = "SHOW TABLES";
    $result = mysql_query($sql);
    if(!$result) throw new Exception($QUERY_FAILED.mysql_error());
    $newTables = array();
    if ( mysql_num_rows ( $result) > 0) {
      //while($row = mysql_fetch_row ( $result ) {
      for($tables=array();$row=mysql_fetch_assoc($res);$tables[]=$row);
      print_r($tables);
      // }
      //$newTables= array_diff_assoc( $array1 , $array2 );                                           
    } else {
      $newTables = $this->Tables;
    }
    mysql_free_result ( $result );
    // добавляем таблицы
    foreach($newTables as $key1 => $val1) {
      $sql = "CREATE TABLE ".$key1." (";
      $addZ = false;
      foreach($val1 as $key2 => $val2) {
        if($addZ) $sql .= ", ";
        $sql .= $key2." ".$val2;
        $addZ = true;
      }
      $sql.=")";
      $result = mysql_query($sql);
    if(!$result) throw new Exception($QUERY_FAILED.mysql_error());
    }
  }
  
  public function addTrades($trades) {
    $ticks = array();
    foreach($trades as $value) {
    $dir = TICKS_BUY;
    if ($value[TRADES_TRADE_TYPE] == TRADES_BID) {
      $dir = TICKS_SELL;
    }
    if ( isset( $ticks [$value [TRADES_DATETIME]]) === false ) {
      $ticks [$value [TRADES_DATETIME]][TICKS_HIGH_PRICE] =$value [TRADES_PRICE];
      $ticks [ $value [TRADES_DATETIME]][TICKS_LOW_PRICE] = $value [TRADES_PRICE];
    } else {
      if($ticks [ $value [TRADES_DATETIME]][TICKS_HIGH_PRICE] < $value [TRADES_PRICE]) $ticks [ $value [TRADES_DATETIME]][TICKS_HIGH_PRICE] = $value [TRADES_PRICE];
      if($ticks [ $value [TRADES_DATETIME]][TICKS_LOW_PRICE] > $value [TRADES_PRICE]) $ticks [ $value [TRADES_DATETIME]][TICKS_LOW_PRICE] = $value [TRADES_PRICE];
    }
    if(isset($ticks[$value[TRADES_DATETIME]][$dir]) === true){
      $ticks [$value [TRADES_DATETIME]][$dir] += $value[TRADES_AMOUNT];
    } else {
      $ticks [$value [TRADES_DATETIME]][$dir] = $value[TRADES_AMOUNT];
    }
  }
addTicks($ticks);
}

protected function addTicks($ticks) {
$result = mysql_connect($db_host,
$db_user, $db_pass);
if (mysql_select_db($db_name) == false) {
// create table
}}}
?>
