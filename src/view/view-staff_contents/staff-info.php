<!-- Content -->
<div id="personal-container">
    <h1 class="text-2xl font-semibold text-gray-900 mb-8">Personal info</h1>

    <!-- Basic Information -->
    <div class="bg-white rounded-lg border border-gray-200 mb-6 shadow-lg">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 custom-gradient-bg">
            <h2 class="text-lg font-medium text-white">Basic information</h2>
            <button class="text-gray-400 hover:text-gray-600">
                <i data-lucide="edit"></i>
            </button>
        </div>
        <div class="p-6">
            <div class="flex space-x-8">
                <div class="flex-shrink-0">
                    <div class="relative group">
                        <div
                            class="w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-lg overflow-hidden">
                            <img id="profileImagePreview" src="" alt="Profile Picture"
                                class="w-full h-full object-cover hidden" />
                            <!-- DEFAULT ICON -->
                            <svg id="defaultProfileIcon" class="w-12 h-12 text-gray-400" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <label
                            class="absolute bottom-0 right-0 bg-blue-600 hover:bg-blue-700 text-white text-xs px-2 py-1 rounded-full shadow cursor-pointer transition-opacity opacity-0 group-hover:opacity-100"
                            for="uploadProfileImage">
                            Upload
                        </label>
                        <input id="uploadProfileImage" type="file" accept="image/*" class="hidden"
                            onchange="previewProfileImage(event)">
                    </div>
                </div>
                <div class="flex-1 grid grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Woman Tanod</h3>
                        <p class="text-gray-600" id="position">Barangay Tanod</p>
                        <div class="flex items-center space-x-2 mt-2">
                            <i data-lucide="Club" class="w-4 h-4 text-gray-900"></i>
                            <span class="text-sm text-gray-600">Male</span>
                        </div>
                        <div class="flex items-center space-x-2 mt-1">
                            <i data-lucide="mail" class="w-4 h-4 text-gray-400"></i>
                            <span class="text-sm text-gray-600">womantanod@gmail.com</span>
                        </div>
                        <div class="flex items-center space-x-2 mt-1">
                            <i data-lucide="phone" class="w-4 h-4 text-gray-400"></i>
                            <span class="text-sm text-gray-600">081323523511</span>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Birth date</label>
                            <p class="text-gray-900">30 Oct 1994</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Productivity</label>
                            <p class="text-gray-900">Low</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Job Details -->
    <div class="bg-white rounded-lg border border-gray-200 mb-6 shadow-lg">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 custom-gradient-bg ">
            <h2 class="text-lg font-medium text-white">Job Details</h2>
            <button class="text-gray-400 hover:text-gray-600"><i data-lucide="edit"></i></button>
        </div>
        <div class="p-6 grid grid-cols-2 gap-6">
            <div>
                <label class="text-sm font-medium text-gray-700">Position</label>
                <p class="text-gray-900">Barangay Tanod</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700">Department</label>
                <p class="text-gray-900">Peace and Order</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700">Appointment Type</label>
                <p class="text-gray-900">Original</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700">Civil Service Eligibility</label>
                <p class="text-gray-900">None</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700">Appointment Date</label>
                <p class="text-gray-900">2023-01-10</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700">Immediate Supervisor</label>
                <p class="text-gray-900">Captain Juan</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700">Monthly Salary</label>
                <p class="text-gray-900">₱15,000</p>
            </div>
        </div>
    </div>

    <!-- Addresses -->
    <div class="bg-white rounded-lg border border-gray-200 mb-6 shadow-lg">
        <div class="flex items-center justify-between px-6 py-4 text-white border-b border-gray-200 custom-gradient-bg">
            <h2 class=" text-lg font-medium text-white">Addresses</h2>
            <button class="text-gray-400 hover:text-gray-600"><i data-lucide="edit"></i></button>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="text-sm font-medium text-gray-700">Permanent Address</label>
                <p class="text-gray-900">123 Main St, Cabuyao, Laguna</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700">Current Address</label>
                <p class="text-gray-900">Blk 4 Lot 10, Barangay Sala, Cabuyao</p>
            </div>
        </div>
    </div>

    <!-- Emergency Contacts -->
    <div class="bg-white rounded-lg border border-gray-200 mb-6 shadow-lg">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 custom-gradient-bg">
            <h2 class="text-lg font-medium text-white">Emergency Contacts</h2>
            <button class="text-gray-400 hover:text-gray-600"><i data-lucide="edit"></i></button>
        </div>
        <div class="p-6 space-y-6">
            <div>
                <p class="font-medium text-gray-900">Maria Santos <span
                        class="text-xs bg-blue-100 text-blue-600 px-2 py-0.5 rounded">Primary</span></p>
                <p class="text-gray-600">Wife</p>
                <p class="text-gray-600">09123456789</p>
                <p class="text-gray-600">womantanod@gmail.com</p>
            </div>
            <div>
                <p class="font-medium text-gray-900">Dinosaur</p>
                <p class="text-gray-600">Brother</p>
                <p class="text-gray-600">09198765432</p>
            </div>
        </div>
    </div>
</div>