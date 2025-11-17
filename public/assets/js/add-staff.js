(function (global) {
    const nextBtn = document.querySelector('.nextBtn');
    const prevBtn = document.querySelector('.prevBtn');
    const formSteps = document.querySelectorAll('.form-step');
    const stepIndicators = document.querySelectorAll('.step-indicator');
    let currentStep = 0;
    const progressBars = document.querySelectorAll('[class*="progressbar-step-"]');
    const departments = {};    

    async function fetchJSON(url) {
      const response = await fetch(url);
      return await response.json();
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

    async function imageHashBase64URL(arrayBuffer) {
			const hashBuffer = await crypto.subtle.digest("SHA-256", arrayBuffer);
			const base64 = btoa(String.fromCharCode(...new Uint8Array(hashBuffer)));
			return base64.replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
		}

    function unixToInputDate(unix) {
      const date = new Date(unix * 1000);
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");
      return `${year}-${month}-${day}`;
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

    function convertListElToData(listEl) {
      const datas = [];
      for (let item of listEl)
        datas.push(retrieveInputsChange(item.querySelectorAll("input[name], select[name], textarea[name]")));
      return datas;
    }

    function gatherFormData(formStep) { 
      const forms = formStep.querySelectorAll("form");
      const data = {};
      for (let form of forms) {
        const type = form.querySelector(":scope > input[type='hidden'][name='type']").value;
        if (form.hasAttribute("form-list")) {
          const deleted = form.querySelector(":scope > input[type='hidden'][name='deleted']")?.value;
          data[type] = {
            data: convertListElToData(form.querySelectorAll("ul > li")),
            delete: deleted?.trim() ? deleted.split(",") : null
          };
          continue;
        }
        data[type] = retrieveInputsChange(form.querySelectorAll("input[name], select[name], textarea[name]"));
      }
      return data;
    }

    function createFilteredEmptyFormData(form) {
      const formData = new FormData(form);
      for (const [key, value] of formData.entries())
        if (value.trim() === "")
          formData.delete(key);
      return formData;
    }

    const EmployeeApi = {
      employee: {
        create: async function(data) {
          const response = await fetch("api/employee", {
            method: "POST",
            headers: {
              "Content-Type": "application/json"
            },
            body: JSON.stringify({
              first_name: data["first_name"],  
              middle_name: data["middle_name"],
              last_name: data["last_name"],
              email: data["email"],
              phone_no: data["phone_no"],
              dob: new Date(data["dob"]).getTime() / 1000,
              gender: data["gender"],
            })
          });
          if (!response.ok)
            throw new Error("Employee Creation Failed!");
          return await response.json();
        },
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
        },
        complete: async function(data) {
          const response = await fetch("api/employee_registered", {
            method: "PATCH",
            headers: {
              "Content-Type": "application/json"
            },
            body: JSON.stringify({employee_id: data.employee_id})
          });
          if (!response.ok)
            throw new Error("Employee Registration Failed!");
          return await response.json();
        }, 
      },
      addresses: {
        create: async function(data) {
          const response = await fetch("api/employee_addresses", {
            method: "POST",
            headers: {
              "Content-Type": "application/json"
            },
            body: JSON.stringify(data.addresses.map(address => ({employee_id: data.employeeId,...address})))
          });
          if (!response.ok)
            throw new Error("Employee Addresses Creation Failed!");
          return await response.json();
        },
        update: async function(data) {
          const response = await fetch("api/employee_addresses", {
            method: "PATCH",
            headers: {
              "Content-Type": "application/json"
            },
            body: JSON.stringify({
              addresses: Object.fromEntries(data.addresses.map(address => {
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
        create: async function(data) {
          const response = await fetch("api/employee_jobs", {
            method: "POST",
            headers: {
              "Content-Type": "application/json"
            },
            body: JSON.stringify(data.jobs.map(job => ({
              employee_id: data.employeeId,
              ...job,
              appointment_date: new Date(job["appointment_date"]).getTime() / 1000,
              department: departments[job.department].name,
              department_id: job.department
            })))
          });
          if (!response.ok)
            throw new Error("Employee Jobs Creation Failed!");
          return await response.json();
        },
        update: async function(data) {
          const response = await fetch("api/employee_jobs", {
            method: "PATCH",
            headers: {
              "Content-Type": "application/json"
            },
            body: JSON.stringify({
              jobs: Object.fromEntries(data.jobs.map(job => {
                const { id, ...data } = job;
                if (data.appointment_date)
                  data.appointment_date = new Date(data["appointment_date"]).getTime() / 1000;
                if (data.department) {
                  data.department_id = data.department;
                  data.department = departments[data.department].name;
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
        create: async function(data) {
          const response = await fetch("api/employee_emergency_contacts", {
            method: "POST",
            headers: {
              "Content-Type": "application/json"
            },
            body: JSON.stringify(data.emergency_contacts.map(emergency_contacts => ({employee_id: data.employeeId,...emergency_contacts})))
          });
          if (!response.ok)
            throw new Error("Employee Emergency Contacts Creation Failed!");
          return await response.json();
        },
        update: async function(data) {
          const response = await fetch("api/employee_emergency_contacts", {
            method: "PATCH",
            headers: {
              "Content-Type": "application/json"
            },
            body: JSON.stringify({
              emergency_contacts: Object.fromEntries(data.emergency_contacts.map(emergency_contact => {
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
      documents: {
        create: async function(data) {
          const response = await fetch("api/employee_documents", {
            method: "POST",
            headers: {
              "Content-Type": "application/json"
            },
            body: JSON.stringify(data.documents.map(doc => ({employee_id: data.employeeId,...doc})))
          });
          if (!response.ok)
            throw new Error("Employee Documents Creation Failed!");
          return await response.json();
        },
        update: async function(data) {
          const response = await fetch("api/employee_documents", {
            method: "PATCH",
            headers: {
              "Content-Type": "application/json"
            },
            body: JSON.stringify({
              documents: data.documents ? Object.fromEntries(data.documents.map(doc => {
                const { id, ...data } = doc;
                return [id, data];
             })) : {},
              delete: data.delete ?? []
            })
          });
          if (!response.ok)
            throw new Error("Employee Documents Update Failed!");
          const json = await response.json();
          if (!json.success)
            throw new Error("Employee Documents Update Failed!");
        }
      },
    }

    function updatePersonalInfoForm(employeeData) {
      const formStep = document.querySelector(".form-step-1"); 
      const personalInfoForm = formStep.querySelector("form");;
      personalInfoForm.innerHTML = `
        <input type="hidden" name="type" value="employee"/>
          <div>
              <h3 class="text-xl font-semibold text-gray-700 mb-6 pb-3 border-b-2 border-blue-100">Full Name
              </h3>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <input type="hidden" name="id" always-included value="${employeeData.id}"/>
                  <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-3">First Name *</label>
                      <input type="text" name="first_name" pattern="^[a-zA-Z ]+$" required class="form-input p-2 w-full rounded-xl text-md"
                          placeholder="Enter first name" value="${employeeData.first_name}" />
                  </div>
                  <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-3">Middle Name</label>
                      <input type="text" name="middle_name" pattern="^[a-zA-Z ]+$" class="form-input p-2 w-full rounded-xl text-md"
                          placeholder="Enter middle name" value="${employeeData.middle_name}"/>
                  </div>
                  <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-3">Last Name *</label>
                      <input type="text" name="last_name" pattern="^[a-zA-Z ]+$" required class="form-input p-2 w-full rounded-xl text-md"
                          placeholder="Enter last name" value="${employeeData.last_name}"/>
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
                          placeholder="example@email.com" value="${employeeData.email}" />
                  </div>
                  <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-3">Phone Number *</label>
                      <input type="tel" name="phone_no" required class="form-input p-2 w-full rounded-xl text-md"
                          placeholder="+63 XXX XXX XXXX" value="${employeeData.phone_no}"/>
                  </div>
              </div>
          </div>

          <div>
              <h3 class="text-xl font-semibold text-gray-700 mb-6 pb-3 border-b-2 border-blue-100">Personal
                  Details</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                  <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-3">Date of Birth *</label>
                      <input type="date" name="dob" required class="form-input p-2 w-full rounded-xl text-md" value="${unixToInputDate(employeeData.dob)}"/>
                  </div>
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Gender</label>
                    <select name="gender" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white">
                      ${["Male","Female","Other","Prefer not to say"].map(genderVal => {
                        return `<option value="${genderVal}" ${genderVal == employeeData.gender ? "selected" : ""}>${genderVal}</option>`; 
                      })}
                    </select>
                  </div>
              </div>
          </div>
      `;
    }

    function updateAddressesForm(address) {
      let addressHTML = ``;
      for (let value of Object.values(address)) {
        const { id, street_name, barangay, city, province, zipcode } = value;
        addressHTML += `
          <li class="space-y-6">
            <input type="hidden" name="id" always-included value="${id}"/>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">Street Address *</label>
                <input type="text" name="street_name" required class="form-input p-2 w-full rounded-xl text-md"
                    placeholder="House number, street name" value="${street_name}" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Barangay *</label>
                    <input type="text" name="barangay" required class="form-input p-2 w-full rounded-xl text-md"
                        placeholder="Enter barangay" value="${barangay}" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">City *</label>
                    <input type="text" name="city" required class="form-input p-2 w-full rounded-xl text-md"
                        placeholder="Enter city" value="${city}" />
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Province *</label>
                    <input type="text" name="province" required class="form-input p-2 w-full rounded-xl text-md"
                        placeholder="Enter province" value="${province}"/>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Zip Code *</label>
                    <input type="text" name="zipcode" required class="form-input p-2 w-full rounded-xl text-md"
                        placeholder="XXXX" value="${zipcode}"/>
                </div>
            </div>
          </li>
        `;
      }
      document.querySelector(".form-step-1 form:nth-of-type(2) ul").innerHTML = addressHTML;
    }

    function updateJobForm(job) {
      let jobHTML = ``;
      for (let value of Object.values(job)) {
        const { id, position, department, appointment_type, civil_service_eligibility, appointment_date, immediate_supervisor, monthly_salary } = value;
        jobHTML += `
          <li class="space-y-10">
            <input type="hidden" name="id" value="${id}" always-included/>
            <div>
                <h3 class="text-xl font-semibold text-gray-700 mb-6 pb-3 border-b-2 border-blue-100">Position
                    Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Position *</label>
                        <select name="position" required class="form-input p-2 w-full rounded-xl text-md">
                            <option value="">Select Position</option>
                            ${[
                            "BARANGAY_KAGAWAD","BARANGAY_CAPTAIN",
                            "BARANGAY_SECRETARY","BARANGAY_TREASURER",
                            "SK_CHAIRMAN","BARANGAY_HEALTH_WORKER",
                            "BARANGAY_TANOD","ADMINISTRATIVE_CLERK",
                            "UTILITY_WORKER","DAY_CARE_WORKER"].map(posVal => {
                              return `<option ${posVal === position ? "selected" : ""} >${posVal}</option>`;
                           })}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Department *</label>
                        <select name="department" required class="form-input p-2 w-full rounded-xl text-md">
                            <option value="">Select Department</option>
                            ${
                              Object.values(departments).map(({id,name}) => {
                                return `<option ${name === department ? "selected" : ""} value="${id}" >${name}</option>`;
                              })
                            }
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
                            ${[
                            "ORIGINAL","PROMOTION",
                            "TRANSFER","REAPPOINTMENT"].map(appTypeVal => {
                              return `<option ${appTypeVal === appointment_type ? "selected" : ""} >${appTypeVal}</option>`;
                            })}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Civil Service Eligibility
                            *</label>
                        <select name="civil_service_eligibility" required class="form-input p-2 w-full rounded-xl text-md">
                            <option value="">Select Eligibility</option>
                            ${[
                            "CAREER_SERVICE_PROFESSIONAL","CAREER_SERVICE_SUB_PROFESSIONAL",
                            "PBET","BARANGAY_ELIGIBILITY",
                            "NONE"].map(civServElVal => {
                              return `<option ${civServElVal === civil_service_eligibility ? "selected" : ""} >${civServElVal}</option>`;
                            })}
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
                        <input name="appointment_date" type="date" required class="form-input p-2 w-full rounded-xl text-md" value="${unixToInputDate(appointment_date)}"/>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Immediate Supervisor
                            *</label>
                        <input name="immediate_supervisor" type="text" required class="form-input p-2 w-full rounded-xl text-md"
                            placeholder="Supervisor's name" value="${immediate_supervisor}" />
                    </div>
                </div>
                <div class="mt-8">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Monthly Salary *</label>
                    <input name="monthly_salary" type="number" required class="form-input p-2 w-full md:w-1/2 rounded-xl text-md"
                        placeholder="0.00" value="${monthly_salary}" />
                </div>
            </div>
          </li>
        `;
      }
      document.querySelector(".form-step-1 form:nth-of-type(3) ul").innerHTML = jobHTML;
    }

    function updateEmergencyContactForm(emergencyContacts) {
      let emergencyContactHTML = ``;
      for (let value of Object.values(emergencyContacts)) {
        const { id, fullname, relationship, phone_no, email, address, is_primary } = value;
        emergencyContactHTML += `
        <li class="space-y-10">    
          <input type="hidden" name="id" value="${id}" always-included/>
          <div>
              <h3 class="text-xl font-semibold text-gray-700 mb-6 pb-3 border-b-2 border-blue-100">Contact
                  Person</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                  <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-3">Full Name *</label>
                      <input type="text" name="fullname" pattern="^[a-zA-Z ]+$" required class="form-input p-2 w-full rounded-xl text-md"
                          placeholder="Enter full name" value="${fullname}" />
                  </div>
                  <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-3">Relationship *</label>
                      <input type="text" name="relationship" required class="form-input p-2 w-full rounded-xl text-md"
                          placeholder="e.g., Spouse, Parent, Sibling" value="${relationship}" />
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
                          placeholder="+63 XXX XXX XXXX" value="${phone_no}" />
                  </div>
                  <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-3">Email Address</label>
                      <input type="email" name="email" class="form-input p-2 w-full rounded-xl text-md"
                          placeholder="example@email.com" value="${email}"/>
                  </div>
              </div>
          </div>

          <div>
              <h3 class="text-xl font-semibold text-gray-700 mb-6 pb-3 border-b-2 border-blue-100">Address
              </h3>
              <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-3">Complete Address *</label>
                  <textarea rows="5" name="address" required class="form-input p-2 w-full rounded-xl text-md resize-none"
                      placeholder="Enter complete address" >${address}</textarea>
              </div>
          </div>

          <div class="bg-blue-50 p-6 rounded-2xl border-2 border-blue-200">
              <div class="flex items-center">
                  <input type="checkbox" name="is_primary" value="0" id="is_primary"
                      class="h-6 w-6 text-blue-600 rounded-lg focus:ring-blue-500 border-gray-300" checked="${Boolean(is_primary)}" />
                  <label for="is_primary" class="ml-4 text-base font-semibold text-gray-700">
                      Set as Primary Emergency Contact
                  </label>
              </div>
              <p class="text-sm text-gray-600 mt-3">This contact will be reached first in case of emergencies
              </p>
          </div>
        </li>`;
        document.querySelector(".form-step-2 form ul").innerHTML = emergencyContactHTML;
      }
    }

    function renderFileList(documents) {
      document.querySelector("#document-input-src").value = documents.map(doc => doc.url).join(",");
      document.querySelector("#file-list").innerHTML = (documents.map(doc => {
        return `<li class="flex justify-between items-center bg-gray-100 p-2 rounded-lg">
          <span class="text-gray-700 text-sm">
            <input type="hidden" name="id" always-included value="${doc.id}" /> 
            <input type="hidden" name="url" always-included value="${doc.url}" /> 
            <img src="${doc.url}" width="300px" heigh="300px" />
          </span>
          <button type="button"
              class="remove-document text-red-500 text-sm font-semibold hover:underline">
              Remove
          </button>
        </li>`;
      })).join("\n");
    }

    function updateBiometricForm(employee) {
      document.querySelector(".form-step-3 form:nth-of-type(2)").innerHTML = `
        <input type="hidden" name="type" value="biometrics" />
        <div>
            <h3 class="text-xl font-semibold text-gray-700 mb-6 pb-3 border-b-2 border-blue-100">
                Fingerprint
            </h3>
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Biometric ID *</label>
                    <input type="number" name="biometric_id"
                        class="form-input p-2 w-full rounded-xl text-md" required value="${employee.biometric_id}"/>
                    <p class="text-sm text-gray-500 mt-2">Please provide the correct id of the employee after they register in the biometric device.</p>
                </div>
            </div>
        </div>
      `;
    }

    async function loadDraft() {
      const employeeDraft = await fetchJSON("api/employee_draft"); 
      const employeeAddresses = await fetchJSON("api/employee_addresses?employee_id="+employeeDraft.id);
      const employeeEmergencyContacts = await fetchJSON("api/employee_emergency_contacts?employee_id="+employeeDraft.id);
      const employeeDocuments = await fetchJSON("api/employee_documents?employee_id="+employeeDraft.id);
      const employeeJobs = await fetchJSON("api/employee_jobs?employee_id="+employeeDraft.id);

      updatePersonalInfoForm(employeeDraft);
      updateBiometricForm(employeeDraft);
      if (Object.keys(employeeAddresses).length)
        updateAddressesForm(employeeAddresses);
      if (Object.keys(employeeJobs).length)
        updateJobForm(employeeJobs);
      if (Object.keys(employeeEmergencyContacts).length)
        updateEmergencyContactForm(employeeEmergencyContacts);
      if (Object.keys(employeeDocuments).length)
        renderFileList(Object.values(employeeDocuments));
    }

    const formSubmitHandlers = {
      "form-step-1": async function(data) {
        const employeeData = data.employee;
        let employeeId = employeeData.id;
        if (Object.keys(employeeData).length > 1 || !employeeId?.trim()) {
          if (!employeeId?.trim()) { 
            const employee = await EmployeeApi.employee.create(employeeData);
            employeeId = employee.id;
            document.querySelector("#add-personalInformation input[name='id']").value = employee.id;
            updatePersonalInfoForm(employee);
          } else
            await EmployeeApi.employee.update(employeeData);
        }
        
        const createAddresses = [];
        const updateAddresses = [];
        for (let address of data.addresses.data) {
          if (Object.keys(address).length === 1 && address.id?.trim())
            continue;
          if (!address.id?.trim())
            createAddresses.push(address);
          else 
            updateAddresses.push(address);
        }
        if (createAddresses.length) {
          const addresses = await EmployeeApi.addresses.create({employeeId, addresses: createAddresses});
          updateAddressesForm(addresses);
        }
        if (updateAddresses.length)
          await EmployeeApi.addresses.update({addresses: updateAddresses});

        const createJobs = [];
        const updateJobs = [];
        for (let job of data.jobs.data) {
          if ((Object.keys(job).length === 1 && job.id?.trim()) || !Object.keys(job).length)
            continue;
          if (!job.id?.trim())
            createJobs.push(job);
          else 
            updateJobs.push(job);
        }
        if (createJobs.length) {
          const jobs = await EmployeeApi.jobs.create({employeeId, jobs: createJobs});
          updateJobForm(jobs);
        }
        if (updateJobs.length)
          await EmployeeApi.jobs.update({jobs: updateJobs});
      },
      "form-step-2": async function(data) {
        const employeeId = document.querySelector("#add-personalInformation input[name='id']").value;
        const createEmergencyContacts = [];
        const updateEmergencyContacts = [];
        for (let emergencyContact of data.emergency_contacts.data) {
          if (Object.keys(emergencyContact).length === 1 && emergencyContact.id?.trim())
            continue;
          if (!emergencyContact.id?.trim())
            createEmergencyContacts.push(emergencyContact);
          else updateEmergencyContacts.push(emergencyContact);
        }
        if (createEmergencyContacts.length) {
          const emergency_contacts = await EmployeeApi.emergencyContacts.create({employeeId, emergency_contacts: createEmergencyContacts});
          updateEmergencyContactForm(emergency_contacts);
        }
        if (updateEmergencyContacts.length)
          await EmployeeApi.emergencyContacts.update({emergency_contacts: updateEmergencyContacts});
      },
      "form-step-3": async function(data) {
        const employeeId = document.querySelector("#add-personalInformation input[name='id']").value;
        const createDocuments = [];
        for (let doc of data.documents.data) {
          if (doc.id?.trim())
            continue;
          createDocuments.push(doc);
        }
        if (createDocuments.length) {
          const documents = await EmployeeApi.documents.create({employeeId, documents: createDocuments});
          renderFileList(documents);
        }
        if (data.documents.delete?.length)
          await EmployeeApi.documents.update({delete: data.documents.delete});

        const biometric_id = data.biometrics.biometric_id;
        if (biometric_id)
          await EmployeeApi.employee.update({id:employeeId,biometric_id});
      }
    };
 
    function updateStepIndicator(step) {
        stepIndicators.forEach((indicator, index) => {
            const circle = indicator.querySelector('.step-circle');
            const title = indicator.querySelector('.step-title');
            
            if (index === step) {
                circle.className = 'step-circle flex items-center justify-center w-10 h-10 rounded-full border-2 border-blue-600 bg-blue-100 text-blue-600';
                title.className = 'step-title text-sm font-medium text-blue-600';
            } else if (index < step) {
                circle.className = 'step-circle flex items-center justify-center w-10 h-10 rounded-full border-2 border-green-600 bg-green-100 text-green-600';
                title.className = 'step-title text-sm font-medium text-green-600';
            } else {
                circle.className = 'step-circle flex items-center justify-center w-10 h-10 rounded-full border-2 border-gray-400 text-gray-400 bg-white';
                title.className = 'step-title text-sm font-medium text-gray-400';
            }
        });
    }

    function copyForm(form) {
       const clone = form.cloneNode(true);
       const originalInputs = form.querySelectorAll("input, textarea, select");
       const clonedInputs = clone.querySelectorAll("input, textarea, select");
       originalInputs.forEach((el, i) => {
         clonedInputs[i].value = el.value;
         if (el.type === "checkbox" || el.type === "radio")
           clonedInputs[i].checked = el.checked;
       });
      return clone;
    }

    function displayReview() {
      const formStep4 = document.querySelector(".form-step-4");
      formStep4.innerHTML = "";
      for (let formStep of formSteps) {
        if (formStep.matches(".form-step-4"))
          break;
        const forms = formStep.querySelectorAll("form");
        for (let form of forms) {
          if (form.matches("#add-documents")) 
            formStep4.insertAdjacentHTML("beforeend",`<form><h3 class="text-xl font-semibold text-gray-700 mb-6 pb-3 border-b-2 border-blue-100 mt-10">Document Upload</h3>` +  document.querySelector("#file-list").outerHTML + `</form>`);
          else
            formStep4.appendChild(copyForm(form));
        }
      }
      for (let input of document.querySelectorAll(".form-step-4 input, .form-step-4 textarea"))
        input.setAttribute("readonly",true); 
      for (let clickable of document.querySelectorAll(".form-step-4 button, .form-step-4 select, .form-step-4 input[type='checkbox']"))
        clickable.setAttribute("disabled",true); 
      for (let button of document.querySelectorAll(".remove-document"))
        button.remove();
    }

    function displayDepartments() {
      for (let li of document.querySelectorAll("#add-jobInformation ul li")) {
        const departmentSelect = li.querySelector("select[name='department']");
        departmentSelect.innerHTML = `<option value="">Select Department</option>
          ${
            Object.values(departments).map(({id,name}) => {
              return `<option value="${id}">${name}</option>`;
            })
          }
        `;
      }
    }

    function showStep(step) {
        formSteps.forEach((form, index) => {
            if (index === step) {
                form.classList.remove('hidden');
            } else {
                form.classList.add('hidden');
            }
        });

        global.scrollTo({
          top: 0,
          behavior: "smooth",
        });

        updateStepIndicator(step);


        prevBtn.disabled = step === 0;
        if (step === 0) {
            prevBtn.className = 'prevBtn px-6 py-2 rounded-lg bg-gray-200 text-gray-400 cursor-not-allowed';
        } else {
            prevBtn.className = 'prevBtn px-6 py-2 rounded-lg bg-gray-200 text-gray-600 hover:bg-gray-300 transition-colors cursor-pointer';
        }


        if (step === formSteps.length - 1) {
            nextBtn.textContent = 'Submit Registration';
            nextBtn.className = 'nextBtn px-6 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition-colors';
            displayReview();  
        } else {
            nextBtn.textContent = 'Next Step';
            nextBtn.className = 'nextBtn px-6 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors';
        }
    }

    function getFieldsError(form) {
      for (let field of form.querySelectorAll('input[name], select[name]')) {
        if (field.hasAttribute("required") && !field.value.trim())
          return {field, errMsg: "Please fill in all required fields before proceeding."};
        const pattern = field.getAttribute("pattern");
        if (pattern && !new RegExp(pattern).test(field.value)) 
          return {field, errMsg: `Inputted value doesn't match expected!`};
        if (field.type == "email" && !/\w+@\w+\.\w+/.test(field.value))
          return {field, errMsg: `Invalid Email!`}; 
      }
    }
    
    function resetFormErrorDisplay(form) {
      for (let field of form.querySelectorAll("input, select"))
        field.classList.remove("!border-red-300");
      if (form.querySelector(".error-message"))
        form.querySelector(".error-message").remove();
    }

    nextBtn.addEventListener('click', async (e) => {
        e.preventDefault();
        const formStep = formSteps[currentStep];
        resetFormErrorDisplay(formStep);
        const fieldsError = getFieldsError(formStep);
        const CompletionAction = {
          "GMAC": async function(employee_id) {
            const newUser = await EmployeeApi.employee.complete({employee_id});
            await Swal.fire({
              icon: 'success',
              title: 'Employee Registered!',
              html: `
                <span>New Employee Login Credentials:</span>
                <div>
                  <label for="username">username:</label>
                  <input type="text" name="username" class="form-input p-2 rounded-xl" value="${newUser.username}" />
                </div>
                <div>
                  <label for="password">password:</label>
                  <input type="text" name="password" class="form-input p-2 rounded-xl" value="${newUser.password}" />
                </div>
              `,
              allowOutsideClick: false,
            });
            await createNotification({
              type: "employee-registration-completion",
              message: `Employee Registration Completed`,
              data: {employee_id},
            });
          },
          "HR": async function(employee_id) {
            await EmployeeApi.employee.update({id: employee_id,status:"PENDING"});
            await Swal.fire({
              icon: 'success',
              title: 'Employee Added ',
              text: 'Waiting for GMAC approval!',
              allowOutsideClick: false,
              showConfirmButton: false,
              timer: 1500,
            });
            await createNotification({
              type: "employee-creation",
              message: `Successfully Created Employee`,
              data: {employee_id},
            });
          },
        };
        if (!fieldsError) {
          try {
            if (currentStep === formSteps.length - 1) {
              const role = document.body.dataset.userRole;
              const id = document.querySelector("#add-personalInformation input[name='id']").value;
              await CompletionAction[role](id); 
              location.assign("dashboard?page=add-staff");
            } else {
              const dataGathered = gatherFormData(formStep);
              await formSubmitHandlers[`form-step-${currentStep + 1}`](dataGathered);
              const currentBars = document.querySelectorAll(`.progressbar-step-${currentStep}`);
              currentBars.forEach(bar => {
                bar.style.width = "100%";
                bar.classList.add("bg-green-500");
              });

              await Swal.fire({
                icon: 'success',
                title: 'Step Completed!',
                text: 'Step completed, proceeding to next step...',
                showConfirmButton: false,
                timer: 1500
              });
              currentStep++;
              document.querySelector('.steps-count').textContent = ` ${currentStep + 1}`;
              showStep(currentStep);
            }
          } catch (e) {
            console.log("Eurka!", e);
            Swal.fire({
                icon: 'error',
                title: 'Registration Failed!',
                text: 'Something went wrong please try again.',
                showConfirmButton: false,
                timer: 1500
            });
          }
        } else {
            let errorMsg = formStep.querySelector('.error-message');
            if (!errorMsg) {
                errorMsg = document.createElement('div');
                errorMsg.className = 'error-message bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mt-4';
                errorMsg.textContent = fieldsError.errMsg;
                formStep.appendChild(errorMsg);
            }
            if (fieldsError.field) {
                fieldsError.field.classList.add("!border-red-300");
                fieldsError.field.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });

    prevBtn.addEventListener('click', (e) => {
        e.preventDefault();
        if (currentStep > 0) {
            const currentBars = document.querySelectorAll(`.progressbar-step-${currentStep}`);
            currentBars.forEach(bar => bar.style.width = "0%");
            
            currentStep--;
             document.querySelector('.steps-count').textContent = ` ${currentStep + 1}`;
            showStep(currentStep);
        }
    });



    document.addEventListener('input', (e) => {
        if (e.target.matches('input, select')) {
            e.target.classList.remove('border-red-500');
        }
    });

    showStep(currentStep);

    async function uploadToCloud(data, uploadKey) {
      const formData = new FormData();
      const { file, publicId } = data;
      const type = file.type.split("/").includes("image") ? "image" : "raw";

      formData.append("file", file);
      formData.append("api_key", uploadKey.api_key);
      formData.append("timestamp", uploadKey.timestamp);
      formData.append("signature", uploadKey.signature);
      formData.append("public_id", publicId);
      formData.append("unique_filename", false);
      formData.append("overwrite", false);
      formData.append("folder", uploadKey.folder);
      formData.append("resource_type", type);

      const response = await fetch(`https://api.cloudinary.com/v1_1/${uploadKey.cloud_name}/${type}/upload`, 
      {
        method: "POST",
        body: formData
      });
      if (!response.ok)
        throw new Error("Upload Failed!");
      return await response.json();
    }

    async function isExistsInCloud(publicId, cloudname) {
      try {
        const url = `https://res.cloudinary.com/${cloudname}/image/upload/v1756796419/documents/${publicId}`;
        const response = await fetch(url, {
          method: "HEAD",
        });
        if (response.ok)
          return url;
      } catch (e) { return null };
      return null;
    }

    function deletePreviousFiles() {
      const inputDeleted = document.querySelector(".form-step-4 form input[name='deleted']");
      for (let li of document.querySelector("#file-list").children) {
        const id = li.querySelector("input[type='hidden'][name='id']").value;
        if (id?.trim()) {
            const deletedValue = inputDeleted.value;
            inputDeleted.value = !deletedValue?.trim() ? id : [...inputDeleted.value.split(","),id].join(",");
        }
      }
    }

    document.querySelector("#document-input").addEventListener("change", async function() {
        Swal.fire({
          title: "Please wait!",
          html: "Processing Documents...",
          allowOutsideClick: false,
        });
        Swal.showLoading();
        const urls = [];
        for (let file of this.files) {
          let retry = 3;
          let done = false;
          let url;
          const arrayBuffer = await file.arrayBuffer();
          const publicId = await imageHashBase64URL(arrayBuffer);
          const uploadKey = await fetchJSON(`api/image_upload_url?public_id=${publicId}`);
          url = await isExistsInCloud(publicId, uploadKey.cloud_name) 
          if (url) {
             urls.push(url);
             continue;
          }
          while (!done && retry > 0) {
            try {
              const imgData = await uploadToCloud({publicId,file},uploadKey);
              url = imgData.url; 
              done = true; 
            } catch (e) {
              retry--;
            }
          }
          if (!retry && !done) {
            Swal.fire({
              icon: "error",
              title: "Upload Failed",
              text: "Document upload failed please try again!",
            });
            return;
          }
          urls.push(url);
        }
        deletePreviousFiles();
        renderFileList(urls.map(url => ({url, id:""})));
        Swal.fire({
          icon: "success",
          title: "Documents Uploaded!",
          text: "Documents uploaded to the cloud",
        });
        Swal.hideLoading();
    });


    document.querySelector("#file-list").addEventListener("click", (e) => {
        if (e.target.matches("button.remove-document")) {
          const inputDeleted = document.querySelector(".form-step-3 form input[name='deleted']");
          const fileList = document.querySelector("#file-list");
          const target = e.target.closest("li");
          const targetId = target.querySelector("input[type='hidden'][name='id']").value;
          if (targetId?.trim()) {
            const deletedValue = inputDeleted.value;
            inputDeleted.value = !deletedValue?.trim() ? targetId : [...inputDeleted.value.split(","),targetId].join(",");
          }
          fileList.removeChild(target);
          if (!fileList.children.length)
            document.querySelector("#document-input-src").value = "";
        }
    });

    async function init() {
      const departmentList = await fetchJSON("api/list-departments");
      Object.assign(departments, departmentList);
      displayDepartments();
      loadDraft();
    }

    init();
})(window);
