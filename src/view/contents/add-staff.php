<div class="add-modal bg-232">
    <div class="p-4 mx-auto w-full gradient-btn shadow-2xl">
        <div class="text-center">
            <div
                class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-full backdrop-blur-sm mb-6">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2-2v16m14 0a2 2 0 002-2v-4M7 3v18"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white mb-4">
                Barangay Employee Registration
            </h1>
            <p class="text-white/90 text-md font-light max-w-3xl mx-auto">
                Complete all sections to register a new barangay employee in our system
            </p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto p-6 -mt-10 relative z-20">
        <!-- Stepper -->
        <div class="form-card rounded-3xl p-8 mb-10 shadow-xl">
            <div class="flex items-center justify-between">
                <!-- Step 1 -->
                <div class="flex items-center step-indicator" data-step="0">
                    <div
                        class="step-circle active flex items-center justify-center w-16 h-16 rounded-full font-semibold text-lg">
                        1
                    </div>
                    <div class="ml-6 hidden lg:block">
                        <p class="step-title text-base font-bold text-blue-600">Personal &amp; Job Information</p>
                        <p class="text-sm text-gray-600">Identity &amp; Role</p>
                    </div>
                </div>

                <div class="flex-1 h-1 mx-4 bg-gray-200 rounded-full">
                    <div class="h-full bg-gradient-to-r from-green-500 to-green-500 rounded-full transition-all duration-500 progressbar-step-0"
                        style="width: 25%"></div>
                </div>

                <!-- Step 2 -->
                <div class="flex items-center step-indicator" data-step="1">
                    <div
                        class="step-circle flex items-center justify-center w-16 h-16 rounded-full border-4 border-gray-300 bg-white text-gray-400 font-semibold text-lg">
                        2
                    </div>
                    <div class="ml-6 hidden lg:block">
                        <p class="step-title text-base font-medium text-gray-400">Emergency Contact</p>
                        <p class="text-sm text-gray-500">Contact Person</p>
                    </div>
                </div>

                <div class="flex-1 h-1 mx-4 bg-gray-200 rounded-full">
                    <div class="h-full bg-gray-200 rounded-full transition-all duration-500 progressbar-step-1"></div>
                </div>

                <!-- Step 3 -->
                <div class="flex items-center step-indicator" data-step="2">
                    <div
                        class="step-circle flex items-center justify-center w-16 h-16 rounded-full border-4 border-gray-300 bg-white text-gray-400 font-semibold text-lg">
                        3
                    </div>
                    <div class="ml-6 hidden lg:block">
                        <p class="step-title text-base font-medium text-gray-400">Documents &amp; Biometrics</p>
                        <p class="text-sm text-gray-500">Verification</p>
                    </div>
                </div>

                <div class="flex-1 h-1 mx-4 bg-gray-200 rounded-full">
                    <div class="h-full bg-gray-200 rounded-full transition-all duration-500 progressbar-step-2"
                        style="width: 0%"></div>
                </div>

                <!-- Step 4 -->
                <div class="flex items-center step-indicator" data-step="3">
                    <div
                        class="step-circle flex items-center justify-center w-16 h-16 rounded-full border-4 border-gray-300 bg-white text-gray-400 font-semibold text-lg">
                        4
                    </div>
                    <div class="ml-6 hidden lg:block">
                        <p class="step-title text-base font-medium text-gray-400">Review &amp; Submit</p>
                        <p class="text-sm text-gray-500">Final Check</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Steps Container -->
        <div class="form-container">
            <!-- Step 1: Profile -->
            <div class="form-step form-step-1 bg-white p-10 rounded-3xl shadow-xl border border-gray-100">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-800 mb-3">Personal Information</h2>
                    <p class="text-gray-600 text-lg">Please provide your personal details accurately</p>
                </div>

                <form class="space-y-10" id="add-personalInformation">
                    <input type="hidden" name="type" value="employee"/>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-6 pb-3 border-b-2 border-blue-100">Full Name
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <input type="hidden" name="id" always-included />
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">First Name *</label>
                                <input type="text" name="first_name" pattern="^[a-zA-Z ]+$" required class="form-input p-2 w-full rounded-xl text-md"
                                    placeholder="Enter first name" />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Middle Name</label>
                                <input type="text" name="middle_name" pattern="^[a-zA-Z ]+$" class="form-input p-2 w-full rounded-xl text-md"
                                    placeholder="Enter middle name" />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Last Name *</label>
                                <input type="text" name="last_name" pattern="^[a-zA-Z ]+$" required class="form-input p-2 w-full rounded-xl text-md"
                                    placeholder="Enter last name" />
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-6 pb-3 border-b-2 border-blue-100">Contact
                            Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Email Address *</label>
                                <input type="email" name="email" required class="form-input p-2 w-full rounded-xl text-md"
                                    placeholder="example@email.com" />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Phone Number *</label>
                                <input type="tel" name="phone_no" required class="form-input p-2 w-full rounded-xl text-md"
                                    placeholder="+63 XXX XXX XXXX" />
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-6 pb-3 border-b-2 border-blue-100">Personal
                            Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Date of Birth *</label>
                                <input type="date" name="dob" required class="form-input p-2 w-full rounded-xl text-md"/>
                            </div>
                            <div>
                              <label class="block text-sm font-semibold text-gray-700 mb-3">Gender</label>
                              <select name="gender" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                                <option value="Prefer not to say">Prefer not to say</option>
                              </select>
                            </div>
                       </div>
                    </div>
                </form>
                <form class="pt-10" form-list>
                    <input type="hidden" name="type" value="addresses"/>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-6 pb-3 border-b-2 border-blue-100">Address
                            Information</h3>
                        <ul class="space-y-6">
                          <li class="space-y-6">
                            <input type="hidden" name="id" always-included/>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Street Address *</label>
                                <input type="text" name="street_name" required class="form-input p-2 w-full rounded-xl text-md"
                                    placeholder="House number, street name" />
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Barangay *</label>
                                    <input type="text" name="barangay" required class="form-input p-2 w-full rounded-xl text-md"
                                        placeholder="Enter barangay" />
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">City *</label>
                                    <input type="text" name="city" required class="form-input p-2 w-full rounded-xl text-md"
                                        placeholder="Enter city" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Province *</label>
                                    <input type="text" name="province" required class="form-input p-2 w-full rounded-xl text-md"
                                        placeholder="Enter province" />
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Zip Code *</label>
                                    <input type="text" name="zipcode" required class="form-input p-2 w-full rounded-xl text-md"
                                        placeholder="XXXX" />
                                </div>
                            </div>
                          </li>
                        </ul>
                    </div>
                </form>
                <form class="space-y-10 pt-10" id="add-jobInformation" form-list>
                  <input type="hidden" name="type" value="jobs" />
                  <ul>
                    <li class="space-y-10">
                      <input type="hidden" name="id" always-included/>
                      <div>
                          <h3 class="text-xl font-semibold text-gray-700 mb-6 pb-3 border-b-2 border-blue-100">Position
                              Details</h3>
                          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                              <div>
                                  <label class="block text-sm font-semibold text-gray-700 mb-3">Position *</label>
                                  <select name="position" required class="form-input p-2 w-full rounded-xl text-md">
                                      <option value="">Select Position</option>
                                      <option>BARANGAY_KAGAWAD</option>
                                      <option>BARANGAY_CAPTAIN</option>
                                      <option>BARANGAY_SECRETARY</option>
                                      <option>BARANGAY_TREASURER</option>
                                      <option>SK_CHAIRMAN</option>
                                      <option>BARANGAY_HEALTH_WORKER</option>
                                      <option>BARANGAY_TANOD</option>
                                      <option>ADMINISTRATIVE_CLERK</option>
                                      <option>UTILITY_WORKER</option>
                                      <option>DAY_CARE_WORKER</option>
                                  </select>
                              </div>
                              <div>
                                  <label class="block text-sm font-semibold text-gray-700 mb-3">Department *</label>
                                  <select name="department" required class="form-input p-2 w-full rounded-xl text-md">
                                      <option value="">Select Department</option>
                                      <option>BARANGAY_OFFICE</option>
                                      <option>BARANGAY_HEALTH_SERVICES</option>
                                      <option>PEACE_AND_ORDER</option>
                                      <option>DISASTER_RISK_REDUCTION</option>
                                      <option>SOCIAL_SERVICES</option>
                                      <option>AGRICULTURE</option>
                                      <option>ENVIRONMENT_AND_SANITATION</option>
                                      <option>YOUTH_AND_SPORTS_DEVELOPMENT</option>
                                  </select>
                              </div>
                          </div>
                      </div>

                      <div>
                          <h3 class="text-xl font-semibold text-gray-700 mb-6 pb-3 border-b-2 border-blue-100">Employment
                              Terms</h3>
                          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                              <div>
                                  <label class="block text-sm font-semibold text-gray-700 mb-3">Appointment Type *</label>
                                  <select name="appointment_type" required class="form-input p-2 w-full rounded-xl text-md">
                                      <option value="">Select Type</option>
                                      <option>ORIGINAL</option>
                                      <option>PROMOTION</option>
                                      <option>TRANSFER</option>
                                      <option>REAPPOINTMENT</option>
                                  </select>
                              </div>
                              <div>
                                  <label class="block text-sm font-semibold text-gray-700 mb-3">Civil Service Eligibility
                                      *</label>
                                  <select name="civil_service_eligibility" required class="form-input p-2 w-full rounded-xl text-md">
                                      <option value="">Select Eligibility</option>
                                      <option>CAREER_SERVICE_PROFESSIONAL</option>
                                      <option>CAREER_SERVICE_SUB_PROFESSIONAL</option>
                                      <option>PBET</option>
                                      <option>BARANGAY_ELIGIBILITY</option>
                                      <option>NONE</option>
                                  </select>
                              </div>
                          </div>
                      </div>

                      <div>
                          <h3 class="text-xl font-semibold text-gray-700 mb-6 pb-3 border-b-2 border-blue-100">Additional
                              Information</h3>
                          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                              <div>
                                  <label class="block text-sm font-semibold text-gray-700 mb-3">Appointment Date *</label>
                                  <input name="appointment_date" type="date" required class="form-input p-2 w-full rounded-xl text-md" />
                              </div>
                              <div>
                                  <label class="block text-sm font-semibold text-gray-700 mb-3">Immediate Supervisor
                                      *</label>
                                  <input name="immediate_supervisor" type="text" required class="form-input p-2 w-full rounded-xl text-md"
                                      placeholder="Supervisor's name" />
                              </div>
                          </div>
                          <div class="mt-8">
                              <label class="block text-sm font-semibold text-gray-700 mb-3">Monthly Salary *</label>
                              <input name="monthly_salary" type="number" required class="form-input p-2 w-full md:w-1/2 rounded-xl text-md"
                                  placeholder="0.00" />
                          </div>
                      </div>
                    </li>
                  </ul>
                </form>
            </div>

            <!-- Step 2: Emergency -->
            <div class="form-step form-step-2 bg-white p-10 rounded-3xl shadow-xl border border-gray-100 hidden">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-800 mb-3">Emergency Contact</h2>
                    <p class="text-gray-600 text-lg">Provide contact information for emergencies</p>
                </div>

                <form class="space-y-10 employee-form" id="add-emergencyContact" form-list>
                  <input type="hidden" name="type" value="emergency_contacts" />
                  <ul>
                    <li class="space-y-10">    
                      <input type="hidden" name="id" always-included/>
                      <div>
                          <h3 class="text-xl font-semibold text-gray-700 mb-6 pb-3 border-b-2 border-blue-100">Contact
                              Person</h3>
                          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                              <div>
                                  <label class="block text-sm font-semibold text-gray-700 mb-3">Full Name *</label>
                                  <input type="text" name="fullname" pattern="^[a-zA-Z ]+$" required class="form-input p-2 w-full rounded-xl text-md"
                                      placeholder="Enter full name" />
                              </div>
                              <div>
                                  <label class="block text-sm font-semibold text-gray-700 mb-3">Relationship *</label>
                                  <input type="text" name="relationship" required class="form-input p-2 w-full rounded-xl text-md"
                                      placeholder="e.g., Spouse, Parent, Sibling" />
                              </div>
                          </div>
                      </div>

                      <div>
                          <h3 class="text-xl font-semibold text-gray-700 mb-6 pb-3 border-b-2 border-blue-100">Contact
                              Information</h3>
                          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                              <div>
                                  <label class="block text-sm font-semibold text-gray-700 mb-3">Phone Number *</label>
                                  <input type="tel" name="phone_no" required class="form-input p-2 w-full rounded-xl text-md"
                                      placeholder="+63 XXX XXX XXXX" />
                              </div>
                              <div>
                                  <label class="block text-sm font-semibold text-gray-700 mb-3">Email Address</label>
                                  <input type="email" name="email" class="form-input p-2 w-full rounded-xl text-md"
                                      placeholder="example@email.com" />
                              </div>
                          </div>
                      </div>

                      <div>
                          <h3 class="text-xl font-semibold text-gray-700 mb-6 pb-3 border-b-2 border-blue-100">Address
                          </h3>
                          <div>
                              <label class="block text-sm font-semibold text-gray-700 mb-3">Complete Address *</label>
                              <textarea rows="5" name="address" required class="form-input p-2 w-full rounded-xl text-md resize-none"
                                  placeholder="Enter complete address"></textarea>
                          </div>
                      </div>

                      <div class="bg-blue-50 p-6 rounded-2xl border-2 border-blue-200">
                          <div class="flex items-center">
                              <input type="checkbox" name="is_primary" value="0" id="is_primary"
                                  class="h-6 w-6 text-blue-600 rounded-lg focus:ring-blue-500 border-gray-300" />
                              <label for="is_primary" class="ml-4 text-base font-semibold text-gray-700">
                                  Set as Primary Emergency Contact
                              </label>
                          </div>
                          <p class="text-sm text-gray-600 mt-3">This contact will be reached first in case of emergencies
                          </p>
                      </div>
                    </li>  
                  </ul>
                </form>
            </div>

            <!-- Step 3: Verification -->
            <div class="form-step form-step-3 bg-white p-10 rounded-3xl shadow-xl border border-gray-100 hidden">
              <div class="mb-8">
                  <h2 class="text-3xl font-bold text-gray-800 mb-3">Document Upload</h2>
                  <p class="text-gray-600 text-lg">Upload required documents for verification</p>
              </div>

              <form class="space-y-10" id="add-documents" form-list>
                  <input type="hidden" name="type" value="documents" />
                  <input type="hidden" name="deleted" />
                  <div class="bg-amber-50 p-6 rounded-2xl border-2 border-amber-200">
                      <div class="flex items-start">
                          <svg class="w-6 h-6 text-amber-600 mt-1 mr-4" fill="none" stroke="currentColor"
                              viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                          </svg>
                          <div>
                              <h4 class="font-bold text-amber-800 text-lg">Document Requirements</h4>
                              <p class="text-base text-amber-700 mt-2">
                                  Please ensure all documents are clear, complete, and in PDF or image format (max 5MB
                                  each).
                              </p>
                          </div>
                      </div>
                  </div>

                  <div>
                      <h3 class="text-xl font-semibold text-gray-700 mb-6 pb-3 border-b-2 border-blue-100">
                          Document Upload
                      </h3>
                      <div class="space-y-6">
                          <div>
                              <label class="block text-sm font-semibold text-gray-700 mb-3">Upload Documents *</label>
                              <input type="hidden" id="document-input-src" name="documents-src" required/>
                              <input type="file" id="document-input" name="documents" multiple
                                  class="form-input p-2 w-full rounded-xl text-md" 
                                  accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" />
                              <p class="text-sm text-gray-500 mt-2">You can select multiple files at once.</p>
                          </div>

                          <!-- Preview list -->
                          <ul id="file-list" class="space-y-2"></ul>
                      </div>
                  </div>
              </form>

              <div class="my-8">
                  <h2 class="text-3xl font-bold text-gray-800 mb-3">Biometrics</h2>
                  <p class="text-gray-600 text-lg">Enter details given by the Biometric Device</p>
              </div>

              <form class="space-y-10 mt-10">
                  <input type="hidden" name="type" value="biometrics" />
                  <div>
                      <h3 class="text-xl font-semibold text-gray-700 mb-6 pb-3 border-b-2 border-blue-100">
                          Fingerprint
                      </h3>
                      <div class="space-y-6">
                          <div>
                              <label class="block text-sm font-semibold text-gray-700 mb-3">Biometric ID *</label>
                              <input type="number" name="biometric_id"
                                  class="form-input p-2 w-full rounded-xl text-md" required/>
                              <p class="text-sm text-gray-500 mt-2">Please provide the correct id of the employee after they register in the biometric device.</p>
                          </div>
                      </div>
                  </div>
              </form>
            </div>

            <!-- Step 4: Review -->
            <div class="form-step form-step-4 bg-white p-10 rounded-3md shadow-xl border border-gray-100 hidden"></div>
        </div>

        <!-- Enhanced Navigation Buttons -->
        <div class="flex justify-between items-center mt-12 p-8 bg-white rounded-2xl shadow-xl border border-gray-100">
            <button type="button" class="prevBtn px-10 py-4 rounded-2xl btn-secondary font-bold text-lg" disabled>
                Previous
            </button>

            <div class="text-lg text-gray-600 font-semibold">
                Step <span class="text-blue-600 steps-count">1</span> of <span class="text-gray-800 ">4</span>
            </div>

            <button type="button" class="nextBtn px-10 py-4 rounded-2xl btn-primary text-white font-bold text-lg">
                Next Step
            </button>
        </div>
    </div>
</div>
