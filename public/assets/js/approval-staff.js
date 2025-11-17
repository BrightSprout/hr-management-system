(function (global) {
  const needApprovals = {};

  function formatLocalDate(date) {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, "0");
    const d = String(date.getDate()).padStart(2, "0");
    return `${y}-${m}-${d}`;
  }

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

  const EmployeeApi = {
    abandon: async function(data) {
      const response = await fetch("api/employee", {
        method: "PATCH",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({id: data.employee_id,employee: {status: "ABANDONED"}})
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
  };

  async function updateLeaveStatus(id, status) {
    const response = await fetch("api/employee_leave_approval", {
      method: "PATCH",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        id,
        status,
      })
    });
    return response.ok && (await response.json()).success;
  }

  async function performApprovalAction({id,status,requestType}) {
    try {
      if (requestType == "leave") {
        if (!await updateLeaveStatus(id, status))
          throw new Error("Leave Status Update Failed!");  
        Swal.fire({
          icon: 'success',
          title: `Status ${status}!`,
          text: `The request is now ${status}...`,
        });
        await createNotification({
          type: `leave-status`,
          message: `Leave Employee Status Updated! `,
          data: {leave_id: id}
        });
      } else if (requestType == "registration") {
        if (status == "APPROVED") {
          const newUser = await EmployeeApi.complete({employee_id: id});
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
            data: {employee_id: id},
          });
        } else {
          await EmployeeApi.abandon({employee_id: id});
          await Swal.fire({
            icon: 'success',
            title: 'Rejected Employee Registration',
            text: 'Employee registration abandoned.',
            allowOutsideClick: false,
            showConfirmButton: false,
            timer: 1500,
          });
          await createNotification({
            type: "employee-rejection",
            message: `Rejected Employee`,
            data: {employee_id: id},
          });
        }
      } else 
        return;
      delete needApprovals[`${requestType}_${id}`];
      displayNeedApprovalsList(needApprovals);
    } catch (e) {
      console.log(e);
      Swal.fire({
        icon: 'error',
        title: `Updating Status Failed!`,
        text: `Something went wrong...`,
      });
    }

   
  }

  function displayNeedApprovalsList(needApprovals) {
    document.querySelector("#need-approvals").innerHTML = Object.values(needApprovals).map((needApproval,idx) => {
      const { id, request_type, employee, created_at, status, description  } = needApproval;
      const { first_name, middle_name, last_name, current_job } = employee;
      return `
        <tr class="hover:bg-white/30 transition">
          <td class="px-6 py-4 font-medium">${idx.toString().padStart(4,"0")}</td>
          <td class="px-6 py-4">${first_name} ${middle_name} ${last_name}</td>
          <td class="px-6 py-4">${current_job.position}</td>
          <td class="px-6 py-4">${request_type[0].toUpperCase() + request_type.slice(1)}</td>
          <td class="px-6 py-4">${formatLocalDate(new Date(created_at * 1000))}</td>
          <td class="px-6 py-4">
            <span
              class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
              ${status}
            </span>
          </td>
          <td class="px-6 py-4">${description}</td>
          <td class="px-6 py-4">
            <div class="class="flex flex-col sm:flex-row gap-2 justify-center items-stretch">
              <button
                data-id="${id}"
                data-request-type="${request_type}"
                data-status="APPROVED"
                class="action-btn flex-1 px-3 py-2 text-xs sm:text-sm font-medium text-white bg-green-500 rounded hover:bg-green-600 transition cursor-pointer">
                Approve
              </button>
              <button
                data-id="${id}"
                data-request-type="${request_type}"
                data-status="REJECTED"
                class="action-btn flex-1 px-3 py-2 text-xs sm:text-sm font-medium text-white bg-red-500 rounded hover:bg-red-600 transition cursor-pointer">
                Reject
              </button>
            </div>
          </td>
          
        </tr>
      `;
    });
   
    for (let btn of document.querySelectorAll("#need-approvals tr .action-btn"))
      btn.addEventListener("click", () => performApprovalAction(btn.dataset));
  }

  async function init() {
    const leavesList = await fetchJSON("api/list-pending-leaves");
    const employeesList = await fetchJSON("api/list-pending-employees");
    const needApprovalsList = {};
    for (let leave of Object.values(leavesList))
      needApprovalsList[`leave_${leave.id}`] = {...leave, request_type: "leave", description: leave.reason};
    for (let employee of Object.values(employeesList)) {
      needApprovalsList[`registration_${employee.id}`] = {
        status: "PENDING",
        id: employee.id,
        created_at: employee.created_at,
        description: "User Registration",
        request_type: "registration",
        employee: {...employee, current_job: employee.jobs}, 
      };
    }
    Object.assign(needApprovals, needApprovalsList);
    displayNeedApprovalsList(needApprovalsList);
  }

  init();
})(window);
