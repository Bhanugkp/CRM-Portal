<?php 
if(!isset($conn)) { 
  include 'db_connect.php'; 
} 

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<style>
  textarea {
    resize: none;
  }
  .img-section {
    margin-top: 15px;
  }
  .custom-file-label::after {
    content: "Browse";
  }
  .required-field::after {
    content: " *";
    color: red;
  }
</style>
<div class="col-lg-12">
  <div class="card card-outline card-primary">
    <div class="card-header">
      <h3 class="card-title">Member Information</h3>
    </div>
    <div class="card-body">
      <form id="manage-member" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <div class="row">
          <div class="col-md-12">
            <div id="msg" class=""></div>

            <!-- Basic Information -->
            <div class="row">
              <div class="col-sm-6 form-group">
                <label for="fname" class="control-label required-field">प्रथम नाम</label>
                <input type="text" name="fname" id="fname" class="form-control" 
                       value="<?php echo isset($fname) ? $fname : '' ?>" required>
              </div>
              <div class="col-sm-6 form-group">
                <label for="lname" class="control-label required-field">अंतिम नाम</label>
                <input type="text" name="lname" id="lname" class="form-control" 
                       value="<?php echo isset($lname) ? $lname : '' ?>" required>
              </div>
            </div>

            <!-- Contact Information -->
            <div class="row">
              <div class="col-sm-6 form-group">
                <label for="phone" class="control-label required-field">मोबाइल न०</label>
                <input type="tel" maxlength="10" name="phone" id="phone" class="form-control" 
                       value="<?php echo isset($phone) ? $phone : '' ?>"
                       pattern="[0-9]{10}" title="Please enter 10 digit mobile number"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
                <small class="text-muted">10 digits only</small>
              </div>
              <div class="col-sm-6 form-group">
                <label for="email" class="control-label">ईमेल</label>
                <input type="email" name="email" id="email" class="form-control" 
                       value="<?php echo isset($email) ? $email : '' ?>">
              </div>
            </div>

            <!-- Personal Details -->
            <div class="row">
              <div class="col-sm-6 form-group">
                <label for="qualification" class="control-label">शैक्षिक योग्यता</label>
                <select name="qualification" id="qualification" class="form-control select2">
                  <option value="">Select Qualification</option>
                  <?php
                  $auth = $conn->query("SELECT name,id FROM qualification ORDER BY id ASC"); 
                  while($row = $auth->fetch_assoc()): ?>
                    <option value="<?php echo $row['id'] ?>" 
                      <?php echo isset($education_id) && $education_id == $row['id'] ? "selected" : '' ?>>
                      <?php echo $row['name']; ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>
              <div class="col-sm-6 form-group">
                <label for="gender" class="control-label required-field">लिंग</label>
                <select name="gender" id="gender" class="form-control select2" required>
                  <option value="">Select Gender</option>
                  <option value="1" <?php echo isset($gender) && $gender == '1' ? 'selected' : '' ?>>पुरुष</option>
                  <option value="2" <?php echo isset($gender) && $gender == '2' ? 'selected' : '' ?>>महिला</option>
                  <option value="3" <?php echo isset($gender) && $gender == '3' ? 'selected' : '' ?>>अन्य</option>
                </select>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-6 form-group">
                <label for="marital_status" class="control-label">वैवाहिक स्थिति</label>
                <select name="marital_status" id="marital_status" class="form-control select2">
                  <option value="">Select Marital Status</option>
                  <option value="1" <?php echo isset($marital_status) && $marital_status == '1' ? 'selected' : '' ?>>अविवाहित</option>
                  <option value="2" <?php echo isset($marital_status) && $marital_status == '2' ? 'selected' : '' ?>>विवाहित</option>
                  <option value="3" <?php echo isset($marital_status) && $marital_status == '3' ? 'selected' : '' ?>>विधवा/विधुर</option>
                </select>
              </div>
              <div class="col-sm-6 form-group">
                <label for="dob" class="control-label required-field">जन्म तिथि</label>
                <div class="input-group date" id="dobDatepicker">
                  <input type="text" id="dob" name="dob" class="form-control" 
                         value="<?php echo isset($dob) ? DateTime::createFromFormat('Y-m-d', $dob)->format('d/m/Y') : '' ?>" 
                         placeholder="DD/MM/YYYY" readonly required>
                  <div class="input-group-append">
                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Address Information -->
            <div class="row">
              <div class="col-sm-4 form-group">
                <label for="state_id" class="control-label">राज्य</label>
                <select name="state" id="state_id" class="form-control select2">
                  <option value="">Select State</option>
                  <?php
                  $states = $conn->query("SELECT name,id FROM states WHERE id != 3");
                  while($row = $states->fetch_assoc()): ?>
                    <option value="<?php echo $row['id'] ?>" 
                      <?php echo isset($state_id) && $state_id == $row['id'] ? "selected" : '' ?>>
                      <?php echo $row['name'] ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>
              <div class="col-sm-4 form-group">
                <label for="dis_id" class="control-label">जनपद</label>
                <select name="district" id="dis_id" class="form-control select2" 
                  <?php echo isset($district_id) ? '' : 'disabled' ?>>
                  <option value="">Select District</option>
                  <?php if(isset($district_id)): ?>
                    <?php $districts = $conn->query("SELECT name,id FROM districts WHERE state_id = ".$state_id);
                    while($row = $districts->fetch_assoc()): ?>
                      <option value="<?php echo $row['id'] ?>" 
                        <?php echo isset($district_id) && $district_id == $row['id'] ? "selected" : '' ?>>
                        <?php echo $row['name'] ?>
                      </option>
                    <?php endwhile; ?>
                  <?php endif; ?>
                </select>
              </div>
              <div class="col-sm-4 form-group">
                <label for="block_id" class="control-label">ब्लाक</label>
                <select name="block" id="block_id" class="form-control select2" 
                  <?php echo isset($block_id) ? '' : 'disabled' ?>>
                  <option value="">Select Block</option>
                  <?php if(isset($block_id)): ?>
                    <?php $blocks = $conn->query("SELECT name, id FROM blocks WHERE district_id = ".$district_id);
                    while($row = $blocks->fetch_assoc()): ?>
                      <option value="<?php echo $row['id'] ?>" 
                        <?php echo isset($block_id) && $block_id == $row['id'] ? "selected" : '' ?>>
                        <?php echo $row['name'] ?>
                      </option>
                    <?php endwhile; ?>
                  <?php endif; ?>
                </select>
              </div>
            </div>

            <!-- Political Information -->
            <div class="row">
              <div class="col-sm-6 form-group">
                <label for="loksabha_id" class="control-label">लोकसभा</label>
                <select name="loksabha" id="loksabha_id" class="form-control select2">
                  <option value="">Select Lok Sabha</option>
                  <?php
                  $loksabha = $conn->query("SELECT name,id FROM loksabha ORDER BY name ASC");
                  while($loksabha_row = $loksabha->fetch_assoc()): ?>
                    <option value="<?php echo $loksabha_row['id'] ?>" 
                      <?php echo isset($loksabha_id) && $loksabha_id == $loksabha_row['id'] ? "selected" : '' ?>>
                      <?php echo $loksabha_row['name'] ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>
              <div class="col-sm-6 form-group">
                <label for="vidhansabha_id" class="control-label">विधानसभा</label>
                <select name="vidhansabha" id="vidhansabha_id" class="form-control select2" 
                  <?php echo isset($vidhansabha_id) ? '' : 'disabled' ?>>
                  <option value="">Select Vidhan Sabha</option>
                  <?php if(isset($vidhansabha_id)): ?>
                    <?php $vidhansabha = $conn->query("SELECT name,id FROM vidhansabha");
                    while($row = $vidhansabha->fetch_assoc()): ?>
                      <option value="<?php echo $row['id'] ?>" 
                        <?php echo isset($vidhansabha_id) && $vidhansabha_id == $row['id'] ? "selected" : '' ?>>
                        <?php echo $row['name'] ?>
                      </option>
                    <?php endwhile; ?>
                  <?php endif; ?>
                </select>
              </div>
            </div>

            <!-- Locality Information -->
            <div class="row">
              <div class="col-sm-6 form-group">
                <label for="locality" class="control-label">स्थानीयता</label>
                <select name="locality" id="locality" class="form-control select2">
                  <option value="">Select Locality Type</option>
                  <option value="1" <?php echo isset($locality) && $locality == 1 ? 'selected' : '' ?>>शहरी</option>
                  <option value="2" <?php echo isset($locality) && $locality == 2 ? 'selected' : '' ?>>ग्रामीण</option>
                </select>
              </div>
            </div>

            <!-- Village Information (shown when locality is rural) -->
            <div class="row for-village" style="display:<?php echo isset($locality) && $locality == 1 ? 'none' : 'block' ?>">
              <div class="col-sm-6 form-group">
                <label for="village" class="control-label">ग्राम पंचायत</label>
                <input type="text" id="village" class="form-control" name="village"
                       value="<?php echo isset($village) ? $village : '' ?>">
              </div>
            </div>
  
            <!-- Urban Information (shown when locality is urban) -->
            <div class="row for-city" style="display:<?php echo isset($locality) && $locality == 2 ? 'none' : 'block' ?>">
              <div class="col-sm-4 form-group">
                <label for="nnigam" class="control-label">निकाय</label>
                <select name="urban_bodies" id="nnigam" class="form-control select2">
                  <option value="">Select Urban Body</option>
                  <option value="1" <?php echo isset($urban_body) && $urban_body == 1 ? 'selected' : '' ?>>नगर निगम</option>
                  <option value="2" <?php echo isset($urban_body) && $urban_body == 2 ? 'selected' : '' ?>>नगर पालिका परिषद्</option>
                  <option value="3" <?php echo isset($urban_body) && $urban_body == 3 ? 'selected' : '' ?>>नगर पंचायत</option>
                </select>
              </div>
              <div class="col-sm-4 form-group">
                <label for="bodies-name" class="control-label">निकाय का नाम</label>
                <input type="text" name="bodies_name" id="bodies-name" class="form-control"
                       value="<?php echo isset($bodies_name) ? $bodies_name : '' ?>">
              </div>
              <div class="col-sm-4 form-group">
                <label for="vardno" class="control-label">वार्ड संख्या</label>
                <input type="number" name="vardno" id="vardno" class="form-control"
                       value="<?php echo isset($vardno) ? $vardno : '' ?>">
              </div>
            </div>

            <!-- Organizational Information -->
            <div class="row">
              <div class="col-sm-6 form-group">
                <label for="authority" class="control-label">पद</label>
                <select name="authority" id="authority" class="form-control select2">
                  <option value="">Select Role</option>
                  <?php
                  $org = $conn->query("SELECT name, id FROM role WHERE id > ".$_SESSION['login_role_id']." AND id != 1");
                  while($row = $org->fetch_assoc()): ?>
                    <option value="<?php echo $row['id'] ?>" 
                      <?php echo isset($role_id) && $role_id == $row['id'] ? 'selected' : '' ?>>
                      <?php echo $row['name']; ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>
              <div class="col-sm-6 form-group">
                <label for="organization" class="control-label">संगठन</label>
                <select name="organization" id="organization" class="form-control select2">
                  <option value="">Select Organization</option>
                  <?php
                  $org = $conn->query("SELECT name, id FROM organization");
                  while($org_row = $org->fetch_assoc()): ?>
                    <option value="<?php echo $org_row['id'] ?>" 
                      <?php echo isset($organization_id) && $organization_id == $org_row['id'] ? 'selected' : '' ?>>
                      <?php echo $org_row['name']; ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>
            </div>

            <!-- Photo Upload -->
            <div class="row">
              <div class="col-sm-6 form-group">
                <label for="photo" class="control-label">फोटो अपलोड करें</label>
                <div class="custom-file">
                  <input type="file" name="photo" id="photo" class="custom-file-input" accept="image/*">
                  <label class="custom-file-label" for="photo">Choose file</label>
                </div>
                <small class="text-muted">Max size: 2MB (JPEG, PNG)</small>
              </div>
              <div class="col-sm-6 form-group text-center">
                <img src="uploads/members_photos/<?php echo isset($photo) ? $photo : 'default.png' ?>" 
                     id="photoPreview" class="img-thumbnail" style="width:100px; height:100px; object-fit:cover;">
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
    <div class="card-footer border-top border-info">
      <div class="d-flex w-100 justify-content-center align-items-center">
        <button class="btn btn-flat bg-gradient-primary mx-2" form="manage-member">
          Save
        </button>
        <a class="btn btn-flat bg-gradient-secondary mx-2" href="./index.php?page=home">
          Cancel
        </a>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  // Initialize Select2
  $('.select2').select2({
    theme: 'bootstrap4',
    width: '100%'
  });

  // Initialize datepicker
  $('#dobDatepicker').datepicker({
    autoclose: true,
    todayHighlight: true,
    format: 'dd/mm/yyyy',
    endDate: '0d',
    startDate: '01/01/1930'
  });

  // Phone number validation
  $('#phone').on('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
  });

  // Photo preview
  $('#photo').change(function(e) {
    const file = e.target.files[0];
    const label = $(this).next('.custom-file-label');
    
    if (file) {
      label.text(file.name);
      
      if (file.type.match('image.*')) {
        const reader = new FileReader();
        reader.onload = function(e) {
          $('#photoPreview').attr('src', e.target.result);
        };
        reader.readAsDataURL(file);
      }
    } else {
      label.text('Choose file');
    }
  });

  // Locality type change handler
  $('#locality').change(function() {
    if ($(this).val() == '1') {
      $('.for-village').hide();
      $('.for-city').show();
    } else if ($(this).val() == '2') {
      $('.for-village').show();
      $('.for-city').hide();
    } else {
      $('.for-village').hide();
      $('.for-city').hide();
    }
  });

  // State change handler
  $('#state_id').change(function() {
    const stateId = $(this).val();
    if (stateId) {
      $.ajax({
        url: 'api/get-districts.php',
        type: 'GET',
        data: { id: stateId },
        dataType: 'json',
        success: function(response) {
          $('#dis_id').empty().append('<option value="">Select District</option>');
          $.each(response.res, function(key, value) {
            $('#dis_id').append(`<option value="${value.id}">${value.name}</option>`);
          });
          $('#dis_id').prop('disabled', false);
        },
        error: function() {
          alert('Error loading districts');
        }
      });
    } else {
      $('#dis_id').empty().append('<option value="">Select District</option>').prop('disabled', true);
      $('#block_id').empty().append('<option value="">Select Block</option>').prop('disabled', true);
    }
  });

  // District change handler
  $('#dis_id').change(function() {
    const districtId = $(this).val();
    if (districtId) {
      $.ajax({
        url: 'api/get-blocks.php',
        type: 'GET',
        data: { id: districtId },
        dataType: 'json',
        success: function(response) {
          $('#block_id').empty().append('<option value="">Select Block</option>');
          $.each(response.res, function(key, value) {
            $('#block_id').append(`<option value="${value.id}">${value.name}</option>`);
          });
          $('#block_id').prop('disabled', false);
        },
        error: function() {
          alert('Error loading blocks');
        }
      });
    } else {
      $('#block_id').empty().append('<option value="">Select Block</option>').prop('disabled', true);
    }
  });

  // Lok Sabha change handler
  $('#loksabha_id').change(function() {
    const loksabhaId = $(this).val();
    if (loksabhaId) {
      $.ajax({
        url: 'api/get-vidhansabha.php',
        type: 'GET',
        data: { id: loksabhaId },
        dataType: 'json',
        success: function(response) {
          $('#vidhansabha_id').empty().append('<option value="">Select Vidhan Sabha</option>');
          $.each(response.res, function(key, value) {
            $('#vidhansabha_id').append(`<option value="${value.id}">${value.name}</option>`);
          });
          $('#vidhansabha_id').prop('disabled', false);
        },
        error: function() {
          alert('Error loading Vidhan Sabha constituencies');
        }
      });
    } else {
      $('#vidhansabha_id').empty().append('<option value="">Select Vidhan Sabha</option>').prop('disabled', true);
    }
  });

  // Form submission
  $('#manage-member').submit(function(e) {
    e.preventDefault();
    start_load();
    
    const formData = new FormData(this);
    
    $.ajax({
      url: 'ajax.php?action=save_member',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function(resp) {
      console.log(resp)
        if (resp.status == 'success') {
          alert_toast(resp.message, "success");
          setTimeout(function() {
            location.href = "index.php?page=member_list";
          }, 1500);
        } else {
          $('#msg').html(`<div class="alert alert-danger">${resp.message}</div>`);
          end_load();
        }
      },
      error: function(xhr) {
        alert_toast("An error occurred while saving data", "error");
        end_load();
      }
    });
  });
});

function start_load() {
  $('body').append('<div class="overlay"><i class="fas fa-spinner fa-spin"></i></div>');
}

function end_load() {
  $('.overlay').remove();
}
</script>