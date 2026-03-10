
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
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-9">
						<h2 class="content-header-title float-left mb-0">Add Meeting</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Add Meeting</a>
								</li>
							</ol>
						</div>
					</div>
					<div class="col-md-3">
						<a href="<?php echo e(route('admin.appointment')); ?>" class="btn btn-primary float-right ">Back</a>
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
									<form class="form" action="<?php echo e(route('admin.add-place')); ?>" method="post" enctype="multipart/form-data">
										<?php echo csrf_field(); ?>
										<div class="form-body">
											<div class="row">
												<div class="col-md-12 col-12">
													<div class="form-group">
														<label for="first-name-column">Title</label>
														<input type="text" value="" class="form-control" placeholder="Title" name="title" required />
														<?php if($errors->has('title')): ?>
														<span class="text-danger"><?php echo e($errors->first('title')); ?> </span>
														<?php endif; ?>
														
														
														<div class="pt-1"><input type="checkbox" value="1" name="group"/> <span style="color:red;">Mark as Group</span></div>
														
													</div>
												</div>
												<div class="col-md-12 col-12">
													<div class="form-group">
														<label for="first-name-column">Meeting Agenda</label>
														<textarea class="form-control" placeholder="Meeting Agenda" name="description"><?php echo e(old('title')); ?></textarea>
														<?php if($errors->has('description')): ?>
														<span class="text-danger"><?php echo e($errors->first('description')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-12 col-md-6">
													<label for="users-list-role">Branch</label>
													<fieldset class="form-group">												
														<select class="form-control select-multiple branch" name="branch" required>
															<option value="">Select Any</option>
																<?php
																	$branch = DB::table('branches')->select('id','branch_location')->where('is_deleted','0')->groupby('branch_location')->get();
																	if(count($branch) > 0){
																		foreach($branch as $value){
																?>
																<option value="<?php echo e($value->id); ?>" <?php if($value->id == old('branch')): ?> selected="selected" <?php endif; ?>><?php echo e(ucwords($value->branch_location)); ?></option>
																<?php } } ?>
														</select>
														<?php if($errors->has('branch')): ?>
														<span class="text-danger"><?php echo e($errors->first('branch')); ?> </span>
														<?php endif; ?>												
													</fieldset>
												</div>
												<div class="col-12 col-md-6">
													<label for="users-list-role">Meeting Place</label>
													<fieldset class="form-group">												
														<select class="form-control select-multiple place" name="meeting_place_id" required>
															<option value="">Select Any</option>
														</select>
														<?php if($errors->has('place')): ?>
														<span class="text-danger"><?php echo e($errors->first('place')); ?> </span>
														<?php endif; ?>												
													</fieldset>
												</div>
												<div class="col-12 col-md-12">
													<label for="users-list-role">Attendees</label>
													<fieldset class="form-group">											
														<?php //print_r($app_arr); die(); ?>
														<select class="form-control select-multiple emp_id" name="emp_id[]" multiple required>
															<option value="">Select Any</option>
																<?php
																	// $users = DB::table('users')->select('id','name','register_id')->where('is_deleted','0')->where('status','1')->get();
																	if(count($app_arr) > 0){
																		foreach($app_arr as $value){
																?>
																<option value="<?=$value['id'];?>"><?=ucwords($value['name']).' - ( '.$value['designation'].' )';?></option>
																<?php } } ?>
														</select>
														<?php if($errors->has('attendees')): ?>
														<span class="text-danger"><?php echo e($errors->first('attendees')); ?> </span>
														<?php endif; ?>												
													</fieldset>
												</div>
												<div class="col-12 col-md-12">
													<label for="users-list-status">Meeting Type</label>
													<fieldset class="form-group">												
														<select class="form-control select-multiple mtype" name="meeting_type" required>
															<option value="">-- Select --</option>
															<option value="1">Physical</option>
															<option value="2">Virtual</option>
															<option value="3">Both</option>
														</select>
														<?php if($errors->has('meeting_type')): ?>
														<span class="text-danger"><?php echo e($errors->first('meeting_type')); ?> </span>
														<?php endif; ?>												
													</fieldset>
												</div>
												<div class="col-md-12 col-12 mUrl">
													<div class="form-group">
														<label for="first-name-column">Meeting URL</label>
														<input type="text" value="" class="form-control" placeholder="Title" name="meeting_url"/>
														<?php if($errors->has('meeting_url')): ?>
														<span class="text-danger"><?php echo e($errors->first('meeting_url')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-12 col-md-12">
													<label for="users-list-status">Meeting Date</label>
													<fieldset class="form-group">												
														<select class="form-control select-multiple meetingDate" name="appointment_date" required>
															<option value="">-- Select --</option>
															<option value="<?=Date('y-m-d');?>">Today</option>
															<option value="<?=Date('y-m-d', strtotime('+1 days'));?>">Tomorrow</option>
															<option value="<?=Date('y-m-d', strtotime('+2 days'));?>">Day After Tomorrow</option>
															<option value="4">Custom</option>
														</select>
														<?php if($errors->has('appointment_date')): ?>
														<span class="text-danger"><?php echo e($errors->first('appointment_date')); ?> </span>
														<?php endif; ?>												
													</fieldset>
												</div>
												
												<!-- Meeting Date & Time -->
												<div class="col-md-12 col-12 cDate">
													<div class="form-group">
														<label for="first-name-column">Custom Date</label>
														<input type="date" value="" class="form-control cudate" placeholder="Title" name=""/>

													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">From</label>
														<input type="time" value="" class="form-control" placeholder="" name="start_time"  required/>
														<?php if($errors->has('start_time')): ?>
														<span class="text-danger"><?php echo e($errors->first('start_time')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">To</label>
														<input type="time" value="" class="form-control" placeholder="Title" name="end_time"  required/>
														<?php if($errors->has('end_time')): ?>
														<span class="text-danger"><?php echo e($errors->first('end_time')); ?> </span>
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

<style type="text/css">
	.mUrl, .cDate {
		display: none;
	}
</style>

<?php $__env->startSection('scripts'); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple').select2({
			placeholder: "-- Select --",
			allowClear: true
		});
	});
	
	$('.mtype').change(function(){ 
		value = $(this).val();
		
		if(value==2 || value==3){
			$('.mUrl').show();
		}else{
			$('.mUrl').hide();
		}
	});
	
	$(".branch").on("change", function () {
		var branch_id = $(".branch option:selected").attr('value'); 
		if (branch_id) {
			$.ajax({
				type : 'POST',
				url : '<?php echo e(route('admin.get-place')); ?>',
				data : {'_token' : '<?php echo e(csrf_token()); ?>', 'branch_id': branch_id},
				dataType : 'html',
				success : function (data){
					$('.place').empty();
					$('.place').append(data);
				}
			});
		}
	});
	
	
	$(".meetingDate").on("change", function () {
		var id = $(".meetingDate option:selected").attr('value'); 
		
		if(id==4){
			$('.cDate').show();
			$('.meetingDate').prop('name', '');
			$('.cudate').prop('name', 'appointment_date');
		}else{
			$('.meetingDate').prop('name', 'appointment_date');
			$('.cudate').prop('name', '');
			$('.cDate').hide();
		}
	});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/meeting/meeting-add.blade.php ENDPATH**/ ?>