
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
						<h2 class="content-header-title float-left mb-0">Add Training Video</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Add Training Video</a>
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
									<form class="form" action="<?php echo e(route('admin.training_video.store')); ?>" method="post" enctype="multipart/form-data">
										<?php echo csrf_field(); ?>
										<div class="form-body">
											<div class="row">
												<div class="col-12 col-md-6">
													<label for="users-list-status">Employee</label>
													<fieldset class="form-group">												
														<select class="form-control select-multiple user_id" name="user_id">
															<option value="">Select Any</option>
															<?php if(count($employee) > 0): ?>
																<?php $__currentLoopData = $employee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																<option value="<?php echo e($value->id); ?>" <?php if($value->id == old('user_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>]
															<?php endif; ?>
														</select>
														<?php if($errors->has('user_id')): ?>
														<span class="text-danger"><?php echo e($errors->first('user_id')); ?> </span>
														<?php endif; ?>												
													</fieldset>
												</div>
												
												<div class="col-12 col-md-6">
													<label for="users-list-status">Department Type</label>
													<fieldset class="form-group">												
														<select class="form-control select-multiple department_type" name="department_type[]" multiple>
															<option value="">Select Any</option>
															<option value="all" <?php if("all" == old('department_type')): ?> selected="selected" <?php endif; ?>>All (Select For All Departments)</option>
															<?php if(count($allDepartmentTypes) > 0): ?>
																<?php $__currentLoopData = $allDepartmentTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																<option value="<?php echo e($value['id']); ?>" <?php if($value['id'] == old('department_type')): ?> selected="selected" <?php endif; ?>><?php echo e($value['name']); ?></option>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															<?php endif; ?>
														</select>
														<?php if($errors->has('department_type')): ?>
														<span class="text-danger"><?php echo e($errors->first('department_type')); ?> </span>
														<?php endif; ?>												
													</fieldset>
												</div>

												<div class="col-12 col-md-6">
													<label for="users-list-status">Category</label>
													<fieldset class="form-group">												
														<select class="form-control select-multiple cat_id" name="cat_id">
															<option value="">Select Any</option>
															<?php if(count($training_category) > 0): ?>
																<?php $__currentLoopData = $training_category; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																<option value="<?php echo e($value->id); ?>" <?php if($value->id == old('cat_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>]
															<?php endif; ?>
														</select>
														<?php if($errors->has('cat_id')): ?>
														<span class="text-danger"><?php echo e($errors->first('cat_id')); ?> </span>
														<?php endif; ?>												
													</fieldset>
												</div>

												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Title</label>
														<input type="text" class="form-control" placeholder="Title" name="title" value="<?php echo e(old('title')); ?>">
														<?php if($errors->has('title')): ?>
														<span class="text-danger"><?php echo e($errors->first('title')); ?> </span>
														<?php endif; ?>
													</div>
												</div>	

												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Date</label>
														<input type="date" class="form-control" placeholder="Date" name="date" value="<?php echo e(old('date')); ?>">
														<?php if($errors->has('date')): ?>
														<span class="text-danger"><?php echo e($errors->first('date')); ?> </span>
														<?php endif; ?>
													</div>
												</div>

												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Video ID</label>
														<input type="text" class="form-control" placeholder="Video ID" name="video_id" value="<?php echo e(old('video_id')); ?>">
														<?php if($errors->has('video_id')): ?>
														<span class="text-danger"><?php echo e($errors->first('video_id')); ?> </span>
														<?php endif; ?>
													</div>
												</div>

												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Video Url</label>
														<input type="text" class="form-control" placeholder="Video Url" name="video_url" value="<?php echo e(old('video_url')); ?>">
														<?php if($errors->has('video_url')): ?>
														<span class="text-danger"><?php echo e($errors->first('video_url')); ?> </span>
														<?php endif; ?>
													</div>
												</div>

												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Image</label>
														<input type="file" class="form-control" name="image_url" value="<?php echo e(old('image_url')); ?>">
														<?php if($errors->has('image_url')): ?>
														<span class="text-danger"><?php echo e($errors->first('image_url')); ?> </span>
														<?php endif; ?>
													</div>
												</div>

												<div class="col-12 col-md-6">
													<label for="users-list-status">Status</label>
													<fieldset class="form-group">												
														<select class="form-control select-multiple status" name="status">
															<?php $status = ['Active', 'Inactive']; ?>
															<option value="">Select Any</option>
															<?php if(count($status) > 0): ?>
																<?php $__currentLoopData = $status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																<option value="<?php echo e($value); ?>" <?php if($value == old('status')): ?> selected="selected" <?php endif; ?>><?php echo e($value); ?></option>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>]
															<?php endif; ?>
														</select>
														<?php if($errors->has('status')): ?>
														<span class="text-danger"><?php echo e($errors->first('status')); ?> </span>
														<?php endif; ?>												
													</fieldset>
												</div>

												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Description</label>
														<textarea class="form-control" placeholder="Description" name="description" ><?php echo e(old('description')); ?></textarea>
														<?php if($errors->has('description')): ?>
														<span class="text-danger"><?php echo e($errors->first('description')); ?> </span>
														<?php endif; ?>
													</div>
												</div>


												<div class="col-12">
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
	$(document).ready(function() {
		$('.select-multiple').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/training_video/add.blade.php ENDPATH**/ ?>