<?php require("header.php"); ?>
<tr><td>
<form  id="login" action ="face.php" method= "post">
<table>
<tr><td>Е-майл:</td>
<td><input type="text" id="email"/></td></tr>
<tr><td>Пароль:</td>
<td><input type="password" id="parole"/></td></tr>
<tr><td><input type="checkbox" id="isreg" value="Регистрация"/></td>
<td><input type="submit" id="buton" value="Войти"/></td></tr>
</table></form>
</td></tr>
<?php require("footer.php"); ?>
