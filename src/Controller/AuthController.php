<?php
  namespace App\Controller;

  use App\Helper\UUIDGenerator;
  use App\Model\UserModel;
  use App\Model\ProfileModel;
  use App\Database\DB;
  use Firebase\JWT\JWT;

  class AuthController {
    public function login() {
      $db = DB::connect();
      $json = json_decode(file_get_contents('php://input'),true);
      $username = $json["username"];
      $user = UserModel::findByUsername($db,
        [
          "username" => $username
        ]
      );

      $pwdPeppered = hash_hmac("sha256", $json["password"], $_ENV["PEPPER_KEY"]);
      if (!password_verify($pwdPeppered, $user->password)) {
        throw new \Exception("Password does not match!");
      }

      $payload = [
        "sub" => [
          "id" => $user->id,
          "username" => $username,
          "role" => $user->role,
        ],
        "exp" => time() + 900 // 15 mins
      ];

      $refreshPayload = [
        "sub" => [
          "id" => $user->id,
          "username" => $username,
          "role" => $user->role,
        ],
        "exp" => time() + 604800 // 7 days
      ];
      
      $accessToken = JWT::encode($payload, $_ENV["JWT_ACCESS_KEY"], "HS256");
      $refreshToken = JWT::encode($refreshPayload, $_ENV["JWT_REFRESH_KEY"], "HS256");

      setcookie("access_token", $refreshToken, time() + 900, "/", "", true, true);
      setcookie("refresh_token", $refreshToken, time() + 604800, "/", "", true, true);

      return "Successful Login!";     
    }
  }
?>
