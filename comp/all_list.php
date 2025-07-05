<?php include 'db_connect.php' ?>
<style>
  /* Modern UI Enhancements */
  .card-primary {
    border-color: #1d74f7;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
  }
  
  .card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    padding: 1rem 1.5rem;
  }
  
  .card-title {
    font-weight: 600;
    color: #1d3b6b;
    font-size: 1.25rem;
  }
  
  .btn-rounded {
    border-radius: 50px;
    padding: 0.375rem 1rem;
    font-weight: 500;
  }
  
  .btn-sm {
    font-size: 0.825rem;
  }
  
  .table thead th {
    background-color: #1d74f7 !important;
    color: white;
    font-weight: 500;
    border-bottom: none;
  }
  
  .table tbody tr {
    transition: all 0.2s;
  }
  
  .table tbody tr:hover {
    background-color: rgba(29, 116, 247, 0.05);
  }
  
  .bg-light {
    background-color: #f8f9fa !important;
  }
  
  .select2-container--default .select2-selection--single {
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    height: calc(2.25rem + 2px);
  }
  
  .btn-group-sm > .btn {
    border-radius: 0.2rem;
  }
  
  .modal-header {
    background-color: #1d74f7;
    color: white;
  }
  
  .nato {
    font-family: "Noto Sans Devanagari", sans-serif;
  }
  
  /* Custom checkbox */
  .custom-checkbox .custom-control-input:checked~.custom-control-label::before {
    background-color: #1d74f7;
    border-color: #1d74f7;
  }
  
  /* Character counter */
  #sms-char-count {
    font-weight: 500;
  }
  
  /* Responsive adjustments */
  @media (max-width: 768px) {
    .card-tools .btn {
      margin-bottom: 0.5rem;
    }
  }
</style>

<div class="col-lg-12">
  <div class="card card-outline card-primary">
    <!-- Card Header with Action Buttons -->
    <div class="card-header">
      <div class="d-flex justify-content-between align-items-center">
        <h3 class="card-title nato mb-0">सदस्य प्रबंधन</h3>
        <div class="card-tools d-flex">
          <div class="btn-group">
            <a href="#" class="btn btn-sm btn-primary btn-rounded mr-2">
              <i class="fas fa-phone mr-1"></i> कॉल
            </a>
            <a href="#" class="btn btn-sm btn-primary btn-rounded mr-2" id="send-sms-btn">
              <i class="fas fa-comment mr-1"></i> संदेश
            </a>
            <a href="new_member" class="btn btn-sm btn-success btn-rounded mr-2">
              <i class="fas fa-plus mr-1"></i> नया सदस्य
            </a>
            <button class="btn btn-sm btn-light btn-rounded border" data-toggle="collapse" href="#collapseCard">
              <i class="fas fa-filter mr-1"></i> फिल्टर
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Filter Section -->
    <div class="collapse" id="collapseCard">
      <div class="card-body bg-light pt-3">
        <form id="manage-member">
          <div class="row">
            <div class="col-md-12">
              <div class="row">
                <div class="col-sm-3 form-group">
                  <label for="state_id" class="control-label nato">राज्य</label>
                  <select name="state" id="state_id" class="form-control select2">
                    <option value="0">सभी</option>
                    <?php
                    $states = $conn->query("SELECT name, id FROM states");
                    while($row = $states->fetch_assoc()): ?>
                    <option value="<?php echo $row['id'] ?>">
                      <?php echo $row['name'] ?>
                    </option>
                    <?php endwhile; ?>
                  </select>
                </div>
                
                <div class="col-sm-3 form-group">
                  <label for="dis_id" class="control-label nato">जनपद</label>
                  <select name="district" id="dis_id" class="form-control select2" disabled>
                    <option value="0">सभी</option>
                  </select>
                </div>

                <div class="col-sm-3 form-group">
                  <label for="block_id" class="control-label nato">ब्लाक</label>
                  <select name="block" id="block_id" class="form-control select2" disabled>
                    <option value="0">सभी</option>
                  </select>
                </div>
                
                <div class="col-sm-3 form-group">
                  <label for="locality" class="control-label nato">स्थानीयता</label>
                  <select name="locality" id="locality" class="form-control select2" disabled>
                    <option value="0">सभी</option>
                  </select>
                </div>
              </div>

              <div class="row mt-2">
                <div class="col-sm-3 form-group">
                  <label for="loksabha" class="control-label nato">लोकसभा</label>
                  <select name="loksabha" id="loksabha" class="form-control select2" disabled>
                    <option value="0">सभी</option>
                  </select>
                </div>
                
                <div class="col-sm-3 form-group">
                  <label for="vidhansabha" class="control-label nato">विधानसभा</label>
                  <select name="vidhansabha" id="vidhansabha" class="form-control select2" disabled>
                    <option value="0">सभी</option>
                  </select>
                </div>
                
                <div class="col-sm-3 form-group">
                  <label for="authority" class="control-label nato">पद</label>
                  <select name="authority" id="authority" class="form-control select2">
                    <option value="0">सभी</option>
                    <?php
                    $auth = $conn->query("SELECT name, id FROM role WHERE id != 1 ORDER BY id DESC"); 
                    while($row = $auth->fetch_assoc()): ?>
                    <option value="<?php echo $row['id'] ?>">
                      <?php echo $row['name']; ?>
                    </option>
                    <?php endwhile; ?>
                  </select>
                </div>
                
                <div class="col-sm-3 form-group">
                  <label for="organization" class="control-label nato">संगठन</label>
                  <select name="organization" id="organization" class="form-control select2">
                    <option value="0">सभी</option>
                    <?php
                    $org = $conn->query("SELECT name, id FROM organization");
                    while($org_row = $org->fetch_assoc()): ?>
                    <option value="<?php echo $org_row['id'] ?>">
                      <?php echo $org_row['name']; ?>
                    </option>
                    <?php endwhile; ?>
                  </select>
                </div>
              </div>
              
              <div class="row mt-3">
                <div class="col-sm-12 text-right">
                  <button type="button" class="btn btn-light mr-2" id="reset-filters">
                    <i class="fas fa-undo mr-1"></i> रीसेट
                  </button>
                  <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search mr-1"></i> खोजें
                  </button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Member Table -->
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover table-bordered" id="member-table">
          <thead class="bg-primary">
            <tr class="nato">
              <th width="5%" class="text-center">#</th>
              <th width="3%"><input type="checkbox" id="select-all" /></th>
              <th>नाम</th>
              <th>मोबाइल</th>
              <th>ईमेल</th>
              <th>पद</th>
              <th>संगठन</th>
              <th width="15%" class="text-center">कार्यवाही</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $i = 1;
            $qry = $conn->query("SELECT m.id, CONCAT(m.fname, IF(m.lname IS NOT NULL, CONCAT(' ', m.lname), '')) AS name,
              m.phone, m.email, r.name as role, o.name as org
              FROM members m
              INNER JOIN role r ON r.id = m.role_id
              INNER JOIN organization o ON o.id = m.organization_id
              ORDER BY m.id DESC");
            
            while($row = $qry->fetch_assoc()): ?>
            <tr>
              <td class="text-center"><?php echo $i++ ?></td>
              <td><input type="checkbox" class="select-row" value="<?php echo $row['id']; ?>" /></td>
              <td><?php echo ucwords($row['name']) ?></td>
              <td><a href="tel:<?php echo $row['phone'] ?>"><?php echo $row['phone'] ?></a></td>
              <td><a href="mailto:<?php echo $row['email'] ?>"><?php echo $row['email'] ?></a></td>
              <td><?php echo $row['role'] ?></td>
              <td><?php echo $row['org'] ?></td>
              <td class="text-center">
                <div class="btn-group btn-group-sm">
                  <button type="button" class="btn btn-info view-member" data-id="<?php echo $row['id'] ?>" title="View">
                    <i class="fas fa-eye"></i>
                  </button>
                  <a href="edit_member?id=<?php echo $row['id'] ?>" class="btn btn-primary" title="Edit">
                    <i class="fas fa-edit"></i>
                  </a>
                  <button type="button" class="btn btn-danger delete-member" data-id="<?php echo $row['id'] ?>" title="Delete">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
    
    <!-- Card Footer with Export Options -->
    <div class="card-footer bg-white">
      <div class="d-flex justify-content-between align-items-center">
        <div class="btn-group">
          <button type="button" class="btn btn-outline-secondary btn-sm" id="export-csv">
            <i class="fas fa-file-csv mr-1"></i> CSV
          </button>
          <button type="button" class="btn btn-outline-secondary btn-sm" id="export-excel">
            <i class="fas fa-file-excel mr-1"></i> Excel
          </button>
          <button type="button" class="btn btn-outline-secondary btn-sm" id="export-pdf">
            <i class="fas fa-file-pdf mr-1"></i> PDF
          </button>
        </div>
        <div>
          <button type="button" class="btn btn-danger btn-sm" id="delete-selected">
            <i class="fas fa-trash-alt mr-1"></i> चयनित हटाएं
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- SMS Modal -->
<div class="modal fade" id="smsModal" tabindex="-1" role="dialog" aria-labelledby="smsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title nato" id="smsModalLabel"><i class="fas fa-comment mr-2"></i>SMS भेजें</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="sms-form">
          <div class="form-group">
            <label class="control-label nato">प्राप्तकर्ता</label>
            <div id="sms-recipients-info" class="alert alert-info py-2">
              <i class="fas fa-info-circle mr-2"></i>
              <span id="recipient-count">0</span> सदस्य चुने गए हैं
            </div>
          </div>
          <div class="form-group">
            <label for="sms-message" class="control-label nato">संदेश</label>
            <textarea class="form-control" id="sms-message" name="sms_message" rows="5" placeholder="SMS संदेश लिखें..." required></textarea>
            <div class="d-flex justify-content-between mt-1">
              <small class="form-text text-muted">अधिकतम 160 अक्षर</small>
              <span class="text-right">
                <span id="sms-char-count">0</span>/160
              </span>
            </div>
          </div>
          <div class="form-group mb-0">
            <div class="custom-control custom-switch">
              <input type="checkbox" class="custom-control-input" id="sms-unicode" name="sms_unicode">
              <label class="custom-control-label nato" for="sms-unicode">यूनिकोड SMS (हिंदी/देवनागरी के लिए)</label>
            </div>
          </div>
          <input type="hidden" id="sms-recipient-ids" name="recipient_ids" value="">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fas fa-times mr-1"></i> रद्द करें
        </button>
        <button type="button" class="btn btn-primary" id="confirm-send-sms">
          <i class="fas fa-paper-plane mr-1"></i> भेजें
        </button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  // Initialize DataTable with enhanced features
  var memberTable = $('#member-table').DataTable({
    dom: '<"top"<"d-flex justify-content-between align-items-center"lf><"d-flex">>rt<"bottom"ip>',
    language: {
      search: "खोजें:",
      lengthMenu: "प्रविष्टियाँ दिखाएँ _MENU_",
      info: "दिखाया जा रहा है _START_ से _END_ तक, कुल _TOTAL_ प्रविष्टियाँ",
      paginate: {
        first: "पहला",
        last: "अंतिम",
        next: "अगला",
        previous: "पिछला"
      }
    },
    responsive: true,
    columnDefs: [
      { orderable: false, targets: [1, 7] },
      { searchable: false, targets: [0, 1, 7] }
    ],
    initComplete: function() {
      // Add custom search input
      $('.dataTables_filter input').addClass('form-control form-control-sm');
      $('.dataTables_filter label').contents().filter(function() {
        return this.nodeType === 3;
      }).remove();
    }
  });

  // Initialize Select2 with better styling
  $('.select2').select2({
    width: '100%',
    placeholder: "चुनें...",
    allowClear: true,
    dropdownParent: $('#collapseCard')
  });

  // Select all functionality
  $('#select-all').on('click', function() {
    var isChecked = $(this).prop('checked');
    $('.select-row').prop('checked', isChecked);
    updateSelectedCount();
  });

  // Update selected count when individual checkboxes change
  $(document).on('change', '.select-row', function() {
    updateSelectedCount();
  });

  function updateSelectedCount() {
    var selectedCount = $('.select-row:checked').length;
    $('#recipient-count').text(selectedCount);
  }

  // View member details
  $('#member-table').on('click', '.view-member', function() {
    var memberId = $(this).data('id');
    uni_modal("सदस्य विवरण", "view_member.php?id=" + memberId, "modal-lg");
  });

  // Delete member with confirmation
  $('#member-table').on('click', '.delete-member', function() {
    var memberId = $(this).data('id');
    Swal.fire({
      title: 'क्या आप सुनिश्चित हैं?',
      text: "आप इस सदस्य को हटाना चाहते हैं!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'हाँ, हटाएं!',
      cancelButtonText: 'रद्द करें'
    }).then((result) => {
      if (result.isConfirmed) {
        delete_member(memberId);
      }
    });
  });

  // Delete selected members with confirmation
  $('#delete-selected').on('click', function() {
    var selectedIds = $('.select-row:checked').map(function() {
      return $(this).val();
    }).get();
    
    if (selectedIds.length === 0) {
      Swal.fire({
        icon: 'warning',
        title: 'चयन आवश्यक',
        text: 'कृपया हटाने के लिए कम से कम एक सदस्य चुनें',
        confirmButtonText: 'ठीक है'
      });
      return;
    }
    
    Swal.fire({
      title: 'क्या आप सुनिश्चित हैं?',
      text: "आप " + selectedIds.length + " सदस्यों को हटाना चाहते हैं!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'हाँ, हटाएं!',
      cancelButtonText: 'रद्द करें'
    }).then((result) => {
      if (result.isConfirmed) {
        delete_multiple_members(selectedIds);
      }
    });
  });

  // Reset filters
  $('#reset-filters').on('click', function() {
    $('#manage-member')[0].reset();
    $('.select2').val('0').trigger('change');
    memberTable.search('').draw();
  });

  // Export buttons
  $('#export-csv').on('click', function() {
    memberTable.button('.buttons-csv').trigger();
  });
  
  $('#export-excel').on('click', function() {
    memberTable.button('.buttons-excel').trigger();
  });
  
  $('#export-pdf').on('click', function() {
    memberTable.button('.buttons-pdf').trigger();
  });

  // SMS functionality
  $('#send-sms-btn').on('click', function(e) {
    e.preventDefault();
    
    var selectedIds = $('.select-row:checked').map(function() {
      return $(this).val();
    }).get();
    
    if (selectedIds.length === 0) {
      Swal.fire({
        icon: 'warning',
        title: 'चयन आवश्यक',
        text: 'कृपया SMS भेजने के लिए कम से कम एक सदस्य चुनें',
        confirmButtonText: 'ठीक है'
      });
      return;
    }
    
    $('#sms-recipient-ids').val(selectedIds.join(','));
    $('#recipient-count').text(selectedIds.length);
    $('#smsModal').modal('show');
  });

  // Character counter for SMS
  $('#sms-message').on('input', function() {
    var length = $(this).val().length;
    $('#sms-char-count').text(length);
    
    if (length > 160) {
      $('#sms-char-count').addClass('text-danger');
    } else {
      $('#sms-char-count').removeClass('text-danger');
    }
  });

  // Send SMS
  $('#confirm-send-sms').on('click', function() {
    var recipientIds = $('#sms-recipient-ids').val();
    var message = $('#sms-message').val();
    var isUnicode = $('#sms-unicode').is(':checked');
    
    if (!message) {
      Swal.fire({
        icon: 'warning',
        title: 'संदेश आवश्यक',
        text: 'कृपया SMS संदेश लिखें',
        confirmButtonText: 'ठीक है'
      });
      return;
    }
    
    if (message.length > 160) {
      Swal.fire({
        icon: 'warning',
        title: 'संदेश बहुत लंबा',
        text: 'SMS संदेश 160 अक्षरों से अधिक नहीं हो सकता',
        confirmButtonText: 'ठीक है'
      });
      return;
    }
    
    start_load();
    
    $.ajax({
      url: 'api/send_sms.php',
      type: 'POST',
      data: {
        action: 'send_bulk_sms',
        recipient_ids: recipientIds,
        message: message,
        is_unicode: isUnicode
      },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          Swal.fire({
            icon: 'success',
            title: 'सफलता!',
            text: response.message || "SMS सफलतापूर्वक भेज दिए गए",
            confirmButtonText: 'ठीक है'
          });
          $('#smsModal').modal('hide');
          $('#sms-message').val('');
        } else {
          Swal.fire({
            icon: 'error',
            title: 'त्रुटि',
            text: response.message || "SMS भेजने में त्रुटि",
            confirmButtonText: 'ठीक है'
          });
        }
        end_load();
      },
      error: function(xhr) {
        Swal.fire({
          icon: 'error',
          title: 'सर्वर त्रुटि',
          text: "SMS भेजने में त्रुटि: " + xhr.statusText,
          confirmButtonText: 'ठीक है'
        });
        end_load();
      }
    });
  });

  // Cascading dropdown functionality
  function setupCascadingDropdown(parentSelector, childSelector, apiUrl) {
    $(parentSelector).on('change', function() {
      var selectedId = $(this).val();
      var $child = $(childSelector);
      
      if (selectedId > 0) {
        $child.prop('disabled', false);
        
        $.ajax({
          url: apiUrl,
          type: 'GET',
          data: { id: selectedId },
          dataType: 'json',
          success: function(response) {
            $child.empty().append('<option value="0">सभी</option>');
            $.each(response.res, function(index, item) {
              $child.append(`<option value="${item.id}">${item.name}</option>`);
            });
          },
          error: function() {
            Swal.fire({
              icon: 'error',
              title: 'त्रुटि',
              text: 'डेटा प्राप्त करने में त्रुटि',
              confirmButtonText: 'ठीक है'
            });
          }
        });
      } else {
        $child.prop('disabled', true).empty().append('<option value="0">सभी</option>');
      }
    });
  }

  // Set up all cascading dropdowns
  setupCascadingDropdown('#state_id', '#dis_id', 'api/get-districts.php');
  setupCascadingDropdown('#dis_id', '#block_id', 'api/get-blocks.php');
  setupCascadingDropdown('#block_id', '#locality', 'api/get-villages.php');
  setupCascadingDropdown('#state_id', '#loksabha', 'api/get-loksabha.php');
  setupCascadingDropdown('#loksabha', '#vidhansabha', 'api/get-vidhansabha.php');

  // Filter form submission
  $('#manage-member').on('submit', function(e) {
    e.preventDefault();
    var formData = $(this).serialize();
    
    $.ajax({
      url: 'api/filter-members.php',
      type: 'POST',
      data: formData,
      dataType: 'json',
      success: function(response) {
        memberTable.clear();
        
        $.each(response, function(index, member) {
          memberTable.row.add([
            index + 1,
            '<input type="checkbox" class="select-row" value="' + member.id + '">',
            member.name,
            '<a href="tel:' + member.phone + '">' + member.phone + '</a>',
            '<a href="mailto:' + member.email + '">' + member.email + '</a>',
            member.role,
            member.org,
            '<div class="btn-group btn-group-sm">' +
              '<button class="btn btn-info view-member" data-id="' + member.id + '" title="View"><i class="fas fa-eye"></i></button>' +
              '<a href="edit_member?id=' + member.id + '" class="btn btn-primary" title="Edit"><i class="fas fa-edit"></i></a>' +
              '<button class="btn btn-danger delete-member" data-id="' + member.id + '" title="Delete"><i class="fas fa-trash"></i></button>' +
            '</div>'
          ]);
        });
        
        memberTable.draw();
        $('#collapseCard').collapse('hide');
      },
      error: function() {
        Swal.fire({
          icon: 'error',
          title: 'त्रुटि',
          text: 'फिल्टर लागू करने में त्रुटि',
          confirmButtonText: 'ठीक है'
        });
      }
    });
  });
});

function delete_member(id) {
  start_load();
  $.ajax({
    url: 'ajax.php?action=delete_member',
    method: 'POST',
    data: { id: id },
    dataType: 'json',
    success: function(response) {
      if (response.status === 'success') {
        Swal.fire({
          icon: 'success',
          title: 'सफलता!',
          text: response.message || "सदस्य सफलतापूर्वक हटा दिया गया",
          confirmButtonText: 'ठीक है'
        }).then(() => {
          location.reload();
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'त्रुटि',
          text: response.message || "सदस्य को हटाने में त्रुटि",
          confirmButtonText: 'ठीक है'
        });
      }
      end_load();
    },
    error: function(xhr) {
      Swal.fire({
        icon: 'error',
        title: 'सर्वर त्रुटि',
        text: "सर्वर त्रुटि: " + xhr.statusText,
        confirmButtonText: 'ठीक है'
      });
      end_load();
    }
  });
}

function delete_multiple_members(ids) {
  start_load();
  $.ajax({
    url: 'ajax.php?action=delete_multiple_members',
    method: 'POST',
    data: { ids: ids },
    dataType: 'json',
    success: function(response) {
      if (response.status === 'success') {
        Swal.fire({
          icon: 'success',
          title: 'सफलता!',
          text: response.message || "चयनित सदस्य सफलतापूर्वक हटा दिए गए",
          confirmButtonText: 'ठीक है'
        }).then(() => {
          location.reload();
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'त्रुटि',
          text: response.message || "सदस्यों को हटाने में त्रुटि",
          confirmButtonText: 'ठीक है'
        });
      }
      end_load();
    },
    error: function(xhr) {
      Swal.fire({
        icon: 'error',
        title: 'सर्वर त्रुटि',
        text: "सर्वर त्रुटि: " + xhr.statusText,
        confirmButtonText: 'ठीक है'
      });
      end_load();
    }
  });
}
</script>