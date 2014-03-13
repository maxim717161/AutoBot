<?php
$pageName = " :: Enter";
if(isset($_POST['button'])) {
  $isError = false;
  $erStr = "";
  if(empty($_POST['email'])) {
    $isError = true;
    $erStr .= " Введите е-майл.";
  } elseif(!preg_match ("/^[a-zA-Z0-9_\.\-]+@([a-zA-Z0-9\-]+\.)+[a-zA-Z]{2,6}$/", $_POST['email'])) {
    $isError = true;
    $erStr .= " Неверный формат е-майл.";
  } else {
    $email = $_POST['email'];
  }
  if(empty($_POST['parole'])) {
    $isError = true;
    $erStr .= " Введите пароль.";
  } elseif(!preg_match ("/\A(\w){8,255}\Z/", $_POST['parole'])) {
    $isError = true;
    $erStr .= " Неверный формат пароля. Введите от 8 до 255 символов.";
  } else {
    $parole = $_POST['parole'];
  }
  if(!empty($_POST['isreg']) && $_POST['isreg'] == "on") {
    $isreg = true;
  } else {
    $isreg = false;
  }
  if(!$isError) {
    $myMemory = new ABMemory();
    try {
      $myMemory -> connectMySQL();
      $user = $myMemory -> getUser($email);
    } catch(Exception $e) {
      $isError = true;
      $erStr = $e -> getMessage();
    }
    if(!$isError) {
      if($isreg) {
        
      } else {
        
      }
    }
  } else {
    $pageName = " :: Re-Enter";
  }
}

require("header.php"); 
?>
<tr><td valign="top" align="justify"> <span style="color:red;"><b>13:12</b></span> Теперь мы видим как бы сквозь тусклое стекло, гадательно, тогда же лицем к лицу; теперь знаю я отчасти, а тогда позна'ю, подобно как я познан. <b><i>(Первое послание к Коринфянам святого апостола Павла)</i></b></td>
<tr><td valign="top" align="center">
<br/>
<form  id="login" name="login" action ="face.php" method= "post">
<table border="0">
<tr><td colspan="3" align="center"><b>Вход / Регистрация</b></td></tr>
<?php if(isset($isError)&&$isError) echo "<tr><td colspan='3' align='center' style='border:1px solid red;'><span style='color:red;'>$erStr</span></td></tr>"; ?>
<tr><td>Е-майл:</td>
<td colspan="2"><input type="text" id="email" name="email" maxlenght="255"<?php if(isset($email)){echo " value='".$email."'";} ?>/></td></tr>
<tr><td>Пароль:</td>
<td colspan="2"><input type="password" id="parole" name="parole" maxlenght="255" <?php if(isset($parole)){echo " value='".$parole."'";} ?>/></td></tr>
<tr><td>Новый?</td>
<td width="10"><input type="checkbox" id="isreg" name="isreg" style="border:3px double black;" <?php if(isset($isreg) && ($isreg == true)){echo " checked";} ?>/></td>
<td align="center"><input type="submit" id="button" name="button" value="Отправить"/></td></tr>
</table></form>
<br/>
</td></tr>
<?php require("footer.php"); ?>
