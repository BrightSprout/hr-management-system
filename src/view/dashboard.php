<?php
session_start();

$navForGMAC = [
  ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'home', 'color' => 'from-blue-500 to-cyan-500'],
  ['id' => 'employee-docs', 'label' => 'Staffs Documents', 'icon' => 'file-text', 'color' => 'from-purple-500 to-violet-500'],
  ['id' => 'register-approval', 'label' => 'Approval Staffs', 'icon' => 'check-circle', 'color' => 'from-orange-500 to-red-500', 'badge' => '8'],
  ['id' => 'staff-management', 'label' => 'Staffs Management', 'icon' => 'users', 'color' => 'from-pink-500 to-rose-500'],
  ['id' => 'reports', 'label' => 'Reports and Logs', 'icon' => 'file-bar-chart', 'color' => 'from-teal-500 to-cyan-500'],
  ['id' => 'department', 'label' => 'Department', 'icon' => 'badge', 'color' => 'from-sky-500 to-indigo-500'],
];

$navForHR = [
  ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'home', 'color' => 'from-blue-500 to-cyan-500'],
  ['id' => 'employee-docs', 'label' => 'Staffs Documents', 'icon' => 'file-text', 'color' => 'from-purple-500 to-violet-500'],
  ['id' => 'staff-management', 'label' => 'Staffs Management', 'icon' => 'users', 'color' => 'from-pink-500 to-rose-500'],
  ['id' => 'verification', 'label' => 'Attendance Sheet', 'icon' => 'user-check', 'color' => 'from-indigo-500 to-blue-500'],
  ['id' => 'reports', 'label' => 'Reports and Logs', 'icon' => 'file-bar-chart', 'color' => 'from-teal-500 to-cyan-500'],
  ['id' => 'upload-data', 'label' => 'Upload Data', 'icon' => 'upload', 'color' => 'from-sky-500 to-indigo-500']
];

// Example data just for displaying
$navigationItems = $userData->role == "HR" ? $navForHR : ($userData->role == "GMAC" ? $navForGMAC : []);

$activeItem = $_GET['page'] ?? 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GMAC NEXUS - Dashboard</title>

    <!-- Lucide Icons CDN -->
    <script src="public/assets/js/lucide.min.js"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="public/dashboard.css">
    <link href="public/assets/css/sweetalert2.min.css" rel="stylesheet">
    <link href="public/output.css" rel="stylesheet">
</head>

<body class="bg-232" data-user-role="<?php echo $userData->role ?>">

    <?php include __DIR__ . '/components/header.php'; ?>



    <div class="flex">
        <?php $userData->role != "STAFF" && include __DIR__ . '/components/sidebar.php'; ?>
        <!-- Main Content Area -->
        <main class="flex-1 p-8">
            <div class="max-w-7xl mx-auto" id="main-content-area">
                <?php
                include __DIR__ . '/load-content.php';
                ?>
            </div>
        </main>
    </div>

    <!-- Mobile Overlay -->
    <div id="mobile-overlay" class="mobile-overlay"></div>




    <script src="public/assets/js/sweetalert2.all.min.js"></script>

    <!-- PATHING FOR THE MEANTIM -->
    <script src="public/assets/js/dashboard.js"></script>
</body>

</html>
