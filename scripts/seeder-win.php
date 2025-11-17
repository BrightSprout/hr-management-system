<?php
  require_once __DIR__ . "/../vendor/autoload.php";

  use App\Helper\UUIDGenerator;
  use App\Model\UserModel;
  use App\Model\DepartmentModel;

  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
  $dotenv->load();

  date_default_timezone_set('UTC');
  
  $schemaFile = ".\\db_schema.sql";

  if (!file_exists($schemaFile)) {
    exit("No database schema file found!");
  }

  echo "Found a database schema file!\n";

  $mysql = "C:\\xampp\\mysql\\bin\\mysql";
  $server = $_ENV["DB_HOST"];
  $username = $_ENV["DB_USER"];
  $password = $_ENV["DB_PASS"];
  $database = $_ENV["DB_NAME"];

  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

  try {
    $db = new mysqli($server, $username, $password);
    $result = $db->query("SHOW DATABASES LIKE '$database'");

    if ($result->num_rows == 0) {
        // Create database if not exists
        $db->query("CREATE DATABASE `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
        echo "✅ Database '$database' created successfully.\n";
    } else {
        echo "ℹ️ Database '$database' already exists.\n";
    }

    $db->select_db($database); 

    echo "Importing database schema to database...\n";
    $command = "$mysql -u $username -p$password $database < $schemaFile";
    shell_exec($command);
    echo "Importing Finished!\n";

    $db->begin_transaction();

    $users = [
      [
        "id" => UUIDGenerator::v4(),
        "username" => $_ENV["ADMIN_USERNAME"],
        "password" => $_ENV["ADMIN_PASSWORD"],
        "role" => "GMAC",
      ],
      [
        "id" => UUIDGenerator::v4(),
        "username" => $_ENV["HR_USERNAME"],
        "password" => $_ENV["HR_PASSWORD"],
        "role" => "HR",
      ],
    ];
    $departments = [
      [
        "id" => UUIDGenerator::v4(),
        "name" => "Admin Office",
        "dayoffs" =>  [0],
        "clock_in" => 8, // 8am
        "clock_out" => 17, // 5pm        
      ],
      [
        "id" => UUIDGenerator::v4(),
        "name" => "Solo Parent Office",
        "dayoffs" =>  [0],
        "clock_in" => 8, // 8am
        "clock_out" => 17, // 5pm        
      ],
      [
        "id" => UUIDGenerator::v4(),
        "name" => "GMAC",
        "dayoffs" =>  [0],
        "clock_in" => 8, // 8am
        "clock_out" => 17, // 5pm        
      ],
      [
        "id" => UUIDGenerator::v4(),
        "name" => "BOTIKA",
        "dayoffs" =>  [0],
        "clock_in" => 8, // 8am
        "clock_out" => 17, // 5pm        
      ],
      [
        "id" => UUIDGenerator::v4(),
        "name" => "BBU",
        "dayoffs" =>  [0],
        "clock_in" => 8, // 8am
        "clock_out" => 17, // 5pm        
      ],
      [
        "id" => UUIDGenerator::v4(),
        "name" => "Lupon Office",
        "dayoffs" =>  [0],
        "clock_in" => 8, // 8am
        "clock_out" => 17, // 5pm        
      ],
      [
        "id" => UUIDGenerator::v4(),
        "name" => "Osca Office",
        "dayoffs" =>  [0],
        "clock_in" => 8, // 8am
        "clock_out" => 17, // 5pm        
      ],
      [
        "id" => UUIDGenerator::v4(),
        "name" => "PWD Office",
        "dayoffs" =>  [0],
        "clock_in" => 8, // 8am
        "clock_out" => 17, // 5pm        
      ],
      [
        "id" => UUIDGenerator::v4(),
        "name" => "VAWC",
        "dayoffs" =>  [0],
        "clock_in" => 6, // 6am
        "clock_out" => 14, // 2pm        
      ],
      [
        "id" => UUIDGenerator::v4(),
        "name" => "Tanod",
        "dayoffs" =>  [0],
        "clock_in" => 2, // 2am
        "clock_out" => 10, // 10am        
      ],
      [
        "id" => UUIDGenerator::v4(),
        "name" => "Command Center",
        "dayoffs" =>  [0],
        "clock_in" => 10, // 10am
        "clock_out" => 18, // 6pm        
      ],
    ];
    echo "Adding initial data to database...\n";
    try {
      for ($i = 0; $i < count($users); $i++) {
        $user = $users[$i];
        $pwdPeppered = hash_hmac("sha256", $user["password"], $_ENV["PEPPER_KEY"]);
        UserModel::create($db,
          [
            "id" => UUIDGenerator::v4(),
            "username" => $user["username"],
            "password" => password_hash($pwdPeppered, PASSWORD_DEFAULT),
            "role" => $user["role"],
          ]
        );
      }
      for ($i = 0; $i < count($departments); $i++) {
        $department = $departments[$i];
        DepartmentModel::create($db,
          [
            "id" => UUIDGenerator::v4(),
            "name" => $department["name"],
            "dayoffs" => $department["dayoffs"],
            "clock_in" => $department["clock_in"] * 3600,
            "clock_out" => $department["clock_out"] * 3600,
          ]
        );
      }
      $db->commit();
    } catch (Throwable $error) {
      echo "Error Adding Initial Data's. Code:" . $error->getCode();
      $db->rollback();
      throw $error;
    }

    echo "Initial data's added!\n";
  } catch (mysqli_sql_exception $e) {
    echo "❌ MySQL Error (" . $e->getCode() . "): " . $e->getMessage() . "\n";
  } finally {
    if (isset($db) && $db->ping()) {
        $db->close();
    }
  }
?>
