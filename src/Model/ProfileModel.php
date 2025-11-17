<?php
  namespace App\Model;

  use App\Model\UserModel;

  class ProfileModel {
    public string $id;
    public string $first_name;
    public string $middle_name;
    public string $last_name;
    public string $gender;
    public string $civil_status;
    public string $dob;
    public string $job_title;
    public string $phone_no;
    public ?UserModel $user;

    public function __construct(array $data) {
      $this->id = $data["id"];
      $this->first_name = $data["first_name"];
      $this->middle_name = $data["middle_name"];
      $this->last_name = $data["last_name"];
      $this->gender = $data["gender"];
      $this->civil_status = $data["civil_status"];
      $this->dob = $data["dob"];
      $this->job_title = $data["job_title"];
      $this->phone_no = $data["phone_no"];
      $this->user = $data["user"] ?? NULL;
    }

    /**
     * @return ProfileModel[] List of ProfileModel instances
     */
    public static function listByType($db, array $data): array {
      $quotedTypes = array_map(function($t) {
          return "'" . $t . "'";
      }, $data["types"]);
      $types = implode(", ", $quotedTypes);
      $sql = "SELECT 
        p.*, 
        u.id as user_id,
        u.role,
        u.username
        FROM user_profiles p 
        LEFT JOIN users u ON p.id = u.profile_id AND u.role IN ($types)"; 
      $result = $db->query($sql);
      $rows = [];
      while ($row = $result->fetch_assoc()) {
        $user = NULL;
        if ($row["user_id"]) {
          $user = new UserModel([
            "id" => $row["user_id"],
            "username" => $row["username"],
            "role" => $row["role"],
            "profile_id" => $row["id"],
          ]);
        }
        $rows[$row["id"]] = new ProfileModel([
          "id" => $row["id"], 
          "first_name" => $row["first_name"], 
          "middle_name" => $row["middle_name"], 
          "last_name" => $row["last_name"], 
          "gender" => $row["gender"],
          "civil_status" => $row["civil_status"],
          "dob" => $row["dob"],
          "job_title" => $row["job_title"],
          "phone_no" => $row["phone_no"],
          "user" => $user,
        ]);
      }  
      return $rows;
    }

    public static function create($db, array $data): ProfileModel {
      $sql = "INSERT INTO user_profiles (id, first_name, middle_name, last_name, gender, civil_status, dob, job_title, phone_no) VALUES (?,?,?,?,?,?,?,?,?)";
      $stmt = $db->prepare($sql);
      $stmt->bind_param("sssssssss",
        $data["id"], 
        $data["first_name"], 
        $data["middle_name"], 
        $data["last_name"], 
        $data["gender"],
        $data["civil_status"],
        $data["dob"],
        $data["job_title"],
        $data["phone_no"],
      );

      if (!$stmt->execute()) {
        throw new \Exception("Insertion Failed!");
      }

      return new ProfileModel([
        "id" => $data["id"], 
        "first_name" => $data["first_name"], 
        "middle_name" => $data["middle_name"], 
        "last_name" => $data["last_name"], 
        "gender" => $data["gender"],
        "civil_status" => $data["civil_status"],
        "dob" => $data["dob"],
        "job_title" => $data["job_title"],
        "phone_no" => $data["phone_no"],
      ]);
    }
  } 
?>
