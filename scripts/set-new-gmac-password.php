<?php
  require_once __DIR__ . "/../vendor/autoload.php";

  use App\Database\DB;

  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
  $dotenv->load();

  $db = DB::connect();
  
  echo "Enter new GMAC password: ";
  $newPassword = trim(fgets(STDIN));
  if (!$newPassword) {
    echo "Please provide a new password!\n";
    throw new \Exception("Please provide a new password!");
  }
  $hashedPassword = hash_hmac("sha256", $newPassword, $_ENV["PEPPER_KEY"]);

  $data = [
    "password" => password_hash($hashedPassword, PASSWORD_DEFAULT),
    "username" => "wowadmin",
  ];
  $sql = "UPDATE users SET password = ? WHERE username = ? AND role = 'GMAC'";
  $stmt = $db->prepare($sql);
  $stmt->bind_param("ss",
    $data["password"],
    $data["username"],
  );
  
  if (!$stmt->execute()) {
    echo "Update fails please try again!\n";  
  } else {
    echo "Successfully update GMAC password!\n";
  }
?>
