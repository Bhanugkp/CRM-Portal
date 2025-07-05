<form action="upload.php" method="post" enctype="multipart/form-data">
  <div class="card-body">
    <div class="form-group">
      <label for="exampleInputFile">Insert Members</label>
      <div class="input-group">
        <div class="custom-file">
          <input type="file" name="excelFile" accept=".xls, .xlsx" class="custom-file-input" id="exampleInputFile" required />
          <label class="custom-file-label" for="exampleInputFile">Choose Excel file</label>
        </div>
      </div>
    </div>
  </div>

  <div class="card-footer">
    <button type="submit" class="btn btn-primary">Submit</button>
  </div>
</form>

<script>
  // Update label with file name after selection
  document.querySelector('.custom-file-input').addEventListener('change', function (e) {
    var fileName = document.getElementById("exampleInputFile").files[0].name;
    var nextSibling = e.target.nextElementSibling;
    nextSibling.innerText = fileName;
  });

  // Validate Excel file type
  document.getElementById('exampleInputFile').addEventListener('change', function() {
    var fileInput = this;
    var filePath = fileInput.value;
    var allowedExtensions = /(\.xls|\.xlsx)$/i;

    if (!allowedExtensions.exec(filePath)) {
      alert('Please upload a valid Excel file (with .xls or .xlsx extension)');
      fileInput.value = '';
      return false;
    }
  });
</script>
