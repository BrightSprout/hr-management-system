<?php
  namespace App\Helper;

  class UUIDGenerator {
    public static function v4(): string {
      $data = random_bytes(16);

      // Set version to 0100 (UUIDv4)
      $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
      // Set bits 6-7 to 10 (RFC 4122 variant)
      $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

      return vsprintf("%s%s-%s-%s-%s-%s%s%s", str_split(bin2hex($data), 4));
    }
  }
?>
