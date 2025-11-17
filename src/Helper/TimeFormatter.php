<?php
  namespace App\Helper;

  class TimeFormatter {
    public static function clockTimeToUnix($time): int {
      list($h, $m, $s) = explode(":",$time);
      return $h * 3600 + $m * 60 + $s;
    }
  }
?>
