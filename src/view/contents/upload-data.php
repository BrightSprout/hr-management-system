<div class="max-w-5xl mx-auto p-6">
    <div class="bg-white rounded-2xl shadow-md p-6">
        <h1 class="text-2xl font-semibold">Upload Attendance CSV</h1>
        <p class="mt-2 text-sm text-gray-500">
            Use this tool to upload daily attendance data.
        </p>
        <div class="mt-4 text-sm text-yellow-700 bg-yellow-50 border-l-4 border-yellow-400 rounded p-3">
            <strong>Note:</strong> Please upload attendance data daily. To keep a regular schedule to avoid missing
            records.
        </div>

        <form id="csv-insert">
          <div id="dropzone" class="mt-6 border-2 border-dashed rounded-lg p-6 text-center bg-gray-50">
              
              <input id="fileInput" type="file" accept=".txt" name="file" class="hidden" />

              <button id="uploadBtn" type="button" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Upload
                  TxT</button>

              <p class="mt-2 text-sm text-gray-500">
                  Drag &amp; drop a TxT file here or click “Upload Txt"
              </p>
          </div>
          <div id="message" class="mt-4"></div>
          <div>
            <button class="w-full hover:cursor-pointer px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Submit</button> 
          <div>
        </form>
    </div>
</div>
