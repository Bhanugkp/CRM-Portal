<?php include 'db_connect.php' ?>
<style>
    .card-primary {
        border-color: #1d74f7;
    }
    .bg-lightblue {
        background-color: #1d74f7;
        color: white;
    }
    .status-badge {
        font-size: 0.8rem;
        padding: 0.35em 0.65em;
    }
    .status-active {
        background-color: #28a745;
    }
    .status-inactive {
        background-color: #dc3545;
    }
    table td {
        vertical-align: middle !important;
    }
    .btn-group-sm > .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    .card-header {
        padding: 0.75rem 1.25rem;
        background-color: #f8f9fa;
    }
    .select2-container {
        width: 100% !important;
    }
</style>

<div class="col-lg-12">
    <div class="card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">User Management</h3>
            <div class="card-tools">
                <a class="btn btn-sm btn-primary btn-flat" href="./index.php?page=create_user">
                    <i class="fa fa-plus mr-1"></i> Add New User
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="user-table">
                    <thead class="bg-lightblue">
                        <tr>
                            <th class="text-center" width="5%">#</th>
                            <th width="3%"><input type="checkbox" id="select-all"></th>
                            <th>नाम</th>
                            <th>मोबाइल</th>
                            <th>ईमेल</th>
                            <th>पद</th>
                            <th>Status</th>
                            <th class="text-center" width="15%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $userId = $_SESSION['login_id'] ?? 0;
                        $i = 1;
                        
                        $stmt = $conn->prepare("SELECT u.id, CONCAT(u.fname,' ', u.lname) as name, 
                                                phone, email, r.name as role, u.is_active
                                                FROM users AS u
                                                INNER JOIN role AS r ON r.id = u.role_id 
                                                WHERE u.ref_by = ?");
                        $stmt->bind_param("i", $userId);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        while($row = $result->fetch_assoc()):
                        $statusClass = $row['is_active'] ? 'status-active' : 'status-inactive';
                        $statusText = $row['is_active'] ? 'Active' : 'Inactive';
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $i++ ?></td>
                            <td><input type="checkbox" class="select-row" value="<?php echo htmlspecialchars($row['id']) ?>"></td>
                            <td><?php echo htmlspecialchars(ucwords($row['name'])) ?></td>
                            <td><?php echo htmlspecialchars($row['phone']) ?></td>
                            <td><?php echo htmlspecialchars($row['email']) ?></td>
                            <td><?php echo htmlspecialchars($row['role']) ?></td>
                            <td>
                                <span class="badge status-badge <?php echo $statusClass ?>">
                                    <?php echo $statusText ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?page=edit_user&id=<?php echo $row['id'] ?>" 
                                       class="btn btn-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger delete-user" 
                                            data-id="<?php echo $row['id'] ?>" 
                                            title="Delete" <?php echo $row['is_active'] ? '' : 'disabled' ?>>
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
        <div class="card-footer">
            <button class="btn btn-danger btn-sm" id="delete-selected">
                <i class="fas fa-trash mr-1"></i> Delete Selected (Active Only)
            </button>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    // Initialize DataTable with export buttons
    var table = $('#user-table').DataTable({
        dom: '<"top"<"d-flex justify-content-between align-items-center"lf>><"rt"><"bottom"ip>',
        buttons: [
            {
                extend: 'copy',
                text: '<i class="fas fa-copy"></i> Copy',
                className: 'btn btn-sm btn-secondary'
            },
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn btn-sm btn-secondary'
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-sm btn-secondary'
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-sm btn-secondary'
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                className: 'btn btn-sm btn-secondary'
            }
        ],
        columnDefs: [
            { orderable: false, targets: [0, 1, 7] },
            { searchable: false, targets: [0, 1] }
        ],
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "No entries found",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        initComplete: function() {
            $('.dt-buttons').addClass('btn-group');
        }
    });

    // Select all functionality
    $('#select-all').on('click', function() {
        $('.select-row').prop('checked', this.checked);
        updateDeleteButtonState();
    });

    // Individual checkbox handling
    $('#user-table').on('change', '.select-row', function() {
        var allChecked = $('.select-row:checked').length === $('.select-row').length;
        $('#select-all').prop('checked', allChecked);
        updateDeleteButtonState();
    });

    // Update delete button state based on selected rows
    function updateDeleteButtonState() {
        var hasActiveSelected = false;
        $('.select-row:checked').each(function() {
            var row = $(this).closest('tr');
            if (!row.find('.delete-user').prop('disabled')) {
                hasActiveSelected = true;
                return false; // break loop
            }
        });
        $('#delete-selected').prop('disabled', !hasActiveSelected);
    }

    // Delete single user with confirmation
    $('#user-table').on('click', '.delete-user:not(:disabled)', function(){
        var userId = $(this).data('id');
        Swal.fire({
            title: 'Confirm Deletion',
            text: "Are you sure you want to delete this active user?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                delete_user(userId);
            }
        });
    });

    // Delete multiple users with confirmation
    $('#delete-selected').click(function(){
        var selectedIds = [];
        $('.select-row:checked').each(function() {
            var row = $(this).closest('tr');
            if (!row.find('.delete-user').prop('disabled')) {
                selectedIds.push($(this).val());
            }
        });
        
        if(selectedIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Active Users Selected',
                text: 'Please select at least one active user to delete',
                confirmButtonText: 'OK'
            });
            return;
        }
        
        Swal.fire({
            title: 'Confirm Deletion',
            text: "Are you sure you want to delete "+selectedIds.length+" active users?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete them!'
        }).then((result) => {
            if (result.isConfirmed) {
                delete_multiple_users(selectedIds);
            }
        });
    });
});

function delete_user(id){
    start_load();
    $.ajax({
        url: 'ajax.php?action=delete_user',
        method: 'POST',
        data: {id: id},
        dataType: 'json',
        success: function(resp){
            if(resp.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: resp.message,
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: resp.message || "Error deleting user",
                    confirmButtonText: 'OK'
                });
                end_load();
            }
        },
        error: function(){
            Swal.fire({
                icon: 'error',
                title: 'Request Failed',
                text: "Please try again",
                confirmButtonText: 'OK'
            });
            end_load();
        }
    });
}

function delete_multiple_users(ids){
    start_load();
    $.ajax({
        url: 'ajax.php?action=delete_multiple_users',
        method: 'POST',
        data: {ids: ids},
        dataType: 'json',
        success: function(resp){
            if(resp.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: resp.message,
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: resp.message || "Error deleting users",
                    confirmButtonText: 'OK'
                });
                end_load();
            }
        },
        error: function(){
            Swal.fire({
                icon: 'error',
                title: 'Request Failed',
                text: "Please try again",
                confirmButtonText: 'OK'
            });
            end_load();
        }
    });
}
</script>