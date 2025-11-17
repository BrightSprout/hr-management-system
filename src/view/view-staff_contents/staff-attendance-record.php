<!-- Attendance Content -->
<div id="attendance-main" class="space-y-6">
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">Attendance Record</h1>

    <!-- Filters -->
    <form id="filter-date-form" class="bg-white p-4 rounded-lg border border-gray-200 flex items-center space-x-4">
        <div>
            <label class="text-sm font-medium text-gray-700">From</label>
            <input type="date" name="start_date" class="ml-2 border border-gray-300 rounded px-2 py-1 text-sm">
        </div>
        <div>
            <label class="text-sm font-medium text-gray-700">To</label>
            <input type="date" name="end_date" class="ml-2 border border-gray-300 rounded px-2 py-1 text-sm">
        </div>
        <button class="ml-auto px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
            Apply
        </button>
    </form>

    <!-- Attendance Table -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left font-medium text-gray-600">Date</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-600">Time-in</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-600">Time-out</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-600">Status</th>
                </tr>
            </thead>
            <tbody id="attendances" class="divide-y divide-gray-200">
                <tr>
                    <td class="px-6 py-4 text-gray-900">2023-08-01</td>
                    <td class="px-6 py-4 text-gray-600">08:00 AM</td>
                    <td class="px-6 py-4 text-gray-600">05:00 PM</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-600">Present</span>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 text-gray-900">2023-08-02</td>
                    <td class="px-6 py-4 text-gray-600">08:15 AM</td>
                    <td class="px-6 py-4 text-gray-600">05:10 PM</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-600">Late</span>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 text-gray-900">2023-08-03</td>
                    <td class="px-6 py-4 text-gray-600">—</td>
                    <td class="px-6 py-4 text-gray-600">—</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-600">Absent</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
