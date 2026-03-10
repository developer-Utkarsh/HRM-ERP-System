
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Edit Topic</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('studiomanager.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Edit Topic</a>
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
									<form class="form" action="<?php echo e(route('studiomanager.topics.update', $topic->id)); ?>" method="post" enctype="multipart/form-data">
										<?php echo method_field('PATCH'); ?>
										<?php echo csrf_field(); ?>
										<div class="form-body">
											<div class="row">
												<div class="col-md-6 col-12" id="course_loader">
													<div class="form-group">
														<label>Course</label>
														<?php $courses = \App\Course::where('status', '1')->orderBy('id', 'desc')->get(); ?>
														<?php if(count($courses) > 0): ?>
														<select class="form-control course_id" name="course_id">
															<option value=""> - Select Course - </option>
															<?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($value->id); ?>" <?php if($topic->course_id == $value->id): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>
														<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
														<?php endif; ?>
														<?php if($errors->has('course_id')): ?>
														<span class="text-danger"><?php echo e($errors->first('course_id')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-6 col-12" id="subject_loader">
													<div class="form-group">
														<label>Subject</label>
														<select class="form-control subject_id" name="subject_id">
															<?php if(isset($topic->subject_id) && !empty($topic->subject_id)): ?>
															<option value="<?php echo e($topic->subject_id); ?>"><?php echo e($topic->subject->name); ?></option>
															<?php endif; ?>
														</select>
														<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label>Chapter</label>
														<select class="form-control chapter_id" name="chapter_id">
															<?php if(isset($topic->chapter_id) && !empty($topic->chapter_id)): ?>
															<option value="<?php echo e($topic->chapter_id); ?>"><?php echo e($topic->chapter->name); ?></option>
															<?php endif; ?>
														</select>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label>Topic Name</label>
														<input type="text" name="name" class="form-control" value="<?php echo e(old('name', $topic->name)); ?>" placeholder="Topic Name">
														<?php if($errors->has('name')): ?>
														<span class="text-danger"><?php echo e($errors->first('name')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label>Duration</label>
														<input type="text" name="duration" class="form-control" value="<?php echo e(old('duration', $topic->duration)); ?>" placeholder="Duration">
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group d-flex align-items-center">
														<label class="mr-2">Status :</label>
														<label>
															<input type="radio" name="status" value="1" <?php echo e(($topic->status == 1) ? "checked" : ""); ?>>
															Active
														</label>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="status" value="0" <?php echo e(($topic->status == 0) ? "checked" : ""); ?>>
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
<script type="text/javascript">
	$(".course_id").on("change", function () {
		var course_id = $(".course_id option:selected").attr('value');
		if (course_id) {
			$.ajax({
				beforeSend: function(){
					$("#course_loader i").show();
				},
				type : 'POST',
				url : '<?php echo e(route('studiomanager.get-batch-subject')); ?>',
				data : {'_token' : '<?php echo e(csrf_token()); ?>', 'course_id': course_id},
				dataType : 'html',
				success : function (data){
					$("#course_loader i").hide();
					$('.subject_id').empty();
					$('.subject_id').append(data);
				}
			});
		}
	});
</script>
<script type="text/javascript">
	$(".subject_id").on("change", function () {
		var subject_id = $(".subject_id option:selected").attr('value');
		if (subject_id) {
			$.ajax({
				beforeSend: function(){
					$("#subject_loader i").show();
				},
				type : 'POST',
				url : '<?php echo e(route('studiomanager.get-chapter')); ?>',
				data : {'_token' : '<?php echo e(csrf_token()); ?>', 'subject_id': subject_id},
				dataType : 'html',
				success : function (data){
					$("#subject_loader i").hide();
					$('.chapter_id').empty();
					$('.chapter_id').append(data);
				}
			});
		}
	});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.studiomanager', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/studiomanager/topic/edit.blade.php ENDPATH**/ ?>