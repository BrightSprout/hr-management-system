<div class="min-h-screen p-6 bg-white">
 
  <div class="bg-gray-50 rounded-2xl border border-gray-200 shadow-md p-5 mb-8">
    <div class="flex justify-between items-center">
      <div class="flex items-center gap-3">
        <i data-lucide="users" class="h-8 w-8 text-blue-600"></i>
        <h2 class="text-2xl font-bold text-gray-900 tracking-wide">Staff Directory</h2>
      </div>
      <input
        id="search-staff"
        type="text"
        placeholder="Search staff..."
        class="rounded-xl bg-white text-gray-900 placeholder-gray-400 px-4 py-2 border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
      />
    </div>
  </div>

  <div id="staff-list" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
    <div
      class="bg-gray-50 border border-gray-200 rounded-2xl shadow-sm p-5 hover:shadow-md hover:bg-blue-50 transition-all duration-300 cursor-pointer">
      <div class="flex flex-col space-y-2">
        <h3 class="text-lg font-semibold text-gray-800">Juan Dela Cruz</h3>
        <p class="text-sm text-gray-600">Position: <span class="font-medium text-gray-700">Barangay Tanod</span></p>
        <p class="text-sm text-gray-600">Phone: <span class="font-medium text-gray-700">0946 092 3984</span></p>
      </div>
    </div>
  </div>

</div>
