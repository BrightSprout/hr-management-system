<?php
  namespace App\Model;

  use App\Helper\QueryBuilder;
  
  class EmployeeEmergencyContactModel {
    public string $id;
    public string $employee_id;
    public string $fullname;
    public string $relationship;
    public string $phone_no;
    public ?string $email;
    public string $address;
    public int $is_primary;
    
    public function __construct(array $data) {
      $this->id = $data["id"];
      $this->employee_id = $data["employee_id"];
      $this->fullname = $data["fullname"];
      $this->relationship = $data["relationship"];
      $this->phone_no = $data["phone_no"];
      $this->email = $data["email"] ?? NULL;
      $this->address = $data["address"];
      $this->is_primary = $data["is_primary"];
    }

    public static function listByEmployeeId($db, array $data): array {
      $sql = "SELECT * FROM employee_emergency_contacts WHERE employee_id = ?"; 
      $stmt = $db->prepare($sql);
      $stmt->bind_param("s", $data["employee_id"]);
      $stmt->execute();
      $result = $stmt->get_result();
      $emergencyContacts = [];

      while ($row = $result->fetch_assoc()) {
        $emergencyContacts[$row["id"]] = new EmployeeEmergencyContactModel([
          "id" => $row["id"],
          "employee_id" => $row["employee_id"],
          "fullname" => $row["fullname"],
          "relationship" => $row["relationship"],
          "phone_no" => $row["phone_no"],
          "email" => $row["email"],
          "address" => $row["address"],
          "is_primary" => $row["is_primary"], 
        ]);
      }

      return $emergencyContacts;
    }

    public static function listByUserId($db, array $data): array {
      $sql = "SELECT * FROM employee_emergency_contacts WHERE employee_id = (SELECT e.id FROM employees e WHERE e.user_id = ?)"; 
      $stmt = $db->prepare($sql);
      $stmt->bind_param("s", $data["user_id"]);
      $stmt->execute();
      $result = $stmt->get_result();
      $emergencyContacts = [];

      while ($row = $result->fetch_assoc()) {
        $emergencyContacts[$row["id"]] = new EmployeeEmergencyContactModel([
          "id" => $row["id"],
          "employee_id" => $row["employee_id"],
          "fullname" => $row["fullname"],
          "relationship" => $row["relationship"],
          "phone_no" => $row["phone_no"],
          "email" => $row["email"],
          "address" => $row["address"],
          "is_primary" => $row["is_primary"], 
        ]);
      }

      return $emergencyContacts;
    }

    public static function create($db, array $data): EmployeeEmergencyContactModel {
      $sql = "INSERT INTO employee_emergency_contacts (id, employee_id, fullname, relationship, phone_no, email, address, is_primary) VALUES (?,?,?,?,?,?,?,?)";
      $db->query("SET time_zone = '+00:00';");
      $stmt = $db->prepare($sql);
      $stmt->bind_param("sssssssi",
        $data["id"],
        $data["employee_id"],
        $data["fullname"],
        $data["relationship"],
        $data["phone_no"],
        $data["email"],
        $data["address"],
        $data["is_primary"],
      );

      if (!$stmt->execute()) {
        throw new \Exception("Insertion Failed!");
      }  
      
      return new EmployeeEmergencyContactModel([
        "id" => $data["id"],
        "employee_id" => $data["employee_id"],
        "fullname" => $data["fullname"],
        "relationship" => $data["relationship"],
        "phone_no" => $data["phone_no"],
        "email" => $data["email"],
        "address" => $data["address"],
        "is_primary" => $data["is_primary"], 
      ]);
    }

    public static function update($db, array $data): bool {
      $query = QueryBuilder::buildDynamicUpdate("employee_emergency_contacts", $data["id"], $data["emergency_contact"]);
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
      $sql = "DELETE FROM employee_emergency_contacts WHERE id = ?";
      $stmt = $db->prepare($sql);
      $stmt->bind_param("s", $data["id"]);
      return $stmt->execute();
    }
  }
?>
