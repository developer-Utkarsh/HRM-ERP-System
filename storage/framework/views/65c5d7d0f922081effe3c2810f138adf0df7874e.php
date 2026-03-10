
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div> 
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Multi Course Planner <?php echo e($plantext); ?></h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Add Request</a>
								</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
			<div class="content-header-right text-md-right col-md-3 col-12 d-md-block">
				<a href="<?php echo e(route('admin.multi-course-planner.planner-request-view')); ?>"><button class="btn btn-primary" type="button">Planner Request View</button></a>
			</div>
		</div>
		<div class="content-body">
			<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="<?php echo e(route('admin.multi-course-planner.save-planner-request')); ?>" method="post" enctype="multipart/form-data">
										<?php echo csrf_field(); ?>
										<div class="form-body">
											<div class="row">
												<div class="col-12 col-md-4">
													<label for="users-list-status">Course <sup class="text-danger">*</sup></label>
													<fieldset class="form-group">	
														<?php $course = \App\Course::where('status', '1')->where('is_deleted', '0')->orderBy('name')->get(); 
														?>								
														<select class="form-control select-multiple2 course_id" name="course_id" required>
															<option value="">Select Any</option>
															<?php $__currentLoopData = $course; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																<option value="<?php echo e($value->id); ?>" 
																	<?php if((!empty($getRequest->course_id) && $getRequest->course_id == $value->id) || (empty($getRequest->course_id) && old('course_id') == $value->id)): ?> 
																		selected 
																	<?php endif; ?>>
																	<?php echo e($value->name); ?>

																</option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>
													</fieldset>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Planner Type Naming <sup class="text-danger">*</sup></label>
														<?php $planner_name = $getRequest->planner_name ?? old('planner_name'); ?>
														<input type="text" class="form-control"  name="planner_name" value="<?php echo e($planner_name); ?>" required>
														<?php if($errors->has('planner_name')): ?>
														<span class="text-danger"><?php echo e($errors->first('planner_name')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-12 col-md-4">
													<label for="users-list-status">City Of Batch <sup class="text-danger">*</sup></label>
													<fieldset class="form-group">	
														<?php $locations = \App\Branch::select('branch_location as name')->where('status', '1')->where('is_deleted', '0')->orderBy('name')->groupby('branch_location')->get(); 
														?>								
														<select class="form-control select-multiple2 branch_location" name="branch_location" required>
															<option value="">Select Any</option>
															<?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																<option value="<?php echo e($value->name); ?>"
																	<?php if((!empty($getRequest->city) && $getRequest->city == $value->name) || (empty($getRequest->city) && old('branch_location') == $value->name)): ?> 
																		selected 
																	<?php endif; ?>>
																	<?php echo e(ucwords($value->name)); ?>

																</option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>
													</fieldset>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Expected Batch Duration (In Days) <sup class="text-danger">*</sup></label>
														
														<?php $duration = $getRequest->duration ?? old('duration'); ?>
														<input type="number" class="form-control" name="duration" value="<?php echo e($duration); ?>" required>
														<?php if($errors->has('duration')): ?>
														<span class="text-danger"><?php echo e($errors->first('duration')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-12 col-md-4 branch_loader">
													<label for="users-list-status">Course Mode <sup class="text-danger">*</sup></label>
													<fieldset class="form-group">	
														<?php $mode = ['Online','Offline','Hybrid']; ?>								
														<select class="form-control select-multiple2 mode" name="mode" required>
															<option value="">Select Any</option>
															<?php $__currentLoopData = $mode; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																<option value="<?php echo e($value); ?>" 
																	<?php if((!empty($getRequest->mode) && $getRequest->mode == $value) || (empty($getRequest->mode) && old('mode') == $value)): ?> 
																		selected 
																	<?php endif; ?>>
																	<?php echo e($value); ?>

																</option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>
													</fieldset>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Planner Timelines <sup class="text-danger">*</sup></label>
														
														<?php $timelines = $getRequest->timelines ?? old('timeline'); ?>
														<input type="date" class="form-control"  id="timeline" placeholder="Planner Timelines" name="timeline" value="<?php echo e($timelines); ?>" required>
														<?php if($errors->has('timeline')): ?>
														<span class="text-danger"><?php echo e($errors->first('timeline')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-12 col-12">
													<div class="form-group">
														<label for="first-name-column">Special Instructions</label>
														<?php $remark = $getRequest->remark ?? old('remark'); ?>
														<textarea name="remark" class="form-control"><?php echo e($remark); ?></textarea>
														<?php if($errors->has('remark')): ?>
														<span class="text-danger"><?php echo e($errors->first('remark')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-12">
													<input type="hidden" name="edit_id" value="<?php echo e($getRequest->id ?? ''); ?>"/>
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
	$('.select-multiple1').select2({
		placeholder: "Select Any",
		allowClear: true
	});
	$('.select-multiple2').select2({
		placeholder: "Select Any",
		allowClear: true
	});
});

function selectAll() {
    $(".select-multiple1 > option").prop("selected", true);
    $(".select-multiple1").trigger("change");
}

function deselectAll() {
    $(".select-multiple1 > option").prop("selected", false);
    $(".select-multiple1").trigger("change");
}


</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const dateInput = document.getElementById("timeline");
        const today = new Date();
        today.setDate(today.getDate() + 7); // add 3 days
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        dateInput.min = `${yyyy}-${mm}-${dd}`;
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/multi-course-planner/planner_request.blade.php ENDPATH**/ ?>