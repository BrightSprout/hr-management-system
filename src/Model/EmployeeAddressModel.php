<?php
  namespace App\Model;

  use App\Helper\QueryBuilder;
  
  class EmployeeAddressModel {
    public string $id;
    public string $employee_id;
    public string $type;
    public string $street_name;
    public string $barangay;
    public string $city;
    public string $province;
    public string $zipcode;
    
    public function __construct(array $data) {
      $this->id = $data["id"];
      $this->employee_id = $data["employee_id"];
      $this->type = $data["type"] ?? "CURRENT";
      $this->street_name = $data["street_name"];
      $this->barangay = $data["barangay"];
      $this->city = $data["city"];
      $this->province = $data["province"];
      $this->zipcode = $data["zipcode"];
    }

    public static function listByEmployeeId($db, array $data): array {
      $sql = "SELECT * FROM employee_addresses WHERE employee_id = ?"; 
      $stmt = $db->prepare($sql);
      $stmt->bind_param("s", $data["employee_id"]);
      $stmt->execute();
      $result = $stmt->get_result();
      $addresses = [];
      while ($row = $result->fetch_assoc()) {
        $addresses[$row["id"]] = new EmployeeAddressModel([
          "id" => $row["id"],
          "employee_id" => $row["employee_id"],
          "type" => $row["type"],
          "street_name" => $row["street_name"],
          "barangay" => $row["barangay"],
          "city" => $row["city"],
          "province" => $row["province"],
          "zipcode" => $row["zipcode"],
        ]);
      }

      return $addresses;
    }

    public static function listByUserId($db, array $data): array {
      $sql = "SELECT * FROM employee_addresses WHERE employee_id = (SELECT e.id FROM employees e WHERE e.user_id = ?)"; 
      $stmt = $db->prepare($sql);
      $stmt->bind_param("s", $data["user_id"]);
      $stmt->execute();
      $result = $stmt->get_result();
      $addresses = [];
      while ($row = $result->fetch_assoc()) {
        $addresses[$row["id"]] = new EmployeeAddressModel([
          "id" => $row["id"],
          "employee_id" => $row["employee_id"],
          "type" => $row["type"],
          "street_name" => $row["street_name"],
          "barangay" => $row["barangay"],
          "city" => $row["city"],
          "province" => $row["province"],
          "zipcode" => $row["zipcode"],
        ]);
      }

      return $addresses;
    } 

    public static function create($db, array $data): EmployeeAddressModel {
      $sql = "INSERT INTO employee_addresses (id, employee_id, type, street_name, barangay, city, province, zipcode) VALUES (?,?,?,?,?,?,?,?)";
      $db->query("SET time_zone = '+00:00';");
      $stmt = $db->prepare($sql);
      $stmt->bind_param("ssssssss",
        $data["id"],
        $data["employee_id"],
        $data["type"],
        $data["street_name"],
        $data["barangay"],
        $data["city"],
        $data["province"],
        $data["zipcode"],
      );

      if (!$stmt->execute()) {
        throw new \Exception("Insertion Failed!");
      }  
      
      return new EmployeeAddressModel([
        "id" => $data["id"],
        "employee_id" => $data["employee_id"],
        "type" => $data["type"],
        "street_name" => $data["street_name"],
        "barangay" => $data["barangay"],
        "city" => $data["city"],
        "province" => $data["province"],
        "zipcode" => $data["zipcode"], 
      ]);
    }

    public static function update($db, array $data): bool {
      $query = QueryBuilder::buildDynamicUpdate("employee_addresses", $data["id"], $data["address"]);
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

    public static function delete($db, array $data): bool {
      $sql = "DELETE FROM employee_addresses WHERE id = ?";
      $stmt = $db->prepare($sql);
      $stmt->bind_param("s", $data["id"]);
      return $stmt->execute();
    }
  }
?>
