<?php
  namespace App\Database;
  
  class DB {
    public static function connect(): \mysqli {
      $server = getenv("DB_HOST");
      $username = getenv("DB_USER");
      $password = getenv("DB_PASS");
      $database = getenv("DB_NAME");

      return mysqli_connect($server,$username,$password,$database);
    }
  }
?>
