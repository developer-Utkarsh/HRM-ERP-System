
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Student List</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<?php if(Auth::user()->role_id != 32){ ?>
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a></li>
								<?php } ?>
								
								<li class="breadcrumb-item active">List View</li>
							</ol>
							
						</div>
					</div>
					<div class="col-4 text-right">
						<a href="<?php echo e(route('admin.attendance-dashboard')); ?>" class="btn btn-primary mr-1">Back</a>
						<button onClick="ExportToExcel()" class="btn btn-primary">Report</button>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="data-list-view" class="data-list-view-header">
				<div class="table-responsive">
					<table class="table data-list-view" id="my-table-id">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Name</th>
								<th>Category</th>
								<th>Reg. No.</th>
								<?php if(Auth::user()->id=='1732'){ ?><th>Mobile</th><?php } ?>
								<th>In Time</th>
								<th>User</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$i = 1;
								
								// print_r($attendance);
								// print_r($attendance[0]->reg_no);
								// die();
								
								
								if($type==1){
									foreach($attendance as $a){
									    $query = DB::connection('mysql2')->table("tbl_registration")->select('s_name','contact','reg_number','cast')
								           ->where('reg_number',$a->reg_no)->where('batch_id',$batch_id)->first(); ?>
										<tr>
											<td><?=$i;?></td>
											<td><?php echo e($query->s_name); ?></td>
											<td><?php echo e($query->cast); ?></td>
											<td><?php echo e($query->reg_number); ?></td>
											<?php if(Auth::user()->id=='1732'){ ?><td><?php echo e($query->contact); ?></td><?php } ?>
										    <td><?php echo e(date('d-m-Y H:i', strtotime($a->date))); ?></td>
											<td><?php echo e($a->uname); ?></td>
										</tr>
										   
									<?php $i++; }										   

								}else{
									$present=[];
									foreach($attendance as $a){
										$present[]=$a->reg_no;
									}
									$query = DB::connection('mysql2')->table("tbl_registration")->select('s_name','contact','reg_number','cast')
								   ->whereNotIN('reg_number',$present)->where('batch_id',$batch_id)->get();	
								
								
								    foreach($query as $data){
										
							?>
							<tr>
								<td><?=$i;?></td>
								<td><?php echo e($data->s_name); ?></td>
								<td><?php echo e($data->cast); ?></td>
								<td><?php echo e($data->reg_number); ?></td>
								<!--<td><?php echo e($data->contact); ?></td>--> 
								<td>Absent</td>
								<td>-</td>
							</tr>
								<?php $i++; } }  ?>
						</tbody>
					</table>
				</div>                   
			</section>
		</div>
	</div>
</div>

<style>
.table thead th {
	font-size:16px !important;
}

.table tbody td{
	font-size:16px !important;
}

</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
	
	
	function ExportToExcel(){
	   var htmltable= document.getElementById('my-table-id');
	   var html = htmltable.outerHTML;
	   window.open('data:application/vnd.ms-excel,' + encodeURIComponent(html));
	}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/batchinventory/student-attendance-view.blade.php ENDPATH**/ ?>