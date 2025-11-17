(function(global) {
  let staffs = {};

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

  function listStaffs(staffs) {
    let staffsHTML = ``;
    for (let value of Object.values(staffs)) {
      const { 
        id,
        first_name, 
        middle_name,
        last_name, 
        email,
        jobs,
        phone_no,
      } = value;  

      staffsHTML += `
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="font-medium text-gray-900">${last_name}, ${first_name} ${middle_name && middle_name[0].toUpperCase()}.</div> </td>
            <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                ${email}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-semibold rounded-full">${jobs.position}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                ${phone_no}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                Secretary
            </td>
            <!-- ICONS -->
            <td class="px-6 py-4 whitespace-nowrap text-right">
                <div class="flex justify-end gap-2">
                    <button
                        class="view-employee-btn"
                        data-navfor="?page=view-staff/personal&employee_id=${id}"
                        class="text-blue-600 hover:text-blue-700 hover:bg-blue-50 p-2 rounded-md transition-colors">
                        <i data-lucide="eye" class="h-4 w-4"></i>
                    </button>
                    <button
                        class="view-employee-btn"
                        data-navfor="?page=view-staff/personal&employee_id=${id}&edit_employee"
                        class="text-gray-600 hover:text-gray-700 hover:bg-gray-50 p-2 rounded-md transition-colors cursor-pointer">
                        <i data-lucide="edit" class="h-4 w-4"></i>
                    </button>
                    <button
                        data-id="${id}"
                        class="delete-employee-btn text-red-600 cursor-pointer hover:text-red-700 hover:bg-red-50 p-2 rounded-md transition-colors"
                        type="button">
                        <i data-lucide="trash-2" class="h-4 w-4"></i>
                    </button>
                </div>
            </td>
         </tr>
         `;
    }
    
    document.querySelector("#staffsContainer").innerHTML = staffsHTML;
    for (let btn of document.querySelectorAll(".view-employee-btn"))
      btn.addEventListener("click", function() { navigatePage(this.dataset.navfor) });
  }

  function initializePageScripts() {
      const modal = document.getElementById("add-staff-modal"); 
      const openModalBtn = document.getElementById("openModalBtn");

      if (openModalBtn && modal) {
          openModalBtn.addEventListener('click', () => {
              modal.classList.remove('hidden');
              // modal.classList.add('flex');
              document.body.style.overflow = 'hidden'; 
          });

          window.closeModal = function() {
              modal.classList.add('hidden');
              document.body.style.overflow = '';
          };

          window.addEventListener('click', function(event) {
              if (event.target === modal) {
                  closeModal();
              }
          });
      }
  }

  async function deleteEmployee(id) {
    const response = await fetch("api/employee", {
      method: "PATCH",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        id,
        employee: {
          deleted: 1
        }
      })
    });

    return await response.json();
  }

  async function deleteStaff() {
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
    if ((await deleteEmployee(this.dataset.id)).success) {
      delete staffs[this.dataset.id];
      displayStaffs(staffs);
      displayStaffRoles(staffs);
      Swal.fire({
        icon: 'success',
        title: 'Deleted!',
        text: 'The item has been deleted.',
        timer: 1500,
        showConfirmButton: false
      });
      await createNotification({
        type: "employee-deletion",
        message: `Employee Deleted`,
        data: {employee_id: this.dataset.id}
      });
    }
  }

  function displayStaffs(staffsList) {
    listStaffs(staffsList);
    lucide.createIcons();
    initializePageScripts();
  
    document.querySelectorAll('.delete-employee-btn').forEach(button => {
      button.addEventListener('click', deleteStaff);
    });
    
    const totalStaffs = Object.values(staffs);
    const totalShowingStaffs = Object.values(staffsList);
    for (let el of document.querySelectorAll(".total-employee-showing"))
      el.textContent = totalShowingStaffs.length;
    for (let el of document.querySelectorAll(".total-employee"))
      el.textContent = totalStaffs.length;
  }

  function displayStaffRoles(staffs) {
    const container = document.querySelector("#select-roles");
    container.innerHTML = `<option value="">All Roles</option>` + [...new Set(Object.values(staffs).map(staff => staff.jobs.position))].map(role => {
      return `<option value="${role}">${role}</option>`
    }).join("\n");

    container.addEventListener("change", function() {
      if (this.value.trim())
        displayStaffs(Object.fromEntries(Object.values(staffs).filter(staff => staff.jobs.position == this.value).map(staff => ([staff.id,staff]))))
      else
        displayStaffs(staffs);
    });
  }

  async function init() {
    Object.assign(staffs, await (await fetch("api/list-employees")).json());
    displayStaffs(staffs);
    displayStaffRoles(staffs);
    document.querySelector("#search-staff").addEventListener("input", function() {
      const value = this.value;
      const filteredStaff = Object.fromEntries(Object.values(staffs)
        .filter(({first_name,middle_name,last_name}) => first_name.startsWith(value) || middle_name.startsWith(value) || last_name.startsWith(value))
        .map((staff => ([staff.id,staff]))));
      displayStaffs(filteredStaff);
    });
  }

  document.querySelector("#addStaffBtn").addEventListener("click", () => global.navigatePage("?page=add-staff"));
  init();
})(window);
