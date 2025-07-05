<?php include'db_connect.php' ?>


<?php 
	$userType = $_SESSION['login_type'];
	$areaId = $_SESSION['login_area_id'];


		// Determine the userType level and area
		if ($userType > 0 && $userType < 5) {
			$userType = 1; // national
			$qry = "SELECT * FROM state";
		} else if ($userType >= 5 && $userType <= 6) {
			$userType = 2; // state
			$qry = "SELECT * FROM district WHERE state_id = ".$areaId;
		} else if ($userType == 7) {
			$userType = 3; // divisions
			
			$qry = "SELECT * FROM district WHERE state_id = ".$areaId;
			
		} else if ($userType >= 8 && $userType <= 9) {
			$userType = 4; // district
			
			$qry = "SELECT * FROM blocks WHERE district_id = ".$areaId;
			
		} else if ($userType >= 10 && $userType <= 12) {
			$userType = 5; // block
			$qry = "SELECT * FROM locality WHERE block_id = ".$areaId;
		} else if ($userType == 13) {
			$userType = 6; // village
			
			
		} else {
			$userType = 7; // none of them
			$qry = "SELECT * FROM state";
		}
?>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-header">
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary " href="./index.php?page=new_user"><i class="fa fa-plus"></i> Add New</a>
			</div>
		</div>
		<div class="card-body">
			<table class="table tabe-hover table-bordered" id="list">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Name</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					
					$i = 1;
					$qry = $conn->query($qry);
					while($row= $qry->fetch_assoc()):
					?>
					<tr>
						<td class="text-center"><?php echo $i++ ?></td>
						<td><?php echo ucwords($row['name']) ?></td>
						<td class="text-center">
		                    <div class="btn-group">
		                        <a href="index.php?page=edit_location&id=<?php echo $row['id'] ?>" class="btn btn-primary btn-flat ">
		                          <i class="fas fa-edit"></i>
		                        </a>
		                        <button type="button" class="btn btn-danger btn-flat delete_staff" data-id="<?php echo $row['id'] ?>">
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
<style>
	table td{
		vertical-align: middle !important;
	}
</style>
<script>
	//pagination
	$(document).ready(function(){
		$('#list').dataTable()
	// 	$('.view_staff').click(function(){
	// 		uni_modal("staff's Details","view_staff.php?id="+$(this).attr('data-id'),"large")
	// })
});

	// //delete staff
	// $('.delete_staff').click(function(){
	// 	_conf("Are you sure to delete this staff?","delete_staff",[$(this).attr('data-id')])
	// 	})
	// })

	// function delete_staff($id){
	// 	start_load()
	// 	$.ajax({
	// 		url:'ajax.php?action=delete_user',
	// 		method:'POST',
	// 		data:{id:$id},
	// 		success:function(resp){
	// 			if(resp==1){
	// 				alert_toast("Data successfully deleted",'success')
	// 				setTimeout(function(){
	// 					location.reload()
	// 				},1500)

	// 			}
	// 		}
	// 	})
	// }
</script>