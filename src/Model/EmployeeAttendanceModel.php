<?php
  namespace App\Model;

  use App\Helper\TimeFormatter;

  class EmployeeAttendanceModel {
    public ?int $id;
    public int $biometric_id;
    public string $attended_date;
    public int $clock_in;
    public ?int $clock_out;
    public ?array $employee;
    
    public function __construct(array $data) {
      $this->id = $data["id"] ?? NULL;
      $this->biometric_id = $data["biometric_id"];
      $this->attended_date = $data["attended_date"];
      $this->clock_in = $data["clock_in"];
      $this->clock_out = $data["clock_out"] ?? NULL;
      $this->employee = $data["employee"] ?? NULL;
    }


    public static function listAttendances($db): array {
      $sql = "SELECT a.*, e.deleted FROM employee_attendances a LEFT JOIN employees e ON a.biometric_id = e.biometric_id WHERE e.deleted = '0'";
      $result = $db->query($sql); 
      $attendances = [];
      while ($row = $result->fetch_assoc()) {
        $attendances[$row["id"]] = new EmployeeAttendanceModel([
          "id" => $row["id"],
          "biometric_id" => $row["biometric_id"],
          "attended_date" => $row["attended_date"],
          "clock_in" => TimeFormatter::clockTimeToUnix($row["clock_in"]),
          "clock_out" => $row["clock_out"] ? TimeFormatter::clockTimeToUnix($row["clock_out"]) : NULL,
        ]);
      }
      return $attendances;
    }

    public static function listTodayAttendances($db): array {
      $today = date("Y-m-d");
      $sql = "SELECT a.*, e.first_name, e.middle_name, e.last_name, j.position FROM employee_attendances a LEFT JOIN employees e ON a.biometric_id = e.biometric_id LEFT JOIN employee_jobs j ON j.id = (SELECT jsub.id FROM employee_jobs jsub WHERE jsub.employee_id = e.id ORDER BY jsub.appointment_date DESC LIMIT 1) WHERE a.attended_date = '$today'";
      $result = $db->query($sql);
      $attendances = [];
      while ($row = $result->fetch_assoc()) {
        $attendances[$row["id"]] = new EmployeeAttendanceModel([
          "id" => $row["id"],
          "biometric_id" => $row["biometric_id"],
          "attended_date" => $row["attended_date"],
          "clock_in" => TimeFormatter::clockTimeToUnix($row["clock_in"]),
          "clock_out" => $row["clock_out"] ? TimeFormatter::clockTimeToUnix($row["clock_out"]) : NULL,
          "employee" => [
            "first_name" => $row["first_name"],
            "middle_name" => $row["middle_name"],
            "last_name" => $row["last_name"],
            "position" => $row["position"],
          ]
        ]);
      }
      return $attendances;
    }

    public static function listByBiometricId($db,array $data): array {
      $sql = "SELECT * FROM employee_attendances WHERE biometric_id = ?";
      $stmt = $db->prepare($sql);
      $stmt->bind_param("i",$data["biometric_id"]);
      $stmt->execute();
      $result = $stmt->get_result(); 
      $attendances = [];
      while ($row = $result->fetch_assoc()) {
        $attendances[$row["id"]] = new EmployeeAttendanceModel([
          "id" => $row["id"],
          "biometric_id" => $row["biometric_id"],
          "attended_date" => $row["attended_date"],
          "clock_in" => TimeFormatter::clockTimeToUnix($row["clock_in"]),
          "clock_out" => $row["clock_out"] ? TimeFormatter::clockTimeToUnix($row["clock_out"]) : NULL,
        ]);
      }
      return $attendances;
    }

    public static function listByUserId($db,array $data): array {
      $sql = "SELECT * FROM employee_attendances WHERE biometric_id = (SELECT e.biometric_id FROM employees e WHERE e.user_id = ?)";
      $stmt = $db->prepare($sql);
      $stmt->bind_param("i",$data["user_id"]);
      $stmt->execute();
      $result = $stmt->get_result(); 
      $attendances = [];
      while ($row = $result->fetch_assoc()) {
        $attendances[$row["id"]] = new EmployeeAttendanceModel([
          "id" => $row["id"],
          "biometric_id" => $row["biometric_id"],
          "attended_date" => $row["attended_date"],
          "clock_in" => TimeFormatter::clockTimeToUnix($row["clock_in"]),
          "clock_out" => $row["clock_out"] ? TimeFormatter::clockTimeToUnix($row["clock_out"]) : NULL,
        ]);
      }
      return $attendances;
    }

    public static function batchCreate($db, array $data) {
      $placeholders = implode(",",$data["rows"]);
      $sql = "INSERT INTO employee_attendances (biometric_id,attended_date,clock_in,clock_out) VALUES $placeholders";
      $db->query("SET time_zone = '+00:00';");
      $stmt = $db->prepare($sql); 
      $isSuccess = $stmt->execute($data["params"]);
      if (!$isSuccess) {
        throw new \Exception("Insertion Failed!");
      }
    }
  }
?>
