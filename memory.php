<?php
require_once("keys.php");
class ABMemory {
  protected $db_host;
  protected $db_name;
  protected $db_user;
  protected $db_pass;
  protected $CONNECT_FAILED = "Unable to connect to DB: ";
  protected $DB_FAILED = "Unable to select DB: ";
  protected $QUERY_FAILED = "Could not successfully run query from DB: ";
  protected $Tables = array();
  
  public function __construct () {
    $this ->db_host = DB_HOST;
    $this ->db_name = DB_NAME;
    $this ->db_user = DB_USER;
    $this ->db_pass = DB_PASS;
    $this->Tables [ "ticks" ] = array ( "time" => "int(11) unsigned" , "idMarket" => "int(11) unsigned" , "vAsk" => "double unsigned" , "vBid" =>"double unsigned" , "pOpen" => "double unsigned" , "pHigh" =>"double unsigned", "pLow" =>"double unsigned", "pClose" =>"double unsigned", "tidClose" =>"int(11) unsigned","PRIMARY KEY" => "(time,idMarket)");
    $this->Tables [ "users" ] = array ( "email" =>"varchar(255)" , "pass" => "char(32)" , "uStatus" => "varchar(12)" , "lastEnter" => "int(11) unsigned" , "PRIMARY KEY" => "(email)");
    $this->Tables ["market"] = array("idMarket"=>"int(11) unsigned", "nameBurse" => "varchar(33)", "typeAPI" => "varchar(33)", "urlTicker" => "varchar(255)", "urlTrades" => "varchar(255)", "urlFee" => "varchar(255)", "urlDepth" => "varchar(255)", "urlInfo" => "varchar(255)", "PRIMARY KEY" => "(idMarket)");
  }
  
  public function getUser($userEmail) {
    $this->my_table_array("users","*","email=".$email);
  }
  
  public function updUserEnter() {
    
  }
  
  public function addNewUser($email, $pass) {
    
  }
  
  public function newTicks() {
    $marts = $this->my_table_array("market","idMarket,urlTrades");
    foreach($marts as $mart) {
      $btc_usd = $this -> retrieveJSON($mart['urlTrades']);
      $ticks = array();
      foreach($btc_usd as $val) {
        if(!isset($ticks[$val['date']]['pClose'])) {
          $ticks[$val['date']]['bid'] = 0;
          $ticks[$val['date']]['ask'] = 0;
          $ticks[$val['date']]['pClose'] = $val['price'];
          $ticks[$val['date']]['pOpen'] = $val['price'];
          $ticks[$val['date']]['pHigh'] = $val['price'];
          $ticks[$val['date']]['pLow'] = $val['price'];
          $ticks[$val['date']]['tidClose'] = $val['tid'];
        } else {
          $ticks[$val['date']]['pOpen'] = $val['price'];
          if($ticks[$val['date']]['pHigh'] < $val['price']) $ticks[$val['date']]['pHigh'] = $val['price'];
          if($ticks[$val['date']]['pLow'] > $val['price']) $ticks[$val['date']]['pLow'] = $val['price'];
        }
        $ticks[$val['date']][$val['trade_type']] += $val['amount'];
      }
      $result = $this->my_query("SELECT * FROM ticks WHERE time IN (".implode(",",array_keys($ticks)).") AND idMarket = ".$mart['idMarket']);
      for($exTrades = array();$row = mysql_fetch_row ( $result ); $exTrades [$row [ 0 ]] =$row);
      foreach($ticks as $key => $val) {
        if(!array_key_exists ( $key , $exTrades )) {
          $this -> my_query("INSERT INTO ticks VALUES(".$key.",".$mart['idMarket'].",".$val['ask'].",".$val['bid'].",".$val['pOpen'].",".$val['pHigh'].",".$val['pLow'].",".$val['pClose'].",".$val['tidClose'].")");
        } else {
          // сверяем пересечения
          if($val['tidClose'] > $exTrades[$key]['tidClose']) {
            $add = array();
            foreach($btc_usd as $val1) {
              if($val1['tid'] > $exTrades[$key]['tidClose'] & $val1['date'] == $key) {
                if (! isset ( $add[ 'pClose' ])) {
                  $add['bid'] = 0;
                  $add[ 'ask' ] = 0;
                  $add[ 'pHigh' ] = $val1[ 'price' ];
                  $add[ 'pLow' ] = $val1[ 'price' ];
                  $add['pClose'] = $val1['price'];
                  $add[ 'tidClose' ] = $val1 [ 'tid' ];
                } else {
                  if($add['pHigh' ] < $val1 [ 'price' ]) $add[ 'pHigh' ] = $val1 [ 'price' ];
                  if( $add['pLow' ] > $val1 ['price' ]) $add['pLow' ] = $val1 [ 'price' ];
                }
                $add[$val1 [ 'trade_type' ]] += $val1 ['amount' ];
              } else {
                break;
              }
            }
            $exTrades[$key]['vAsk'] += $add['ask'];
            $exTrades[$key]['vBid'] += $add['bid'];
            $exTrades[$key]['pClose'] = $add['pClose'];
            $exTrades[$key]['tidClose'] = $add['tidClose'];
            if($exTrades[$key]['pHigh'] < $add['pHigh']) $exTrades[$key]['pHigh'] = $add['pHigh'];
            if($exTrades[$key]['pLow'] > $add['pLow']) $exTrades[$key]['pLow'] = $add['pLow'];
            $this->my_query("UPDATE ticks SET vAsk=".$exTrades[$key]['vAsk'].",vBid=".$exTrades[$key]['vBid'].",pHigh=".$exTrades[$key]['pHigh'].",vLow=".$exTrades[$key]['pLow'].",pClose=".$exTrades[$key]['pClose'].",tidClose=".$exTrades[$key]['tidClose']." WHERE time=".$key." & idMarket=".$mart['idMarket']);
          }
        }
      }
    }
  }
  
  protected function my_table_array($table, $columns = "*", $where = "") {
    if($where != "") $where = " WHERE ".$where;
    $result = $this -> my_query ( "SELECT " . $columns . " FROM " . $table.$where);
    for( $my_table = array (); $row = mysql_fetch_assoc ( $result ); $my_table[] =$row){}
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
  
  protected function retrieveJSON($URL) {
    $opts = array('http' => array( 'method'  => 'GET', 'timeout' => 10 ));
    $context  = stream_context_create($opts);
    $feed = file_get_contents($URL, false, $context);
    $json = json_decode($feed, true);
    return $json;
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

}
?>
