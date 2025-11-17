<?php
  namespace App\Model;

  use App\Model\UserModel;

  class NotificationModel {
    public string $id;
    public string $type; 
    public string $message; 
    public array $data; 
    public ?int $created_at; 
    public string $created_by;
    public ?array $user;

    public function __construct(array $data) {
      $this->id = $data["id"];
      $this->type = $data["type"]; 
      $this->message = $data["message"]; 
      $this->data = $data["data"]; 
      $this->created_at = $data["created_at"] ?? NULL; 
      $this->created_by = $data["created_by"];
      $this->user = $data["user"] ?? NULL;
    } 

    public static function listNotifications($db): array {
      $sql = "SELECT n.*,u.username,u.role FROM notifications n JOIN users u ON n.created_by = u.id"; 
      $result = $db->query($sql);
      $notifications = [];
      while ($row = $result->fetch_assoc()) {
        $notifications[$row["id"]] = new NotificationModel([
          "id" => $row["id"],
          "type" => $row["type"],
          "message" => $row["message"],
          "data" => json_decode($row["data"], true),
          "created_by" => $row["created_by"],
          "user" => [
            "username" => $row["username"],
            "role" => $row["role"],
          ],
          "created_at" => strtotime($row["created_at"]),
        ]);
      }
      return $notifications;
    }

    public static function create($db, array $data) {
      $sql = "INSERT INTO notifications (id,type,message,data,created_by) VALUES (?,?,?,?,?)";
      $db->query("SET time_zone = '+00:00';"); //temporary fix (should we apply this to all db query?)
      $stmt = $db->prepare($sql);
      $json = json_encode($data["data"]);
      $stmt->bind_param("sssss",
        $data["id"],
        $data["type"],
        $data["message"],
        $json,
        $data["user_id"],
      ); 

      if (!$stmt->execute()) {
        throw new \Exception("Insertion Failed!");
      }

      return new NotificationModel([
        "id" => $data["id"],
        "type" => $data["type"],
        "message" => $data["message"],
        "data" => $data["data"],
        "created_by" => $data["user_id"],
      ]);
    }
  }
?>
