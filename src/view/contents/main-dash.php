<?php
$user = [
    'name' => 'MAC ADMIN',
    'role' => 'GMAC',
    'avatar' => 'MA'
];
$activities = [
    ['action' => 'Staff Performance Review', 'user' => 'System', 'time' => '2 min ago', 'status' => 'completed'],
    ['action' => 'Document Generation', 'user' => 'MAC Admin', 'time' => '5 min ago', 'status' => 'processing'],
    ['action' => 'Registration Approval', 'user' => 'Auto-System', 'time' => '12 min ago', 'status' => 'approved'],
    ['action' => 'Verification Process', 'user' => 'MAC Admin', 'time' => '18 min ago', 'status' => 'pending']
];

$stats = [
    ["id" => "staffs-total", 'title' => 'Active Staff', 'value' => '247', 'change' => '+12', 'icon' => 'users'],
    ["id" => "pendings-total", 'title' => 'Pending Approval', 'value' => '18', 'change' => '-5', 'icon' => 'check-circle'],
    ["id" => "documents-total",'title' => 'Documents', 'value' => '1,234', 'change' => '+89', 'icon' => 'file-text'],
    ["id" => "performance-score", 'title' => 'Performance', 'value' => '94.2%', 'change' => '+2.1%', 'icon' => 'trending-up']
];

?>

<!-- Hero Section -->
<div class="mb-8 relative">
    <div
        class="bg-gradient-to-r from-white via-white to-gray-50 rounded-3xl p-8 shadow-xl border border-gray-200/50 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 custom-gradient-fade rounded-full -translate-y-20 translate-x-20">
        </div>
        <div class="relative z-10">
            <h2 class="text-4xl font-bold text-gray-900 mb-3">Command Dashboard</h2>
            <p class="text-gray-600 text-lg mb-6">
                Welcome back, <?php echo $user['name']; ?>. Your barangay systems are running at optimal
                performance.
            </p>
            <div class="flex items-center space-x-6">
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-green-500 rounded-full pulse-animation"></div>
                    <span class="text-sm font-medium text-gray-700">All Systems Online</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i data-lucide="activity" class="w-4 h-4 brand-red"></i>
                    <span class="text-sm font-medium text-gray-700">Monitoring</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Advanced Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <?php foreach ($stats as $index => $stat): ?>
        <div
            class="bg-white rounded-2xl shadow-lg border border-gray-200/50 p-6 relative overflow-hidden group hover:shadow-xl transition-all duration-300">
            <div
                class="stat-gradient absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-5 transition-opacity duration-300">
            </div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="stat-icon w-12 h-12 rounded-xl flex items-center justify-center shadow-lg">
                        <i data-lucide="<?php echo $stat['icon']; ?>" class="w-6 h-6 text-white"></i>
                    </div>
                    <!-- 
                    <span class="px-2 py-1 bg-green-100 text-green-800 border border-green-200 rounded text-sm font-medium">
                        <?php echo $stat['change']; ?>
                    </span>
                    -->
                </div>
                <h3 class="text-sm font-medium text-gray-600 mb-1"><?php echo $stat['title']; ?></h3>
                <p id="<?php echo $stat["id"]; ?>" $title class="text-3xl font-bold text-gray-900"><?php echo $stat['value']; ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Activity Feed -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-200/50 p-6">
    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
        <i data-lucide="activity" class="w-5 h-5 mr-2 brand-red"></i>
            Recent Activity Stream
    </h3>
    <div class="space-y-4" id="notifications-list">
        <?php foreach ($activities as $activity): ?>
            <div
                class="flex items-center space-x-4 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors duration-200">
                <div class="w-3 h-3 rounded-full <?php
                echo $activity['status'] === 'completed' ? 'bg-green-500' :
                    ($activity['status'] === 'processing' ? 'bg-yellow-500' :
                        ($activity['status'] === 'approved' ? 'bg-blue-500' : 'bg-gray-400'));
                ?>"></div>
                <div class="flex-1">
                    <p class="font-medium text-gray-900"><?php echo $activity['action']; ?></p>
                    <p class="text-sm text-gray-600">
                        by <?php echo $activity['user']; ?> • <?php echo $activity['time']; ?>
                    </p>
                </div>
                <span class="px-2 py-1 border border-gray-300 rounded text-sm capitalize bg-white">
                    <?php echo $activity['status']; ?>
                </span>
            </div>
        <?php endforeach; ?>
    </div>
</div>
