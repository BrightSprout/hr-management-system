(function (global) {
  
  function displayStaffs(employees) {
    document.querySelector("#staff-list").innerHTML = Object.values(employees).map(employee => {
      const {id, first_name, middle_name, last_name, phone_no, jobs} = employee;
      return `
        <div
          data-navfor="?page=view-staff/documents&employee_id=${id}"
          class="bg-gray-50 border border-gray-200 rounded-2xl shadow-sm p-5 hover:shadow-md hover:bg-blue-50 transition-all duration-300 cursor-pointer">
          <div class="flex flex-col space-y-2">
            <h3 class="text-lg font-semibold text-gray-800">${first_name} ${middle_name} ${last_name}</h3>
            <p class="text-sm text-gray-600">Position: <span class="font-medium text-gray-700">${jobs.position.split("_").map(word => word[0] + word.slice(1).toLowerCase()).join(" ")}</span></p>
            <p class="text-sm text-gray-600">Phone: <span class="font-medium text-gray-700">${phone_no}</span></p>
          </div>
        </div>
      `;
    }).join("\n");

    for (let div of document.querySelectorAll("#staff-list > div")) {
      const url = div.dataset.navfor;
      div.addEventListener("click", () => navigatePage(url)); 
    }
  }

  async function init() {
    const response = await fetch("api/list-employees");
    const employees = await response.json();

    displayStaffs(employees);

    document.querySelector("#search-staff").addEventListener("input", function() {
      const search = this.value.toLowerCase();
      displayStaffs(Object.fromEntries(Object.values(employees).filter(employee => {
        const {first_name,middle_name,last_name} = employee;
        return first_name.toLowerCase().startsWith(search) || middle_name.toLowerCase().startsWith(search) || last_name.toLowerCase().startsWith(search);
      }).map(employee => ([employee.id,employee]))));
    });
  }

  init();
})(window);
