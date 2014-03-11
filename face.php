<?php require("header.php"); ?>
<tr><td valign="top" align="justify"> <span style="color:red;"><b>13:12</b></span> Теперь мы видим как бы сквозь тусклое стекло, гадательно, тогда же лицем к лицу; теперь знаю я отчасти, а тогда позна'ю, подобно как я познан. <b><i>(Первое послание к Коринфянам святого апостола Павла)</i></b></td>
<tr><td valign="top" align="center">
<br/>
<form  id="login" action ="face.php" method= "post">
<table border="0">
<tr><td colspan="3" align="center"><b>Вход / Регистрация</b></td></tr>
<tr><td>Е-майл:</td>
<td colspan="2"><input type="text" id="email"/></td></tr>
<tr><td>Пароль:</td>
<td colspan="2"><input type="password" id="parole"/></td></tr>
<tr><td>Новый?</td>
<td width="10"><input type="checkbox" id="isreg" onchange="if(document.getElementById( 'isreg' ).value){document.getElementById( 'button' ).value='Регистрция'}else{document.getElementById( 'button' ).value='Войти'}"/></td>
<td align="center"><input type="submit" id="button" value="Войти"/></td></tr>
</table></form>
<br/>
</td></tr>
<?php require("footer.php"); ?>
