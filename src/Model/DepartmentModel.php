<?php
  namespace App\Model;

  use App\Helper\TimeFormatter;
  use App\Helper\QueryBuilder;
  
  class DepartmentModel {
    public string $id;
    public string $name;
    public array $dayoffs;
    public int $clock_in;
    public int $clock_out;
    
    public function __construct(array $data) {
      $this->id = $data["id"];
      $this->name = $data["name"];
      $this->dayoffs = $data["dayoffs"];
      $this->clock_in = $data["clock_in"];
      $this->clock_out = $data["clock_out"];  
    }

    public static function listDepartments($db): array {
      $sql = "SELECT * FROM departments WHERE deleted = 0";
      $result = $db->query($sql); 
      $departments = [];
      while ($row = $result->fetch_assoc()) {
        $departments[$row["id"]] = new DepartmentModel([
          "id" => $row["id"],
          "name" => $row["name"],
          "dayoffs" => json_decode($row["dayoffs"],true),
          "clock_in" => TimeFormatter::clockTimeToUnix($row["clock_in"]),
          "clock_out" => TimeFormatter::clockTimeToUnix($row["clock_out"]),
        ]);
      }
      return $departments; 
    }

    public static function create($db, array $data) {
      $sql = "INSERT INTO departments (id, name, dayoffs, clock_in, clock_out) VALUES (?,?,?,DATE_ADD('1970-01-01 00:00:00', INTERVAL ? SECOND),DATE_ADD('1970-01-01 00:00:00', INTERVAL ? SECOND))";
      $db->query("SET time_zone = '+00:00';");
      $dayoffs = json_encode($data["dayoffs"]);
      $stmt = $db->prepare($sql);
      $stmt->bind_param("sssii",
        $data["id"],
        $data["name"],
        $dayoffs,
        $data["clock_in"],
        $data["clock_out"],
      );

      if (!$stmt->execute()) {
        throw new \Exception("Insertion Failed!");
      }  
      
      return new DepartmentModel([
        "id" => $data["id"],
        "name" => $data["name"],
        "dayoffs" => $data["dayoffs"],
        "clock_in" => $data["clock_in"],
        "clock_out" => $data["clock_out"],
      ]);
    }

    public static function update($db, array $data): bool {
      $query = QueryBuilder::buildDynamicUpdate("departments", $data["id"], $data["department"]);
      $db->query("SET time_zone = '+00:00';");
      $stmt = $db->prepare($query["sql"]);
      $params = $query["params"];
      $tmp = [];
      foreach ($params as $key => $value) {
        $tmp[$key] = &$params[$key];
      }
      call_user_func_array([$stmt, "bind_param"], $tmp);
      return $stmt->execute();
    }
  }
?>
