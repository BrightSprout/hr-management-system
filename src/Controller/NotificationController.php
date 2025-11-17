<?php
  namespace App\Controller;
  
  use App\Helper\UUIDGenerator;
  use App\Database\DB;
  use App\Model\NotificationModel;
  use App\Model\UserModel;
  
  class NotificationController {
    public function listNotifications(): array {
      $db = DB::connect();

      return NotificationModel::listNotifications($db);
    }

    public function createNotification(UserModel $user): NotificationModel {
      $db = DB::connect();
      $json = json_decode(file_get_contents('php://input'),true);
      
      return NotificationModel::create($db, [
        "id" => UUIDGenerator::v4(),
        "type" => $json["type"],
        "message" => $json["message"],
        "data" => $json["data"],
        "user_id" => $user->id,
      ]);
    }
  }
?>
