<?php

/*

   Los passwords en la base de datos deben estar construidos de la siguiente forma:

   md5(username:realm:password)


*/

require_once("include/auth.inc.php");

echo "<p>BIEN HECHO NENEEEE!!!!</p>";
echo "<p>" . microtime(TRUE) . "</p>";
echo "<a href='logout.php'>Logout</a>";

echo "<pre>";
echo session_id() . "\n";
print_r($_SESSION);
echo $_SERVER["PHP_AUTH_DIGEST"];
echo "</pre>";

?>
