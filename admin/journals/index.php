<?php
function format_num($number){
	$decimals = 0;
	$num_ex = explode('.',$number);
	$decimals = isset($num_ex[1]) ? strlen($num_ex[1]) : 0 ;
	return number_format($number,$decimals);
}
?>
<style>
	th.p-0, td.p-0{
		padding: 0 !important;
	}
</style>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Journal Entries</h3>
		<div class="card-tools">
			<button class="btn btn-primary btn-flat btn-sm" id="create_new" type="button"><i class="fa fa-pen-square"></i> Add New Journal Entry</button>
		</div>
	</div>
	<div class="card-body">
        <div class="container-fluid">
			<table class="table table-hover table-striped table-bordered">
				<colgroup>
					<col width="15%">
					<col width="15%">
					<col width="45%">
					<col width="15%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th>Date</th>
						<th>Journal Code</th>
						<th class="p-0">
							<div class="d-flex w-100">
								<div class="col-6 border">Description</div>
								<div class="col-3 border">Debit</div>
								<div class="col-3 border">Credit</div>
							</div>
						</th>
						<th>Encoded By</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$swhere = "";
					if($_settings->userdata('type') != 1){
						$swhere = " where user_id = '{$_settings->userdata('id')}' ";
					}
					$users = $conn->query("SELECT id,username FROM `users` where id in (SELECT `user_id` FROM `journal_entries` {$swhere})");
					$user_arr = array_column($users->fetch_all(MYSQLI_ASSOC),'username','id');
					$journals = $conn->query("SELECT * FROM `journal_entries` {$swhere} order by date(journal_date) asc");
					while($row = $journals->fetch_assoc()):
					?>
					<tr>
						<td class="text-center"><?= date("M d, Y", strtotime($row['journal_date'])) ?></td>
						<td class=""><?= $row['code'] ?></td>
						<td class="p-0">
							<div class="d-flex w-100">
								<div class="col-6 border"><?= $row['description'] ?></div>
								<div class="col-3 border"></div>
								<div class="col-3 border"></div>
							</div>
							<?php 
							$jitems = $conn->query("SELECT j.*,a.name as account, g.type as `type` FROM `journal_items` j inner join account_list a on j.account_id = a.id inner join group_list g on j.group_id = g.id where j.journal_id = '{$row['id']}'");
							while($rowss = $jitems->fetch_assoc()):
							?>
							<div class="d-flex w-100">
								<div class="col-6 border"><span class="pl-4"><?= $rowss['account'] ?></span></div>
								<div class="col-3 border text-right"><?= $rowss['type'] == 1 ? format_num($rowss['amount']) : '' ?></div>
								<div class="col-3 border text-right"><?= $rowss['type'] == 2 ? format_num($rowss['amount']) : '' ?></div>
							</div>
							<?php endwhile; ?>
						</td>
						<td><?= isset($user_arr[$row['user_id']]) ? $user_arr[$row['user_id']] : "N/A" ?></td>
						<td class="text-center">
							<button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
									Action
								<span class="sr-only">Toggle Dropdown</span>
							</button>
							<div class="dropdown-menu" role="menu">
								<a class="dropdown-item edit_data" href="javascript:void(0)" data-id ="<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"  data-code="<?php echo $row['code'] ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
							</div>
						</td>
					</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('#create_new').click(function(){
			uni_modal("Add New Journal Entry","journals/manage_journal.php",'mid-large')
		})
		$('.edit_data').click(function(){
			uni_modal("Edit Journal Entry","journals/manage_journal.php?id="+$(this).attr('data-id'),"mid-large")
		})
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this Journal Entry permanently?","delete_book",[$(this).attr('data-id')])
		})
		$('.table td,.table th').addClass('py-1 px-2 align-middle')
		$('.table').dataTable({
            columnDefs: [
                { orderable: false, targets: [2,4] }
            ],
        });
	})
	function delete_book($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_book",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>