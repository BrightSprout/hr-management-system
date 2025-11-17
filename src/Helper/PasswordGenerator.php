<?php
  namespace App\Helper;

  class PasswordGenerator {
    public static function generate(int $len) {
      $alphanum = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
      $pass = array();
      $alphaLength = strlen($alphanum) - 1;
      for ($i = 0; $i < $len; $i++) {
          $n = rand(0, $alphaLength);
          $pass[] = $alphanum[$n];
      }
      return implode($pass); 
    }
  }
?>
