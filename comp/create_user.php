<?php if(!isset($conn)){ 
  include 'db_connect.php'; 
  } ?>

<div class="col-lg-6 mx-auto">
  <div class="card card-outline card-primary">
    <div class="card-body">
      <form action="index.php?page=new_user" method="post" id="manage-dept">
        <input
          type="hidden"
          name="id"
          value="<?php echo isset($id) ? $id : '' ?>"
        />
        <div class="row">
          <div class="col-md-12">
            <div id="msg" class=""></div>

            <div class="row">
              <div class="col-sm-12 form-group">
                <label for="id" class="control-label">Enter Mobile No.</label>
                <input
                  type="phone"
                  name="phone"
                  id="phone"
                  class="form-control"
                  value=""
                  required
                />
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
    <div class="card-footer border-top border-info">
      <div class="d-flex w-100 justify-content-center align-items-center">
        <button
          class="btn btn-flat bg-gradient-primary mx-2"
          form="manage-dept"
        >
          Next
        </button>
        <a
          class="btn btn-flat bg-gradient-secondary mx-2"
          href="./index.php?page=home"
          >Cancel</a
        >
      </div>
    </div>
  </div>
</div>

<script>
  // $("#manage-dept").submit(function (e) {
  //   e.preventDefault();
  //   start_load();
  //   $.ajax({
  //     url: "ajax.php?action=create_user",
  //     data: new FormData($(this)[0]),
  //     cache: false,
  //     contentType: false,
  //     processData: false,
  //     method: "POST",
  //     type: "POST",
  //     success: function (resp) {
  //       console.log(resp)
  //       // if (resp == 1) {
  //       //   alert_toast("Data successfully saved", "success");
  //       //   setTimeout(function () {
  //       //     location.href = "index.php?page=branch_list";
  //       //   }, 2000);
  //       // }
  //       end_load();
  //     },
  //   });
  // });
</script>
