
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Edit Holiday</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Edit Holiday</a>
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
									<form class="form" action="<?php echo e(route('admin.batch_holiday.update', $holiday->id)); ?>" method="post" enctype="multipart/form-data">
										<!-- <?php echo method_field('PATCH'); ?>-->
										<?php echo csrf_field(); ?>
										<div class="form-body">
											<div class="row">

												<div class="col-12 col-md-4 branch_loader">
													<label for="users-list-status">Location</label>
													<fieldset class="form-group">	
														<?php $locations = \App\Location::where('status', '1')->where('is_deleted', '0')->orderBy('name')->get(); 
														?>											
														<select class="form-control select-multiple2 branch_location" name="branch_location" id="">
															<option value="">Select Any</option>
															<?php if(count($locations) > 0): ?>
															<?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($value->name); ?>" <?php if((!empty(old('branch_location')) && $value->id == old('branch_location')) || (!empty($holiday->location))): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															<?php endif; ?>
														</select>
													</fieldset>
												</div>
												<div class="col-12 col-md-4 branch_loader">
													<label for="users-list-status">Course</label>
													<?php $course = \App\Course::where('status', '1')->get(); ?>
													<fieldset class="form-group">												
														<select class="form-control select-multiple1 course_id" name="course_id" id="">
															<option value="">Select Any</option>
															<?php if(count($course) > 0): ?>
															<?php $__currentLoopData = $course; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($value->id); ?>"  <?php if((!empty(old('batch_id')) && $value->id == old('batch_id')) || (!empty($holiday->course_id))): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															<?php endif; ?>
														</select>
														<?php if($errors->has('batch_id')): ?>
														<span class="text-danger"><?php echo e($errors->first('batch_id')); ?> </span>
														<?php endif; ?>
														<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
													</fieldset>
												</div>
												<div class="col-12 col-md-4 branch_loader">
													<label for="users-list-status">Batch</label>
													<?php $batch = \App\Batch::where('status', '1')->get(); ?>
													<fieldset class="form-group">												
														<select class="form-control select-multiple1 batch_id" name="batch_id[]" id="" multiple>
															<option value="">Select Any</option>
															<?php if(count($batch) > 0): ?>
															<?php $__currentLoopData = $batch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($value->id); ?>"  <?php if((!empty(old('batch_id')) && $value->id == old('batch_id')) || (!empty($holiday->batch_id) && in_array($value->id, json_decode($holiday->batch_id)))): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															<?php endif; ?>
														</select>
														<?php if($errors->has('batch_id')): ?>
														<span class="text-danger"><?php echo e($errors->first('batch_id')); ?> </span>
														<?php endif; ?>
														<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
													</fieldset>
												</div>
												<!--
												<div class="col-12 col-md-2">
													<button class="btn btn-primary mt-1 float-right" type="button" onclick="selectAll()">Select All</button>
												</div>
												<div class="col-12 col-md-2">
													<button class="btn btn-primary mt-1" type="button" onclick="deselectAll()">Unselect All</button>
												</div>
												-->
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Title</label>
														<input type="text" class="form-control" placeholder="Title" name="title" value="<?php echo e(old('name', $holiday->title)); ?>">
														<?php if($errors->has('title')): ?>
														<span class="text-danger"><?php echo e($errors->first('title')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Date</label>
														<input type="date" class="form-control" name="date" value="<?php echo e(old('name', $holiday->date)); ?>">
														<?php if($errors->has('date')): ?>
														<span class="text-danger"><?php echo e($errors->first('date')); ?> </span>
														<?php endif; ?>
													</div> 
												</div>
												
												<div class="col-md-4 col-12" style="display:none;">
													<div class="form-group d-flex align-items-center mt-2">
														<label class="mr-2">Type :</label>
														<label>
															<input type="radio" name="type" value="Public" <?php echo e(($holiday->type == 'Public') ? "checked" : ""); ?>>
															Public
														</label>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="type" value="Optional" <?php echo e(($holiday->type == 'Optional') ? "checked" : ""); ?>>
															Optional
														</label>
													</div>
												</div>	

												<div class="col-md-4 col-12">
													<div class="form-group d-flex align-items-center mt-2">
														<label class="mr-2">Status :</label>
														<label>
															<input type="radio" name="status" value="1" <?php echo e(($holiday->status == 1) ? "checked" : ""); ?>>
															Active
														</label>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="status" value="0" <?php echo e(($holiday->status == 0) ? "checked" : ""); ?>>
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

$(".branch_location").on("change", function () {
	var b_location = $(this).val();
	if (b_location) {
		$.ajax({
			type : 'POST',
			url : '<?php echo e(route('admin.get-multi-location-wise-branch')); ?>',
			data : {'_token' : '<?php echo e(csrf_token()); ?>', 'b_location': b_location},
			dataType : 'html',
			success : function (data){
				$('.branch_id').empty();
				$('.branch_id').append(data);
			}
		});
		
	}
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/batch_holiday/edit.blade.php ENDPATH**/ ?>