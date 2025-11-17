<?php
  namespace App\Model;

  use App\Helper\QueryBuilder;
  
  class EmployeeJobModel {
    public string $id;
    public string $employee_id;
    public string $position;
    public string $department;
    public ?string $department_id;
    public string $appointment_type;
    public string $civil_service_eligibility;
    public int $appointment_date;
    public string $immediate_supervisor;
    public int $monthly_salary;
    
    public function __construct(array $data) {
      $this->id = $data["id"];
      $this->employee_id = $data["employee_id"];
      $this->position = $data["position"];
      $this->department = $data["department"];
      $this->department_id = $data["department_id"];
      $this->appointment_type = $data["appointment_type"];
      $this->civil_service_eligibility = $data["civil_service_eligibility"];
      $this->appointment_date = $data["appointment_date"];
      $this->immediate_supervisor = $data["immediate_supervisor"];
      $this->monthly_salary = $data["monthly_salary"];
    }

    public static function listByEmployeeId($db, array $data): array {
      $sql = "SELECT * FROM employee_jobs WHERE employee_id = ?"; 
      $stmt = $db->prepare($sql);
      $stmt->bind_param("s", $data["employee_id"]);
      $stmt->execute();
      $result = $stmt->get_result();
      $jobs = [];

      while ($row = $result->fetch_assoc()) {
        $jobs[$row["id"]] = new EmployeeJobModel([
          "id" => $row["id"],
          "employee_id" => $row["employee_id"],
          "position" => $row["position"],
          "department" => $row["department"],
          "department_id" => $row["department_id"],
          "appointment_type" => $row["appointment_type"],
          "civil_service_eligibility" => $row["civil_service_eligibility"],
          "appointment_date" => strtotime($row["appointment_date"]),
          "immediate_supervisor" => $row["immediate_supervisor"],
          "monthly_salary" => $row["monthly_salary"], 
        ]);
      }

      return $jobs;
    }

    public static function listByUserId($db, array $data): array {
      $sql = "SELECT * FROM employee_jobs WHERE employee_id = (SELECT e.id FROM employees e WHERE e.user_id = ?)"; 
      $stmt = $db->prepare($sql);
      $stmt->bind_param("s", $data["user_id"]);
      $stmt->execute();
      $result = $stmt->get_result();
      $jobs = [];

      while ($row = $result->fetch_assoc()) {
        $jobs[$row["id"]] = new EmployeeJobModel([
          "id" => $row["id"],
          "employee_id" => $row["employee_id"],
          "position" => $row["position"],
          "department" => $row["department"],
          "department_id" => $row["department_id"],
          "appointment_type" => $row["appointment_type"],
          "civil_service_eligibility" => $row["civil_service_eligibility"],
          "appointment_date" => strtotime($row["appointment_date"]),
          "immediate_supervisor" => $row["immediate_supervisor"],
          "monthly_salary" => $row["monthly_salary"], 
        ]);
      }

      return $jobs;
    }

    public static function create($db, array $data): EmployeeJobModel {
      $sql = "INSERT INTO employee_jobs (id, employee_id, position, department, department_id, appointment_type, civil_service_eligibility, appointment_date, immediate_supervisor, monthly_salary) VALUES (?,?,?,?,?,?,?,DATE_ADD('1970-01-01 00:00:00', INTERVAL ? SECOND),?,?)";
      $db->query("SET time_zone = '+00:00';");
      $stmt = $db->prepare($sql);
      $stmt->bind_param("sssssssisi",
        $data["id"],
        $data["employee_id"],
        $data["position"],
        $data["department"],
        $data["department_id"],
        $data["appointment_type"],
        $data["civil_service_eligibility"],
        $data["appointment_date"],
        $data["immediate_supervisor"],
        $data["monthly_salary"],
      );

      if (!$stmt->execute()) {
        throw new \Exception("Insertion Failed!");
      }  
      
      return new EmployeeJobModel([
        "id" => $data["id"],
        "employee_id" => $data["employee_id"],
        "position" => $data["position"],
        "department" => $data["department"],
        "department_id" => $data["department_id"],
        "appointment_type" => $data["appointment_type"],
        "civil_service_eligibility" => $data["civil_service_eligibility"],
        "appointment_date" => $data["appointment_date"],
        "immediate_supervisor" => $data["immediate_supervisor"],
        "monthly_salary" => $data["monthly_salary"], 
      ]);
    }

    public static function update($db, array $data): bool {
      $query = QueryBuilder::buildDynamicUpdate("employee_jobs", $data["id"], $data["job"]);
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
      $sql = "DELETE FROM employee_jobs WHERE id = ?";
      $stmt = $db->prepare($sql);
      $stmt->bind_param("s", $data["id"]);
      return $stmt->execute();
    }
  }
?>
