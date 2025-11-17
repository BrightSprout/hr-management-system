<div class="max-w-6xl mx-auto p-6">
    <!-- Header -->
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-50 rounded-2xl border border-gray-200 shadow-md p-5">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
                <i data-lucide="clipboard-list" class="h-7 w-7 text-blue-600"></i>
                Attendance Sheet
            </h1>
            <p class="text-gray-500 mt-1">Summary of today's attendance</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
            <!-- Date Display -->
            <div class="bg-blue-50 text-blue-700 font-medium px-4 py-2 rounded-lg text-sm">
                Date: <span id="today-date" class="font-semibold">August 7, 2025</span>
            </div>
            &rarr;
            <button
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium text-sm shadow transition-colors flex gap-x-3 cursor-pointer today-summary-btn">
                <i data-lucide="file-down" class="inline w-4 h-4 mr-2"></i>
                Summary
            </button>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="overflow-x-auto bg-white rounded-lg shadow border border-gray-200">
        <table class="min-w-full table-auto text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-6 py-4">Name</th>
                    <th class="px-6 py-4">Role</th>
                    <th class="px-6 py-4">Time In</th>
                    <th class="px-6 py-4">Time Out</th>
                </tr>
            </thead>
            <tbody id="attendance-records" class="divide-y divide-gray-200">
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900">Dinosaur Tanod</td>
                    <td class="px-6 py-4 text-gray-700">Tanod</td>
                    <td class="px-6 py-4 text-green-600">08:00 AM</td>
                    <td class="px-6 py-4 text-red-600">05:00 PM</td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900">Gwen Secretary</td>
                    <td class="px-6 py-4 text-gray-700">Secretary</td>
                    <td class="px-6 py-4 text-green-600">08:15 AM</td>
                    <td class="px-6 py-4 text-red-600">04:50 PM</td>
                </tr>

            </tbody>
        </table>
    </div>
</div>
