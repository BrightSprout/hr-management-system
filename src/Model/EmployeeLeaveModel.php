<?php
  namespace App\Model;

  class EmployeeLeaveModel {
    public string $id;
    public string $employee_id;
    public string $type;
    public string $reason;
    public int $start_date;
    public int $end_date;
    public string $status;
    public ?int $created_at;
    public ?array $employee;
    
    public function __construct(array $data) {
      $this->id = $data["id"];
      $this->employee_id = $data["employee_id"];
      $this->type = $data["type"];
      $this->reason = $data["reason"];
      $this->start_date = $data["start_date"];
      $this->end_date = $data["end_date"];
      $this->status = $data["status"] ?? "PENDING";
      $this->created_at = $data["created_at"] ?? NULL;
      $this->employee = $data["employee"] ?? NULL;
    }

    public static function listLeaves($db): array {
      $sql = "SELECT l.*, e.deleted FROM employee_leaves l LEFT JOIN employees e ON l.employee_id = e.id WHERE deleted = '0'"; 
      $result = $db->query($sql);
      $leaves = [];
      while ($row = $result->fetch_assoc()) {
        $leaves[$row["id"]] = new EmployeeLeaveModel([
          "id" => $row["id"],
          "employee_id" => $row["employee_id"],
          "type" => $row["type"],
          "reason" => $row["reason"],
          "start_date" => strtotime($row["start_date"]),
          "end_date" => strtotime($row["end_date"]),
          "status" => $row["status"],
          "created_at" => strtotime($row["created_at"]),
        ]);
      }

      return $leaves;
    }
    public static function listByEmployeeId($db, array $data): array {
      $sql = "SELECT * FROM employee_leaves WHERE employee_id = ?"; 
      $stmt = $db->prepare($sql);
      $stmt->bind_param("s", $data["employee_id"]);
      $stmt->execute();
      $result = $stmt->get_result();
      $leaves = [];
      while ($row = $result->fetch_assoc()) {
        $leaves[$row["id"]] = new EmployeeLeaveModel([
          "id" => $row["id"],
          "employee_id" => $row["employee_id"],
          "type" => $row["type"],
          "reason" => $row["reason"],
          "start_date" => strtotime($row["start_date"]),
          "end_date" => strtotime($row["end_date"]),
          "status" => $row["status"],
          "created_at" => strtotime($row["created_at"]),
        ]);
      }

      return $leaves;
    }

    public static function listByUserId($db, array $data): array {
      $sql = "SELECT * FROM employee_leaves WHERE employee_id = (SELECT e.id FROM employees e WHERE e.user_id = ?)"; 
      $stmt = $db->prepare($sql);
      $stmt->bind_param("s", $data["user_id"]);
      $stmt->execute();
      $result = $stmt->get_result();
      $leaves = [];
      while ($row = $result->fetch_assoc()) {
        $leaves[$row["id"]] = new EmployeeLeaveModel([
          "id" => $row["id"],
          "employee_id" => $row["employee_id"],
          "type" => $row["type"],
          "reason" => $row["reason"],
          "start_date" => strtotime($row["start_date"]),
          "end_date" => strtotime($row["end_date"]),
          "status" => $row["status"],
          "created_at" => strtotime($row["created_at"]),
        ]);
      }

      return $leaves;
    }

    public static function listPendingLeaves($db): array {
      $sql = "SELECT e.first_name, e.middle_name, e.last_name, e.deleted, j.position, l.* FROM employee_leaves l JOIN employees e ON l.employee_id = e.id JOIN employee_jobs j ON j.id = (SELECT jsub.id FROM employee_jobs jsub WHERE jsub.employee_id = e.id ORDER BY jsub.appointment_date DESC LIMIT 1) WHERE l.status = 'PENDING' && e.deleted = '0'";
      $result = $db->query($sql); 
      $leaves = [];
      while ($row = $result->fetch_assoc()) {
        $leaves[$row["id"]] = new EmployeeLeaveModel([
          "id" => $row["id"],
          "employee_id" => $row["employee_id"],
          "type" => $row["type"],
          "reason" => $row["reason"],
          "start_date" => strtotime($row["start_date"]),
          "end_date" => strtotime($row["end_date"]),
          "status" => $row["status"],
          "created_at" => strtotime($row["created_at"]),
          "employee" => [
             "first_name" => $row["first_name"],
             "middle_name" => $row["middle_name"],
             "last_name" => $row["last_name"],
             "current_job" => [
               "position" => $row["position"],
             ]
           ]
        ]);
      }
      return $leaves;
    }

    public static function create($db, array $data): EmployeeLeaveModel {
      $sql = "INSERT INTO employee_leaves (id, employee_id, type, reason, start_date, end_date) VALUES (?,?,?,?,CONVERT_TZ(TIMESTAMPADD(SECOND, ?, '1970-01-01 00:00:00'), '+00:00', @@session.time_zone),CONVERT_TZ(TIMESTAMPADD(SECOND, ?, '1970-01-01 00:00:00'), '+00:00', @@session.time_zone))";
      $db->query("SET time_zone = '+00:00';");
      $stmt = $db->prepare($sql);
      $stmt->bind_param("ssssii",
        $data["id"],
        $data["employee_id"],
        $data["type"],
        $data["reason"],
        $data["start_date"],
        $data["end_date"],
      );

      if (!$stmt->execute()) {
        throw new \Exception("Insertion Failed!");
      }  
      
      return new EmployeeLeaveModel([
        "id" => $data["id"],
        "employee_id" => $data["employee_id"],
        "type" =>  $data["type"],
        "reason" => $data["reason"],
        "start_date" => $data["start_date"],
        "end_date" => $data["end_date"], 
      ]);
    }

    public static function updateStatus($db, array $data): bool {
      $sql = "UPDATE employee_leaves SET status = ? WHERE id = ?"; 
      $db->query("SET time_zone = '+00:00';");
      $stmt = $db->prepare($sql);
      $stmt->bind_param("ss",
        $data["status"],
        $data["id"],
      );
      return $stmt->execute();
    }
  }
?>
