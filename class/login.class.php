<?php

class Login
{
   private $realm;
   private $nonce;
   private $opaque;
   private $digest;
   private $database;
   private $user;

   public function __construct($digest) {

      $this->database = Database::get_instance();
      $this->realm = "Private Secure Network";
      $this->nonce = uniqid();
      $this->opaque = md5($this->realm);
      $this->digest = $digest;
   }

   public function force_login() {

      header("HTTP/1.1 401 Unauthorized");
      $html_digest = 'WWW-Authenticate: Digest realm="' . $this->realm . '",nonce="' . $this->nonce . 
	 '",qop="auth",opaque="' . $this->opaque . '"';
      header($html_digest);

      echo "<p>Access Denied</p>";
      exit();
   }

   public function authenticate() {

      if ($this->parse_digest_data() === TRUE AND $this->get_user() === TRUE) {

	 /* verificar password */
	 $digest =& $this->digest;

	 $A1 = $this->user["password"];
	 $A2 = md5($_SERVER["REQUEST_METHOD"] . ":" . $this->digest["uri"]);
	 $valid_response = md5($A1 . ":" . $digest["nonce"] . ":" . $digest["nc"] . ":" . $digest["cnonce"] .
	    ":" . $digest["qop"] . ":" . $A2);

	 if ($valid_response === $this->digest["response"])
	    return TRUE;
	 else return FALSE;
      }
      else return FALSE;
   }

   public function user_data() {
      return $this->user;
   }

   protected function parse_digest_data() {

      $needed_parts = array(
	 "nonce"     => 1,
	 "nc"	     => 1,
	 "cnonce"    => 1,
	 "qop"	     => 1,
	 "username"  => 1,
	 "uri"	     => 1,
	 "response"  => 1
      );

      $keys = implode ("|", array_keys($needed_parts));
      $data = array();

      preg_match_all("@($keys)=(?:(['\"])([^\2]+?)\2|([^\s,]+))@",
	 $this->digest, $matches, PREG_SET_ORDER);

      foreach ($matches as $match) {
	 $data[$match[1]] = $match[3] ? $match[3] : $match[4];
	 unset($needed_parts[$match[1]]);
      }

      if (empty($needed_parts)) {

	 foreach ($data as $key => $value) {
	    $data[$key] = str_replace("\"", "", $value);
	 }

	 $this->digest = $data;
	 return TRUE;
      }
      else {
	 $this->digest = NULL;
	 return FALSE;
      }
   }

   protected function get_user() {

      $query = "SELECT * FROM users WHERE username='{$this->digest["username"]}'";
      $users = $this->database->query($query);

      if (!empty($users)) {
	 $this->user = $users[0];
	 return TRUE;
      }

      return FALSE;
   }
}

?>
