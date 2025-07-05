<?php if(!isset($conn)){ 
  include 'db_connect.php'; 
  } ?>

<style>
  textarea {
    resize: none;
  }
</style>
<div class="col-12">
  <div class="card card-primary card-outline card-outline-tabs">
    <div class="card-header p-0 border-bottom-0">
      <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
        <li class="nav-item">
          <a
            class="nav-link active"
            id="custom-tabs-four-home-tab"
            data-toggle="pill"
            href="#custom-tabs-four-home"
            role="tab"
            aria-controls="custom-tabs-four-home"
            aria-selected="true"
          >
            ग्राम सभा</a
          >
        </li>
        <li class="nav-item">
          <a
            class="nav-link"
            id="custom-tabs-four-profile-tab"
            data-toggle="pill"
            href="#custom-tabs-four-profile"
            role="tab"
            aria-controls="custom-tabs-four-profile"
            aria-selected="false"
          >
            नगर पंचायत</a
          >
        </li>
        <li class="nav-item">
          <a
            class="nav-link"
            id="custom-tabs-four-messages-tab"
            data-toggle="pill"
            href="#custom-tabs-four-messages"
            role="tab"
            aria-controls="custom-tabs-four-messages"
            aria-selected="false"
          >
            नगर पालिका परिषद्</a
          >
        </li>
        <li class="nav-item">
          <a
            class="nav-link"
            id="custom-tabs-four-settings-tab"
            data-toggle="pill"
            href="#custom-tabs-four-settings"
            role="tab"
            aria-controls="custom-tabs-four-settings"
            aria-selected="false"
          >
           नगर निगम</a
          >
        </li>
      </ul>
    </div>
    <div class="card-body">
      <div class="tab-content" id="custom-tabs-four-tabContent">
		<!-- villages keliye -->
        <div
          class="tab-pane fade active show"
          id="custom-tabs-four-home"
          role="tabpanel"
          aria-labelledby="custom-tabs-four-home-tab"
        >
          <form action="" id="manage-dept">
            <input
              type="hidden"
              name="id"
              value="<?php echo isset($id) ? $id : '' ?>"
            />
            <div class="row">
              <div class="col-md-12">
                <div id="msg" class=""></div>

                <div class="row">
                  <div class="col-sm-6 form-group">
                    <label for="" class="control-label">राज्य</label>
                    <select
                      name="state"
                      id="state_id1"
                      class="form-control input-sm select2"
                    >
                      <option value=""></option>
                      <?php
						$states = $conn->query("SELECT name,id FROM state"); while($row =
                      $states->fetch_assoc()): ?>
                      <option value="<?php echo $row['id'] ?>">
                        <?php echo ucfirst($row['name']) ?>
                      </option>
                      <?php endwhile; ?>
                    </select>
                  </div>
                  <div class="col-sm-6 form-group">
                    <label for="" class="control-label">जनपद</label>
                    <select
                      name="district"
                      id="dis_id1"
                      class="form-control input-sm select2"
                      disabled
                    >
                      <option value=""></option>
                    </select>
                  </div>
                </div>

				<div class="row">
					<div class="col-sm-6 form-group">
					  <label for="" class="control-label nato">लोकसभा</label>
					  <select
						name="loksabha"
						id="loksabha1"
						class="form-control input-sm select2"
					  >
						<option value="0">All</option>
					  </select>
					</div>
					<div class="col-sm-6 form-group">
					  <label for="" class="control-label nato">विधानसभा</label>
					  <select
						name="vidhansabha"
						id="vidhansabha1"
						class="form-control input-sm select2"
					  >
						<option value="0">All</option>
					  </select>
					</div>
				</div>

                <div class="row for-village">
                  <div class="col-sm-6 form-group">
                    <label for="" class="control-label">ब्लाक</label>
                    <select
                      name="block"
                      id="block_id"
                      class="form-control input-sm select2"
                      disabled
                    >
                      <option value=""></option>
                    </select>
                  </div>
                  <div class="col-sm-6 form-group">
                    <label for="id" class="control-label">ग्राम पंचायत का नाम</label>
                    <input
                      type="text"
                      name="village"
                      id="branch"
                      class="form-control"
                      value="<?php echo isset($name) ? $name : '' ?>"
                      required
                    />
                  </div>
                </div>

              </div>
            </div>

			<div class="card-footer border-top border-info">
				<div class="d-flex w-100 justify-content-center align-items-center">
					<button class="btn btn-flat bg-gradient-primary mx-2" form="manage-dept">Save</button>
					<a class="btn btn-flat bg-gradient-secondary mx-2" href="./index.php">Cancel</a>
				</div>
			</div>
          </form>
        </div>
		<!-- nagar panchayat ke liye -->
        <div
          class="tab-pane fade"
          id="custom-tabs-four-profile"
          role="tabpanel"
          aria-labelledby="custom-tabs-four-profile-tab"
        >
          <form action="" id="manage-dept">
            <input
              type="hidden"
              name="id"
              value="<?php echo isset($id) ? $id : '' ?>"
            />
            <div class="row">
              <div class="col-md-12">
                <div id="msg" class=""></div>

                <div class="row">
                  <div class="col-sm-6 form-group">
                    <label for="" class="control-label">राज्य</label>
                    <select
                      name="state"
                      id="state_id2"
                      class="form-control input-sm select2"
                    >
                      <option value=""></option>
                      <?php
						$states = $conn->query("SELECT name,id FROM state"); while($row =
                      $states->fetch_assoc()): ?>
                      <option value="<?php echo $row['id'] ?>">
                        <?php echo ucfirst($row['name']) ?>
                      </option>
                      <?php endwhile; ?>
                    </select>
                  </div>
                  <div class="col-sm-6 form-group">
                    <label for="" class="control-label">जनपद</label>
                    <select
                      name="district"
                      id="dis_id2"
                      class="form-control input-sm select2"
                      disabled
                    >
                      <option value=""></option>
                    </select>
                  </div>
                </div>
				<div class="row">
					<div class="col-sm-6 form-group">
					  <label for="" class="control-label nato">लोकसभा</label>
					  <select
						name="loksabha"
						id="loksabha3"
						class="form-control input-sm select2"
					  >
						<option value="0">All</option>
					  </select>
					</div>
					<div class="col-sm-6 form-group">
					  <label for="" class="control-label nato">विधानसभा</label>
					  <select
						name="vidhansabha"
						id="vidhansabha3"
						class="form-control input-sm select2"
					  >
						<option value="0">All</option>
					  </select>
					</div>
				</div>

                <div class="row">
                  <div class="col-sm-6 form-group">
                    <label for="id" class="control-label"
                      >नगर पंचायत का नाम</label
                    >
                    <input
                      type="text"
                      name="branch"
                      id="branch"
                      class="form-control"
                      value="<?php echo isset($name) ? $name : '' ?>"
                      required
                    />
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
		<!-- nagar palika ke liye -->
        <div
          class="tab-pane fade"
          id="custom-tabs-four-messages"
          role="tabpanel"
          aria-labelledby="custom-tabs-four-messages-tab"
        >
          <form action="" id="manage-dept">
            <input
              type="hidden"
              name="id"
              value="<?php echo isset($id) ? $id : '' ?>"
            />
            <div class="row">
              <div class="col-md-12">
                <div id="msg" class=""></div>

                <div class="row">
                  <div class="col-sm-6 form-group">
                    <label for="" class="control-label">राज्य</label>
                    <select
                      name="state"
                      id="state_id3"
                      class="form-control input-sm select2"
                    >
                      <option value=""></option>
                      <?php
						$states = $conn->query("SELECT name,id FROM state"); while($row =
                      $states->fetch_assoc()): ?>
                      <option value="<?php echo $row['id'] ?>">
                        <?php echo ucfirst($row['name']) ?>
                      </option>
                      <?php endwhile; ?>
                    </select>
                  </div>
                  <div class="col-sm-6 form-group">
                    <label for="" class="control-label">जनपद</label>
                    <select
                      name="district"
                      id="dis_id3"
                      class="form-control input-sm select2"
                      disabled
                    >
                      <option value=""></option>
                    </select>
                  </div>
                </div>
				<div class="row">
					<div class="col-sm-6 form-group">
					  <label for="" class="control-label nato">लोकसभा</label>
					  <select
						name="loksabha"
						id="loksabha3"
						class="form-control input-sm select2"
					  >
						<option value="0">All</option>
					  </select>
					</div>
					<div class="col-sm-6 form-group">
					  <label for="" class="control-label nato">विधानसभा</label>
					  <select
						name="vidhansabha"
						id="vidhansabha3"
						class="form-control input-sm select2"
					  >
						<option value="0">All</option>
					  </select>
					</div>
				</div>

                <div class="row ">
                  <div class="col-sm-6 form-group">
                    <label for="id" class="control-label"
                      >नगर पालिका का नाम</label
                    >
                    <input
                      type="text"
                      name="branch"
                      id="branch"
                      class="form-control"
                      value="<?php echo isset($name) ? $name : '' ?>"
                      required
                    />
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
		<!-- nagar nigam ke liye -->
        <div
          class="tab-pane fade"
          id="custom-tabs-four-settings"
          role="tabpanel"
          aria-labelledby="custom-tabs-four-settings-tab"
        >
          जोड़ने की जरुरत नहीं हैं
        </div>
      </div>
    </div>
  </div>
</div>
<!-- <div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-body">
			<form action="" id="manage-dept">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <div class="row">
			
          <div class="col-md-12">
            <div id="msg" class=""></div>
  			
            <div class="row">
              <div class="col-sm-6 form-group">
                <label for="" class="control-label">राज्य</label>
                <select
                  name="state"
                  id="state_id"
                  class="form-control input-sm select2"
                >
                  <option value=""></option>
                  <?php
                    $states = $conn->query("SELECT name,id FROM state");
                  while($row = $states->fetch_assoc()): ?>
                  <option value="<?php echo $row['id'] ?>">
                    <?php echo ucfirst($row['name']) ?>
                  </option>
                  <?php endwhile; ?>
                </select>
              </div>
              <div class="col-sm-6 form-group">
                <label for="" class="control-label">जनपद</label>
                <select
                  name="district"
                  id="dis_id"
                  class="form-control input-sm select2"
                  disabled
                >
                  <option value=""></option>
                </select>
              </div>
            </div>
			<div class="row for-village">
              <div class="col-sm-6 form-group">
                <label for="" class="control-label">ब्लाक</label>
                <select
                  name="block"
                  id="block_id"
                  class="form-control input-sm select2"
                  disabled
                >
                  <option value=""></option>
                </select>
              </div>
              <div class="col-sm-6 form-group ">
                <label for="id" class="control-label">Name</label>
                <input type="text" name="branch" id="branch" class="form-control" value="<?php echo isset($name) ? $name : '' ?>" required>
              </div>
            </div>


          </div>
        </div>
      </form>
  	</div>
  	<div class="card-footer border-top border-info">
  		<div class="d-flex w-100 justify-content-center align-items-center">
  			<button class="btn btn-flat bg-gradient-primary mx-2" form="manage-dept">Save</button>
  			<a class="btn btn-flat bg-gradient-secondary mx-2" href="./index.php">Cancel</a>
  		</div>
  	</div>
	</div>
</div> -->
<script>
  $("#manage-dept").submit(function (e) {
    e.preventDefault();
    start_load();
    $.ajax({
      url: "ajax.php?action=save_village",
      data: new FormData($(this)[0]),
      cache: false,
      contentType: false,
      processData: false,
      method: "POST",
      type: "POST",
      success: function (resp) {
        if (resp == 1) {
          alert_toast("Data successfully saved", "success");
		  end_load();
        //   setTimeout(function () {
        //     location.href = "index.php?page=branch_list";
        //   }, 2000);
        }
      },
    });
  });

      // Define Mselector class
	function Mselector(obj) {
      $(obj.target).change(function () {
        var _id = $(this).val();

        if (_id) {
          $(obj.view).prop("disabled", false);

          $.ajax({
            url: "/fts/api/" + obj.url,
            type: "GET",
            data: { id: _id },
            dataType: "json",
            success: function (response) {
              $(obj.view).empty();
              $(obj.view).append('<option value=""></option>');

              $.each(response.res, function (index, a) {
                $(obj.view).append(
                  '<option value="' + a.id + '">' + a.name + "</option>"
                );
              });
            },
            error: function () {
              alert("Error retrieving data");
            },
          });
        } else {
          $(obj.view)
            .prop("disabled", true)
            .empty()
            .append('<option value=""></option>');
        }
      });
    }

    // Initialize Mselector instances
    new Mselector({
      target: "#state_id1",
      url: "get-districts.php",
      view: "#dis_id1",
    });
	new Mselector({
      target: "#state_id1",
      url: "get-loksabha.php",
      view: "#loksabha1",
    });
	new Mselector({
      target: "#loksabha1",
      url: "get-vidhansabha.php",
      view: "#vidhansabha1",
    });
	new Mselector({
      target: "#dis_id1",
      url: "get-blocks.php",
      view: "#block_id",
    });
</script>
