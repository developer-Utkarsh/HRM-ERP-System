
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Edit Course</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('studiomanager.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">Edit Course
								</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
						<a href="<?php echo e(route('studiomanager.course.index')); ?>" class="btn btn-primary mr-1">Back</a>
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
									<form class="form" action="<?php echo e(route('studiomanager.course.chapter-topic-update')); ?>" method="post" enctype="multipart/form-data">
										<?php echo csrf_field(); ?>
										<div class="form-body">
											<div class="row">
												<div class="col-md-12 col-12">
													<div class="form-group">
														<label for="first-name-column">Topic Name</label>
														<textarea name="tname" class="form-control" required><?php echo e($topic->name); ?></textarea>
														<?php if($errors->has('tname')): ?>
														<span class="text-danger"><?php echo e($errors->first('tname')); ?> </span>
														<?php endif; ?> 
													</div>
												</div>
												<div class="col-md-12 col-12">
													<div class="form-group">
														<label for="first-name-column">Duration</label>
														<input type="number" name="tduration" class="form-control" value="<?php echo e($topic->duration); ?>" required />
														<?php if($errors->has('tduration')): ?>
														<span class="text-danger"><?php echo e($errors->first('tduration')); ?> </span>
														<?php endif; ?>
													</div>
												</div>

												<div class="col-md-12 col-12">
													<div class="form-group">
														<label for="first-name-column">Status</label>
														<select class="form-control" name="status">
															<option value="1" <?php if($topic->status==1): ?> selected <?php endif; ?>>Active</option>
															<option value="0" <?php if($topic->status==0): ?> selected <?php endif; ?>>Inactive</option>
														</select>
													</div>
												</div>

												<div class="col-md-6 col-12">
													<input type="hidden" name="topic_id" value="<?php echo e($topic->id); ?>"/>
													<button type="submit" class="btn btn-primary mr-1 mb-1">Update</button>
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
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple').select2({
			placeholder: "Select Subjects",
			allowClear: true
		});
	});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.studiomanager', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/studiomanager/course/ctupdate.blade.php ENDPATH**/ ?>