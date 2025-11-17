<?php
  namespace App\Controller;

  use App\Database\DB;
  use App\Helper\UUIDGenerator;
  use App\Model\UserModel;
  use App\Model\EmployeeModel;
  use App\Model\EmployeeAddressModel;
  use App\Model\EmployeeEmergencyContactModel;
  use App\Model\EmployeeDocumentModel;
  use App\Model\EmployeeJobModel;
  use App\Model\EmployeeAttendanceModel;
  use App\Model\EmployeeLeaveModel;
  use App\Helper\PasswordGenerator;

  class EmployeeController {
    public function getMyDraft(UserModel $user): EmployeeModel {
      $db = DB::connect();
      return EmployeeModel::getDraftBy($db, [
        "user_id" => $user->id
      ]);  
    }

    public function getEmployeeAddresses(): array {
      $db = DB::connect();

      return EmployeeAddressModel::listByEmployeeId($db, [
        "employee_id" => $_GET["employee_id"]
      ]);
    }

    public function getEmployeeEmergencyContacts(): array {
      $db = DB::connect();

      return EmployeeEmergencyContactModel::listByEmployeeId($db, [
        "employee_id" => $_GET["employee_id"]
      ]);
    }

    public function getEmployeeDocuments(): array {
      $db = DB::connect();

      return EmployeeDocumentModel::listByEmployeeId($db, [
        "employee_id" => $_GET["employee_id"]
      ]);
    }

    public function getEmployeeJobs(): array {
      $db = DB::connect();

      return EmployeeJobModel::listByEmployeeId($db, [
        "employee_id" => $_GET["employee_id"]
      ]);
    }

    public function listEmployees(): array {
      $db = DB::connect();

      return EmployeeModel::listEmployees($db);
    }

    public function getEmployeeById(): EmployeeModel {
      $db = DB::connect();

      return EmployeeModel::getById($db, [
        "employee_id" => $_GET["employee_id"]
      ]);
    }

    public function listEmployeeAttendance(): array {
      $db = DB::connect();

      return EmployeeAttendanceModel::listByBiometricId($db, [
        "biometric_id" => $_GET["biometric_id"] 
      ]);
    }

    public function listEmployeeLeaves(): array {
      $db = DB::connect();

      return EmployeeLeaveModel::listByEmployeeId($db, [
        "employee_id" => $_GET["employee_id"]
      ]);
    }

    public function listPendingLeaves(): array {
      $db = DB::connect();

      return EmployeeLeaveModel::listPendingLeaves($db);
    }

    public function listAttendances(): array {
      $db = DB::connect();

      return EmployeeAttendanceModel::listAttendances($db);
    }

    public function listLeaves(): array {
      $db = DB::connect();

      return EmployeeLeaveModel::listLeaves($db);
    }

    public function listTodayAttendances(): array {
      $db = DB::connect();

      return EmployeeAttendanceModel::listTodayAttendances($db);
    }

    public function listDocuments(): array {
      $db = DB::connect();

      return EmployeeDocumentModel::listDocuments($db);
    }

    public function listPendingEmployees(): array {
      $db = DB::connect();

      return EmployeeModel::listPendingEmployees($db);
    }
    
    public function countPendingEmployees(): array {
      $db = DB::connect();

      return [
        "total" => EmployeeModel::countAllPendingEmployees($db)
      ];
    }

    public function createEmployee(UserModel $user): EmployeeModel {
      $db = DB::connect(); 
      $json = json_decode(file_get_contents('php://input'),true);
      
      return EmployeeModel::create($db, [
        "id" => UUIDGenerator::v4(),
        "first_name" => $json["first_name"],
        "middle_name" => $json["middle_name"],
        "last_name" => $json["last_name"],
        "email" => $json["email"],
        "phone_no" => $json["phone_no"],
        "dob" => $json["dob"],
        "gender" => $json["gender"],
        "created_by" => $user->id,
      ]);
    }

    public function createEmployeeAddresses() {
      $db = DB::connect(); 
      $json = json_decode(file_get_contents('php://input'),true);
      $addresses = [];  
      
      foreach ($json as $address) {
        $addresses[] = EmployeeAddressModel::create($db, [
          "id" => UUIDGenerator::v4(),
          "employee_id" => $address["employee_id"],
          "type" => $address["type"] ?? "CURRENT",
          "street_name" => $address["street_name"],
          "barangay" => $address["barangay"],
          "city" => $address["city"],
          "province" => $address["province"],
          "zipcode" => $address["zipcode"],
        ]);
      }

      return $addresses;
    }

    public function createEmployeeEmergencyContacts() {
      $db = DB::connect(); 
      $json = json_decode(file_get_contents('php://input'),true);
      $emergencyContacts = [];
      
      foreach ($json as $employeeContact) {
        $emergencyContacts[] =  EmployeeEmergencyContactModel::create($db, [
          "id" => UUIDGenerator::v4(),
          "employee_id" => $employeeContact["employee_id"],
          "fullname" => $employeeContact["fullname"],
          "relationship" => $employeeContact["relationship"],
          "phone_no" => $employeeContact["phone_no"],
          "email" => $employeeContact["email"],
          "address" => $employeeContact["address"],
          "is_primary" => $employeeContact["is_primary"] ?? 0,
        ]);
      }

      return $emergencyContacts;
    }

    public function createEmployeeDocuments() {
      $db = DB::connect(); 
      $json = json_decode(file_get_contents('php://input'),true);
      $documents = [];
      
      foreach ($json as $document) {
        $documents[] =  EmployeeDocumentModel::create($db, [
          "id" => UUIDGenerator::v4(),
          "employee_id" => $document["employee_id"],
          "type" => $document["type"] ?? NULL,
          "url" => $document["url"],
        ]);
      }

      return $documents;
    }

    public function createEmployeeJobs() {
      $db = DB::connect(); 
      $json = json_decode(file_get_contents('php://input'),true);
      $jobs = [];
      
      foreach ($json as $job) {
        $jobs[] =  EmployeeJobModel::create($db, [
          "id" => UUIDGenerator::v4(),
          "employee_id" => $job["employee_id"],
          "position" => $job["position"],
          "department" => $job["department"],
          "department_id" => $job["department_id"],
          "appointment_type" => $job["appointment_type"],
          "civil_service_eligibility" => $job["civil_service_eligibility"],
          "appointment_date" => $job["appointment_date"],
          "immediate_supervisor" => $job["immediate_supervisor"],
          "monthly_salary" => $job["monthly_salary"],
        ]);
      }

      return $jobs;
    }

    public function createEmployeeLeave() {
      $db = DB::connect();
      $json = json_decode(file_get_contents('php://input'),true);

      return EmployeeLeaveModel::create($db,[
        "id" => UUIDGenerator::v4(),
        "employee_id" => $json["employee_id"],
        "type" =>  $json["type"],
        "reason" => $json["reason"],
        "start_date" => $json["start_date"],
        "end_date" => $json["end_date"], 
      ]);
    }

    public function insertEmployeeAttendancesCSV() {
      $db = DB::connect();
      $result = ["success" => [], "duplicates" => [], "fails" => []];

      mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

      try {
        if ($handle = fopen($_FILES['file']['tmp_name'],"r")) {
          
          while ($row = fgetcsv($handle,0,",")) {
            $rows = [];
            $params = [];
            $params[] = (int) $row[0];
            $params[] = date("Y-m-d", strtotime($row[1]));
            $params[] = $row[2];
            $params[] = $row[3] ?? NULL;


            $rows[] = "(?,?,?,?)";

            try {
              EmployeeAttendanceModel::batchCreate($db,["rows"=>$rows,"params"=>$params]);
              $result["success"][] = $params;
            } catch (\Exception $e) {
              if ($e->getCode() == 1062) {
                $result["duplicates"][] = $params; 
              } else {
                $result["fails"][] = $params;
              }
            }
          }
        }
      } catch (\Exception $e) {
        throw $e;
      }

      return $result;
    }

    public function updateEmployee() {
      $db = DB::connect();
      $json = json_decode(file_get_contents('php://input'),true);
      return [
        "success" => EmployeeModel::update($db, $json),
      ];
    }

    public function updateEmployeeAddresses() {
      $db = DB::connect();
      $json = json_decode(file_get_contents('php://input'),true);

      $db->begin_transaction();
      try {
        foreach ($json["addresses"] as $id => $address) {
          $isSuccess = EmployeeAddressModel::update($db, [
            "id" => $id,
            "address" => $address
          ]);
          if (!$isSuccess) {throw new Exception("Update Failed!");}
        } 
        foreach (($json["delete"] ?? []) as $id) {
          $isSuccess = EmployeeAddressModel::delete($db, ["id" => $id]); 
          if (!$isSuccess) {throw new Exception("Delete Failed!");}
        }
        $db->commit();
      } catch (\Exception $e) {
        $db->rollback();
        throw $e;
      }
      
      return [
        "success" => true
      ];
    }

    public function updateEmployeeEmergenctContacts() {
      $db = DB::connect();
      $json = json_decode(file_get_contents('php://input'),true);

      $db->begin_transaction();
      try {
        foreach ($json["emergency_contacts"] as $id => $emergency_contact) {
          $isSuccess = EmployeeEmergencyContactModel::update($db, [
            "id" => $id,
            "emergency_contact" => $emergency_contact
          ]);
          if (!$isSuccess) {throw new Exception("Update Failed!");}
        } 
        foreach (($json["delete"] ?? []) as $id) {
          $isSuccess = EmployeeEmergencyContactModel::delete($db, ["id" => $id]); 
          if (!$isSuccess) {throw new Exception("Delete Failed!");}
        }
        $db->commit();
      } catch (\Exception $e) {
        $db->rollback();
        throw $e;
      }

      return [
        "success" => true
      ];
    }

    public function updateEmployeeDocuments() {
      $db = DB::connect();
      $json = json_decode(file_get_contents('php://input'),true);

      $db->begin_transaction();
      try {
        foreach ($json["documents"] as $id => $document) {
          $isSuccess = EmployeeDocumentModel::update($db, [
            "id" => $id,
            "document" => $document
          ]);
          if (!$isSuccess) {throw new Exception("Update Failed!");}
        } 
        foreach (($json["delete"] ?? []) as $id) {
          $isSuccess = EmployeeDocumentModel::delete($db, ["id" => $id]); 
          if (!$isSuccess) {throw new Exception("Delete Failed!");}
        }
        $db->commit();
      } catch (\Exception $e) {
        $db->rollback();
        throw $e;
      }

      return [
        "success" => true
      ];
    }

    public function updateEmployeeJobs() {
      $db = DB::connect();
      $json = json_decode(file_get_contents('php://input'),true);

      $db->begin_transaction();
      try {
        foreach ($json["jobs"] as $id => $job) {
          $isSuccess = EmployeeJobModel::update($db, [
            "id" => $id,
            "job" => $job
          ]);
          if (!$isSuccess) {throw new Exception("Update Failed!");}
        } 
        foreach (($json["delete"] ?? []) as $id) {
          $isSuccess = EmployeeJobModel::delete($db, ["id" => $id]); 
          if (!$isSuccess) {throw new Exception("Delete Failed!");}
        }
        $db->commit();
      } catch (\Exception $e) {
        $db->rollback();
        throw $e;
      }

      return [
        "success" => true
      ];
    }

    public function updateEmployeeLeaveStatus() {
      $db = DB::connect();
      $json = json_decode(file_get_contents('php://input'),true);

      return [
        "success" => EmployeeLeaveModel::updateStatus($db,[
           "status" => $json["status"],
           "id" => $json["id"],  
         ])
      ];
    }

    public function completeEmployeeRegistration() {
      $db = DB::connect();
      $json = json_decode(file_get_contents('php://input'),true);

      $db->begin_transaction();
      try {
        $password = PasswordGenerator::generate(10);
        $pwdPeppered = hash_hmac("sha256", $password, $_ENV["PEPPER_KEY"]);  
        $user = UserModel::create($db,
          [
            "id" => UUIDGenerator::v4(),
            "username" => "wowuser" . UserModel::countAllStaff($db),
            "password" => password_hash($pwdPeppered, PASSWORD_DEFAULT),
            "role" => "STAFF",
          ]
        ); 
        if (!EmployeeModel::update($db, [
          "id" => $json["employee_id"],
          "employee" => [
            "status" => "COMPLETED",
            "user_id" => $user->id 
          ]
        ])) {
          throw new \Exception("Employee Update Failed!");
        }
        $db->commit();
      } catch (\Exception $e) {
        $db->rollback();
        throw $e;
      }
      $user->password = $password;
      return $user;
    }
  }
?>
