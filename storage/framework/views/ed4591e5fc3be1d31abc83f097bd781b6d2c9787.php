
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Send Links</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">Send Links
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
									<form class="form" action="<?php echo e(route('admin.links.faculty_link')); ?>" method="post" enctype="multipart/form-data">
										<?php echo csrf_field(); ?>
										<div class="form-body">
											<div class="row">
												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<h3>Faculty Links</h3>
														<label for="first-name-column">Faculty Name</label>
														<?php if(count($faculties) > 0): ?>
														<select class="form-control select-multiple1" name="employee_id[]" multiple>
															<option value=""> - Select Any - </option>
															<?php $__currentLoopData = $faculties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($value->id); ?>"><?php echo e($value->name . ' ( ' .$value->register_id.' ) '); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>
														<?php endif; ?>
														<?php if($errors->has('employee_id')): ?>
														<span class="text-danger"><?php echo e($errors->first('employee_id')); ?> </span>
														<?php endif; ?>
													</div>
												</div>                                 
												<div class="col-md-6 col-12 mt-4">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Send Link</button>
													&nbsp;&nbsp;&nbsp;&nbsp;
													<a href="<?php echo e(route('admin.links.all_send', 'faculty')); ?>" class="btn btn-primary mr-1 mb-1" >All Faculty Send Link</a>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
						
						
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="<?php echo e(route('admin.links.faculty_link')); ?>" method="post" enctype="multipart/form-data">
										<?php echo csrf_field(); ?>
										<div class="form-body">
											<div class="row">
												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<h3>Studio Manager / TimeTable Manager Links</h3>
														<label for="first-name-column">Studio Manager / TimeTable Manager Name</label>
														<?php if(count($studiomanager) > 0): ?>
														<select class="form-control select-multiple1" name="employee_id[]" multiple>
															<option value=""> - Select Any - </option>
															<?php $__currentLoopData = $studiomanager; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($value->id); ?>"><?php echo e($value->name . ' ( ' .$value->register_id.' ) '); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>
														<?php endif; ?>
													</div>
												</div>                                 
												<div class="col-md-6 col-12 mt-4">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Send Link</button>
													&nbsp;&nbsp;&nbsp;&nbsp;
													<a href="<?php echo e(route('admin.links.all_send', 'studio_manager')); ?>" class="btn btn-primary mr-1 mb-1" >All Studio Manager / TimeTable Manager Send Link</a>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
						
						
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="<?php echo e(route('admin.links.faculty_link')); ?>" method="post" enctype="multipart/form-data">
										<?php echo csrf_field(); ?>
										<div class="form-body">
											<div class="row">
												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<h3>Studio Assistant Links</h3>
														<label for="first-name-column">Studio Assistant Name</label>
														<?php if(count($assistants) > 0): ?>
														<select class="form-control select-multiple1" name="employee_id[]" multiple>
															<option value=""> - Select Any - </option>
															<?php $__currentLoopData = $assistants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($value->id); ?>"><?php echo e($value->name . ' ( ' .$value->register_id.' ) '); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>
														<?php endif; ?>
													</div>
												</div>                                 
												<div class="col-md-6 col-12 mt-4">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Send Link</button>
													&nbsp;&nbsp;&nbsp;&nbsp;
													<a href="<?php echo e(route('admin.links.all_send', 'studio_assistant')); ?>" class="btn btn-primary mr-1 mb-1" >All Studio Assistant Send Link</a>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
						
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="<?php echo e(route('admin.links.faculty_link')); ?>" method="post" enctype="multipart/form-data">
										<?php echo csrf_field(); ?>
										<div class="form-body">
											<div class="row">
												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<h3>Drivers Links</h3>
														<label for="first-name-column">Drivers Name</label>
														<?php if(count($drivers) > 0): ?>
														<select class="form-control select-multiple1" name="employee_id[]" multiple>
															<option value=""> - Select Any - </option>
															<?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($value->id); ?>"><?php echo e($value->name . ' ( ' .$value->register_id.' ) '); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>
														<?php endif; ?>
													</div>
												</div>                                 
												<div class="col-md-6 col-12 mt-4">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Send Link</button>
													&nbsp;&nbsp;&nbsp;&nbsp;
													<a href="<?php echo e(route('admin.links.all_send', 'drivers')); ?>" class="btn btn-primary mr-1 mb-1" >All Drivers Send Link</a>
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
			<!-- // Basic Floating Label Form section end -->
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

<script>
$('.select-multiple1').select2({
	placeholder: "Select",
	allowClear: true
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/links/index.blade.php ENDPATH**/ ?>