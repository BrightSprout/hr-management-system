<?php
  namespace App\Model;

  use App\Helper\QueryBuilder;
  
  class EmployeeModel {
    public string $id;
    public string $first_name;
    public ?string $middle_name;
    public string $last_name;
    public ?string $email;
    public string $phone_no;
    public int $dob;
    public string $gender;
    public ?int $biometric_id;
    public string $status;
    public string $created_by;
    public ?int $created_at;
    public array $jobs;
    public array $user;
    
    public function __construct(array $data) {
      $this->id = $data["id"];
      $this->first_name = $data["first_name"];
      $this->middle_name = $data["middle_name"] ?? NULL;
      $this->last_name = $data["last_name"];
      $this->email = $data["email"] ?? NULL;
      $this->phone_no = $data["phone_no"];
      $this->dob = $data["dob"];
      $this->gender = $data["gender"];
      $this->biometric_id = $data["biometric_id"] ?? NULL;
      $this->status = $data["status"] ?? "DRAFT";
      $this->created_by = $data["created_by"];
      $this->created_at = $data["created_at"] ?? NULL;
      $this->jobs = $data["jobs"] ?? [];
      $this->user = $data["user"] ?? [];
    }

    public static function getDraftBy($db, array $data): EmployeeModel {
      $sql = "SELECT * FROM employees WHERE created_by = '" . $data["user_id"] . "' AND status = 'DRAFT' LIMIT 1";
      $result = $db->query($sql);
      $employee = $result->fetch_assoc();
      return new EmployeeModel([
        "id" => $employee["id"],
        "first_name" => $employee["first_name"],
        "middle_name" => $employee["middle_name"],
        "last_name" => $employee["last_name"],
        "email" => $employee["email"],
        "phone_no" => $employee["phone_no"],
        "dob" => strtotime($employee["dob"]),
        "gender" => $employee["gender"],
        "biometric_id" => $employee["biometric_id"],
        "created_by" => $employee["created_by"], 
      ]);
    }

    public static function getById($db, array $data): EmployeeModel {
      $sql = "SELECT e.*, j.position, j.department_id, j.appointment_date, u.username FROM employees e LEFT JOIN users u ON e.user_id = u.id LEFT JOIN employee_jobs j ON j.employee_id = e.id WHERE e.status = 'COMPLETED' AND e.deleted = 0 AND e.id = ?";
      $stmt = $db->prepare($sql);
      $stmt->bind_param("s",$data["employee_id"]);
      $stmt->execute();
      $result = $stmt->get_result();
      $employee = $result->fetch_assoc();
      return new EmployeeModel([
        "id" => $employee["id"],
        "first_name" => $employee["first_name"],
        "middle_name" => $employee["middle_name"],
        "last_name" => $employee["last_name"],
        "email" => $employee["email"],
        "phone_no" => $employee["phone_no"],
        "dob" => strtotime($employee["dob"]),
        "gender" => $employee["gender"],
        "biometric_id" => $employee["biometric_id"],
        "created_by" => $employee["created_by"], 
        "user" => [
          "id" => $employee["user_id"],
          "username" => $employee["username"],
        ],
        "jobs" => [
          "position" => $employee["position"],  
          "department_id" => $employee["department_id"], 
          "appointment_date" => strtotime($employee["appointment_date"]), 
        ], 
      ]);
    }

    public static function listEmployees($db): array {
      $sql = "SELECT e.*, j.position, j.department_id, j.appointment_date FROM employees e LEFT JOIN employee_jobs j ON e.id = j.employee_id WHERE status = 'COMPLETED' AND deleted = 0";
      $result = $db->query($sql); 
      $employees = [];
      while ($row = $result->fetch_assoc()) {
        $employees[$row["id"]] = new EmployeeModel([
          "id" => $row["id"],
          "first_name" => $row["first_name"],
          "middle_name" => $row["middle_name"],
          "last_name" => $row["last_name"],
          "email" => $row["email"],
          "phone_no" => $row["phone_no"],
          "dob" => strtotime($row["dob"]),
          "gender" => $row["gender"],
          "jobs" => [
            "position" => $row["position"],  
            "department_id" => $row["department_id"], 
            "appointment_date" => strtotime($row["appointment_date"]), 
          ], 
          "biometric_id" => $row["biometric_id"],
          "created_by" => $row["created_by"], 
        ]);
      }
      return $employees;
    }

    public static function listPendingEmployees($db): array {
      $sql = "SELECT e.*,j.position FROM employees e LEFT JOIN employee_jobs j ON j.employee_id = e.id WHERE e.status = 'PENDING'";
      $result = $db->query($sql); 
      $employees = [];
      while ($row = $result->fetch_assoc()) {
        $employees[$row["id"]] = new EmployeeModel([
          "id" => $row["id"],
          "first_name" => $row["first_name"],
          "middle_name" => $row["middle_name"],
          "last_name" => $row["last_name"],
          "email" => $row["email"],
          "phone_no" => $row["phone_no"],
          "dob" => strtotime($row["dob"]),
          "gender" => $row["gender"],
          "jobs" => [
            "position" => $row["position"],  
          ], 
          "biometric_id" => $row["biometric_id"],
          "created_by" => $row["created_by"], 
          "created_at" => strtotime($row["created_at"]),
        ]);
      }
      return $employees;
    }

    public static function countAllPendingEmployees($db): int {
      $sql = "SELECT COUNT(*) FROM employees WHERE status = 'PENDING'";
      $result = $db->query($sql);  

      return $result->fetch_row()[0];
    }

    public static function create($db, array $data): EmployeeModel {
      $sql = "INSERT INTO employees (id, first_name, middle_name, last_name, email, phone_no, dob, gender, status, created_by) VALUES (?,?,?,?,?,?,DATE_ADD('1970-01-01 00:00:00', INTERVAL ? SECOND),?,'DRAFT',?)";
      $db->query("SET time_zone = '+00:00';");
      $stmt = $db->prepare($sql);
      $stmt->bind_param("ssssssiss",
        $data["id"],
        $data["first_name"],
        $data["middle_name"],
        $data["last_name"],
        $data["email"],
        $data["phone_no"],
        $data["dob"],
        $data["gender"],
        $data["created_by"],
      );

      if (!$stmt->execute()) {
        throw new \Exception("Insertion Failed!");
      }  
      
      return new EmployeeModel([
        "id" => $data["id"],
        "first_name" => $data["first_name"],
        "middle_name" => $data["middle_name"],
        "last_name" => $data["last_name"],
        "email" => $data["email"],
        "phone_no" => $data["phone_no"],
        "dob" => $data["dob"],
        "gender" => $data["gender"],
        "created_by" => $data["created_by"], 
      ]);
    }

    public static function update($db, array $data): bool {
      $query = QueryBuilder::buildDynamicUpdate("employees", $data["id"], $data["employee"]);
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
