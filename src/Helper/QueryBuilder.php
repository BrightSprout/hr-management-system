<?php
  namespace App\Helper;

  class QueryBuilder {
    public static function buildDynamicUpdate(string $tablename, string $id, array $data): array {
      $dateKeys = ["dob","appointment_date","clock_in","clock_out"];
      $types = "";
      $values = [];
      $placeholderKeys = [];
      foreach ($data as $key => $value) {
        if (in_array($key, $dateKeys)) {
          $placeholderKeys[] = "$key = DATE_ADD('1970-01-01 00:00:00', INTERVAL ? SECOND)";
        } else {
          $placeholderKeys[] = "$key = ?";
        }

        $values[] = $value; 
        if (is_int($value)) {
          $types .= "i";
        } elseif (is_float($value)) {
          $types .= "d";
        } else {
          $types .= "s";
        }
      }  
      $placeholder = implode(",", $placeholderKeys);
      $sql = "UPDATE $tablename SET $placeholder WHERE id = '" . $id . "'";
      return [
        "params" => array_merge([$types], $values),
        "sql" => $sql,
      ];
    }
  }  
?>
