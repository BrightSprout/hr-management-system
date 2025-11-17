<?php
require_once __DIR__ . "/vendor/autoload.php";

use App\Model\UserModel;
use App\Controller\AuthController;
use App\Controller\EmployeeController;
use App\Controller\DepartmentController;
use App\Controller\NotificationController;
use App\Controller\UserController;
use App\Middleware\AuthMiddleware;
use App\Middleware\ProtectedPageMiddleware;
use App\Helper\RedirectDef;

date_default_timezone_set("UTC");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$basePath = dirname($_SERVER["SCRIPT_NAME"]);

$parsed_url = parse_url($_SERVER["REQUEST_URI"]);
$path = rtrim(str_replace($basePath, "", $parsed_url["path"]), "/");
if ($path === "")
  $path = "/";

$method = $_SERVER["REQUEST_METHOD"];

$VIEW_PATH = [
  "GET /login" => function () {
    try {
      $isStaff = false;
      (new AuthMiddleware())->handle(function(UserModel $user) use (&$isStaff) {
        $isStaff = $user->role == "STAFF"; 
      });
      return new RedirectDef("dashboard" . ($isStaff ? "?page=view-staff/personal" : ""));
    } catch (\Throwable $error) {
      require_once __DIR__ . "/src/view/login.php";
    }
  },
  "GET /register" => function () {
    require_once __DIR__ . "/src/view/register.php";
  },
  "GET /dashboard" => withMiddleware(
    [ProtectedPageMiddleware::class], 
    function (UserModel $userData) {
      require_once __DIR__ . "/src/view/dashboard.php";
  }),
  "GET /dashboard_content" => withMiddleware(
    [AuthMiddleware::class], 
    function (UserModel $userData) {
      $page = $_GET["page"];
      $pageForGMAC = [
        "dashboard" => "contents/main-dash.php",
        "employee-docs" => "contents/staff-documents.php",
        "register-approval" => "contents/approval-staff.php",
        "staff-management" => "contents/staff-management.php",
        "reports" => "contents/report-logs.php",
        "add-staff" => "contents/add-staff.php",
        "upload-data" => "contents/upload-data.php",
        "manage-attendance" => "contents/attendance-management.php",
        "department" => "contents/department-management.php",
        "view-staff" => "contents/view-staff.php",
        "view-staff/personal" => "view-staff_contents/staff-info.php",
        "view-staff/attendance" => "view-staff_contents/staff-attendance-record.php",
        "view-staff/documents" => "view-staff_contents/staff-documents.php",
        "view-staff/reset-password" => "view-staff_contents/staff-set-new-password.php",
        "view-staff/performance" => "view-staff_contents/staff-performance.php",
      ];
      $pageForHR = [
        "dashboard" => "contents/main-dash.php",
        "employee-docs" => "contents/staff-documents.php",
        "staff-management" => "contents/staff-management.php",
        "verification" => "contents/attendance-sheet.php",
        "reports" => "contents/report-logs.php",
        "add-staff" => "contents/add-staff.php",
        "upload-data" => "contents/upload-data.php",
        "manage-attendance" => "contents/attendance-management.php",
        "view-staff" => "contents/view-staff.php",
        "view-staff/personal" => "view-staff_contents/staff-info.php",
        "view-staff/attendance" => "view-staff_contents/staff-attendance-record.php",
        "view-staff/documents" => "view-staff_contents/staff-documents.php",
      ];
      $pageForStaff = [
        "dashboard" => "contents/view-staff.php",
        "view-staff" => "contents/view-staff.php",
        "view-staff/personal" => "view-staff_contents/staff-info.php",
        "view-staff/attendance" => "view-staff_contents/staff-attendance-record.php",
        "view-staff/documents" => "view-staff_contents/staff-documents.php",
        "view-staff/leave" => "view-staff_contents/staff-leave-request.php",
      ];
      $userRole = $userData->role;
      $PAGE_MAP = $userRole == "HR" ? $pageForHR : ($userRole == "GMAC" ? $pageForGMAC : $pageForStaff);
      $mapped = $PAGE_MAP[$page];
      if (!$mapped)
        throw new \Exception("You are not allowed to access this page!");
      require_once __DIR__ . "/src/view/$mapped";
  }),
];

$API_PATH = [
  "GET /api/image_upload_url" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC","HR"])],
    function() {
      $publicId = $_GET["public_id"];
      $secret = $_ENV["CLOUDINARY_API_SECRET"];
      $timestamp = time();
      $folder = "documents";
      $signature = sha1("folder=$folder&overwrite=false&public_id=$publicId&timestamp=$timestamp&unique_filename=false" . $secret);
      return [
        "timestamp" => $timestamp,
        "signature" => $signature,
        "api_key" => $_ENV["CLOUDINARY_API_KEY"], 
        "cloud_name" => $_ENV["CLOUDINARY_CLOUDNAME"],
        "folder" => $folder
      ]; 
  }),
  "GET /api/auth_check" => withMiddleware(
    [AuthMiddleware::class],
    function() {
      return "authed";
  }),
  "GET /api/employee_draft" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC","HR"])],
    function(UserModel $user) {
    return (new EmployeeController())->getMyDraft($user);
  }),
  "GET /api/employee_addresses" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC","HR"])],
    function() {
    return (new EmployeeController())->getEmployeeAddresses();
  }),
  "GET /api/employee_emergency_contacts" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC","HR"])],
    function() {
    return (new EmployeeController())->getEmployeeEmergencyContacts();
  }),
  "GET /api/employee_documents" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC","HR"])],
    function() {
    return (new EmployeeController())->getEmployeeDocuments();
  }),
  "GET /api/employee_jobs" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC","HR"])],
    function() {
    return (new EmployeeController())->getEmployeeJobs();
  }),
  "GET /api/list-employees" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC","HR"])],
    function() {
    return (new EmployeeController())->listEmployees(); 
  }),
  "GET /api/employee" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC","HR"])],
    function() {
    return (new EmployeeController())->getEmployeeById(); 
  }),
  "GET /api/list-departments" => withMiddleware(
    [AuthMiddleware::class],
    function() {
    return (new DepartmentController())->listDepartments(); 
  }),
  "GET /api/list-employee_attendance" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC","HR"])],
    function() {
    return (new EmployeeController())->listEmployeeAttendance(); 
  }),
  "GET /api/list-employee_leaves" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC","HR"])],
    function() {
    return (new EmployeeController())->listEmployeeLeaves(); 
  }),
  "GET /api/list-pending-leaves" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC","HR"])],
    function() {
    return (new EmployeeController())->listPendingLeaves(); 
  }),
  "GET /api/list-attendances" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC","HR"])],
    function() {
    return (new EmployeeController())->listAttendances(); 
  }),
  "GET /api/list-leaves" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC","HR"])],
    function() {
    return (new EmployeeController())->listLeaves(); 
  }),
  "GET /api/list-today-attendances" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC","HR"])],
    function() {
    return (new EmployeeController())->listTodayAttendances(); 
  }),
  "GET /api/list-documents" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC","HR"])],
    function() {
    return (new EmployeeController())->listDocuments(); 
  }),
  "GET /api/list-notifications" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC","HR"])],
    function() {
    return (new NotificationController())->listNotifications(); 
  }),
  "GET /api/list-pending-employees" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC"])],
    function() {
    return (new EmployeeController())->listPendingEmployees();
  }),
  "GET /api/count-pending-employees" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC","HR"])],
    function() {
    return (new EmployeeController())->countPendingEmployees();
  }),
  "GET /api/my-employee-info" => withMiddleware(
    [AuthMiddleware::class],
    function(UserModel $user) {
    return (new UserController())->getEmployeeInfo($user);
  }),
  "GET /api/my-addresses" => withMiddleware(
    [AuthMiddleware::class],
    function(UserModel $user) {
    return (new UserController())->listMyAddresses($user);
  }),
  "GET /api/my-jobs" => withMiddleware(
    [AuthMiddleware::class],
    function(UserModel $user) {
    return (new UserController())->listMyJobs($user);
  }),
  "GET /api/my-emergency-contacts" => withMiddleware(
    [AuthMiddleware::class],
    function(UserModel $user) {
    return (new UserController())->listMyEmergencyContacts($user);
  }),
  "GET /api/my-documents" => withMiddleware(
    [AuthMiddleware::class],
    function(UserModel $user) {
    return (new UserController())->listMyDocuments($user);
  }),
  "GET /api/my-attendances" => withMiddleware(
    [AuthMiddleware::class],
    function(UserModel $user) {
    return (new UserController())->listMyAttendances($user);
  }),
  "GET /api/my-leaves" => withMiddleware(
    [AuthMiddleware::class],
    function(UserModel $user) {
    return (new UserController())->listMyLeaves($user);
  }),
  "POST /api/login" => function () {
    return (new AuthController())->login();
  },
  "POST /api/register" => function () {
    return (new AuthController())->register();
  },
  "GET /api/logout" => function() {
    setcookie("access_token","",time() - 3600,"/");
    setcookie("refresh_token","",time() - 3600,"/");
    return new RedirectDef("../login");
  },
  "POST /api/employee" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC","HR"])],
    function(UserModel $user) {
      return (new EmployeeController())->createEmployee($user);
  }),
  "POST /api/employee_jobs" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC","HR"])],
    function() {
      return (new EmployeeController())->createEmployeeJobs();
  }),
  "POST /api/employee_addresses" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC","HR"])],
    function() {
      return (new EmployeeController())->createEmployeeAddresses();
  }),
  "POST /api/employee_emergency_contacts" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC","HR"])],
    function() {
      return (new EmployeeController())->createEmployeeEmergencyContacts();
  }),
  "POST /api/employee_documents" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC","HR"])],
    function() {
      return (new EmployeeController())->createEmployeeDocuments();
  }),
  "POST /api/department" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC","HR"])],
    function() {
      return (New DepartmentController())->createDepartment();
  }),
  "POST /api/employee_attendances/csv_insert" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["HR"])],
    function() {
      return (New EmployeeController())->insertEmployeeAttendancesCSV();
  }),
  "POST /api/my_leave" => withMiddleware(
    [AuthMiddleware::class],
    function(UserModel $user) {
      return (New UserController())->createMyLeave($user);
  }),
  "POST /api/notification" => withMiddleware(
    [AuthMiddleware::class],
    function(UserModel $user) {
      return (New NotificationController())->createNotification($user);
  }),
  "PATCH /api/employee" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC","HR"])],
    function() {
      return (new EmployeeController())->updateEmployee();
  }),
  "PATCH /api/employee_addresses" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC","HR"])],
    function() {
      return (new EmployeeController())->updateEmployeeAddresses();
  }),
  "PATCH /api/employee_emergency_contacts" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC","HR"])],
    function() {
      return (new EmployeeController())->updateEmployeeEmergenctContacts();
  }),
  "PATCH /api/employee_documents" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC","HR"])],
    function() {
      return (new EmployeeController())->updateEmployeeDocuments();
  }),
  "PATCH /api/employee_jobs" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC","HR"])],
    function() {
      return (new EmployeeController())->updateEmployeeJobs();
  }),
  "PATCH /api/employee_leave_approval" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC"])],
    function() {
      return (new EmployeeController())->updateEmployeeLeaveStatus();
    }),
  "PATCH /api/department" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC"])],
    function() {
      return (new DepartmentController())->updateDepartment();
    }),
  "PATCH /api/employee_registered" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC"])],
    function() {
      return (new EmployeeController())->completeEmployeeRegistration();
    }),
  "PATCH /api/reset-password" => withMiddleware(
    [AuthMiddleware::class, allowedRoles(["GMAC"])],
    function() {
      return (new UserController())->updatePassword();
    }),
];

function allowedRoles(array $roles) {
  return function (callable $handler) use ($roles) {
    return function(UserModel $user) use ($handler,$roles) {
      if (!in_array($user->role,$roles)) {
        throw new \Exception("You do not have permission to access this path");
      }
      return $handler($user);
    };
  };
}

function withMiddleware(array $middlewares, callable $handler): callable
{
  return array_reduce(
    array_reverse($middlewares),
    function ($next, $middleware) {
      if (is_callable($middleware)) {
        return $middleware($next);
      }
      return function () use ($middleware, $next) {
        return (new $middleware())->handle($next);
      };
    },
    $handler
  );
}

function findPath($path)
{
  global $VIEW_PATH, $API_PATH;

  if (substr($path, 0, 5) === "/api/") {
    return $API_PATH;
  }

  return $VIEW_PATH;
}

function runRoute($method, $path)
{
  $routes = findPath($path);
  $requestString = "$method $path";

  if (!isset($routes[$requestString])) {
    echo "Page not found.";
    http_response_code(404);
    return;
  }

  try {
    $output = $routes[$requestString]();
    if ($output instanceof RedirectDef) {
      $output->redirect();
    } else if (is_object($output) or is_array($output)) {
      header("Content-Type: application/json");
      echo json_encode($output);
    } else if (is_string($output)) {
      echo $output;
    } 
  } catch (\Throwable $error) {
    http_response_code(500);
  }
}
runRoute($method, $path);
?>
