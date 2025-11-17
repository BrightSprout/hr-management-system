<div class="p-6">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-50 rounded-2xl border border-gray-200 shadow-md p-5">
        <div class="flex items-center gap-3">
        <i data-lucide="check-circle" class="h-6 w-6 text-green-600"></i>

        <h2 class="text-2xl font-bold text-gray-900 tracking-wide">Staff Approval Request</h2>
      </div>
    </div>
    
    <div class="overflow-x-auto rounded-xl shadow-lg backdrop-blur-md bg-white/30 border border-white/30">
        <table class="min-w-full divide-y divide-gray-300 text-sm text-left text-gray-700">
            <thead class="bg-white/20 backdrop-blur-md text-gray-800 uppercase tracking-wide text-xs">
                <tr>
                    <th scope="col" class="px-6 py-4">ID #</th>
                    <th scope="col" class="px-6 py-4">Name</th>
                    <th scope="col" class="px-6 py-4">Role</th>
                    <th>Request Type</th>
                    <th scope="col" class="px-6 py-4">Date Applied</th>
                    <th scope="col" class="px-6 py-4">Status</th>
                    <th scope="col" class="px-6 py-4">Description</th>
                    <th scope="col" class="px-6 py-4">Actions</th>
                </tr>
            </thead>
            <tbody id="need-approvals" class="divide-y divide-gray-200 backdrop-blur-md">
                <!-- Sample Row -->
                <tr class="hover:bg-white/30 transition">
                    <td class="px-6 py-4 font-medium">00124</td>
                    <td class="px-6 py-4">Woman</td>
                    <td class="px-6 py-4">Tanod</td>
                    <td class="px-6 py-4">Leave</td>
                    <td class="px-6 py-4">2025-08-01</td>

                    <td class="px-6 py-4">
                        <span
                            class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                            Pending
                        </span>
                    </td>
                    <td class="px-6 py-4">Leave</td>
                    <td class="px-6 py-4 space-x-2">
                        <button
                            class="px-3 py-1 text-xs font-medium text-white bg-green-500 rounded hover:bg-green-600 transition cursor-pointer">
                            Approve
                        </button>
                        <button
                            class="px-3 py-1 text-xs font-medium text-white bg-red-500 rounded hover:bg-red-600 transition cursor-pointer">
                            Reject
                        </button>
                    </td>
                </tr>


            </tbody>
        </table>
    </div>
</div>
