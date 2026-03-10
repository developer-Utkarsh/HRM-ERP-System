
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
									<form class="form" action="<?php echo e(route('studiomanager.course.update', $course->id)); ?>" method="post" enctype="multipart/form-data">
										<?php echo method_field('PATCH'); ?>
										<?php echo csrf_field(); ?>
										<div class="form-body">
											<div class="row">
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Course Name</label>
														<input type="text" class="form-control" placeholder="Course Name" name="name" value="<?php echo e(old('name', $course->name)); ?>">
														<?php if($errors->has('name')): ?>
														<span class="text-danger"><?php echo e($errors->first('name')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Subjects</label>
														<?php $subjects = \App\Subject::where('status', '1')->orderBy('id', 'desc')->get(); ?>
														<?php if(count($subjects) > 0): ?>
															<?php
															if(1){
																?>
																<select class="form-control select-multiple" multiple="multiple" name="subject_id[]" style="width:100%;">
																	<option value=""> - Select Subjects - </option>
																	<?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																		<option value="<?php echo e($value->id); ?>" <?php if(array_search($value->id, array_column($subject_course, 'subject_id')) !== false): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
																		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
																</select>
																<?php
															}
															else{
															?>
														
														<select class="form-control select-multiple" multiple="multiple" name="subject_id[]">
															<option value=""> - Select Subjects - </option>
															<?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($value->id); ?>" <?php $__currentLoopData = $course->course_subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																<?php if($subject->pivot->subject_id == $value->id): ?> selected="selected" <?php endif; ?>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>><?php echo e($value->name); ?></option>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>
															<?php } ?>
														<?php endif; ?>
														</div>
													</div>
													<div class="col-md-6 col-12">
														<div class="form-group d-flex align-items-center">
															<label class="mr-2">Status :</label>
															<label>
																<input type="radio" name="status" value="1" <?php echo e(($course->status == 1) ? "checked" : ""); ?>>
																Active
															</label>
															&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
															<label>
																<input type="radio" name="status" value="0" <?php echo e(($course->status == 0) ? "checked" : ""); ?>>
																Inactive
															</label>
														</div>
													</div>                                       
													<div class="col-12">
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

<?php echo $__env->make('layouts.studiomanager', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/studiomanager/course/edit.blade.php ENDPATH**/ ?>