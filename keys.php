<?php
const DB_HOST = "mysql.serversfree.com"; // адрес базы данных
const DB_NAME = "u366911672_ab"; // имя базы данных
const DB_USER = "u366911672_ab"; // пользователь базы данных
const DB_PASS = "parole"; // пароль к базе данных

function md5bin( $target ) {
  $md5 = md5(md5( $target ));
  $ret = '' ;
  for ( $i = 0 ; $i < 32; $i += 2) {
    $ret .= chr( hexdec( $md5 {$i + 1 } ) + hexdec( $md5 { $i } ) * 16 );
  }
  return md5($ret) ;
}
?>
