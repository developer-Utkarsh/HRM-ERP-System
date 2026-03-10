
<?php $__env->startSection('content'); ?>

<?php $role_id = Auth::user()->role_id; ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Task</h2>
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
								<form action="<?php echo e(route('admin.task.index')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Employee Name</label>
											<fieldset class="form-group">
												<input type="text" class="form-control name" name="name" placeholder="Name, EMP Code" value="<?php echo e(app('request')->input('name')); ?>">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Branch</label>
											<?php $branches = \App\Branch::where('status', '1')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 branch_id" name="branch_id">
													<option value="">Select Any</option>
													<?php if(count($branches) > 0): ?>
													<?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" <?php if($value->id == app('request')->input('branch_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php endif; ?>
												</select>												
											</fieldset>
										</div>
										<?php if($role_id == 20 || $role_id == 24 || $role_id == 29 ){ ?>
										<div class="col-md-2">
											<div class="form-group">
												<label for="first-name-column">Department Type</label>
												<?php if(count($allDepartmentTypes) > 0): ?>
												<select class="form-control get_role select-multiple1 department_type" name="department_type" id="se_department_type">
													<option value=""> - Select Any - </option>
													<?php $__currentLoopData = $allDepartmentTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value['id']); ?>" <?php if($value['id'] == app('request')->input('department_type')): ?> selected="selected" <?php endif; ?>><?php echo e($value['name']); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>
												<?php endif; ?>
											</div>
										</div>
										<?php } ?>
										
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-verified">From</label>
											<fieldset class="form-group">
												<input type="date" name="fdate" class="form-control StartDateClass fdate" value="<?php echo e(app('request')->input('fdate')); ?>" id="">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-verified">To</label>
											<fieldset class="form-group">
												<input type="date" name="tdate" class="form-control EndDateClass tdate" value="<?php echo e(app('request')->input('tdate')); ?>" id="">
											</fieldset>
										</div>
									</div>
									<fieldset class="form-group" style="float:right;">		
									<button type="submit" class="btn btn-primary">Search</button>
									<a href="<?php echo e(route('admin.task.index')); ?>" class="btn btn-warning">Reset</a>
									<a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a>
									<a href="javascript:void(0)" id="download_pdf" class="btn btn-primary">Export in PDF</a>
									</fieldset>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table class="table data-list-view" style="background:#fff;">
						<thead style="text-align: ;">
							<tr>
								<th>S. No.</th>
								<th>Employee Name</th>
								<th>Date</th>
								<th>Task</th>
								<th>Plan Hour</th>
								<th>Status</th>
								<th>Task History</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody >
						<?php
							if(count($taskDate) > 0){
						    $i = 1;						    
						    $statusArray = array();
						    // 
							foreach($taskDate as  $key => $task_val){  
									?>
									<tr >
										<td><?=$i++;?></td>
										<td class="product-category"><?php echo e($task_val->user ?  $task_val->user->name : ''); ?></td>
										<td class="product-category"><?php echo e(date('d-m-Y',strtotime($task_val->date))); ?></td>
										<td class="product-category" colspan="4">
										<table class="table data-list-view" style="background: #f7f7f73d;">
											<?php
											foreach($task_val->task_details as $task_details){
												?>
												<tr>
													<td style="width:200px;"><?=$task_details->name?></td>
													<td><?=$task_details->plan_hour?></td>
													<td><?=$task_details->status?></td>
													<td>
													<a href="<?php echo e(route('admin.task.task_history', 
													[
														'task_id' => $task_details->task_id, 
														'task_detail_id' => $task_details->id
													])); ?>" class="btn btn-sm btn-primary waves-effect waves-light">History</a>
													</td>
												</tr>
												<?php
											}
											?>
											
											
										</table>
										</td>
												
										<td class="product-action">
											
											<?php //@if(Auth::user()->role_id != 21)
												$usr = false;
												if(Auth::user()->role_id == 1 || Auth::user()->role_id == 24 || Auth::user()->role_id == 21 || Auth::user()->role_id == 20 || Auth::user()->role_id == 28 || Auth::user()->role_id == 16 || Auth::user()->role_id == 6 || Auth::user()->role_id == 22 || Auth::user()->role_id == 23 || Auth::user()->role_id == 25 || Auth::user()->role_id == 26 || Auth::user()->role_id == 30){
													$usr = true;
												}
											?>
											
											
											<?php if($usr || Auth::user()->role_id == 29): ?>
											<a href="<?php echo e(route('admin.task.edit', $task_val->id)); ?>">
												<span class="action-edit"><i class="feather icon-edit"></i></span>
											</a>
											<a href="<?php echo e(route('admin.task.task_delete', $task_val->id)); ?>" onclick="return confirm('Are You Sure To Delete Task')">
												<span class="action-delete"><i class="feather icon-trash"></i></span>
											</a>
											<?php endif; ?> 
											
											<a href="<?php echo e(route('admin.task.view', $task_val->id)); ?>">
												<span class="action-edit"><i class="feather icon-eye"></i></span>
											</a>
										</td>
									</tr>
									<?php
									$statusArray = array();
								}
							
							}else{	
							?>
								<tr><td class="text-center text-primary" colspan="9">No Record Found</td></tr>	
							<?php } ?>
							
						</tbody>
					</table>
				</div>                   
			</section>
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
	$('.select-multiple2').select2({
		placeholder: "Select Any",
		allowClear: true
	});
});

$("body").on("click", "#download_excel", function (e) {
	/* if ($userTable.data().count() == 0) {
		swal("Warning!", "Not have any data!", "warning");
		return;
	} */
	var data = {};
		data.branch_id = $('.branch_id').val(),
		data.name = $('.name').val(),
		data.fdate = $('.fdate').val(),
		data.tdate = $('.tdate').val(),
		data.department_type = $('.department_type').val(),
	window.location.href = "<?php echo URL::to('/admin/'); ?>/task-report-excel?" + Object.keys(data).map(function (k) {
		return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
	}).join('&');
});


$("body").on("click", "#download_pdf", function (e) {
	
	var data = {};
		
	data.branch_id = $('.branch_id').val(),
	data.name = $('.name').val(),
	data.fdate = $('.fdate').val(),
	data.tdate = $('.tdate').val(),
	data.department_type = $('.department_type').val(),
	window.open("<?php echo URL::to('/admin/'); ?>/task-report-pdf?" + Object.keys(data).map(function (k) {
		return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
	}).join('&'));
	/* window.location.href = "<?php echo URL::to('/admin/'); ?>/studio-report-pdf?" + Object.keys(data).map(function (k) {
		return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
	}).join('&'); */
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/task/index.blade.php ENDPATH**/ ?>