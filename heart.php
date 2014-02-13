<?php
require_once("memory.php");
$datetime = getdate();
echo $datetime['hours'].":".$datetime['minutes'].":".$datetime['seconds'];
$myMemory = ABMemory('mysql.serversfree.com', 'u366911672_ab', 'u366911672_ab', 'parole');
try {
  $myMemory -> connectMySQL();
} catch (ABMemoryException $e) {
  echo $e;
}
?>
