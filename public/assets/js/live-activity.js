(function(global) {
  function getInputDateWithNoTime(date) {
    const newDate = new Date(date);
    newDate.setHours(0,0,0,0);
    return newDate;
  }

  function formatLocalDate(date) {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, "0");
    const d = String(date.getDate()).padStart(2, "0");
    return `${y}-${m}-${d}`;
  }

  async function fetchJSON(url, option) {
    if (option?.default) {
      try {
        const response = await fetch(url);
        return await response.json();
      } catch (e) {return option.default;}
    }

    const response = await fetch(url);
    return await response.json();
  }  

  function simpleTimeAgo(unixTime) {
    const seconds = Math.floor(Date.now() / 1000 - unixTime);
    const units = [
      { label: "year", seconds: 31536000 },
      { label: "month", seconds: 2592000 },
      { label: "day", seconds: 86400 },
      { label: "hour", seconds: 3600 },
      { label: "minute", seconds: 60 },
      { label: "second", seconds: 1 }
    ];

    for (const u of units) {
      const interval = Math.floor(seconds / u.seconds);
      if (interval >= 1)
        return `${interval} ${u.label}${interval > 1 ? "s" : ""} ago`;
    }
    return "just now";
  }

  function getTotalAttendancePerDay({employees, departments, attendancesList, leavesList}) {
    const absentsPerDay = [0,0,0,0,0,0,0]; // included the weekends
    const presentsPerDay = [0,0,0,0,0,0,0]; 
    const leavesPerDay = [0,0,0,0,0,0,0]; 

    for (let employee of Object.values(employees)) {
      const department = departments[employee.jobs.department_id];
      const attendances = attendancesList[employee.biometric_id];
      const leaves = leavesList[employee.id] ?? [];

      const startDate = new Date(employee.jobs.appointment_date * 1000);
      const endDate = new Date();
      endDate.setDate(endDate.getDate() + 1); // we end the loop until the next day after the end date to include the end date.
      let startLocalDate;

      while ((startLocalDate = formatLocalDate(startDate)) !== formatLocalDate(endDate)) {
         if (startDate.getTime() > endDate.getTime())
           break;
         if (department.dayoffs.includes((startDate.getDay()))) {
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

        startDate.setDate(startDate.getDate() + 1);  
      }
    }
    return {
      absentsPerDay,
      presentsPerDay,
      leavesPerDay,
    };
  }

  function displayLiveActivity(notifications) {
    document.querySelector("#notifications-list").innerHTML = Object.values(notifications)
    .sort((a,b) => b.created_at - a.created_at)
    .slice(0, 5)
    .map(notification => {
      const {message,created_at,user} = notification;
      return `
        <div
          class="flex items-center space-x-4 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors duration-200">
          <div class="w-3 h-3 rounded-full bg-green-500"></div>
          <div class="flex-1">
              <p class="font-medium text-gray-900">${message}</p>
              <p class="text-sm text-gray-600">
                  by ${user.username} (${user.role}) • ${simpleTimeAgo(created_at)}
              </p>
          </div>
          <span class="px-2 py-1 border border-gray-300 rounded text-sm capitalize bg-green-400/10 text-green-700 font-medium">
            COMPLETED
          </span>
        </div>
     `
    }).join("\n");
  }


  async function init() {
    const employeesList = await fetchJSON("api/list-employees", {default: {}});    
    const departmentsList = await fetchJSON("api/list-departments");    
    const attendancesList = await fetchJSON("api/list-attendances", {default: {}});    
    const leavesList = await fetchJSON("api/list-leaves", {default: {}});    
    const documentsList = await fetchJSON("api/list-documents");
    const notificationsList = await fetchJSON("api/list-notifications", {default: {}});
    const totalPendingEmployees = await fetchJSON("api/count-pending-employees");

    const employeesAttendancesDict = {};
    const employeeLeavesDictUNIX = {};
    for (let attendance of Object.values(attendancesList)) {
      if (employeesAttendancesDict[attendance.biometric_id])
        employeesAttendancesDict[attendance.biometric_id][attendance.attended_date] = attendance;
      else 
        employeesAttendancesDict[attendance.biometric_id] = {
          [attendance.attended_date]: attendance
        };
    }

   for (let leave of Object.values(leavesList)) {
      if (leave.status != "APPROVED")
        continue;
      const dates = [];
      const endDate = getInputDateWithNoTime(leave.end_date*1000).getTime()/1000;
      // add a day from start_date up to end_date
      for (let i = getInputDateWithNoTime(leave.start_date * 1000).getTime()/1000; i < endDate; i+=86400)
        dates.push(i);
      dates.push(endDate);
      if (employeeLeavesDictUNIX[leave.employee_id])
         employeeLeavesDictUNIX[leave.employee_id] = [...(new Set([...employeeLeavesDictUNIX[leave.employee_id],...dates]))];
      else 
         employeeLeavesDictUNIX[leave.employee_id] = dates;
    }

    const {absentsPerDay, leavesPerDay, presentsPerDay} = getTotalAttendancePerDay({
      employees: employeesList,
      departments: departmentsList,
      attendancesList: employeesAttendancesDict,
      leavesList: employeeLeavesDictUNIX,
    });

    const totalPresents = presentsPerDay.reduce((a,b) => a+b);
    const totalWorkingDays = totalPresents + absentsPerDay.reduce((a,b) => a+b) + leavesPerDay.reduce((a,b) => a+b);
    const performanceScore =  totalPresents / (Object.values(employeesList).length * totalWorkingDays) * 100;
    document.querySelector("#performance-score").textContent = `${performanceScore.toFixed(2)}%`;
    document.querySelector("#staffs-total").textContent = Object.values(employeesList).length;
    document.querySelector("#pendings-total").textContent = Object.values(leavesList).filter(leave => leave.status == "PENDING").length + totalPendingEmployees.total;
    document.querySelector("#documents-total").textContent = Object.values(documentsList).length;

    displayLiveActivity(notificationsList);
  }
  init();
})(window);
