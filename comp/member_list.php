<?php include 'db_connect.php' ?>
<div class="col-lg-12">
    <div class="card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Member Management</h3>
            <div class="card-tools">
                <a class="btn btn-sm btn-primary btn-flat" href="./index.php?page=new_member">
                    <i class="fa fa-plus mr-1"></i> Add New Member
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="member-table">
                    <thead class="bg-lightblue">
                        <tr>
                            <th class="text-center" width="5%">#</th>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Organization</th>
                            <th>Designation</th>
                            <th class="text-center" width="15%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        $login_id = $_SESSION['login_id'] ?? 0;
                        
                        // Using prepared statement for security
                        $stmt = $conn->prepare("SELECT 
                            m.id as id,
                            CONCAT(m.fname, IF(m.lname IS NOT NULL, CONCAT(' ', m.lname), '')) AS name,
                            m.phone as phone,
                            r.name as role,
                            o.name as org
                        FROM members AS m
                        INNER JOIN role AS r ON r.id = m.role_id
                        INNER JOIN organization AS o ON o.id = m.organization_id
                        WHERE m.ref_by = ?
                        ORDER BY m.fname ASC");
                        
                        $stmt->bind_param("i", $login_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        while($row = $result->fetch_assoc()):
                        ?>
                        <tr data-member-id="<?php echo htmlspecialchars($row['id']) ?>">
                            <td class="text-center"><?php echo $i++ ?></td>
                            <td><?php echo htmlspecialchars(ucwords($row['name'])) ?></td>
                            <td><?php echo htmlspecialchars($row['phone']) ?></td>
                            <td><?php echo htmlspecialchars($row['org']) ?></td>
                            <td><?php echo htmlspecialchars($row['role']) ?></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-info view-member" 
                                        data-id="<?php echo htmlspecialchars($row['id']) ?>" title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="index.php?page=edit_member&id=<?php echo htmlspecialchars($row['id']) ?>" 
                                       class="btn btn-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger delete-member" 
                                        data-id="<?php echo htmlspecialchars($row['id']) ?>" title="Delete">
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
    </div>
</div>

<style>
    table td {
        vertical-align: middle !important;
    }
    .card-header {
        padding: 0.75rem 1.25rem;
    }
    .btn-group-sm > .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>

<script>
$(document).ready(function(){
    // Initialize DataTable with export buttons
    var table = $('#member-table').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        columnDefs: [
            { orderable: false, targets: [0, 5] }, // Disable sorting on action columns
            { searchable: false, targets: [0] }    // Disable search on index column
        ],
        language: {
            search: "Search:",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });

    // View member details
    $('#member-table').on('click', '.view-member', function(){
        var memberId = $(this).data('id');
        uni_modal("Member Details", "view_member.php?id=" + memberId, "large");
    });

    // Delete member
    $('#member-table').on('click', '.delete-member', function(){
        var memberId = $(this).data('id');
        _conf("Are you sure you want to delete this member?", "delete_member", [memberId]);
    });
});

function delete_member(id) {
    start_load();
    $.ajax({
        url: 'ajax.php?action=delete_member',
        method: 'POST',
        data: { 
            id: id,
            csrf_token: '<?php echo isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : ''; ?>'
        },
        dataType: 'json',
        success: function(resp) {
            if(resp && resp.status === 'success') {
                alert_toast(resp.message || "Member deleted successfully", 'success');
                // Remove the row without reloading
                $('tr[data-member-id="'+id+'"]').fadeOut(400, function() {
                    $(this).remove();
                });
            } else {
                alert_toast(resp && resp.message ? resp.message : "Error deleting member", 'error');
            }
        },
        error: function(xhr) {
            let errorMsg = "Request failed";
            if(xhr.status === 403) {
                errorMsg = "You don't have permission for this action";
            } else if(xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            alert_toast(errorMsg, 'error');
        },
        complete: function() {
            end_load();
        }
    });
}
</script>