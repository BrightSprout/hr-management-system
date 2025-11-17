<?php
  namespace App\Controller;
 
  use App\Database\DB;
  use App\Model\UserModel;
  use App\Model\EmployeeAddressModel;
  use App\Model\EmployeeJobModel;
  use App\Model\EmployeeEmergencyContactModel;
  use App\Model\EmployeeDocumentModel;
  use App\Model\EmployeeAttendanceModel;
  use App\Model\EmployeeLeaveModel;
  use App\Helper\UUIDGenerator;

  class UserController {
    public function getEmployeeInfo(UserModel $user) {
      $db = DB::connect();

      return UserModel::getEmployeeInfo($db, [
        "id" => $user->id,
      ]);
    }

    public function listMyAddresses(UserModel $user): array {
      $db = DB::connect();

      return EmployeeAddressModel::listByUserId($db, [
        "user_id" => $user->id,
      ]); 
    }

    public function listMyJobs(UserModel $user): array {
      $db = DB::connect();

      return EmployeeJobModel::listByUserId($db, [
        "user_id" => $user->id,
      ]); 
    }

    public function listMyEmergencyContacts(UserModel $user): array {
      $db = DB::connect();

      return EmployeeEmergencyContactModel::listByUserId($db, [
        "user_id" => $user->id,
      ]); 
    }

    public function listMyDocuments(UserModel $user): array {
      $db = DB::connect();

      return EmployeeDocumentModel::listByUserId($db, [
        "user_id" => $user->id,
      ]); 
    }

    public function listMyAttendances(UserModel $user): array {
      $db = DB::connect();

      return EmployeeAttendanceModel::listByUserId($db, [
        "user_id" => $user->id,
      ]);
    }

    public function listMyLeaves(UserModel $user): array {
      $db = DB::connect();

      return EmployeeLeaveModel::listByUserId($db, [
        "user_id" => $user->id,
      ]);
    }

    public function createMyLeave(UserModel $user) {
      $db = DB::connect();
      $json = json_decode(file_get_contents('php://input'),true);

      $employee = UserModel::getEmployeeInfo($db, [
        "id" => $user->id,
      ]);
      return EmployeeLeaveModel::create($db,[
        "id" => UUIDGenerator::v4(),
        "employee_id" => $employee->id,
        "type" =>  $json["type"],
        "reason" => $json["reason"],
        "start_date" => $json["start_date"],
        "end_date" => $json["end_date"], 
      ]);
    } 

    public function updatePassword() {
      $db = DB::connect(); 
      $json = json_decode(file_get_contents('php://input'),true);

      $newPassword = hash_hmac("sha256", $json["new_password"], $_ENV["PEPPER_KEY"]);
      
      return [
        "success" => UserModel::updatePassword($db, [
          "password" => password_hash($newPassword, PASSWORD_DEFAULT),
          "id" => $json["user_id"],
        ])
      ];
    }
  }
?>
