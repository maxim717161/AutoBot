<?php
const PN_ENTER = " :: Enter";
const PN_REENT = " :: Re-Enter";
const PN_CONPAS = " :: Confirm Pass";
const PN_CONMAIL = " :: Confirm E-mail";
const PN_MAINPAGE = " :: Main Page";
$pageName = PN_ENTER;
if(isset($_POST['login'])) {
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
    $erStr .= " Неверный формат пароля. Введите от 8 до 255 символов a-z, A-Z, 0-9.";
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
        if(isset($user) && count($user) == 0) {
          $parole = md5($parole);
          $pageName = PN_CONPAS;
        } elseif(isset($user) && count($user) > 0) {
          $isError = true;
          $erStr .= " Пользователь с таким е-майлом [".$email."] уже зарегистрирован.";
      } else {
        $isError = true;
        $erStr .= " Неизвестная ошибка. Обратитесь <a href='support.php'>службу поддержки</a>.";
      }
    }
  } else {
    $pageName = PN_REENT;
  }
}

require("header.php"); 
switch($pageName) {
case PN_ENTER:
  $numPos = "13:12";
  $txtPos = "Теперь мы видим как бы сквозь тусклое стекло, гадательно, тогда же лицем к лицу; теперь знаю я отчасти, а тогда позна'ю, подобно как я познан.";
  $autPos = "Первое послание к Коринфянам святого апостола Павла";
  break;
case PN_REENT:
  $numPos = "";
  $txtPos = "";
  $autPos = "";
  break;
case PN_CONPAS:
  $numPos = "4:22";
  $txtPos = "Нет ничего тайного, что не сделалось бы явным, и ничего не бывает потаенного, что не вышло бы наружу.";
  $uatPos = "От Марка святое Благовествование";
  break;
}

switch($pageName) {
case PN_ENTER:
case PN_REENT:
  $forma = "<form  id='forma1' name='forma1' action ='face.php' method= 'post'>";
  $forma .= "<table border='0'>";
  $forma .= "<tr><td colspan='3' align='center'><b>Вход / Регистрация</b></td></tr>";
  if(isset($isError)&&$isError) {$forma .= "<tr><td colspan='3' align='center' style='border:1px solid red;'><span style='color:red;'>".$erStr."</span></td></tr>";}
  $forma .= "<tr><td>Е-майл:</td>";
  $forma .= "<td colspan='2'><input type='text' id='email' name='email' maxlenght='255'";
  if(isset($email)) {$forma .= " value='".$email."'";} 
  $forma .= "/></td></tr>";
  $forma .= "<tr><td>Пароль:</td>";
  $forma .= "<td colspan='2'><input type='password' id='parole' name='parole' maxlenght='255'";
  if(isset($parole)) {$forma .= " value='".$parole."'";} 
  $forma .= "/></td></tr>";
  $forma .= "<tr><td>Новый?</td>";
  $forma .= "<td width='10'><input type='checkbox' id='isreg' name='isreg' style='border:3px double black;'";
  if(isset($isreg) && ($isreg == true)){$forma .= " checked";}
  $forma .= "/></td>";
  $forma .= "<td align='center'><input type='submit' id='login' name='login' value='Отправить'/></td></tr></table></form>";
  break;
case PN_CONPAS:
  break;
}
?>
<tr><td valign="top" align="justify"> <span style="color:red;"><b><?php echo $numPos; ?></b></span> <?php echo $txtPos; ?> <b><i><?php echo $autPos; ?></i></b></td>
<tr><td valign="top" align="center">
<br/>
<?php echo $forma; ?>
<br/>
</td></tr>
<tr><td valign="top" align="justify"> <span style="color:red;"><b>4:22</b></span>  Нет ничего тайного, что не сделалось бы явным, и ничего не бывает потаенного, что не вышло бы наружу. <b><i>(От Марка святое Благовествование)</i></b></td>
<tr><td valign="top" align="center">
<br/>
<form  id="forma2" name="forma2" action ="face.php" method= "post">
<table border="0">
<tr><td colspan="2" align="center"><b>Подтверждение пароля</b></td></tr>
<?php if(isset($isError)&&$isError) echo "<tr><td colspan='2' align='center' style='border:1px solid red;'><span style='color:red;'>$erStr</span></td></tr>"; ?>
<tr><td>Пароль:</td>
<td><input type="password" id="parole2" name="parole2" maxlenght="255" <?php if(isset($parole2)){echo " value='".$parole2."'";} ?>/></td></tr>
<tr><td>&nbsp;<input type="hidden" id="parole" name="parole" <?php echo "value='".$parole."'"; ?>/></td>
<td align="center"><input type="submit" id="conpas" name="conpas" value="Отправить"/></td></tr>
</table></form>
<br/>
</td></tr>
<?php 
}
require("footer.php"); 
?>
