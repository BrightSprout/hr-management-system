<?php
  namespace App\Controller;

  use App\Database\DB;
  use App\Helper\UUIDGenerator;
  use App\Model\DepartmentModel;

  class DepartmentController {
    public function listDepartments(): array {
      $db = DB::connect();

      return DepartmentModel::listDepartments($db);
    }

    public function createDepartment(): DepartmentModel {
      $db = DB::connect();
      $json = json_decode(file_get_contents('php://input'),true);
      
      return DepartmentModel::create($db, [
        "id" => UUIDGenerator::v4(),
        "name" => $json["name"],
        "dayoffs" => $json["dayoffs"],
        "clock_in" => $json["clock_in"],
        "clock_out" => $json["clock_out"],
      ]);
    }

    public function updateDepartment(): array {
      $db = DB::connect();
      $json = json_decode(file_get_contents('php://input'),true);
      
      return [
        "success" => DepartmentModel::update($db, $json),
      ];
    }
  }
?>
