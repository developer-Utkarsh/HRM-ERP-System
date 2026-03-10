
<?php $__env->startSection('content'); ?>

<?php if(Auth::viaRemember()): ?>
    <?php echo e(666); ?>

<?php else: ?>
    <?php echo e(777); ?>

<?php endif; ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Add Supervisor</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Add Supervisor</a>
								</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
			<div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
			</div>
		</div>
		<div class="content-body">
			<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<h5 class="mb-2">Add Supervisor By Department</h5>
									<form class="form" action="<?php echo e(route('admin.store-supervisor-by-department')); ?>" method="post" enctype="multipart/form-data" onsubmit="return confirm('Are you sure to want to add supervisor?');">
										<?php echo csrf_field(); ?>
										<div class="form-body"> 
											<div class="row">
												<div class="col-md-3 col-12">
													<div class="form-group">
														<label for="first-name-column">Department</label>
														<select class="form-control select-multiple from_department" name="from_department">
															
															<option value="">Department</option>
															<?php if(count($department_list) > 0): ?>
																<?php $__currentLoopData = $department_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																	<option value="<?php echo e($value->id); ?>" <?php if($value->id == old('from_department')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															<?php endif; ?>
														</select>
														<?php if($errors->has('from_department')): ?>
														<span class="text-danger"><?php echo e($errors->first('from_department')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-3 col-12">
													<div class="form-group">
														<label for="first-name-column">Sub Department</label>
														<select class="form-control select-multiple from_sub_department" name="from_sub_department">
															<option value="">Sub Department</option>
														</select>
														<?php if($errors->has('from_department')): ?>
														<span class="text-danger"><?php echo e($errors->first('from_department')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-3">
													<label for="users-list-status">Branch</label>
													<?php $branches = \App\Branch::where('status', '1')->orderBy('id','desc')->get(); ?>
													<fieldset class="form-group">												
														<select class="form-control select-multiple1" name="branch_id[]">
															<option value="">Select Any</option>
															<?php if(count($branches) > 0): ?>
																<?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																	<option value="<?php echo e($value->id); ?>" <?php if($value->id == old('branch_id')): ?>) selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															<?php endif; ?>
														</select>												
													</fieldset>
												</div>
												<div class="col-md-3 col-12">
													<div class="form-group">
														<label for="first-name-column">Select Supervisor</label>
														<select class="form-control select-multiple to_department_employee" name="to_department_employee">
															<option value="">Select Employee</option>
															<?php if(count($employees_list) > 0): ?>
																<?php $__currentLoopData = $employees_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																	<option value="<?php echo e($value->id); ?>" <?php if(!empty(old('to_department_employee')) && $value->id == old('to_department_employee')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?>(<?php echo e(!empty($value->register_id) ? $value->register_id : '--'); ?>)</option>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															<?php endif; ?>
														</select>
														<?php if($errors->has('to_department_employee')): ?>
														<span class="text-danger"><?php echo e($errors->first('to_department_employee')); ?> </span>
														<?php endif; ?>
													</div>
												</div>	                                      
												<div class="col-md-3 col-12 mt-2">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Submit</button>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>


					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<h5 class="mb-2">Add Supervisor By Employees</h5>
									<form class="form" action="<?php echo e(route('admin.store-supervisor-by-employee')); ?>" method="post" enctype="multipart/form-data" onsubmit="return confirm('Are you sure to want to add supervisor?');">
										<?php echo csrf_field(); ?>
										<div class="form-body"> 
											<div class="row">
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Employees</label>
														<select class="form-control select-multiple employee_name" name="employee_name[]" multiple>
															
															<option value="">Select Employees</option>
															<?php if(count($employees_list) > 0): ?>
																<?php $__currentLoopData = $employees_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																	<option value="<?php echo e($value->id); ?>" <?php if(!empty(old('employee_name')) && in_array($value->id, old('employee_name'))): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?>(<?php echo e(!empty($value->register_id) ? $value->register_id : '--'); ?>)</option>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															<?php endif; ?>
														</select>
														<?php if($errors->has('employee_name')): ?>
														<span class="text-danger"><?php echo e($errors->first('employee_name')); ?> </span>
														<?php endif; ?>
													</div>
												</div> 
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Select Supervisor</label>
														<select class="form-control select-multiple supervisor_employee" name="supervisor_employee">
															<option value="">Select Employee</option>
															<?php if(count($employees_list) > 0): ?>
																<?php $__currentLoopData = $employees_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																	<option value="<?php echo e($value->id); ?>" <?php if(!empty(old('supervisor_employee')) && $value->id == old('supervisor_employee')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?>(<?php echo e(!empty($value->register_id) ? $value->register_id : '--'); ?>)</option>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															<?php endif; ?>
														</select>
														<?php if($errors->has('supervisor_employee')): ?>
														<span class="text-danger"><?php echo e($errors->first('supervisor_employee')); ?> </span>
														<?php endif; ?>
													</div>
												</div>	                                      
												<div class="col-md-4 col-12 mt-2">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Submit</button>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>

					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<h5 class="mb-2">Add Supervisor By Branch</h5>
									<form class="form" action="<?php echo e(route('admin.store-supervisor-by-branch')); ?>" method="post" enctype="multipart/form-data" onsubmit="return confirm('Are you sure to want to add supervisor?');">
										<?php echo csrf_field(); ?>
										<div class="form-body"> 
											<div class="row">											
												<div class="col-md-4 col-12">
													<label for="users-list-status">Location</label>											
													<fieldset class="form-group">												
														<select class="form-control select-multiple1 branch_location" name="branch_location" onchange="locationBranch(this.value);">
															<option value="">Select Any</option>													
															<!--option value="jodhpur">Jodhpur</option>
															<option value="jaipur">Jaipur</option>-->
															<option value="jodhpur" <?php if(!empty(app('request')->input('branch_location')) && 'jodhpur' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>Jodhpur</option>
															<option value="jaipur" <?php if(!empty(app('request')->input('branch_location')) && 'jaipur' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>Jaipur</option>
															<option value="delhi" <?php if(!empty(app('request')->input('branch_location')) && 'delhi' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>Delhi</option>
															<option value="prayagraj" <?php if(!empty(app('request')->input('branch_location')) && 'prayagraj' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>Prayagraj</option>
														</select>	
														<?php if($errors->has('branch_location')): ?>
														<span class="text-danger"><?php echo e($errors->first('branch_location')); ?> </span>
														<?php endif; ?>														
													</fieldset>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Branch</label>
														<select class="form-control select-multiple branch_id" name="branch_id[]" multiple>
															
															<option value="">Branch</option>
															<?php if(count($branch_list) > 0): ?>
																<?php $__currentLoopData = $branch_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																	<option value="<?php echo e($value->id); ?>" <?php if($value->id == old('branch_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															<?php endif; ?>
														</select>
														<?php if($errors->has('branch_id')): ?>
														<span class="text-danger"><?php echo e($errors->first('branch_id')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Select Supervisor</label>
														<select class="form-control select-multiple to_branch_employee" name="to_branch_employee">
															<option value="">Select Employee</option>
															<?php if(count($employees_list) > 0): ?>
																<?php $__currentLoopData = $employees_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																	<option value="<?php echo e($value->id); ?>" <?php if(!empty(old('to_branch_employee')) && $value->id == old('to_branch_employee')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?>(<?php echo e(!empty($value->register_id) ? $value->register_id : '--'); ?>)</option>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															<?php endif; ?>
														</select>
														<?php if($errors->has('to_branch_employee')): ?>
														<span class="text-danger"><?php echo e($errors->first('to_branch_employee')); ?> </span>
														<?php endif; ?>
													</div>
												</div>	                                      
												<div class="col-md-4 col-12 mt-2">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Submit</button>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
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
			url : '<?php echo e(route('admin.employee.get-branch')); ?>',
			data : {'_token' : '<?php echo e(csrf_token()); ?>', 'branch_id': value},
			dataType : 'html',
			success : function (data){
				$('.branch_id').empty();
				$('.branch_id').append(data);
			}
		});
	}


	$(document).ready(function() {
		$('.select-multiple').select2({
			placeholder: "Select",
			allowClear: true
		});
	})
	$(document).ready(function() {
		$('.select-multiple1').select2({
			placeholder: "Select Any",
			allowClear: true
		});
		
		$(".from_department").on("change", function () {
			var department_type_id = $(".from_department option:selected").attr('value');
			if (department_type_id) {
				$.ajax({
					type : 'POST',
					url : '<?php echo e(route('admin.get-sub-department')); ?>',
					data : {'_token' : '<?php echo e(csrf_token()); ?>', 'department_type_id': department_type_id},
					dataType : 'html',
					success : function (data){
						$('.from_sub_department').empty();
						$('.from_sub_department').append(data);
					}
				});
			}
		});
	});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/employee/add-supervisor.blade.php ENDPATH**/ ?>