
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Staff Movement System</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="<?php echo e(route('admin.staff.index')); ?>" method="get">
									<div class="row">
									
										
										<input type="hidden" class="faculty_id_get" value="<?php echo e(app('request')->input('faculty_id')); ?>">
										
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Employee</label>
											<?php $employee = \App\User::where('role_id', '!=' ,'1')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 emp_id" name="emp_id">
													<option value="">Select Any</option>
													<?php if(count($employee) > 0): ?>
													<?php $__currentLoopData = $employee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" <?php if($value->id == app('request')->input('emp_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name." (".$value['register_id'].")"); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php endif; ?>
												</select>												
											</fieldset>
										</div>	
										<div class="col-12 col-sm-6 col-lg-2">										
											<label for="users-list-status">From Date</label>								
											<fieldset class="form-group">																		
											<input type="date" name="fdate" placeholder="Date" value="<?php echo e(app('request')->input('fdate')); ?>" class="form-control StartDateClass fdate">		
											</fieldset>	
										</div>	
										
										<div class="col-12 col-sm-6 col-lg-2">											
											<label for="users-list-status">To Date</label>									
											<fieldset class="form-group">																			
											<input type="date" name="tdate" placeholder="Date" value="<?php echo e(app('request')->input('tdate')); ?>" class="form-control EndDateClass tdate">	
											</fieldset>									
										</div>	

										<div class="col-12 col-sm-6 col-lg-5">	
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="<?php echo e(route('admin.staff.index')); ?>" class="btn btn-warning">Reset</a>
											<a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a>
											</fieldset>
										</div>										
											
									</div>
									
								</form>
							</div>
						</div>
					</div>
				</div>
				
				<div class="table-responsive">
				<table class="table data-list-view">
					<thead>
						<tr>
							<th>S.No.</th>
							<th>Name</th>
							<th>From Time</th>
							<th>To Time</th>
							<th>Reason</th>
							<th>Status</th>
							<th>Date</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
				<?php 
						$dataFound = 1;
						$Id		   = 0;
						if(count($staff_report) > 0) {
							foreach ($staff_report as $staff_report_value) {
								$Id	=	$staff_report_value->id;
						?>
							<tr style="">
							<td><?=$dataFound?></td>
							<td><?php 
							if(!empty($staff_report_value->employee->name)){
								echo $staff_report_value->employee->name; 
							} ?>
							</td>
							<td><?php 
							if(!empty($staff_report_value->from_time)){
								echo $staff_report_value->from_time; 
							} ?>
							</td>
							<td><?php 
							if(!empty($staff_report_value->to_time)){
								echo $staff_report_value->to_time; 
							} ?>
							</td>
							<td><?php 
							if(!empty($staff_report_value->reason)){
								echo $staff_report_value->reason; 
							} ?>
							</td>
							<td><?php 
							if(!empty($staff_report_value->status)){
								echo $staff_report_value->status; 
							} ?>
							</td>
							<td><?php 
							if(!empty($staff_report_value->cdate)){
								echo date('d-m-Y',strtotime($staff_report_value->cdate)); 
							} ?>
							</td>
							<td>
						 <button type="button" class="p-0 btn btn-outline-primary change_sts" data-edit="<?php echo e($staff_report_value->id); ?>" oNChn>Change Status</button>
								</td>
							</tr>
						 
						<?php  
										$dataFound++;
									} 
									
									$Id	=	$Id;
								}else{
							?>
							<tr><td class="text-center text-primary" colspan="8">No Record Found</td></tr>
							<?php } ?>
				</body>
				</table>
					 
				</div>       

			</section>
		</div>
	</div>
</div>



<div class="modal" id="mySubModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Change Status</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <form method="post" id="my_url" action="">
			<?php echo csrf_field(); ?>
			<div class="modal-body">
				<label>Status:</label>
				<select class="form-control select-multiple1 pl-3" name="newStatus" required>
					<option value="">Select</option>
					<option value="Pending">Pending</option>
					<option value="Approved">Approved</option>
					<option value="Rejected">Rejected</option>
					<option value="Deleted">Deleted</option>
				</select>
				<br>
			</div>

	      <!-- Modal footer -->
	      <div class="modal-footer">
	      	<button type="submit" class="btn btn-primary">Save</button>
	        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
	      </div>
      </form>

    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>	
<script type="text/javascript">
$(document).ready(function() {
	$('.select-multiple1').select2({
		placeholder: "Select Any",
		allowClear: true
	});
	
	$("body").on("click", "#download_excel", function (e) {
		var data = {};
			data.emp_id   = $('.emp_id').val(),
			data.fdate = $('.fdate').val(), 
			data.tdate = $('.tdate').val(), 
			window.location.href = "<?php echo URL::to('/admin/'); ?>/staff-movement-system-report-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});

	$("body").on("click", ".change_sts", function (e) {
		var edit_id = $(this).attr("data-edit");
		$("#my_url").attr("action","");
		$("#my_url").attr("action","<?php echo e(url('admin/staff/update/')); ?>/"+edit_id);

		$('#mySubModal').modal({
				backdrop: 'static',
				keyboard: true, 
				show: true
		});

	});
});
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/staffmovementsystem/index.blade.php ENDPATH**/ ?>