
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Software Form</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">Form</li>
							</ol>
						</div>
					</div>
                    <div class="col-4 text-right">
						<a href="<?php echo e(route('software-management')); ?>" class="btn btn-outline-primary mr-1">&#8592; Back</a>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
								<form action="<?php echo e(isset($software) ? route('software-management.update', $software->id) : route('software-management.store')); ?>" enctype="multipart/form-data" method="POST">
									<?php echo csrf_field(); ?>
   									 <?php if(isset($software)): ?>
   									     <?php echo method_field('PUT'); ?>
   									 <?php endif; ?>
										<div class="form-body">
											<div class="row">
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="soft_name">Software Name <span class="text-danger">*</span></label>
														<input type="text" name="soft_name" placeholder="Enter Software Name..." class="form-control" value="<?php echo e(old('soft_name', $software->name ?? '')); ?>" <?php echo e(isset($software) ? 'readonly' : ''); ?>>
														<?php if($errors->has('soft_name')): ?>
														<span class="text-danger"><?php echo e($errors->first('soft_name')); ?> </span>
														<?php endif; ?> 
													</div>
												</div>
                                                <div class="col-md-6 col-12">
													<div class="form-group">
														<label for="soft_type">Software Type <span class="text-danger">*</span></label>
                                                        <select name="soft_type" class="form-control">
    														<option value="">Select Software Type</option>
    														<option value="Free" <?php echo e(old('soft_type', $software->soft_type ?? '') == 'Free' ? 'selected' : ''); ?>>Free</option>
    														<option value="Paid" <?php echo e(old('soft_type', $software->soft_type ?? '') == 'Paid' ? 'selected' : ''); ?>>Paid</option>
														</select>

														<?php if($errors->has('soft_type')): ?>
														<span class="text-danger"><?php echo e($errors->first('soft_type')); ?> </span>
														<?php endif; ?> 
													</div>
												</div>	
                                               <div class="col-md-6 col-12">
													<div class="form-group">
														<label for="description">Description</label>
														<textarea name="description" class="form-control"> <?php echo e(old('description', $software->description ?? '')); ?></textarea>
														<?php if($errors->has('description')): ?>
														<span class="text-danger"><?php echo e($errors->first('description')); ?> </span>
														<?php endif; ?> 
													</div>
												</div>
                                                 <div class="col-md-6 col-12">
													<div class="form-group">
														<label for="soft_owner">Software Owner <span class="text-danger">*</span></label>
                                                       <select name="soft_owner" class="form-control select2">
    														<option value="">Select Owner</option>
    														<?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    														    <option value="<?php echo e($user->id); ?>" <?php echo e((old('soft_owner', $software->owner_id ?? '') == $user->id) ? 'selected' : ''); ?>>
    														        <?php echo e($user->name); ?> (<?php echo e($user->register_id); ?>)
    														    </option>
    														<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>

														<?php if($errors->has('soft_owner')): ?>
														<span class="text-danger"><?php echo e($errors->first('soft_owner')); ?> </span>
														<?php endif; ?> 
													</div>
												</div>
												<div class="col-md-12 col-12 text-right">
													<button type="submit" class="btn font-weight-bold btn-primary mr-1 mb-1">Save</button>
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

<?php $__env->startSection('scripts'); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.select2').select2({
			placeholder: "Select Software Owner",
			allowClear: true
		});
	});
</script>
<?php $__env->stopSection(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/software-management/create.blade.php ENDPATH**/ ?>