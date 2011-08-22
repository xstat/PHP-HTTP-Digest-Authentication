<?php

error_reporting(E_ALL);
ini_set("display_errors", TRUE);

function __autoload($class_name) {

   $file_name = "class/" . strtolower($class_name) . ".class.php";
   require_once($file_name);
}

session_start();

if (isset($_SERVER["PHP_AUTH_DIGEST"])) {

   if (!isset($_SESSION["user"])) {

      $login = new Login($_SERVER["PHP_AUTH_DIGEST"]);

      if ($login->authenticate() === TRUE) {

	 $user = new User($login->user_data());
	 $_SESSION["user"] = serialize($user);
      }
      else $login->force_login();
   }
   else if ($_SESSION["user"] === FALSE) {

      unset($_SESSION["user"]);
      $login = new Login($_SERVER["PHP_AUTH_DIGEST"]);
      $login->force_login();
   }
}
else {

   $login = new Login(NULL);
   $login->force_login();
}

?>
