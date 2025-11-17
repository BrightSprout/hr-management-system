<!-- Layout -->
<div class="max-w-7xl mx-auto p-4" id="view-main-container">
    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-6 overflow-auto pt-5">
        <!-- Breadcrumb -->
        <div class="bg-white px-6 py-4 border border-gray-200 rounded-lg mb-6">
            <div class="flex items-center space-x-2 text-sm text-gray-600">
                <span class="font-medium text-gray-900">Employee</span>
                <span>/</span>
                <span id="breadcrumb-label" class="font-medium text-gray-900">Personal Info</span>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="bg-white flex justify-between border border-gray-200 rounded-lg mb-6">
            <nav class="px-6" id="view-staff-nav">
                <div class="flex space-x-8 overflow-x-auto">
                    <button data-navfor="?page=view-staff/personal"
                        class="border-b-2 border-blue-500 text-blue-600 py-4 text-sm font-medium whitespace-nowrap cursor-pointer">Personal
                        info</button>
                    <button data-navfor="?page=view-staff/attendance"
                        class="text-gray-500 hover:text-gray-700 py-4 text-sm font-medium whitespace-nowrap cursor-pointer">Attendance
                        Record</button>
                    <button data-navfor="?page=view-staff/documents"
                        class="text-gray-500 hover:text-gray-700 py-4 text-sm font-medium whitespace-nowrap cursor-pointer">Documents</button>
                    <button data-navfor="?page=view-staff/leave"
                        class="hidden text-gray-500 hover:text-gray-700 py-4 text-sm font-medium whitespace-nowrap cursor-pointer">Leave
                        Request</button>
                    <button data-navfor="?page=view-staff/performance"
                        class="hidden text-gray-500 hover:text-gray-700 py-4 text-sm font-medium whitespace-nowrap cursor-pointer">Performance</button>
                    <button data-navfor="?page=view-staff/reset-password"
                        class="hidden text-gray-500 hover:text-gray-700 py-4 text-sm font-medium whitespace-nowrap cursor-pointer">Set New Password</button>
                </div>
            </nav>
            <div id="staff-logout" class="hidden flex align-center justify-center pr-6">
              <form class="flex align-center" action="api/logout">
                 <button
                   class="cursor-pointer w-full flex items-center justify-start px-3 py-2 text-red-600 hover:bg-red-50 rounded-xl transition-colors">
                   <i data-lucide="log-out" class="h-5 w-5 mr-3"></i>
                   Log Out
                 </button>
              </form> 
            </div>
        </div>
        <div id="secondary-content-area">
        </div>
    </main>
</div>
