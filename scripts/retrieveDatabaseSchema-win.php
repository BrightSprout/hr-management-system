<?php
require_once __DIR__ . "/../vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->load();

$mysqldump = 'C:\\xampp\\mysql\\bin\\mysqldump.exe'; // xampp mysqldump location in windows
$server = $_ENV["DB_HOST"];
$username = $_ENV["DB_USER"];
$password = $_ENV["DB_PASS"];
$database = $_ENV["DB_NAME"];

$filename = "./db_schema.sql";

$command = "$mysqldump -u $username -p$password --no-data $database > $filename";
echo shell_exec($command);
?>