<?php
  namespace App\Database;
  
  class DB {
    public static function connect(): \mysqli {
      $server = $_ENV["DB_HOST"];
      $username = $_ENV["DB_USER"];
      $password = $_ENV["DB_PASS"];
      $database = $_ENV["DB_NAME"];

      return mysqli_connect($server,$username,$password,$database);
    }
  }
?>
