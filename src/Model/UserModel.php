<?php
  namespace App\Model;

  use App\Model\EmployeeModel;
  
  class UserModel {
    public string $id;
    public string $username;
    public ?string $password;
    public string $role;  
    
    public function __construct(array $data) {
      $this->id = $data["id"];
      $this->username = $data["username"];
      $this->password = $data["password"] ?? NULL;
      $this->role = $data["role"];
    }

    public static function countAllStaff($db): int {
      $sql = "SELECT COUNT(*) FROM users WHERE role='STAFF'";
      $result = $db->query($sql);
      
      return $result->fetch_row()[0];
    }

    public static function getEmployeeInfo($db,array $data): EmployeeModel {
      $sql = "SELECT e.*, j.position, j.department_id, j.appointment_date, u.username FROM employees e LEFT JOIN users u ON e.user_id = u.id LEFT JOIN employee_jobs j ON j.employee_id = e.id WHERE e.status = 'COMPLETED' AND e.deleted = 0 AND e.user_id = ?";
      $stmt = $db->prepare($sql);
      $stmt->bind_param("s",$data["id"]);
      $stmt->execute();
      $result = $stmt->get_result();

      if (!($employee = $result->fetch_assoc())) {
        throw new \Exception("Employee with given user id is not found!");
      } 

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

     public static function create($db, array $data): UserModel {
      $sql = "INSERT INTO users (id, username, password, role) VALUES (?,?,?,?)";
      $db->query("SET time_zone = '+00:00';");
      $stmt = $db->prepare($sql);
      $stmt->bind_param("ssss",
        $data["id"],
        $data["username"],
        $data["password"],
        $data["role"],
      );

      if (!$stmt->execute()) {
        throw new \Exception("Insertion Failed!");
      }

      return new UserModel([
        "id" => $data["id"],
        "username" => $data["username"],
        "role" => $data["role"],
      ]);
    }

    public static function findByUsername($db, array $data): UserModel {
      $sql = "SELECT * FROM users WHERE username=?";
      $stmt = $db->prepare($sql);
      $stmt->bind_param("s", $data["username"]);
      $stmt->execute();
      $result = $stmt->get_result();

      if (!($user = $result->fetch_assoc())) {
        throw new \Exception("No user with given username found!"); 
      }
       
      return new UserModel([
        "id" => $user["id"],
        "username" => $user["username"], 
        "password" => $user["password"],
        "role" => $user["role"],
      ]);
    }

    public static function updatePassword($db, array $data) {
      $sql = "UPDATE users SET password = ? WHERE id = ? AND role = 'STAFF'";
      $stmt = $db->prepare($sql);
      $stmt->bind_param("ss",
        $data["password"],
        $data["id"],
      );
      return $stmt->execute();
    }
  }
?>
