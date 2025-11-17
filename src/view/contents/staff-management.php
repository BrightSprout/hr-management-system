<div>
    <div class="max-w-7xl mx-auto p-4">
        <!-- Header -->
        <div class=" bg-gray-50 rounded-2xl border border-gray-200 shadow-md p-5 mb-8">
            <div class="flex items-center gap-3 mb-2">
                <i data-lucide="users" class="h-8 w-8 text-blue-600"></i>
                <h1 class="text-3xl font-bold text-gray-900">Staff Management System</h1>
            </div>

            <p class="text-gray-600">Manage your organization's staff members</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border mb-6 p-6 border-gray-300">
            <div class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
                <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                    <form method="GET" class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700">Filter by Role:</label>
                        <select name="role" id="select-roles"
                            class="border border-gray-300 rounded-md px-3 py-2 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="ALL">All Roles</option>
                            <option value="GMAC">GMAC</option>
                            <option value="SECRETARY">Secretary</option>
                            <option value="STAFFS">Staffs</option>
                        </select>
                    </form>
                    <div class="text-sm text-gray-600">
                        Showing <span class="total-employee-showing">5</span> of <span class="total-employee">6969</span> staff members
                    </div>
                </div>

                <button
                    id="addStaffBtn"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center gap-2 transition-colors cursor-pointer openStaff">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    Add Staff
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <div class="flex items-center justify-between my-4 pr-2">
                <h2 class="text-xl font-semibold text-gray-800">Staff List</h2>
                <div class="text-sm text-gray-600 flex gap-x-2 align-center">
                    <div class="flex items-end">
                      <p class="h-auto">Showing <span class="total-employee-showing font-medium">1</span> of <span class="total-employee font-medium">1</span> employees</p>
                    </div>
                    <div>
                      <input type="text" id="search-staff" placeholder="Search Staff..." class="border rounded-xl bg-white text-gray-900 placeholder-gray-400 px-4 py-2 border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-300 w-[15vw]" />
                    </div>
                </div>
            </div>
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Checked by</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody id="staffsContainer" class="bg-white divide-y divide-gray-200">
                </tbody>
            </table>
        </div>

    </div>

    <div>

    </div>

</div>
