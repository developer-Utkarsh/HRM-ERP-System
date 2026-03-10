
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Absent Users</h2>
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
								<form action="<?php echo e(route('admin.attendance.absentuser')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-4 col-lg-3">
											<label for="users-list-role">Employee Name</label>
											<fieldset class="form-group">
												<input type="text" class="form-control name" name="name" placeholder="Name, EMP Code" value="<?php echo e(app('request')->input('name')); ?>">
											</fieldset>
										</div>
										<div class="col-12 col-sm-4 col-lg-3">
											<label for="users-list-verified">Date</label>
											<fieldset class="form-group">
											<?php
											$date = date('Y-m-d');
											if(!empty(app('request')->input('fdate'))){
												$date = app('request')->input('fdate');
											}
											?>
												<input type="date" name="fdate" class="form-control fdate" value="<?php echo e($date); ?>" id="">
											</fieldset>
										</div>
										<div class="col-md-3">
											<label for="users-list-status">Location</label>											
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 branch_location" name="branch_location" onchange="locationBranch(this.value);">
													<option value="">Select Any</option>													
													<option value="jodhpur" <?php if(!empty(app('request')->input('branch_location')) && 'jodhpur' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>Jodhpur</option>
													<option value="jaipur" <?php if(!empty(app('request')->input('branch_location')) && 'jaipur' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>Jaipur</option>
													<option value="delhi" <?php if(!empty(app('request')->input('branch_location')) && 'delhi' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>Delhi</option>
													<option value="prayagraj" <?php if(!empty(app('request')->input('branch_location')) && 'prayagraj' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>Prayagraj</option>
													<option value="indore" <?php if(!empty(app('request')->input('branch_location')) && 'indore' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>Indore</option>
												</select>												
											</fieldset>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label for="first-name-column">Branch</label>
												<?php if(count($allBranches) > 0): ?>
												<select class="form-control get_role select-multiple1 branch_id" name="branch_id">
													<option value=""> - Select Any - </option>
													<?php $__currentLoopData = $allBranches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value['id']); ?>" <?php if($value['id'] == app('request')->input('branch_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value['name']); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>
												<?php endif; ?>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label for="first-name-column">Department Type</label>
												<?php if(count($allDepartmentTypes) > 0): ?>
												<select class="form-control get_role select-multiple1" name="department_type">
													<option value=""> - Select Any - </option>
													<?php $__currentLoopData = $allDepartmentTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value['id']); ?>" <?php if($value['id'] == app('request')->input('department_type')): ?> selected="selected" <?php endif; ?>><?php echo e($value['name']); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>
												<?php endif; ?>
											</div>
										</div>
										
										
										
									</div>
									
									<fieldset class="form-group"  style="float:right;">		
									<button type="submit" class="btn btn-primary">Search</button>
									<a href="<?php echo e(route('admin.attendance.absentuser')); ?>" class="btn btn-warning">Reset</a>
									<a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a>
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
								<th>Employee Code</th>
								<th>Mobile</th>
							</tr>
						</thead>
						<tbody >
						<?php
							if(count($responseArray) > 0){
								$i = 1;
								foreach($responseArray as  $key => $value){
							?>
							<tr >
								<td><?=$i++;?></td>
								<td class="product-category"><?php echo e(isset($value->name) ?  $value->name : ''); ?></td>
								<td class="product-category"><?php echo e(isset($value->register_id) ?  $value->register_id : ''); ?></td>
								<td class="product-category"><?php echo e(isset($value->mobile) ?  $value->mobile : ''); ?></td>
							</tr>
							<?php
								}	
							}else{
							?>
							<tr>
								<td class="text-center" colspan="5">No Data Found</td>
							</tr>
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
function locationBranch(value){
	$.ajax({
		type : 'POST',
		url : '<?php echo e(route('admin.attendance.get-branch')); ?>',
		data : {'_token' : '<?php echo e(csrf_token()); ?>', 'branch_id': value},
		dataType : 'html',
		success : function (data){
			$('.branch_id').empty();
			$('.branch_id').append(data);
		}
	});
}

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
		// data.branch_id = $('.branch_id').val(),
		data.name = $('.name').val(),
		data.fdate = $('.fdate').val(),
		data.branch_location = $('.branch_location').val(),
		data.branch_id = $('.branch_id').val(),
		data.department_type = $('.department_type').val(),
	window.location.href = "<?php echo URL::to('/admin/'); ?>/absentuser-download-report-excel?" + Object.keys(data).map(function (k) {
		return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
	}).join('&');
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/attendance/absentuser.blade.php ENDPATH**/ ?>