<div id="set-new-password-main">
  <h1 class="text-2xl font-semibold text-gray-900 mb-6">Set New Password</h1> 
  
  <div class="bg-white p-10 rounded-3xl mb-10 shadow-xl border border-gray-100">
    <form id="reset-password-form">
      <input type="hidden" name="id" />
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-3">Username</label>
          <input type="text" name="username" readonly value="wowusernumber" class="form-input p-2 w-full rounded-xl text-md"/>
        </div>
        <div></div>
      </div>
      <div class="flex justify-end">
        <button id="generate-new-password" type="button" class="bg-blue-500 text-white px-3 py-2 rounded-xl cursor-pointer">Generate New Password</button>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:my-8 mb-8">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-3">New Password</label>
          <div class="flex gap-x-4 form-input p-2 w-full rounded-xl text-md">
            <input type="password" name="password" class="w-full glass-input outline-none" />
            <button tabindex="-1" class="size-8 outline-none toggle-view-password" type="button">
              <i data-lucide="eye-off"></i>
            </button>
          </div>
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-3">Confirm Password</label>
          <div class="flex gap-x-4 form-input p-2 w-full rounded-xl text-md">
            <input type="password" name="confirm_password" class="w-full glass-input outline-none" />
            <button tabindex="-1"  class="size-8 outline-none toggle-view-password" type="button">
              <i data-lucide="eye-off"></i>
            </button>
          </div>
        </div>
      </div>
      <div class="flex justify-center align-center col-span-2">
        <button class="w-full cursor-pointer bg-red-500 font-medium text-sm text-white px-4 py-3 rounded-xl">Reset Password</button>
      </div>
    </form>
  </div>
</div>
