<?php include'db_connect.php' ?>
	<?php
					$i = 1;
					if (isset($_GET['state_id'])) {
						$type = 2;
						$state_id = $_GET['state_id'];
						$method_name = "district_id";
						$sql = "SELECT d.id, d.name, count(m.id) as total from `districts` as d
						left join `members` as m
						on m.district_id =  d.id
						where d.state_id = ".$state_id."
						group by d.id order by d.name asc;";
					} elseif (isset($_GET['district_id'])) {
						$type = 2;
						$method_name = "block_id";
						$district_id = $_GET['district_id'];
						$sql = "SELECT b.id, b.name, count(m.id) as total from `blocks` as b
						left join `members` as m
						on m.block_id =  b.id
						where b.district_id = ".$district_id."
						group by b.id order by b.name asc;";
					}  elseif (isset($_GET['block_id'])) {
						$block_id = $_GET['block_id'];
						$type = 1;
						$method_name = '';
						$sql = "SELECT 
						m.id as id,
						concat(m.fname, ' ', m.lname) as name,
						m.phone as phone,
						b.name AS block,
						d.name AS district,
						s.name AS state,
						(select name from designation where id = m.designation) as auth,
						(SELECT concat(u.fname, ' ', u.lname) from users as u where id = 1 )as ref_by
					FROM `members` AS m

						INNER JOIN `blocks` AS b ON m.block_id = b.id
						INNER JOIN `districts` AS d ON m.district_id = d.id
						INNER JOIN states AS s ON m.state_id = s.id
					WHERE m.block_id = ".$block_id." ORDER BY m.fname ASC ;";
					} else {
						$method_name = "state_id";
						$type = 2;
						$sql = "SELECT s.id, s.name, count(m.id) as total from `states` as s
						left join `members` as m
						on m.state_id =  s.id
						group by s.id order by s.name asc;";
					}
					$qry = $conn->query($sql);
					
	?>


<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-header">
			<?php if(!isset($locality_id)): ?>

			<div class="col-lg-12">
				<ol class="breadcrumb mb-0 p-0 float-sm-right bg-transparent">
					<?php if (isset($_GET['state_id']) || isset($_GET['district_id']) || isset($_GET['block_id']) || isset($_GET['locality_id'])): ?>
						<li class="breadcrumb-item">
							<a href="#"><?php echo "State"; ?></a>
						</li>
					<?php endif; ?>

					<?php if (isset($_GET['district_id']) || isset($_GET['block_id']) || isset($_GET['locality_id'])): ?>
						<li class="breadcrumb-item">
							<a href="#"><?php echo "District"; ?></a>
						</li>
					<?php endif; ?>

					<?php if (isset($_GET['block_id']) || isset($_GET['locality_id'])): ?>
						<li class="breadcrumb-item">
							<a href="#"><?php echo "Block"; ?></a>
						</li>
					<?php endif; ?>

					<?php if(isset($_GET['locality_id'])) : ?>
						<li class="breadcrumb-item">
							<a href="#"><?php echo "Locality"; ?></a>
						</li>
					<?php endif; ?>

					<li class="breadcrumb-item active">
						<?php
						if (isset($_GET['locality_id'])) {
							echo "Members";
						} elseif (isset($_GET['block_id'])) {
							echo "Locality";
						} elseif (isset($_GET['district_id'])) {
							echo "Block";
						} elseif (isset($_GET['state_id'])) {
							echo "District";
						} else {
							echo "State";
						}
						?>
					</li>
				</ol>
            </div>
			<?php else: ?>
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary " href="./index.php?page=new_user"><i class="fa fa-plus"></i> Add New</a>
			</div>
			<?php endif;?>
		</div>
		<div class="card-body">
			<form>
				<table class="table tabe-hover table-bordered" id="list">
					<thead>
						<tr>
							<th class="text-center">#</th>
							<?php if($type == 2):?>
							<th>Name</th>
							<th>Total</th>
							<?php else: ?>
							<th><input type="checkbox" id="select-all"></th>
							<th>Name</th>
							<th>Mobile</th>
							<th>Authority</th>
							<th> Ref by</th>
							<th>Action</th>
							<?php endif;?>
						</tr>
					</thead>
					<tbody>
						<?php while($row= $qry->fetch_assoc()):?>
						<tr>
							<td class="text-center"><?php echo $i++ ?></td>

							<?php if($type == 2):?>
							
							<td><b><a href="./index.php?page=list_page&<?php echo $method_name."=".$row['id'] ?>"><?php echo $row['name'] ?></a></b></td>
							<td><b><?php echo ($row['total']) ?></b></td>
							<?php else: ?>
							<td><input type="checkbox" class="select-row" name="<?php echo $row['id']; ?>"></td>
							<td><?php echo ucwords($row['name']) ?></td>
							<td><?php echo ($row['phone']) ?></td>
							<td><?php echo $row['auth']; ?></td>
							<td><?php echo ucwords($row['ref_by']) ?></td>
							<td class="text-center">
								<div class="btn-group">
									<button type="button" class="btn text-primary view_member" data-id="<?php echo $row['id'] ?>">
									<i class="fas fa-eye"></i>
									</button>
									<a href="index.php?page=edit_member&id=<?php echo $row['id'] ?>" class="btn text-success">
									<i class="fas fa-edit"></i>
									</a>
									<button type="button" class="btn text-danger delete_user" data-id="<?php echo $row['id'] ?>">
									<i class="fas fa-trash"></i>
									</button>
							</div>
							</td>
							<?php endif;?>
							

						</tr>	
					<?php endwhile; ?>
					</tbody>
				</table>
			</form>
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
	var columnData;var formObject;
	$(document).ready(function(){
		// Initialize DataTables
			var table = $('#list').DataTable({
				"columnDefs": [
            { "orderable": false, "targets": 1 }  // Disable sorting on the first column (checkbox column)
        ]
			});

			// Handle click on "Select all" control
			$('#select-all').on('click', function() {
				var rows = table.rows({ 'search': 'applied' }).nodes();
				$('input.select-row', rows).prop('checked', this.checked);
			});

			// Handle click on individual checkbox
			$('#list').on('change', 'input.select-row', function() {
				var checkbox = $('input.select-row');
				$('#select-all').prop('checked', checkbox.length === checkbox.filter(':checked').length);
			});

					


			columnData = table.column(3).data().toArray(); // 1 is the index of the column

			$('.view_member').click(function(){
				uni_modal("Member Details","view_member.php?id="+$(this).attr('data-id'),"large")
			})

			//delete staff
			$('.delete_staff').click(function(){
				_conf("Are you sure to delete this staff?","delete_staff",[$(this).attr('data-id')])
				})
	})

	function orm() {
				var form = document.forms[0];
				var formData = new FormData(form);

				// Convert FormData to a plain object
				formObject = Object.fromEntries(formData.entries());

				formObject = Object.keys(formObject);
				formObject.pop();

				const data = formObject.map(i => columnData[i]);

				
				

				console.log(data);
	}
	function delete_staff($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_user',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
</script>