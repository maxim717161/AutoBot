<?php
$ab_db_host = ""; // адрес базы данных
$ab_db_name = ""; // имя базы данных
$ab_db_user = ""; // пользователь базы данных
$ab_db_pass = ""; // пароль к базе данных
$ab_tmp1 = ""; // временные переменные для отладки
$ab_tmp2 = ""; //
$ab_tmp3 = ""; //
$ab_tmp4 = ""; //

function md5bin( $target ) {
  $md5 = md5(md5( $target ));
  $ret = '' ;
  for ( $i = 0 ; $i < 32; $i += 2) {
    $ret .= chr( hexdec( $md5 {$i + 1 } ) + hexdec( $md5 { $i } ) * 16 );
  }
  return md5($ret) ;
}
?>
