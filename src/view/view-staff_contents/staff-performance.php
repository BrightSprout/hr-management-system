<div>
<!-- HEADER -->
    <header class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-50 rounded-2xl border border-gray-200 shadow-md p-5">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m2 0a2 2 0 002-2V7a2 2 0 00-2-2h-2l-2-2H9L7 5H5a2 2 0 00-2 2v3a2 2 0 002 2h14z" />
                </svg>
                Performance Logs
            </h1>
            <p class="text-gray-500 text-xs sm:text-sm">Analytics, Attendance, Activity, and Leave reports</p>
        </div>
    </header>
<!-- ANALYTICS CARDS WITH CHARTS -->
    <section class="mb-8 sm:mb-10">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 mb-4 sm:mb-6">
            <h2 class="text-base sm:text-lg font-semibold text-gray-800">Analytics and Performance Report</h2>
            <div class="flex items-center gap-2 text-xs sm:text-sm text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 sm:w-4 sm:h-4 text-gray-400" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span id="today-date-txt">August 2025</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            <!-- Attendance Chart -->
            <div class="p-4 sm:p-4 rounded-lg border border-gray-200 bg-white shadow-sm">
                <h3 class="text-xs sm:text-sm font-semibold text-blue-600 uppercase mb-1 sm:mb-2">Attendance Trends</h3>
                <div class="aspect-[16/9] w-full">
                    <canvas id="attendanceChart" class="w-full h-full"></canvas>
                </div>
                <p class="mt-1 sm:mt-2 text-xs text-gray-500">Shows the most common absent days per week.</p>
            </div>

            <!-- Leave Chart -->
            <div class="p-4 sm:p-4 rounded-lg border border-gray-200 bg-white shadow-sm">
                <h3 class="text-xs sm:text-sm font-semibold text-green-600 uppercase mb-1 sm:mb-2">Leave Analysis</h3>
                <div class="aspect-[16/9] w-full">
                    <canvas id="leaveChart" class="w-full h-full"></canvas>
                </div>
                <p class="mt-1 sm:mt-2 text-xs text-gray-500">Highlights monthly leave request trends.</p>
            </div>

            <!-- Performance Chart -->
            <div class="p-4 sm:p-4 rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between mb-1 sm:mb-2">
                    <h3 class="text-xs sm:text-sm font-semibold text-orange-600 uppercase">Performance Score</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 text-green-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12l5 5L20 7" />
                    </svg>
                </div>
                <div class="aspect-[16/9] w-full">
                    <canvas id="performanceChart" class="w-full h-full"></canvas>
                </div>
                <p class="mt-1 sm:mt-2 text-xs text-gray-500">Calculated as: (Total Employee # of present ÷ total
                    employee x
                    total working days) × 100.</p>
            </div>
        </div>
    </section>

    <!-- LEAVE & ABSENCE REPORTS SECTION -->
    <section class="mb-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
            <h2 class="text-lg sm:text-xl font-semibold text-gray-800">Leave & Absence Reports</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 sm:gap-6 mb-6">
            <!-- Leave Requests Summary -->
            <div class="p-4 bg-blue-50 rounded-lg border border-blue-100">
                <h3 class="text-sm font-semibold text-blue-800 mb-2">Leave Requests Status</h3>
                <div class="flex flex-col gap-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-700">Pending</span>
                        <span id="pending-leave-total" class="text-sm font-medium text-blue-600">12</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-700">Approved</span>
                        <span id="approved-leave-total" class="text-sm font-medium text-green-600">24</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-700">Rejected</span>
                        <span id="rejected-leave-total" class="text-sm font-medium text-red-600">3</span>
                    </div>
                </div>
            </div>

            <!-- AWOL Flags -->
            <div class="p-4 bg-red-50 rounded-lg border border-red-100">
                <h3 class="text-sm font-semibold text-red-800 mb-2">Absence Without Leave</h3>
                <div class="flex flex-col gap-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-700">AWOL Cases</span>
                        <span id="awol-total" class="text-sm font-medium text-red-600">2</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-700">Unexcused Absences</span>
                        <span id="unexcused-absence-total" class="text-sm font-medium text-orange-600">7</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-700">Under Investigation</span>
                        <span id="investigate-absence-total" class="text-sm font-medium text-blue-600">3</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- DETAILED LEAVE TABLE -->
        <details class="group [&_summary::-webkit-details-marker]:hidden">
            <summary
                class="flex items-center justify-between p-2 bg-gray-100 rounded-lg cursor-pointer hover:bg-gray-200">
                <h3 class="text-base font-medium text-gray-900">View Detailed Leave Records</h3>
                <svg class="w-5 h-5 text-gray-500 transition-transform duration-300 group-open:rotate-180"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </summary>

            <div class="mt-2 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name</th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date Leave</th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date Returning</th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description</th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type of Leave</th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Approved By</th>
                            <th scope="col"
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                        </tr>
                    </thead>
                    <tbody id="display-leave-records" class="bg-white divide-y divide-gray-200">
                        <!-- Sample Row 1 -->
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">Hitler</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">2025-08-01</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">2025-08-07</td>
                            <td class="px-4 py-3 text-sm text-gray-500">Leaving to launch some nuclear bomb</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">Annual Leave</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">Secretary</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                            </td>
                        </tr>



                    </tbody>
                </table>
            </div>
        </details>
    </section>
</div>
