<div class="card card-outline card-primary">
  <form action="upload.php" method="post" enctype="multipart/form-data">
    <div class="card-body">
      <div class="d-block float-right">
        <a href="assets/excel-format.xlsx">Download Format</a>
      </div>
      <div class="form-group">
        <label for="exampleInputFile">Insert Members</label>
        <div class="input-group">
          <div class="custom-file">
            <input
              type="file"
              name="excelFile"
              accept=".xls, .xlsx"
              class="custom-file-input"
              id="exampleInputFile"
              required
            />
            <label class="custom-file-label" for="exampleInputFile"
              >Choose Excel file</label
            >
          </div>
        </div>
      </div>
    </div>
    <div class="card-footer border-top border-info">
      <div class="d-flex w-100 justify-content-center align-items-center">
        <button class="btn btn-flat bg-gradient-primary mx-2" type="submit">
          Upload
        </button>
        <a
          class="btn btn-flat bg-gradient-secondary mx-2"
          href="./index.php?page=home"
          >Cancel</a
        >
      </div>
    </div>
  </form>
</div>

<script>
  // Update label with file name after selection
  document
    .querySelector(".custom-file-input")
    .addEventListener("change", function (e) {
      var fileName = document.getElementById("exampleInputFile").files[0].name;
      var nextSibling = e.target.nextElementSibling;
      nextSibling.innerText = fileName;
    });

  // Validate Excel file type
  document
    .getElementById("exampleInputFile")
    .addEventListener("change", function () {
      var fileInput = this;
      var filePath = fileInput.value;
      var allowedExtensions = /(\.xls|\.xlsx)$/i;

      if (!allowedExtensions.exec(filePath)) {
        alert(
          "Please upload a valid Excel file (with .xls or .xlsx extension)"
        );
        fileInput.value = "";
        return false;
      }
    });
</script>
