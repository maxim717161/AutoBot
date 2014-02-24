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
  protected $Tables = array();
  
  public function __construct ( $db_host , $db_name , $db_user, $db_pass) {
    $this ->db_host = $db_host;
    $this ->db_name = $db_name;
    $this ->db_user = $db_user;
    $this ->db_pass = $db_pass;
    $this->Tables [ "ticks" ] = array ( "time" => "datetime" , "idMarket" => "int(11)" , "vAsk" => "double" , "vBid" =>"double" , "pOpen" => "double" , "pHigh" =>"double", "pLow" =>"double", "pClose" =>"double", "PRIMARY KEY" => "(time,idMarket)");
    $this->Tables [ "users" ] = array ( "email" =>"char(255)" , "pass" => "char(255)" , "uStatus" => "char(12)" , "lastEnter" => "datetime" , "PRIMARY KEY" => "(email)");
    $this->Tables ["market"] = array("idMarket"=>"int(11)", "nameBurse" => "char(33)", "urlTicker" => "text", "PRIMARY KEY" => "(idMarket)");
  }
  
  public function newTicks() {
    $marts = $this->my_table_array("market");
    foreach($marts as $val) {
      
    }
  }
  
  protected function my_table_array($table, $columns = "*") {
    $result = $this -> my_query ( "SELECT " . $columns . " FROM " . $table);
    for( $my_table = array (); $row = mysql_fetch_row ( $result ); $my_table[] =$row){}
    mysql_free_result ( $result );
    return $my_table;
  }
  
  protected function my_query($sql) {
    echo "<br>".$sql;
    $result = mysql_query ( $sql );
    if (! $result ) throw new Exception( $QUERY_FAILED .mysql_error ());
    echo ". ok!";
    return $result;
  }
  
  public function connectMySQL() {
    if(!mysql_connect($this->db_host, $this->db_user, $this->db_pass)) throw new Exception($CONNECT_FAILED.mysql_error());
    if (!mysql_select_db($this->db_name)) throw new Exception($DB_FAILED.mysql_error());
    $this->my_db_test();
  }
  
  protected function my_db_test() {
    $result = $this->my_query("SHOW TABLES");
    $newTables = array();
    if ( mysql_num_rows ( $result) > 0) {
      for($tbl=array();$row=mysql_fetch_row($result);$tbl[$row[0]]=array());
      $newTables=array_diff_assoc( $this->Tables , $tbl );
      $editTables=array_intersect_assoc($this->Tables, $tbl);
      mysql_free_result($result);
      foreach($editTables as $key1 => $val1) {
        $result =$this->my_query("SHOW FIELDS FROM ".$key1);
        for( $tbl =array (), $pk=array(); $row =mysql_fetch_row ($result );$tbl[$row[0]]=$row[1]){
          if($row[3]=="PRI") $pk[]=$row[0];
        }
        if(count($pk) >0) $tbl["PRIMARY KEY"] = "(".implode(",",$pk).")";
        mysql_free_result($result);
        $diffCols = array_diff_assoc($val1, $tbl);
        $keys = array_keys($val1);
        $oldkeys = array_keys($tbl);
        $delColsNames = array_diff($oldkeys, $keys);
        foreach($diffCols as $key2=>$val2) {
          if($key2 == "PRIMARY KEY") {
            if(array_key_exists($key2,$tbl)) {$this->my_query("ALTER TABLE ".$key1." DROP PRIMARY KEY");}
            $this->my_query("ALTER TABLE ".$key1." ADD PRIMARY KEY ".$val2);
          } elseif(array_key_exists($key2,$tbl)) {
            $this->my_query("ALTER TABLE ".$key1." CHANGE ".$key2." ".$key2." ".$val2);
          } else {
            $after = "";
            $index = array_search($key2, $keys);
            if($index < count($val1)) {
              if($index==0) {
                $after = " FIRST";
              } else {
                if(count($keys) > 1)$after = " AFTER ".$keys[$index -1];
              }
            }
            $this->my_query("ALTER TABLE ".$key1." ADD ".$key2." ".$val2.$after);
          }
        }
        // del cols
        foreach($delColsNames as $val3) {
          $this->my_query("ALTER TABLE ".$key1." DROP ".$val3);
        }
      }
    } else {
      $newTables = $this->Tables;
      mysql_free_result($result);
    }
    
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
      $this->my_query($sql);
    }
  }
  
  public function addTrades() {
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
}

}
?>
