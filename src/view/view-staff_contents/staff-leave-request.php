<!-- Leave Request -->
<div id="leave-main" class="space-y-6">
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">Leave Request</h1>

    <!-- Request Form -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Submit a Leave Request</h2>
        <form id="create-leave-form" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700">Leave Type</label>
                    <select
                        class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        name="type" required
                    >
                        <option value="SICK">Sick Leave</option>
                        <option value="VACATION">Vacation Leave</option>
                        <option value="EMERGENCY">Emergency Leave</option>
                        <option value="MATERNITY">Maternity Leave</option>
                        <option value="OTHER">Other</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Reason</label>
                    <input type="text" placeholder="Enter reason" name="reason" required
                        class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" name="start_date" required
                        class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">End Date</label>
                    <input type="date" name="end_date" required
                        class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 flex items-center space-x-2">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    <span>Submit Request</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Previous Requests -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-x-auto mt-3">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Previous Requests</h2>
        </div>
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left font-medium text-gray-600">Leave Type</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-600">Dates</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-600">Reason</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-600">Status</th>
                </tr>
            </thead>
            <tbody id="leaves" class="divide-y divide-gray-200">
                <tr>
                    <td class="px-6 py-4 text-gray-900">Sick Leave</td>
                    <td class="px-6 py-4 text-gray-600">2023-08-10 → 2023-08-12</td>
                    <td class="px-6 py-4 text-gray-600">Flu</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-600">Pending</span>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 text-gray-900">Vacation Leave</td>
                    <td class="px-6 py-4 text-gray-600">2023-07-20 → 2023-07-25</td>
                    <td class="px-6 py-4 text-gray-600">Family Trip</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-600">Approved</span>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 text-gray-900">Emergency Leave</td>
                    <td class="px-6 py-4 text-gray-600">2023-06-05</td>
                    <td class="px-6 py-4 text-gray-600">Urgent family matter</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-600">Rejected</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
