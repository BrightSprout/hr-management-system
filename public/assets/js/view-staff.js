(function(global) {
  const tabs = document.querySelectorAll("nav#view-staff-nav button");
  let employeeId = (new URLSearchParams(location.search)).get("employee_id");
  const tabMap = {
    "personal": "Personal Info",
    "attendance": "Attendance Record",
    "documents": "Documents",
    "leave": "Leave Request",
    "reset-password": "New Password",
    "performance": "Performance Logs",
  }
  const cached = {};
  let EmployeeInfoEndpoints = {
    employee: "api/employee?employee_id="+employeeId,
    addresses: "api/employee_addresses?employee_id="+employeeId,
    jobs: "api/employee_jobs?employee_id="+employeeId,
    emergencyContacts: "api/employee_emergency_contacts?employee_id="+employeeId,
    documents: "api/employee_documents?employee_id="+employeeId,
    leaves: "api/list-employee_leaves?employee_id="+employeeId,
  }

  async function cacheOrFetchJSON(url, option) {
    if (cached[url])
      return cached[url];
    if (option?.catchError) {
      try {
        const response = await fetch(url);
        if (response.ok) {
          cached[url] = await response.json();
          return cached[url];
        }
        return null;
      } catch (e) {}
    }
    const response = await fetch(url);
    cached[url] = await response.json();
    return cached[url];
  }

  function getCached(url) {
    if (!cached[url])
      throw new Error(`${url} does not hit`);
    return cached[url];
  }

  function updateCached(url, newValue) {
    if (!cached[url])
      throw new Error(`${url} does not hit`);
    return cached[url] = newValue;
  }

  function memoizeFn(fn) {
    const cache = new Map();
    return (data) => {
      if (cache.has(data)) 
        return cache.get(data);
      const output = fn(data);
      cache.set(data,output);
      return output;
    }
  }

  function formatLocalDate(date) {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, "0");
    const d = String(date.getDate()).padStart(2, "0");
    return `${y}-${m}-${d}`;
  }

  function formatTime(seconds) {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);

    const hour12 = hours % 12 || 12;
    const ampm = hours >= 12 ? "PM" : "AM";

    return `${hour12.toString().padStart(2, "0")}:${minutes
      .toString()
      .padStart(2, "0")} ${ampm}`;
  }

  function getInputDateWithNoTime(date) {
    const newDate = new Date(date);
    newDate.setHours(0,0,0,0);
    return newDate;
  }

  function retrieveInputsChange(inputs) {
    const changes = {}; 
    for (let input of inputs) {
      let defaultValue = input.defaultValue;
      if (input.tagName === "SELECT")
        defaultValue = input.querySelector("option[selected]")?.value;
      if (input.value != defaultValue || input.hasAttribute("always-included")) 
        changes[input.getAttribute("name")] = input.value;
    }
    return changes;
  }

  function gatherFormData(container) { 
    const forms = container.querySelectorAll("form");
    const data = {};
    for (let form of forms)
      data[form.dataset.type] = retrieveInputsChange(form.querySelectorAll("input[name], select[name], textarea[name]"));
    return data;
  }

  async function createNotification({type,message,data}) {
    const response = await fetch("api/notification", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        type,
        message,
        data
      }),
    });
    return await response.json();
  }

  const EmployeeApi = {
    employee: {
      update: async function(data) {
        const {id, ...employee} = data;
        if (employee.dob)
          employee.dob = new Date(employee["dob"]).getTime() / 1000;
        const response = await fetch("api/employee", {
          method: "PATCH",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({id,employee})
        });
        if (!response.ok)
          throw new Error("Employee Update Failed!");
        const json = await response.json();
        if (!json.success)
          throw new Error("Employee Update Failed!");
      }
    },
    addresses: {
      update: async function(data) {
        const response = await fetch("api/employee_addresses", {
          method: "PATCH",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({
            addresses: Object.fromEntries(data.map(address => {
              const { id, ...data } = address;
              return [id, data];
           }))
          })
        });
        if (!response.ok)
          throw new Error("Employee Addresses Creation Failed!");
        const json = await response.json();
        if (!json.success)
          throw new Error("Employee Addresses Update Failed!");
      }
    },
    jobs: {
      update: async function(data) {
        const response = await fetch("api/employee_jobs", {
          method: "PATCH",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({
            jobs: Object.fromEntries(data.map(job => {
              const { id, ...data } = job;
              if (data.appointment_date)
                data.appointment_date = new Date(data["appointment_date"]).getTime() / 1000;
              if (data.department_id) {
                data.department_id = data.department_id;
                data.department = getCached("api/list-departments")[data.department_id].name;
              }
              return [id, data];
           }))
          })
        });
        if (!response.ok)
          throw new Error("Employee Jobs Update Failed!");
        const json = await response.json();
        if (!json.success)
          throw new Error("Employee Jobs Update Failed!");
      }
    },
    emergencyContacts: {
      update: async function(data) {
        const response = await fetch("api/employee_emergency_contacts", {
          method: "PATCH",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({
            emergency_contacts: Object.fromEntries(data.map(emergency_contact => {
              const { id, ...data } = emergency_contact;
              return [id, data];
           }))
          })
        });
        if (!response.ok)
          throw new Error("Employee Emergency Contacts Update Failed!");
        const json = await response.json();
        if (!json.success)
          throw new Error("Employee Emergency Contacts Update Failed!");
      }
    },
  }

  const getRequestLeaveTotalPerStatus = memoizeFn((leaves) => {
    const result = {pending: 0, approved: 0, rejected: 0};
    for (let leave of Object.values(leaves))
      result[leave.status.toLowerCase()]++;
    return result;
  });

  async function updateEmployeeInformation({employee,job,address,emergency_contact}) {
    let isChange = false;
    if (Object.keys(employee).length > 1) {
      await EmployeeApi.employee.update(employee);
      const url = "api/employee?employee_id="+employeeId;
      if (employee.dob)
        employee.dob = new Date(employee["dob"]).getTime() / 1000;
      updateCached(url, {...getCached(url), ...employee});
      isChange = true;
    }
    if (Object.keys(job).length > 1) {
      await EmployeeApi.jobs.update([job]);
      const url = "api/employee_jobs?employee_id="+employeeId;
      const updatedJobs = getCached(url);
      updatedJobs[job.id] = {...updatedJobs[job.id], ...job};
      if (job.appointment_date)
        updatedJobs[job.id].appointment_date = new Date(job.appointment_date).getTime() / 1000;
      updateCached(url, updatedJobs);
      isChange = true;
    }
    if (Object.keys(address).length > 1) {
      await EmployeeApi.addresses.update([address]);
      const url = "api/employee_addresses?employee_id="+employeeId;
      const updatedAddresses = getCached(url);
      updatedAddresses[address.id] = {...updatedAddresses[address.id], ...address};
      updateCached(url, updatedAddresses);
      isChange = true;
    }
    if (Object.keys(emergency_contact).length > 1) {
      await EmployeeApi.emergencyContacts.update([emergency_contact]);
      const url = "api/employee_emergency_contacts?employee_id="+employeeId;
      const updatedEmergencyContacts = getCached(url);
      updatedEmergencyContacts[emergency_contact.id] = {...updatedEmergencyContacts[emergency_contact.id], ...emergency_contact};
      updateCached(url, updatedEmergencyContacts);
      isChange = true;
    }
    
    return isChange;
  }

  function populateModal({employee, addresses, jobs, emergency_contacts}) {
    const currentJob = Object.values(jobs)[0];
    const emergencyContact = Object.values(emergency_contacts)[0];
    const address = Object.values(addresses)[0];
    document.querySelector("#personal-container .modal-container").innerHTML =  `
        <div id="editModal" class="hidden backdrop-blur-md fixed inset-0  bg-opacity-50 flex items-center justify-center p-4" style="z-index: 999;">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl max-h-[95vh] overflow-y-auto" style="position: relative; z-index: 100;">
      
      <!-- Header -->
      <div class="sticky top-0 bg-white border-b border-gray-100 px-8 py-6 rounded-t-3xl">
        <div class="flex justify-between items-center">
          <h2 class="text-2xl font-semibold text-gray-800">Edit Personal Information</h2>
          <button id="closeModalBtn" class="w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Form -->
      <div id="form-container" class="px-8 py-6 space-y-8">
        
        <!-- Basic Information -->
        <form class="space-y-6" data-type="employee">
          <input type="hidden" name="id" value="${employee.id}" always-included/>
          <div class="flex items-center gap-3 mb-6">
            <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
              <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Basic Information</h3>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="space-y-2">
              <label class="text-sm font-medium text-gray-700">First Name</label>
              <input type="text" name="first_name" value="${employee.first_name}" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium text-gray-700">Middle Name</label>
              <input type="text" name="middle_name" value="${employee.middle_name}" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium text-gray-700">Last Name</label>
              <input type="text" name="last_name" value="${employee.last_name}" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
            </div>
            
            <div class="space-y-2">
              <label class="text-sm font-medium text-gray-700">Gender</label>
              <select name="gender" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white">
                ${["Male","Female","Other","Prefer not to say"].map(genderVal => {
                  return `<option value="${genderVal}" ${genderVal == employee.gender ? "selected" : ""}>${genderVal}</option>`; 
                })}
              </select>
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium text-gray-700">Birth Date</label>
              <input type="date" name="dob" value="${typeof employee.dob === "string" ? employee.dob : formatLocalDate(new Date(employee.dob * 1000))}" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
            </div>
            <div class="space-y-2 md:col-span-2 lg:col-span-1">
              <label class="text-sm font-medium text-gray-700">Email Address</label>
              <input type="email" name="email" value="${employee.email}"  class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium text-gray-700">Phone Number</label>
              <input type="text" name="phone_no" value="${employee.phone_no}" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
            </div>
          </div>
        </form>

        <!-- Job Details -->
        <form class="space-y-6" data-type="job">
          <input type="hidden" name="id" value="${currentJob.id}" always-included />
          <div class="flex items-center gap-3 mb-6">
            <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
              <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Job Details</h3>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="space-y-2">
              <label class="text-sm font-medium text-gray-700">Position</label>
              <select name="position" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white">
                ${[
                 "BARANGAY_KAGAWAD","BARANGAY_CAPTAIN",
                 "BARANGAY_SECRETARY","BARANGAY_TREASURER",
                 "SK_CHAIRMAN","BARANGAY_HEALTH_WORKER",
                 "BARANGAY_TANOD","ADMINISTRATIVE_CLERK",
                 "UTILITY_WORKER","DAY_CARE_WORKER"].map(posVal => {
                   return `<option ${posVal === currentJob.position ? "selected" : ""} >${posVal}</option>`;
                })}
              </select>
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium text-gray-700">Department</label>
              <select name="department_id" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white">
                ${Object.values(getCached("api/list-departments")).map(({id,name}) => {
                  return `<option value="${id}" ${currentJob.department_id == id ? "selected" : ""}>${name}</option>`;
                })}
              </select>
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium text-gray-700">Appointment Type</label>
              <select name="appointment_type" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white">
                ${[
                "ORIGINAL","PROMOTION",
                "TRANSFER","REAPPOINTMENT"].map(appTypeVal => {
                  return `<option ${appTypeVal === currentJob.appointment_type ? "selected" : ""} >${appTypeVal}</option>`;
                })}    
              </select>
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium text-gray-700">Civil Service Eligibility</label>
              <select name="civil_service_eligibility" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white">
                ${[
                "CAREER_SERVICE_PROFESSIONAL","CAREER_SERVICE_SUB_PROFESSIONAL",
                "PBET","BARANGAY_ELIGIBILITY",
                "NONE"].map(civServElVal => {
                  return `<option ${civServElVal === currentJob.civil_service_eligibility ? "selected" : ""} >${civServElVal}</option>`;
                })}
              </select>
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium text-gray-700">Appointment Date</label>
              <input type="date" name="appointment_date" value="${typeof currentJob.appointment_date === "string" ? currentJob.appointment_date : formatLocalDate(new Date(currentJob.appointment_date * 1000))}" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium text-gray-700">Immediate Supervisor</label>
              <input type="text" name="immediate_supervisor" value="${currentJob.immediate_supervisor}" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
            </div>
            <div class="space-y-2 md:col-span-2 lg:col-span-3">
              <label class="text-sm font-medium text-gray-700">Monthly Salary</label>
              <input type="text" name="monthly_salary" value="${currentJob.monthly_salary}" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
            </div>
          </div>
        </form>

        <!-- Address -->
        <form class="space-y-6" data-type="address">
          <input type="hidden" name="id" value="${address.id}" always-included />
          <div class="flex items-center gap-3 mb-6">
            <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
              <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Address</h3>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"> 
            <div class="space-y-2">
              <label class="text-sm font-medium text-gray-700">Street</label>
              <input type="text" name="street_name" value="${address.street_name}" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium text-gray-700">Barangay</label>
              <input type="text" name="barangay" value="${address.barangay}" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium text-gray-700">City</label>
              <input type="text" name="city" value="${address.city}" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium text-gray-700">Province</label>
              <input type="text" name="province" value="${address.province}" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium text-gray-700">Zip Code</label>
              <input type="text" name="zipcode" value="${address.zipcode}" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
            </div>
          </div>
        </form>

        <!-- Emergency Contact -->
        <form class="space-y-6" data-type="emergency_contact">
          <input type="hidden" name="id" value="${emergencyContact.id}" always-included/>
          <div class="flex items-center gap-3 mb-6">
            <div class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center">
              <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Emergency Contact</h3>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
              <label class="text-sm font-medium text-gray-700">Name</label>
              <input type="text" name="fullname" value="${emergencyContact.fullname}" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all">
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium text-gray-700">Relationship</label>
              <input type="text" name="relationship" value="${emergencyContact.relationship}"  class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all">
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium text-gray-700">Phone Number</label>
              <input type="text" name="phone_no" value="${emergencyContact.phone_no}"  class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all">
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium text-gray-700">Email</label>
              <input type="email" name="email" value="${emergencyContact.email}" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all">
            </div>
          </div>
        </form>

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
      `
    ;

    const modal = document.getElementById("editModal");
    const openModalBtn = document.getElementById("openModalBtn");
    const closeModalBtn = document.getElementById("closeModalBtn");
    const cancelBtn = document.getElementById("cancelBtn");
    const saveBtn = document.getElementById("saveBtn");

    function openModal() {
      modal.classList.remove("hidden");
      modal.style.display = "flex";
      document.body.style.overflow = "hidden";
    }

    if ((new URLSearchParams(location.search)).has("edit_employee"))
      openModal();

    openModalBtn.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      openModal(); 
    });


    const closeModal = () => {
      modal.classList.add("hidden");
      modal.style.display = "none";
      document.body.style.overflow = "auto"; 

    };

    closeModalBtn.addEventListener("click", closeModal);
    cancelBtn.addEventListener("click", closeModal);


    modal.addEventListener("click", (e) => {
      if (e.target === modal) {

        closeModal();
      }
    });


    saveBtn.addEventListener("click", async (e) => {
      const data = gatherFormData(document.querySelector("#form-container"));
      try {
        const isChange = await updateEmployeeInformation(data);
        if (isChange) {
          await Swal.fire({
            icon: "success",
            title: "Information Updated!",
            text: "Update for employee data is written...",
            timer: 1500,
          });
          updatePersonalContainer({
            employee: getCached("api/employee?employee_id="+employeeId),
            addresses: getCached("api/employee_addresses?employee_id="+employeeId),
            jobs: getCached("api/employee_jobs?employee_id="+employeeId), 
            emergency_contacts: getCached("api/employee_emergency_contacts?employee_id="+employeeId),
          });
          //open the modal after update.
          const modal = document.getElementById("editModal");
          modal.classList.remove("hidden");
          modal.style.display = "flex";
          await createNotification({
            type: "employee-update",
            message: `Updated Employee Information!`,
            data
          });
        }
      } catch (e) {
        console.log(e);
        Swal.fire({
          icon: "error",
          title: "Update Failed",
          text: "Failed to update employee information...",
        });
      }
    });
  }

  function updatePersonalContainer({employee,addresses,jobs,emergency_contacts,performance_score}) {
    const currentJob = Object.values(jobs)[0];
    const ProductivityColor = {
      "Very Low": "red",
      "Low": "orange",
      "Medium": "yellow",
      "High": "green"
    };
    const productivityLevel = performance_score < 25 ? "Very Low" : performance_score < 50 ? "Low" : performance_score < 75 ? "Medium" : "High";
    const productivityColor = ProductivityColor[productivityLevel];
    document.querySelector("#personal-container").innerHTML = `
      <div class="flex items-center justify-between mb-6">
        <div class="header">
          <h1 class="text-2xl font-semibold text-gray-900">Personal info</h1> 
        </div>
        <div>
          <button
            id="openModalBtn" 
            class="${document.body.dataset.userRole == "STAFF" ? "hidden":""} px-4 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 hover:shadow-lg cursor-pointer transition font-medium">
            Edit Info
          </button>
        </div>
      </div>

      <!-- Basic Information --> 
      <div class="bg-white rounded-lg border border-gray-200 mb-6 shadow-lg"> <div class="flex items-center justify-between px-6    py-4 border-b border-gray-200 custom-gradient-bg"> 
          <h2 class="text-lg font-medium text-white">Basic information</h2> 
      </div>
        <div class="p-6">
            <div class="flex space-x-8">
                <div class="flex-shrink-0">
                    <div class="relative group">
                        <label
                            class="absolute bottom-0 right-0 bg-blue-600 hover:bg-blue-700 text-white text-xs px-2 py-1 rounded-full shadow cursor-pointer transition-opacity opacity-0 group-hover:opacity-100"
                            for="uploadProfileImage">
                            Upload
                        </label>
                        <input id="uploadProfileImage" type="file" accept="image/*" class="hidden">
                    </div>
                </div>
                <div class="flex-1 grid grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">${employee.first_name} ${employee.middle_name} ${employee.last_name}</h3>
                        <p class="text-gray-600" id="position">${currentJob.position}</p>
                        <div class="flex items-center space-x-2 mt-2">
                            <i data-lucide="Club" class="w-4 h-4 text-gray-900"></i>
                            <span class="text-sm text-gray-600">${employee.gender}</span>
                        </div>
                        <div class="flex items-center space-x-2 mt-1">
                            <i data-lucide="mail" class="w-4 h-4 text-gray-400"></i>
                            <span class="text-sm text-gray-600">${employee.email}</span>
                        </div>
                        <div class="flex items-center space-x-2 mt-1">
                            <i data-lucide="phone" class="w-4 h-4 text-gray-400"></i>
                            <span class="text-sm text-gray-600">${employee.phone_no}</span>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Birth date</label>
                            <p class="text-gray-900">${new Date(employee.dob * 1000).toDateString()}</p>
                        </div>
                         <div>
                            <label class="text-sm font-medium text-gray-700">Biometric ID</label>
                            <p class="text-gray-900">${employee.biometric_id}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Productivity</label>
                            <p class="text-gray-900 text-${productivityColor}-500">${productivityLevel}</p>
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
          
       </div>
        ${Object.values(jobs).map(job => {
          return `
            <div class="p-6 grid grid-cols-2 gap-6">
                <div>
                    <label class="text-sm font-medium text-gray-700">Position</label>
                    <p class="text-gray-900">${job.position}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Department</label>
                    <p class="text-gray-900">${job.department}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Appointment Type</label>
                    <p class="text-gray-900">${job.appointment_type}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Civil Service Eligibility</label>
                    <p class="text-gray-900">${job.civil_service_eligibility}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Appointment Date</label>
                    <p class="text-gray-900">${new Date(job.appointment_date * 1000).toDateString()}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Immediate Supervisor</label>
                    <p class="text-gray-900">${job.immediate_supervisor}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Monthly Salary</label>
                    <p class="text-gray-900">₱${job.monthly_salary.toLocaleString("en-US")}</p>
                </div>
            </div>
          `
        })}
        
    </div>

    <!-- Addresses -->
    <div class="bg-white rounded-lg border border-gray-200 mb-6 shadow-lg">
        <div class="flex items-center justify-between px-6 py-4 text-white border-b border-gray-200 custom-gradient-bg">
            <h2 class=" text-lg font-medium text-white">Addresses</h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        ${Object.values(addresses).map(address => {
          return `
            <div>
              <label class="text-sm font-medium text-gray-700">${address.type[0] + address.type.slice(1).toLowerCase()} Address</label>
              <p class="text-gray-900">123 Main St, Cabuyao, Laguna${address.street_name}, ${address.barangay}, ${address.city},  ${address.province}</p>
            </div>
          `;
        })}
        </div>   
        
    </div>

    <!-- Emergency Contacts -->
    <div class="bg-white rounded-lg border border-gray-200 mb-6 shadow-lg">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 custom-gradient-bg">
            <h2 class="text-lg font-medium text-white">Emergency Contacts</h2>
        </div>
        <div class="p-6 space-y-6">
          ${Object.values(emergency_contacts).map(emergency_contact => {
            return `
              <div>
                  <p class="font-medium text-gray-900">${emergency_contact.fullname}
                  ${emergency_contact.is_primary ? `<span class="text-xs bg-blue-100 text-blue-600 px-2 py-0.5 rounded">Primary</span></p>`: ""}
                  <p class="text-gray-600">${emergency_contact.relationship}</p>
                  <p class="text-gray-600">${emergency_contact.phone_no}</p>
                  <p class="text-gray-600">${emergency_contact.email}</p>
              </div>
            `;
          })}
        </div>
    </div>
    <div class="modal-container"></div>
    `;

    populateModal({employee, addresses, jobs, emergency_contacts});

    lucide.createIcons();
  }

  function displayAttendanceLists({attendances,department,currentJob,leaves,startDate,endDate = new Date()}) {
    const StatusColors = {
      "PRESENT": "green",
      "LEAVE": "blue",
      "ABSENT": "red",
      "LATE": "yellow",
    };
    const appointmentDate = new Date(currentJob.appointment_date * 1000);
    if ((startDate && startDate.getTime() < appointmentDate.getTime()) || !startDate)
      startDate = appointmentDate;
    endDate.setDate(endDate.getDate() + 1); // we end the loop until the next day after the end date to include the end date. 
    let attendancesHTMLs = ``;

    let startLocalDate;
    while ((startLocalDate = formatLocalDate(startDate)) !== formatLocalDate(endDate)) {
       if (startDate.getTime() > endDate.getTime())
         break;
       if (department.dayoffs.includes(startDate.getDay())) {
         startDate.setDate(startDate.getDate() + 1);  
         continue;
       }
       let clock_in = `--`; 
       let clock_out = `--`;
       let status = "ABSENT";
       const attendance = attendances[startLocalDate];
       if (leaves.includes(getInputDateWithNoTime(startDate.getTime()).getTime()/1000))
         status = "LEAVE";
       else if (attendance) {
         clock_in = formatTime(attendance.clock_in);
         clock_out = attendance.clock_out ? formatTime(attendance.clock_out) : "NULL";
         if (attendance.clock_in > department.clock_in) // it means that employee clocked in after the appointed department clock in time 
           status = "LATE";
         else 
           status = "PRESENT";
       }
       const statusColor = StatusColors[status];
       attendancesHTMLs += `
        <tr>
          <td class="px-6 py-4 text-gray-900">${startLocalDate}</td>
          <td class="px-6 py-4 text-gray-600">${clock_in}</td>
          <td class="px-6 py-4 text-gray-600">${clock_out}</td>
          <td class="px-6 py-4">
              <span class="px-2 py-1 text-xs rounded-full bg-${statusColor}-100 text-${statusColor}-600">${status[0] + status.slice(1).toLowerCase()}</span>
          </td>
        </tr>
      `; 
      startDate.setDate(startDate.getDate() + 1);  
    }
    document.querySelector("#attendances").innerHTML = attendancesHTMLs; 
  }

  function displayLeaveLists({leaves}) {
    const StatusColors = {
      "PENDING": "yellow",
      "APPROVED": "green",
      "REJECTED": "red",
    };
    document.querySelector("#leaves").innerHTML = Object.values(leaves).reverse().map(({type,reason,start_date,end_date,status}) => {
      const statusColor = StatusColors[status];
      return `
        <tr>
          <td class="px-6 py-4 text-gray-900">${type[0] + type.slice(1).toLowerCase()} Leave</td>
          <td class="px-6 py-4 text-gray-600">${formatLocalDate(new Date(start_date * 1000))} → ${formatLocalDate(new Date(end_date * 1000))}</td>
          <td class="px-6 py-4 text-gray-600">${reason}</td>
          <td class="px-6 py-4">
            <span class="px-2 py-1 text-xs rounded-full bg-${statusColor}-100 text-${statusColor}-600">${status[0] + status.slice(1).toLowerCase()}</span>
          </td>
        </tr>
      `;
    });
  }

  function displayDocuments({documents}) {
    document.querySelector("#documents").innerHTML = Object.values(documents).map(doc => {
      return `
        <div
          class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-300 p-2 hover:shadow-lg transition">
          <a href="${doc.url}">
            <img src="${doc.url}" alt="${doc.id}"
              class="h-80 w-full object-contain bg-gray-100 rounded-lg" />
          </a>
        </div>
      `;
    }).join(" ");
  }
  
  function updateTab(page) {
    for (let btn of tabs) {
      const href = btn.dataset.navfor;
      if ((new URLSearchParams(href)).get("page") !== page) {
        btn.classList.remove("border-b-2", "border-blue-500", "text-blue-600");
        btn.classList.add("text-gray-500");
      } else {
        btn.classList.add("border-b-2", "border-blue-500", "text-blue-600");
        btn.classList.remove("text-gray-500");    
      }
    }
  }

  function getTotalAttendancePerDay({employee, department, attendances, leaves}) {
    const absentsPerDay = [0,0,0,0,0,0,0]; // included the weekends
    const presentsPerDay = [0,0,0,0,0,0,0]; 
    const leavesPerDay = [0,0,0,0,0,0,0]; 
    const allAWOL = {};

    function getAWOLType(totalAbsent) {
      return totalAbsent > 6 ? "awol" :
        totalAbsent > 2 ? "investigate" : "unexcused"; 
    }

    const employeeAWOL = {
      awol: 0,
      investigate: 0,
      unexcused: 0,
    };

    const startDate = new Date(employee.jobs.appointment_date * 1000);
    const endDate = new Date();
    endDate.setDate(endDate.getDate() + 1); // we end the loop until the next day after the end date to include the end date.
    let startLocalDate;
    let absentCount = 0;
    while ((startLocalDate = formatLocalDate(startDate)) !== formatLocalDate(endDate)) {
       if (startDate.getTime() > endDate.getTime())
         break;
       if (department.dayoffs.includes(startDate.getDay())) {
         startDate.setDate(startDate.getDate() + 1);  
         continue;
       }
       const attendance = attendances?.[startLocalDate];
       const onLeave = leaves.includes(getInputDateWithNoTime(startDate.getTime()).getTime()/1000);
       const dayOfTheWeek = startDate.getDay();
       if (onLeave)
         leavesPerDay[dayOfTheWeek]++;  
       else if (attendance)
         presentsPerDay[dayOfTheWeek]++;
       else
         absentsPerDay[dayOfTheWeek]++;

       (onLeave || attendance) ? (absentCount > 0 && employeeAWOL[getAWOLType(absentCount)]++,absentCount=0) : absentCount++;
      startDate.setDate(startDate.getDate() + 1);  
    }
    if (absentCount > 0)
      employeeAWOL[getAWOLType(absentCount)]++;
    allAWOL[employee.id] = employeeAWOL;

    return {
      absentsPerDay,
      presentsPerDay,
      leavesPerDay,
      allAWOL,
    };
  }

  function getMonthlyLeaves({leaves}) {
    const monthlyLeaves = [0,0,0,0,0,0,0,0,0,0,0,0];
    for (let leave of Object.values(leaves)) {
      if (leave.status != "APPROVED")
        continue;
      if (leave.start_date > leave.end_date) // for preventing errors
        continue;
      const startDate = new Date(leave.start_date * 1000);
      startDate.setDate(1); // preventing day overflow with february, if the date in start date is 31.
      const endDate = new Date(leave.end_date * 1000);
      while(startDate.getMonth() !== endDate.getMonth() || startDate.getFullYear() !== endDate.getFullYear()) {
        monthlyLeaves[startDate.getMonth()]++;
        startDate.setMonth(startDate.getMonth() + 1);
      }
      monthlyLeaves[endDate.getMonth()]++; // include the end date month
    }
    return monthlyLeaves;
  }

  function displayAbsentsPerDay(absents) {
    const attendanceCtx = document.getElementById('attendanceChart');
    new Chart(attendanceCtx, {
      type: 'bar',
      data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', "Sat", "Sun"],
        datasets: [{
          label: 'Most No. of Absent',
          data: absents,
          backgroundColor: '#3b82f6'
        }]
      },
      options: { responsive: true, plugins: { legend: { display: false } } }
    });   
  }

  function displayMonthlyLeaves(monthlyLeaves) {
    const leaveCtx = document.getElementById('leaveChart');
      new Chart(leaveCtx, {
        type: 'line',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', "June", "July", "Aug", "Sept", "Oct","Nov", "Dec"],
          datasets: [{
            label: 'Leaves Taken',
            data: monthlyLeaves,
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.2)',
            fill: true
          }]
        },
        options: { responsive: true }
      });
  }

  function displayPerformanceScore(performanceScore) {
    const performanceCtx = document.getElementById('performanceChart');
      new Chart(performanceCtx, {
          type: 'doughnut',
          data: {
              labels: ['Present %', 'Absent %'],
              datasets: [{
                  data: [performanceScore.toFixed(2), (100 - performanceScore).toFixed(2)],
                  backgroundColor: ['#f59e0b', '#e5e7eb']
              }]
          },
          options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
      });
  }

  function displayEmployeeLeaves({leaveRequestsTotalPerStatus}) {
    document.querySelector("#pending-leave-total").textContent = leaveRequestsTotalPerStatus.pending;
    document.querySelector("#approved-leave-total").textContent = leaveRequestsTotalPerStatus.approved;
    document.querySelector("#rejected-leave-total").textContent = leaveRequestsTotalPerStatus.rejected;
  }

  function displayAWOLs({awolsTotalPerType}) {
    document.querySelector("#awol-total").innerHTML = awolsTotalPerType.awol; 
    document.querySelector("#unexcused-absence-total").innerHTML = awolsTotalPerType.unexcused; 
    document.querySelector("#investigate-absence-total").innerHTML = awolsTotalPerType.investigate; 
  }

  function displayLeaveRecords({employee, leaves}) {
    const StatusColor = {PENDING: "yellow", APPROVED: "green", REJECTED: "red"};
    document.querySelector("#display-leave-records").innerHTML = Object.values(leaves).map(leave => {
      const  {first_name,middle_name,last_name} = employee;
      const statusColor = StatusColor[leave.status];
      return `
        <tr class="hover:bg-gray-50">
          <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">${first_name} ${middle_name} ${last_name}</td>
          <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">${formatLocalDate(new Date(leave.start_date * 1000))}</td>
          <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">${formatLocalDate(new Date(leave.end_date * 1000))}</td>
          <td class="px-4 py-3 text-sm text-gray-500">${leave.reason}</td>
          <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">${leave.type[0] + leave.type.slice(1).toLowerCase()} Leave</td>
          <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">Secretary</td>
          <td class="px-4 py-3 whitespace-nowrap">
            <span
              class="px-2 py-1 text-xs font-semibold rounded-full bg-${statusColor}-100 text-${statusColor}-800">${leave.status[0] + leave.status.slice(1).toLowerCase()}</span>
          </td>
        </tr>`;
    }).join("\n");
  }

  async function loadViewStaffPage(page) {
    const [,sub] = page.split("view-staff/");

    let employee,currentJob,attendances,leaves,leaveDates,department, absentsPerDay,leavesPerDay, presentsPerDay;

    async function getEmployeeData() {
      const employee = await cacheOrFetchJSON(EmployeeInfoEndpoints.employee);
      const jobs = await cacheOrFetchJSON(EmployeeInfoEndpoints.jobs);
      const currentJob = Object.values(jobs)[0];
      const attendances = await cacheOrFetchJSON(document.body.dataset.userRole == "STAFF" ? "api/my-attendances" : "api/list-employee_attendance?biometric_id="+employee.biometric_id, {catchError:true}) ?? {};
      const leaves = await cacheOrFetchJSON(EmployeeInfoEndpoints.leaves);
      const leaveDates = [...new Set(Object.values(leaves).filter(leave => leave.status === "APPROVED").map(leave => {
        const dates = [];
        const endDate = getInputDateWithNoTime(leave.end_date*1000).getTime()/1000;
        // add a day from start_date up to end_date
        for (let i = getInputDateWithNoTime(leave.start_date * 1000).getTime()/1000; i < endDate; i+=86400)
          dates.push(i);
        dates.push(endDate);
        return dates;
      }).flat())];
      const department = getCached("api/list-departments")[currentJob.department_id]; 
      return {employee,jobs,currentJob,leaves,leaveDates,attendances,department};
    }

    function computePerformanceScore(presents, absents, leaves) {
      const totalPresents = presents.reduce((a,b) => a+b);
      const totalWorkingDays = totalPresents + absents.reduce((a,b) => a+b) + leaves.reduce((a,b) => a+b);
      return performanceScore =  totalWorkingDays > 0 ? totalPresents / totalWorkingDays * 100 : 0;
    }
    
    switch (sub) {
      case "personal": 
        ({employee,department,attendances,leaves,leaveDates} = await getEmployeeData());
        ({absentsPerDay, leavesPerDay, presentsPerDay} = getTotalAttendancePerDay({
          employee,
          department,
          attendances,
          leaves: leaveDates,
        }));
        updatePersonalContainer({
          employee: await cacheOrFetchJSON(EmployeeInfoEndpoints.employee),
          addresses: await cacheOrFetchJSON(EmployeeInfoEndpoints.addresses),
          jobs:  await cacheOrFetchJSON(EmployeeInfoEndpoints.jobs),
          emergency_contacts: await cacheOrFetchJSON(EmployeeInfoEndpoints.emergencyContacts),
          performance_score: computePerformanceScore(presentsPerDay,absentsPerDay,leavesPerDay),
        }); 
        break;
      case "attendance":
        ({employee,jobs,currentJob,department,attendances,leaves,leaveDates,department} = await getEmployeeData());
        const lastMonth = new Date();
        lastMonth.setMonth(lastMonth.getMonth() - 1,1);
        displayAttendanceLists({
          attendances: Object.fromEntries(Object.values(attendances).map(attendance => ([attendance.attended_date,attendance]))),
          currentJob,
          department,
          startDate: new Date(lastMonth.getTime()),
          leaves: leaveDates,
        });
        document.querySelector("#filter-date-form input[name='start_date']").value = lastMonth.toISOString().split("T")[0];
        document.querySelector("#filter-date-form").addEventListener("submit", function(e) {
          e.preventDefault();
          const formData = new FormData(this);
          displayAttendanceLists({
            attendances: Object.fromEntries(Object.values(attendances).map(attendance => ([attendance.attended_date,attendance]))),
            department: getCached("api/list-departments")[currentJob.department_id],
            currentJob,
            startDate: !formData.get("start_date") ? new Date(currentJob.appointment_date * 1000): new Date(formData.get("start_date")),
            endDate: !formData.get("end_date") ? new Date(): new Date(formData.get("end_date")),
            leaves: leaveDates, 
          });
        });
        break;
      case "documents":
        const documents = await cacheOrFetchJSON(EmployeeInfoEndpoints.documents);
        displayDocuments({documents});
        break;
      case "leave":
        const employeeLeaveURL = EmployeeInfoEndpoints.leaves;
        displayLeaveLists({
          leaves: await cacheOrFetchJSON(employeeLeaveURL),
        });
        document.querySelector("#create-leave-form").addEventListener("submit", async function(e) {
          e.preventDefault(); 
          const formData = new FormData(this);
          const startDate = new Date(formData.get("start_date")).getTime(); 
          const endDate = new Date(formData.get("end_date")).getTime(); 
          if (startDate > endDate) 
            return Swal.fire({
              icon: "error",
              title: "Invalid Dates",
              text: "Start Date must be smaller than End Date!",
            });

          const response = await fetch("api/my_leave", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify({
              type: formData.get("type"),
              reason: formData.get("reason"),
              start_date: startDate / 1000,
              end_date: endDate / 1000,
            })
          });
          if (response.ok) { 
            const leaves = await cacheOrFetchJSON(employeeLeaveURL);
            const newLeave = await response.json();
            updateCached(employeeLeaveURL, {...leaves, [newLeave.id]: {...newLeave, created_at: ((new Date()).getTime() / 1000)}});
            Swal.fire({
              icon: "success",
              title: "Leave Application Created!",
              text: "Leave application is now waiting for approval...",
            });
            displayLeaveLists({
              leaves: await cacheOrFetchJSON(employeeLeaveURL),
            });
            await createNotification({
              type: "leave-creation",
              message: `Employee Leave Created`, 
              data: {leave_id: newLeave.id}, 
            });
          } else 
            Swal.fire({
              icon: "error",
              title: "Leave Creation Failed!",
              text: "Something went wrong...",
            });
        });
        break;
      case "reset-password":
        const employeeData = await cacheOrFetchJSON(EmployeeInfoEndpoints.employee);
        document.querySelector("input[name='username']").value = employeeData.user.username;
        document.querySelector("input[name='id']").value = employeeData.user.id;
        break;
      case "performance": 
        ({employee,department,attendances,leaves,leaveDates} = await getEmployeeData());
        ({absentsPerDay, leavesPerDay, presentsPerDay, allAWOL} = getTotalAttendancePerDay({
          employee,
          department,
          attendances,
          leaves: leaveDates,
        }));
        const allAWOLTotal = Object.values(allAWOL).reduce((all, next) => {
          all.awol += next.awol;
          all.investigate += next.investigate;
          all.unexcused += next.unexcused;
          return all;
        }, {awol: 0,investigate: 0, unexcused: 0});
        displayAbsentsPerDay(absentsPerDay);
        const monthlyLeaves = getMonthlyLeaves({leaves});
        displayMonthlyLeaves(monthlyLeaves);
        displayPerformanceScore(computePerformanceScore(presentsPerDay,absentsPerDay,leavesPerDay));
        displayEmployeeLeaves({leaveRequestsTotalPerStatus: getRequestLeaveTotalPerStatus(leaves)});
        displayAWOLs({awolsTotalPerType: allAWOLTotal});
        displayLeaveRecords({employee, leaves});
        const today = new Date();
        document.querySelector("#today-date-txt").textContent = ["January","February","March","April","May","June","July","August","September","October","November","December"][today.getMonth()] + " " + today.getFullYear();
        break;
    }

    document.querySelector("#breadcrumb-label").textContent = tabMap[sub];
  }
  
  for (let btn of tabs) {
    const href = btn.dataset.navfor;
    btn.addEventListener("click", async () => {
      const page = (new URLSearchParams(href)).get("page");
      await navigatePage(href + (document.body.dataset.userRole != "STAFF" ? "&employee_id="+employeeId : ""))
      updateTab(page);
      await loadViewStaffPage(page);
    });
  }

  async function init() {
    if (document.body.dataset.userRole == "STAFF") {
      EmployeeInfoEndpoints = {
        employee: "api/my-employee-info",
        addresses: "api/my-addresses",
        jobs: "api/my-jobs",
        emergencyContacts: "api/my-emergency-contacts",
        documents: "api/my-documents",
        leaves: "api/my-leaves",
      };
      const response = await cacheOrFetchJSON(EmployeeInfoEndpoints.employee);
      employeeId = response.ok && (await response.json()).id;
      document.querySelector("button[data-navfor='?page=view-staff/leave']").classList.remove("hidden");
      document.querySelector("#staff-logout").classList.remove("hidden");
    } else if (document.body.dataset.userRole == "GMAC") {
      document.querySelector("button[data-navfor='?page=view-staff/reset-password']").classList.remove("hidden");
      document.querySelector("button[data-navfor='?page=view-staff/performance']").classList.remove("hidden");
    }
    await cacheOrFetchJSON("api/list-departments");
    const page = (new URLSearchParams(location.search)).get("page")  ?? "view-staff/personal";
    await loadViewStaffPage(page);
    updateTab(page);
  }
  
  init();

})(window);
