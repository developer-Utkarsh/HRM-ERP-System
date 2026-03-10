
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-md-6">
						<h2 class="content-header-title float-left mb-0">Job Role List</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-md-6">
						<a href="<?php echo e(route('admin.employees.add-job-role')); ?>" class="btn btn-primary float-right">Add Job Role</a>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="<?php echo e(route('admin.employees.job-role')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-md-3">
											<label for="users-list-status">Employee</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple emp_id" name="emp_id">
													<option value="">Select Any</option>
													<?php if(count($employees_list) > 0): ?>
													<?php $__currentLoopData = $employees_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" <?php if(!empty(app('request')->input('emp_id')) && $value->id == app('request')->input('emp_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?>(<?php echo e(!empty($value->register_id) ? $value->register_id : '--'); ?>)</option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php endif; ?>
												</select>												
											</fieldset>
										</div>
										<div class="col-md-5">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="<?php echo e(route('admin.employees.job-role')); ?>" class="btn btn-warning">Reset</a>
											<!--a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a-->
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
								<th>S. No.</th>
								<th>Employee</th>
								<th>Description</th>
								<?php if(Auth::user()->role_id == 29 || Auth::user()->role_id == 24): ?>
								<th>Status</th>
								<?php endif; ?>
								<th>Created</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php if(count($job_role_result) > 0): ?>
							<?php $__currentLoopData = $job_role_result; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr>
								<td><?php echo e($key + 1); ?></td>
								<td class="product-category"><?php echo e(!empty($value->user) ? $value->user->name : ''); ?></td>
								<td class="product-category"><?php echo $value->description; ?></td>
								<?php if(Auth::user()->role_id == 29 || Auth::user()->role_id == 24): ?>
								<td>
									<a href="<?php echo e(route('admin.job_role.status', $value->id)); ?>">
									<strong class="fa fa-lg <?php echo e(!empty($value->status) && $value->status == 'Unlock'  ? 'fa-toggle-on text-success' : 'fa-toggle-off text-grey'); ?>" title="<?php echo e(!empty($value->status) && $value->status == 'Lock'  ? 'Click For Unlock' : 'Click For Lock'); ?>"></strong>
									</a>
								</td>
								<?php endif; ?>
								<td><?php echo e($value->created_at->format('d-m-Y')); ?></td>
								
								<td class="product-action">
									<?php if(Auth::user()->role_id == 29 || Auth::user()->role_id == 24 || $value->status == 'Unlock'): ?>
									<a href="<?php echo e(route('admin.employees.add-job-role', $value->id)); ?>">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
									</a>
									<a href="<?php echo e(route('admin.job_role.delete', $value->id)); ?>" onclick="return confirm('Are You Sure To Delete Job Role')">
										<span class="action-delete"><i class="feather icon-trash"></i></span>
									</a>
									<?php endif; ?>
								</td>
								
							</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php else: ?>
							<tr><td class="text-center text-primary" colspan="10">No Record Found</td></tr>	
							<?php endif; ?>
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
		$('.select-multiple').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
	
	// $("body").on("click", "#download_excel", function (e) {
	// 	var data = {};
	// 		data.name   = $('.name').val(),
	// 		data.status = $('.status').val(), 
	// 		window.location.href = "<?php echo URL::to('/admin/'); ?>/department-report-excel?" + Object.keys(data).map(function (k) {
	// 		return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
	// 	}).join('&');
	// });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/employee/job-role.blade.php ENDPATH**/ ?>