(function(global) {
    const fileInput = document.getElementById('fileInput');
    const uploadBtn = document.getElementById('uploadBtn');
    const dropzone = document.getElementById('dropzone');
    const message = document.getElementById('message');


    uploadBtn.addEventListener('click', () => fileInput.click());

    fileInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        handleFile(e.target.files[0]);
    }
    });


    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('border-blue-400', 'bg-blue-50');
    });
    dropzone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropzone.classList.remove('border-blue-400', 'bg-blue-50');
    });
    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('border-blue-400', 'bg-blue-50');
        if (e.dataTransfer.files.length > 0) {
            handleFile(e.dataTransfer.files[0]);
        }
    });

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
    
    async function uploadCSVAttendanceData(file) {
      const text = await file.text(); 
      const attendances = {};
      const row = text.trim().split("\n").slice(1);
      let csvString = "";

      for (let log of row) {
        const [,,biometric_id,,,,datetime] = log.trim().split("\t");
        const [date,time] = datetime.split(/\s+/);
        const key = `${Number(biometric_id)} ${date}`;
        if (attendances[key])
          attendances[key].push(time);
        else
          attendances[key] = [time];
      }

      for (let [key,value] of Object.entries(attendances)) {
        const [biometric_id,date] = key.split(" ");
        const [clock_in,clock_out] = value;
        csvString += `${biometric_id},${date},${clock_in}${clock_out ? "," + clock_out : ""}\n`;
      }

      const formData = new FormData();
      const csvFile = new Blob([csvString],{type:"text/csv;charset=utf-8"}); 
      formData.append("file", csvFile);

      const response = await fetch("api/employee_attendances/csv_insert", {
        method: "POST",
        body: formData
      });
      return response;
    }

    async function handleFile(file) {
      message.innerHTML = '';
      if (!file.name.toLowerCase().endsWith('.txt')) {
          message.innerHTML =
          `<div class=\"text-sm text-red-700 bg-red-50 border-l-4 border-red-400 rounded p-3\">Please upload a .txt file only.</div>`;
          return;
      }
      message.innerHTML =
          `<div class='text-sm text-green-700 bg-green-50 border-l-4 border-green-400 rounded p-3'>${file.name} is a valid TxT file.</div>`;
    }

    document.querySelector("#csv-insert").addEventListener("submit", async function(e) {
      e.preventDefault();
      const formData = new FormData(e.target);
      const file = formData.get("file");
      if (!file)
        return;
      const response = await uploadCSVAttendanceData(file);
      if (response.ok) {
        const json = await response.json();
        await Swal.fire({
          icon: 'success',
          title: 'Attendance Inserted!',
          text: `inserted: ${json.success.length}, duplicates: ${json.duplicates.length}, fails: ${json.fails.length}`,
        });
        await createNotification({
          type: "attendance-upload",
          message: "Attendance Uploaded!",
          data: {},
        });
      } else {
        await Swal.fire({
          icon: 'error',
          title: 'Insertion Failed!',
          text: `something went wrong...`,
        });
      }
    });
}(window));
