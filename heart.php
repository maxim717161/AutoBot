<?php
require_once("memory.php");
$datetime = getdate();
echo $datetime['hours'].":".$datetime['minutes'].":".$datetime['seconds'];
$myMemory = new ABMemory();
try {
  $myMemory -> connectMySQL();
  $myMemory -> newTicks();
} catch(Exception $e) {
  echo $e -> getMessage();
}
?>
