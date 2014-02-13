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
protected $CONNECT_FAILED = "Connection with MySQL failed.";
protected $DB_FAILED = "Create BD.";
protected $Tables = array("ticks" => array("dt" => "int", "vAsk" => "int", "vBid"=>"int", "pOpen" => "int",
                                           "pHigh" => "int", "pLow" => "int", "pClose" => "int"));
public function __construct ( $db_host , $db_name , $db_user, $db_pass) {
$this -> db_host = $db_host;
$this -> db_name = $db_name;
$this -> db_user = $db_user;
$this -> db_pass = $db_pass;
}

public function connectMySQL() {
if(!mysql_connect($db_host, $db_user, $db_pass)) throw new Exception($CONNECT_FAILED);
if (!mysql_select_db($db_name)) throw new Exception($DB_FAILED);
$q = mysql_query("SHOW TABLES");
echo "В таблице mytable ".mysql_num_rows($q)." записей";
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
