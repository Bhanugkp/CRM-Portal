<?php
// Database connection and session check
if (!isset($conn)) { 
    include 'db_connect.php'; 
}


// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
// Initialize variables
$memberData = [];
$error = '';

// Process phone number lookup
if (isset($_POST['phone'])) {
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_NUMBER_INT);
    
    if ($phone && strlen($phone) >= 10) {
        try {
            $stmt = $conn->prepare('SELECT id, fname, lname, email, phone, role_id, organization_id FROM members WHERE phone = ?');
            if ($stmt === false) {
                throw new Exception("SQL Prepare failed: " . $conn->error);
            }
            
            $stmt->bind_param('s', $phone);
            if (!$stmt->execute()) {
                throw new Exception("SQL Execute failed: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $memberData = $result->fetch_assoc();
                // Sanitize all output data
                foreach ($memberData as $key => $value) {
                    $memberData[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                }
            } else {
                $error = "No member found with that phone number.";
            }
            $stmt->close();
        } catch (Exception $e) {
            error_log($e->getMessage());
            $error = "An error occurred while processing your request.";
        }
    } else {
        $error = "Please enter a valid 10-digit phone number.";
    }
} else {
    $error = "Phone number is required.";
}
?>

<div class="col-lg-12">
  <div class="card card-outline card-primary">
    <div class="card-body">
      <form id="manage-user" method="post" autocomplete="off">
        <input type="hidden" name="id" value="<?= $memberData['id'] ?? '' ?>">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        
        <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <div id="msg" class="mb-3"></div>

        <div class="row">
          <div class="col-md-12">
            <div class="row">
              <div class="col-sm-6 form-group">
                <label class="control-label">प्रथम नाम</label>
                <input type="text" name="fname" class="form-control form-control-sm" 
                  value="<?= $memberData['fname'] ?? '' ?>" required>
              </div>
              <div class="col-sm-6 form-group">
                <label class="control-label">अंतिम नाम</label>
                <input type="text" name="lname" class="form-control form-control-sm" 
                  value="<?= $memberData['lname'] ?? '' ?>" required>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-6 form-group">
                <label class="control-label">ईमेल</label>
                <input type="email" name="email" class="form-control form-control-sm" 
                  value="<?= $memberData['email'] ?? '' ?>" required>
              </div>
              <div class="col-sm-6 form-group">
                <label class="control-label">मोबाइल न०</label>
                <input type="tel" name="phone" class="form-control form-control-sm" 
                  value="<?= $memberData['phone'] ?? '' ?>" required minlength="10" maxlength="10">
              </div>
            </div>

            <div class="row">
              <div class="col-sm-6 form-group">
                <label class="control-label">पासवर्ड</label>
                <input type="password" name="password" class="form-control form-control-sm" required>
              </div>
              <div class="col-sm-6 form-group">
                <label class="control-label">पासवर्ड दोबारा डाले</label>
                <input type="password" name="repassword" class="form-control form-control-sm" required>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-6 form-group">
                <label class="control-label">पद</label>
                <select name="auth_id" class="form-control select2" required>
                  <option value="">-- Select --</option>
                  <?php
                  $roleId = $memberData['role_id'] ?? 0;
                  $roles = $conn->query('SELECT * FROM role WHERE id = ' . intval($roleId));
                  while($row = $roles->fetch_assoc()): ?>
                  <option value="<?= $row['id'] ?>" selected><?= htmlspecialchars($row['name']) ?></option>
                  <?php endwhile; ?>
                </select>
              </div>
              <div class="col-sm-6 form-group">
                <label class="control-label">संगठन</label>
                <select name="org_id" class="form-control select2" required>
                  <option value="">-- Select --</option>
                  <?php
                  $orgId = $memberData['organization_id'] ?? 0;
                  $orgs = $conn->query('SELECT * FROM organization WHERE id = ' . intval($orgId));
                  while($row = $orgs->fetch_assoc()): ?>
                  <option value="<?= $row['id'] ?>" selected><?= htmlspecialchars($row['name']) ?></option>
                  <?php endwhile; ?>
                </select>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-6 form-group">
                <label class="control-label">राज्य</label>
                <select name="state" id="state_id" class="form-control select2" required>
                  <option value="">-- Select --</option>
                  <?php
                  $states = $conn->query("SELECT name, id FROM states");
                  while($row = $states->fetch_assoc()): ?>
                  <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                  <?php endwhile; ?>
                </select>
              </div>
              <div class="col-sm-6 form-group">
                <label class="control-label">जनपद</label>
                <select name="district" id="district_id" class="form-control select2" disabled required>
                  <option value="">-- Select --</option>
                </select>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-6 form-group">
                <label class="control-label">ब्लाक</label>
                <select name="block" id="block_id" class="form-control select2" disabled required>
                  <option value="">-- Select --</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
    <div class="card-footer border-top border-info">
      <div class="d-flex justify-content-center">
        <button type="submit" form="manage-user" class="btn btn-primary mx-2">
          Save
        </button>
        <a href="./index.php?page=home" class="btn btn-secondary mx-2">Cancel</a>
      </div>
    </div>
  </div>
</div>

<style>
  .card-header {
    padding: 0.75rem 1.25rem;
  }
  .form-control-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
  }
</style>

<script>
$(document).ready(function() {
  // Password confirmation validation
  $('[name="repassword"]').on('keyup', function() {
    const passwordMatch = $(this).val() === $('[name="password"]').val();
    $(this).toggleClass('is-invalid', !passwordMatch);
    $(this).toggleClass('is-valid', passwordMatch);
  });

  // Form submission
  $("#manage-user").submit(function(e) {
    e.preventDefault();
    
    // Clear previous messages
    $("#msg").empty();
    
    // Validate password match
    if ($('[name="password"]').val() !== $('[name="repassword"]').val()) {
      $("#msg").html('<div class="alert alert-danger">Passwords do not match</div>');
      return false;
    }
    
    start_load();
    $.ajax({
      url: "ajax.php?action=save_user",
      data: new FormData(this),
      cache: false,
      contentType: false,
      processData: false,
      method: "POST",
      dataType: "json", // Expect JSON response
      success: function(resp) {
        if (resp && resp.status === 'success') {
          alert_toast(resp.message || "Data successfully saved", "success");
          setTimeout(function() {
            location.href = "index.php?page=user_list";
          }, 2000);
        } else {
          // Handle different error cases
          let errorMessage = "An error occurred";
          if (resp && resp.message) {
            errorMessage = resp.message;
          } else if (resp && resp.code) {
            switch(resp.code) {
              case 2:
                errorMessage = "Phone number already registered";
                break;
              case 3:
                errorMessage = "No matching member found";
                break;
              case 5:
                errorMessage = "Database error occurred";
                break;
            }
          }
          $("#msg").html(`<div class="alert alert-danger">${errorMessage}</div>`);
        }
      },
      error: function(xhr, status, error) {
        let errorMessage = "An error occurred while processing your request";
        if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMessage = xhr.responseJSON.message;
        } else if (xhr.responseText) {
          try {
            const response = JSON.parse(xhr.responseText);
            errorMessage = response.message || errorMessage;
          } catch (e) {
            errorMessage = xhr.responseText || errorMessage;
          }
        }
        $("#msg").html(`<div class="alert alert-danger">${errorMessage}</div>`);
      },
      complete: function() {
        end_load();
      }
    });
  });

  // Role-based field disabling
  $("#auth_id").change(function() {
    const authType = Number($(this).val());
    let authId = 0;
    
    if (authType > 0 && authType < 5) authId = 1;
    else if (authType > 4 && authType < 7) authId = 2;
    else if (authType > 6 && authType < 8) authId = 3;
    else if (authType > 7 && authType < 10) authId = 4;
    else if (authType > 9 && authType < 13) authId = 5;
    else if (authType > 12 && authType < 14) authId = 6;
    
    // Update field states based on role
    $("#district_id").prop("disabled", authId < 3);
    $("#block_id").prop("disabled", authId < 5);
  });

  // Cascading dropdowns with error handling
  function setupCascadingDropdown(parent, child, url) {
    $(parent).change(function() {
      const id = $(this).val();
      const $child = $(child);
      
      if (id) {
        $child.prop("disabled", true).html('<option value="">Loading...</option>');
        
        $.ajax({
          url: "/nishad/api/" + url,
          type: "GET",
          data: { id: id },
          dataType: "json",
          success: function(response) {
            if (response && (response.res || response.data)) {
              const items = response.res || response.data;
              $child.empty().append('<option value="">-- Select --</option>');
              $.each(items, function(i, item) {
                $child.append(`<option value="${item.id}">${item.name}</option>`);
              });
              $child.prop("disabled", false);
            } else {
              $child.empty().append('<option value="">No options available</option>');
              console.error("Invalid response format from server");
            }
          },
          error: function(xhr) {
            $child.empty().append('<option value="">Error loading data</option>');
            console.error("Error loading dropdown data:", xhr.statusText);
          }
        });
      } else {
        $child.prop("disabled", true).empty().append('<option value="">-- Select --</option>');
      }
    });
  }

  // Initialize cascading dropdowns
  setupCascadingDropdown("#state_id", "#district_id", "get-districts.php");
  setupCascadingDropdown("#district_id", "#block_id", "get-blocks.php");
});
</script>