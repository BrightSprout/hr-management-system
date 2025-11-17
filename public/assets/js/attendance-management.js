(function(global) {
  const departments = {};

  async function fetchJSON(url) {
    const response = await fetch(url);
    return await response.json();
  }

  function retrieveInputsChange(inputs) {
    const changes = {}; 
    for (let input of inputs) {
      if (input.tagName === "INPUT" && input.type == "checkbox" && input.hasAttribute("multiple-check") && input.checked != input.defaultChecked) {
        const name = input.getAttribute("name");
        const index = input.getAttribute("multiple-check");
        if (!changes[name])
          changes[name] = [];
        changes[name][index] = input.checked;
      } else {
        let defaultValue = input.defaultValue;
        if (input.tagName === "SELECT")
          defaultValue = input.querySelector("option[selected]")?.value;
        if (input.value != defaultValue || input.hasAttribute("always-included")) 
          changes[input.getAttribute("name")] = input.value;
      }
    }
    return changes;
  }

  function gatherFormData(formContainer) { 
    const forms = formContainer.querySelectorAll("form");
    const data = {};
    for (let form of forms) {
      const type = form.dataset.type;
      data[type] = retrieveInputsChange(form.querySelectorAll("input[name], select[name], textarea[name]"));
    }
    return data;
  }

  function convertToInputTime(seconds) {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    return `${hours.toString().padStart(2,"0")}:${minutes.toString().padStart(2,"0")}`;  
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

 async function deleteDepartment(id) {
   const reply = await Swal.fire({
     title: 'Are you sure?',
     text: "This action cannot be undone.",
     icon: 'warning',
     showCancelButton: true,
     confirmButtonColor: '#d33',
     cancelButtonColor: '#3085d6',
     confirmButtonText: 'Yes, delete it!'
   })
   if (!reply.isConfirmed) 
     return;
   try {
     const response = await fetch("api/department", {
       method: "PATCH",
       headers: {
         "Content-Type": "application/json"
       },
       body: JSON.stringify({
         id,
         department: {
           deleted: 1
         }
       })
     });
     if (response.ok) {
       const json = await response.json();
       if (json.success)
         await Swal.fire({
           icon: 'success',
           title: 'Department Updated!',
           text: 'Updating department completed.',
         });
       delete departments[id];
       displayDepartments(departments);
     }
   } catch(e) {
     console.log(err);
     await Swal.fire({
       icon: 'error',
       title: 'Update Department Failed!',
       text: 'Updating department failed.',
     });
   }
 }

 function openDepartmentModal({id, name,dayoffs,clock_in,clock_out}) {
   const modal = document.getElementById("editModal");
   modal.classList.remove("hidden");
   modal.style.display = "flex";
   document.body.style.overflow = "hidden";    
   document.querySelector("#edit-form").innerHTML = `  
      <div class="px-6 space-y-4">
        <input name="id" value="${id}" type="hidden" always-included/>
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
          <input value="${name}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400" name="name" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Day Offs</label>
          <div class="flex flex-wrap gap-2">
            <label class="cursor-pointer">
              <input multiple-check="1" type="checkbox" name="dayoffs" value="1" class="peer hidden" ${dayoffs.includes(1) ? "checked": ""} />
              <span class="px-4 py-2 rounded-full border border-gray-300 text-gray-600 text-sm 
                peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-500
                transition-all duration-200">
                Monday
              </span>
            </label>
            <label class="cursor-pointer">
              <input multiple-check="2" type="checkbox" name="dayoffs" value="2" class="peer hidden" ${dayoffs.includes(2) ? "checked" : ""}/>
              <span class="px-4 py-2 rounded-full border border-gray-300 text-gray-600 text-sm 
                peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-500
                transition-all duration-200">
                Tuesday
              </span>
            </label>
            <label class="cursor-pointer">
              <input multiple-check="3" type="checkbox" name="dayoffs" value="3" class="peer hidden" ${dayoffs.includes(3) ? "checked" : ""}/>
              <span class="px-4 py-2 rounded-full border border-gray-300 text-gray-600 text-sm 
                peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-500
                transition-all duration-200">
                Wednesday
              </span>
            </label>
            <label class="cursor-pointer">
              <input multiple-check="4" type="checkbox" name="dayoffs" value="4" class="peer hidden" ${dayoffs.includes(4) ? "checked" : ""}/>
              <span class="px-4 py-2 rounded-full border border-gray-300 text-gray-600 text-sm 
                peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-500
                transition-all duration-200">
                Thursday
              </span>
            </label>
            <label class="cursor-pointer">
              <input multiple-check="5" type="checkbox" name="dayoffs" value="5" class="peer hidden" ${dayoffs.includes(5) ? "checked" : ""}/>
              <span class="px-4 py-2 rounded-full border border-gray-300 text-gray-600 text-sm 
                peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-500
                transition-all duration-200">
                Friday
              </span>
            </label>
            <label class="cursor-pointer">
              <input multiple-check="6" type="checkbox" name="dayoffs" value="6" class="peer hidden" ${dayoffs.includes(6) ? "checked" : ""}/>
              <span class="px-4 py-2 rounded-full border border-gray-300 text-gray-600 text-sm 
                peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-500
                transition-all duration-200">
                Saturday
              </span>
            </label>
            <label class="cursor-pointer">
              <input multiple-check="0" type="checkbox" name="dayoffs" value="0" class="peer hidden" ${dayoffs.includes(0) ? "checked" : ""} />
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
          <input value="${convertToInputTime(clock_in)}" type="time" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400" name="clock_in" />
        </div>

        <div>
          <label for="clock_out" class="block text-sm font-medium text-gray-700 mb-1">Time Out</label>
          <input value="${convertToInputTime(clock_out)}" type="time" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400" name="clock_out" />
        </div>
      </div>
    `;
 }

 function displayDepartments(departments) {
  document.querySelector("#departments").innerHTML = `
    <div class="custom-gradient-bg p-4">
      <h2 class="text-xl font-semibold text-center text-white tracking-wide">Department Info</h2>
    </div>
    <table class="min-w-full table-auto text-sm text-left">
      <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
        <tr class="uppercase text-xs tracking-wider">
          <th class="px-6 py-4">Name</th>
          <th class="px-6 py-4">Day Offs</th>
          <th class="px-6 py-4">Time In</th>
          <th class="px-6 py-4">Time Out</th>
          <th class="px-6 py-4">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        ${Object.values(departments).map(department => {
          const { id, name, dayoffs, clock_in, clock_out } = department;
          return `
            <tr class="hover:gray-100 transition duration-200">
              <td class="px-6 py-4">${name}</td>
              <td class="px-6 py-4">${dayoffs}</td>
              <td class="px-6 py-4">${formatTime(clock_in)}</td>
              <td class="px-6 py-4">${formatTime(clock_out)}</td>
              <td class="px-6 py-4">
                <button
                    data-id="${id}"
                    class="edit-department-btn text-gray-600 hover:text-gray-700 hover:bg-gray-50 p-2 rounded-md transition-colors cursor-pointer">
                    <i data-lucide="edit" class="h-4 w-4"></i>
                </button>
              </td>
            </tr>
          `;
        }).join('')}
      </tbody>
    </table>
  `;
   
   for (let editBtn of document.querySelectorAll(".edit-department-btn"))
     editBtn.addEventListener("click", () => openDepartmentModal(departments[editBtn.dataset.id]));
   // should we add delete department?
   // for (let deleteBtn of document.querySelectorAll(".delete-department-btn"))
   //   deleteBtn.addEventListener("click", () => deleteDepartment(deleteBtn.dataset.id));
   lucide.createIcons();
}



  document.querySelector("#create-department").addEventListener("submit", async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const json = Object.fromEntries(formData.entries());
    json.dayoffs = json.dayoffs ? formData.getAll("dayoffs").map(Number) : [];

    if (json.clock_in) {
      const [hours,minutes] = json.clock_in.split(":").map(Number);
      json.clock_in = hours * 3600 + minutes * 60; 
    }

    if (json.clock_out) {
      const [hours,minutes] = json.clock_out.split(":").map(Number);
      json.clock_out = hours * 3600 + minutes * 60; 
    }

    const response = await fetch("api/department", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(json)
    });
    if (response.ok) {
      const department = await response.json();
      Object.assign(departments, {[department.id]: department});
      displayDepartments(departments);
      await createNotification({
        type: "department-creation",
        message: `Department Created!`,
        data: {department_id: department.id, department_name: department.name}
      });
    } else 
      alert("creating department failed!");
  });

  function addModalListener() {
    const modal = document.getElementById("editModal");
    const closeModalBtn = document.getElementById("closeModalBtn");
    const cancelBtn = document.getElementById("cancelBtn");
    const saveBtn = document.getElementById("saveBtn");

    const closeModal = () => {
      modal.classList.add("hidden");
      modal.style.display = "none";
      document.body.style.overflow = "auto"; 
    };

    closeModalBtn.addEventListener("click", closeModal);
    cancelBtn.addEventListener("click", closeModal);

    modal.addEventListener("click", (e) => {
      if (e.target === modal)
        closeModal();
    });

    saveBtn.addEventListener("click", async (e) => {
      try {
        const dataGathered = gatherFormData(document.querySelector("#form-container"));
        const updatedDepartment = dataGathered.department;
        if (Object.keys(updatedDepartment).length < 2) 
          return;
        let newDayOffs;
        if (updatedDepartment.dayoffs) {
          newDayOffs = [...departments[updatedDepartment.id].dayoffs];
          for (let i = 0;i < updatedDepartment.dayoffs.length;i++) {
            const day = updatedDepartment.dayoffs[i]; 
            if (day == undefined || day == null)
              continue;
            if (day)
              newDayOffs.push(i);
            else
              newDayOffs.splice(i,1);
          }
          newDayOffs.sort();
          updatedDepartment.dayoffs = JSON.stringify(newDayOffs);
        }
        if (updatedDepartment.clock_in) {
          const [hours,minutes] = updatedDepartment.clock_in.split(":").map(Number);
          updatedDepartment.clock_in = hours * 3600 + minutes * 60; 
        }

        if (updatedDepartment.clock_out) {
          const [hours,minutes] = updatedDepartment.clock_out.split(":").map(Number);
          updatedDepartment.clock_out = hours * 3600 + minutes * 60; 
        }
        const {id, ...department} = updatedDepartment;
        const response = await fetch("api/department", {
          method: "PATCH", 
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({id,department})
        });
        if (updatedDepartment.dayoffs)
          updatedDepartment.dayoffs = JSON.parse(updatedDepartment.dayoffs);
        departments[updatedDepartment.id] = Object.assign(departments[updatedDepartment.id],updatedDepartment);
        displayDepartments(departments);
        if (response.ok) {
          const json = await response.json();
          if (json.success)
            await Swal.fire({
                icon: 'success',
                title: 'Department Updated!',
                text: 'Updating department completed.',
            });
        }
      } catch (err) {
        console.log(err);
        await Swal.fire({
          icon: 'error',
          title: 'Update Department Failed!',
          text: 'Updating department failed.',
        });
      }
    });
  }

  async function init() {
    const departmentList = await fetchJSON("api/list-departments");
    displayDepartments(departmentList);
    Object.assign(departments, departmentList);
    addModalListener();
  }

  init();
}(window));
