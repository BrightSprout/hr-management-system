<?php

$month = $_GET['month'] ?? date('Y-m');
$totalDays = date("t", strtotime($month));

// just for displaying
$employees = [
    "Woman Tanod" => [
        1 => "P",
        2 => "P",
        3 => "P",
        4 => "L",
        5 => "-",
        6 => "-",
        7 => "-"
    ],
];

// Mapping the staus
function statusClass($status)
{
    return match ($status) {
        "P" => "bg-green-500 text-white",
        "A" => "bg-red-500 text-white",
        "H" => "bg-blue-500 text-white",
        "L" => "bg-yellow-500 text-white",
        "T" => "bg-purple-500 text-white",
        default => "bg-gray-200 text-gray-600"
    };
}
?>
<div class="w-full mx-auto px-4">
    <!-- Profile Header -->
    <div class="prof-bg rounded-lg shadow-md overflow-hidden mb-6">
        <div class="px-6 py-8">
            <div class="flex items-center space-x-6">
                <!-- Profile Picture + Upload -->
                <div class="relative group">
                    <div
                        class="w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-lg overflow-hidden">
                        <img id="profileImagePreview" src="" alt="Profile Picture"
                            class="w-full h-full object-cover hidden" />
                        <!-- DEFAULT ICON -->
                        <svg id="defaultProfileIcon" class="w-12 h-12 text-gray-400" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                clip-rule="evenodd">
                            </path>
                        </svg>
                    </div>

                    <!-- Upload Button -->
                    <label
                        class="absolute bottom-0 right-0 bg-blue-600 hover:bg-blue-700 text-white text-xs px-2 py-1 rounded-full shadow cursor-pointer transition-opacity opacity-0 group-hover:opacity-100"
                        for="uploadProfileImage">
                        Upload
                    </label>
                    <input id="uploadProfileImage" type="file" accept="image/*" class="hidden"
                        onchange="previewProfileImage(event)">
                </div>

                <!-- Basic Info -->
                <div class="text-white">
                    <h1 class="text-3xl font-bold">Woman Tanod</h1>
                    <p class="text-blue-100 text-lg">Tanod Staff</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Contact Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                    </path>
                </svg>
                Contact Information
            </h2>
            <div class="space-y-3">
                <div class="flex items-center">
                    <span class="text-gray-600 font-medium w-20">Email:</span>
                    <span class="text-gray-800">woman@gmail.com</span>
                </div>
                <div class="flex items-center">
                    <span class="text-gray-600 font-medium w-20">Phone:</span>
                    <span class="text-gray-800">09461783921</span>
                </div>
            </div>
        </div>

        <!-- Personal Details -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Personal Details
            </h2>
            <div class="space-y-3">
                <div class="flex items-center">
                    <span class="text-gray-600 font-medium w-28">Gender:</span>
                    <span class="text-gray-800">Male</span>
                </div>
                <div class="flex items-center">
                    <span class="text-gray-600 font-medium w-28">Civil Status:</span>
                    <span class="text-gray-800">Single</span>
                </div>
                <div class="flex items-center">
                    <span class="text-gray-600 font-medium w-28">Date of Birth:</span>
                    <span class="text-gray-800">January 1, 2001</span>
                </div>
            </div>
        </div>

        <!-- Employment Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6">
                    </path>
                </svg>
                Employment Information
            </h2>
            <div class="space-y-3">
                <div class="flex items-center">
                    <span class="text-gray-600 font-medium w-24">Position:</span>
                    <span class="text-gray-800">Tanod Staff</span>
                </div>
                <div class="flex items-center">
                    <span class="text-gray-600 font-medium w-24">Date Hired:</span>
                    <span class=" text-sm bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Juky 10, 2069</span>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                    </path>
                </svg>
                Performance
            </h2>
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center p-4 bg-red-50 rounded-lg">
                    <div class="text-2xl font-bold text-red-600">--</div>
                    <div class="text-sm text-red-700">Total Absent</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">--</div>
                    <div class="text-sm text-green-700">Total Present</div>
                </div>
            </div>
        </div>

    </div>

    <div class="p-6 bg-white rounded-xl shadow">
        <!-- Title -->
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Detailed Summary of Staff Attendance</h1>

        <!-- Month Selector -->
        <form method="GET" class="mb-6 flex items-center space-x-4">
            <label for="month" class="text-gray-700 font-medium">Select Month:</label>
            <input type="month" id="month" name="month" value="<?= $month ?>"
                class="border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            <button type="submit"
                class="bg-blue-500 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-600 focus:ring-2 focus:ring-blue-500">
                Load
            </button>
        </form>

        <!-- Legend -->
        <div class="flex flex-wrap items-center space-x-6 mb-6">
            <div class="flex items-center space-x-2">
                <span
                    class="w-6 h-6 flex items-center justify-center bg-green-500 text-white font-bold rounded">P</span>
                <span class="text-sm text-gray-700">Present</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-6 h-6 flex items-center justify-center bg-red-500 text-white font-bold rounded">A</span>
                <span class="text-sm text-gray-700">Absent</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-6 h-6 flex items-center justify-center bg-blue-500 text-white font-bold rounded">H</span>
                <span class="text-sm text-gray-700">Holiday</span>
            </div>
            <div class="flex items-center space-x-2">
                <span
                    class="w-6 h-6 flex items-center justify-center bg-yellow-500 text-white font-bold rounded">L</span>
                <span class="text-sm text-gray-700">Leave</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-6 h-6 flex items-center justify-center bg-gray-500 text-white font-bold rounded">W</span>
                <span class="text-sm text-gray-700">Weekends</span>
            </div>
        </div>

        <!-- Attendance Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300 text-sm text-center">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-2 py-1 text-left">Employee Name</th>
                        <?php for ($d = 1; $d <= $totalDays; $d++): ?>
                            <th class="border border-gray-300 px-2 py-1"><?= $d ?></th>
                        <?php endfor; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $name => $attendance): ?>
                        <tr>
                            <td class="border border-gray-300 px-2 py-1 text-left font-medium"><?= $name ?></td>
                            <?php for ($d = 1; $d <= $totalDays; $d++):
                                $status = $attendance[$d] ?? "-";
                                ?>
                                <td class="border border-gray-300 <?= statusClass($status) ?>"><?= $status ?></td>
                            <?php endfor; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>