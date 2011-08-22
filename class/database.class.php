<?php

class Database {

   private static $instance = NULL;

   public static function get_instance() {

      if (is_null(Database::$instance))
	 Database::$instance = new Database();
      return Database::$instance;
   }

   private $pdo;

   private function __construct() {

      $dsn = "mysql:host=localhost;dbname=xstat";
      $this->pdo = new PDO($dsn, "root", "admin");
   }

   public function query($query) {

      $stmt = $this->pdo->prepare($query);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
   }
}

?>
