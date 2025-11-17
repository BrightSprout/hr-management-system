<div id="department-main" class="max-w-6xl mx-auto p-6">
  <div id="departments" class="my-6">



  </div>
  <form id="create-department" class=" mx-auto bg-white shadow-lg rounded-2xl border border-gray-200 overflow-hidden mt-6">
    <div class="custom-gradient-bg p-4">
      <h2 class="text-xl font-semibold text-center text-white tracking-wide">Create Department</h2>
    </div>


    <div class="p-6 space-y-4">
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
        <input class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400" name="name" />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Day Offs</label>
        <div class="flex flex-wrap gap-2">
          <label class="cursor-pointer">
            <input type="checkbox" name="dayoffs" value="1" class="peer hidden" />
            <span class="px-4 py-2 rounded-full border border-gray-300 text-gray-600 text-sm 
              peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-500
              transition-all duration-200">
              Monday
            </span>
          </label>
          <label class="cursor-pointer">
            <input type="checkbox" name="dayoffs" value="2" class="peer hidden" />
            <span class="px-4 py-2 rounded-full border border-gray-300 text-gray-600 text-sm 
              peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-500
              transition-all duration-200">
              Tuesday
            </span>
          </label>
          <label class="cursor-pointer">
            <input type="checkbox" name="dayoffs" value="3" class="peer hidden" />
            <span class="px-4 py-2 rounded-full border border-gray-300 text-gray-600 text-sm 
              peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-500
              transition-all duration-200">
              Wednesday
            </span>
          </label>
          <label class="cursor-pointer">
            <input type="checkbox" name="dayoffs" value="4" class="peer hidden" />
            <span class="px-4 py-2 rounded-full border border-gray-300 text-gray-600 text-sm 
              peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-500
              transition-all duration-200">
              Thursday
            </span>
          </label>
          <label class="cursor-pointer">
            <input type="checkbox" name="dayoffs" value="5" class="peer hidden" />
            <span class="px-4 py-2 rounded-full border border-gray-300 text-gray-600 text-sm 
              peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-500
              transition-all duration-200">
              Friday
            </span>
          </label>
          <label class="cursor-pointer">
            <input type="checkbox" name="dayoffs" value="6" class="peer hidden" />
            <span class="px-4 py-2 rounded-full border border-gray-300 text-gray-600 text-sm 
              peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-500
              transition-all duration-200">
              Saturday
            </span>
          </label>
          <label class="cursor-pointer">
            <input type="checkbox" name="dayoffs" value="0" class="peer hidden" />
            <span class="px-4 py-2 rounded-full border border-gray-300 text-gray-600 text-sm 
              peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-500
              transition-all duration-200">
              Sunday
            </span>
          </label>
        </div>
      </div>

      <div>
        <label for="clock_in" class="block text-sm font-medium text-gray-700 mb-1">Time In</label>
        <input type="time" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400" name="clock_in" />
      </div>

      <div>
        <label for="clock_out" class="block text-sm font-medium text-gray-700 mb-1">Time Out</label>
        <input type="time" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400" name="clock_out" />
      </div>

      <button type="submit" class="w-full bg-green-400 hover:bg-green-500 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 cursor-pointer">
        Submit
      </button>
    </div>
  </form>


  <div id="editModal" class="hidden backdrop-blur-md fixed inset-0  bg-opacity-50 flex items-center justify-center p-4" style="z-index: 999;">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl max-h-[95vh] overflow-y-auto" style="position: relative; z-index: 100;">
      <!-- Header -->
      <div class="sticky top-0 bg-white border-b border-gray-100 px-8 py-6 rounded-t-3xl">
        <div class="flex justify-between items-center">
          <h2 class="text-2xl font-semibold text-gray-800">Edit Department</h2>
          <button id="closeModalBtn" class="w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Form -->
      <div id="form-container" class="px-8 py-6 space-y-8">
        <form id="edit-form" class="space-y-6" data-type="department"></form>
        <!-- Actions -->
        <div class="sticky bottom-0 bg-white border-t border-gray-100 -mx-8 px-8 py-6 rounded-b-3xl">
          <div class="flex justify-end gap-3">
            <button type="button" id="cancelBtn" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 rounded-xl text-gray-700 font-medium transition-colors">Cancel</button>
            <button type="submit" id="saveBtn" class="px-6 py-3 bg-slate-800 hover:bg-slate-700 text-white rounded-xl font-medium shadow-lg transition-all">Save Changes</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
