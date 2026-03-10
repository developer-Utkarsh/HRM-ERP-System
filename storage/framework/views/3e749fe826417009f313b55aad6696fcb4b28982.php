
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Add Studio</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('studiomanager.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">Add Studio
								</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
						<a href="<?php echo e(route('studiomanager.studios.index')); ?>" class="btn btn-primary mr-1">Back</a>
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
									<form class="form" action="<?php echo e(route('studiomanager.studios.store')); ?>" method="post" enctype="multipart/form-data">
										<?php echo csrf_field(); ?>
										<div class="form-body">
											<div class="row">
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Studio Name</label>
														<input type="text" class="form-control" placeholder="Studio Name" name="name" value="<?php echo e(old('name')); ?>">
														<?php if($errors->has('name')): ?>
														<span class="text-danger"><?php echo e($errors->first('name')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-6 col-12" id="branch_loader">
													<div class="form-group">
														<?php $branches = \App\Branch::where('status', '1')->orderBy('id', 'desc')->get(); ?>
														<label for="first-name-column">Branch</label>
														<?php if(count($branches) > 0): ?>
														<select class="form-control branch_id select-multiple1" name="branch_id">
															<option value=""> - Select Branch - </option>
															<?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($value->id); ?>" <?php if($value->id == old('branch_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>
														<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
														<?php endif; ?>
														<?php if($errors->has('branch_id')): ?>
														<span class="text-danger"><?php echo e($errors->first('branch_id')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-6 col-12" id="chk_loader">
													<div class="form-group">
														<label for="first-name-column">Studio Assistant</label>
														<select class="form-control assistant_id select-multiple2" name="assistant_id">
															<option value=""> - Select Studio Assistant - </option>
														</select>
														<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
														<small class="msg text-danger"></small>
														<?php if($errors->has('assistant_id')): ?>
														<span class="text-danger"><?php echo e($errors->first('assistant_id')); ?> </span>
														<?php endif; ?>
													</div>
												</div>												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Floor</label>
														<select class="form-control select-multiple3" name="floor">
															<option value=""> - Select Floor - </option>
															<?php for($i=1;$i<=10;$i++) { ?>
															<option value="<?php echo e($i); ?>"><?php echo e($i); ?></option>
															<?php } ?>
														</select>
														<?php if($errors->has('floor')): ?>
														<span class="text-danger"><?php echo e($errors->first('floor')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Order No</label>
														<input type="number" class="form-control" placeholder="Order No" name="order_no" min="1">
														<?php if($errors->has('order_no')): ?>
														<span class="text-danger"><?php echo e($errors->first('order_no')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												
												<div class="col-md-6 col-12">
													<div class="form-group d-flex align-items-center">
														<label class="mr-2">Status :</label>
														<label>
															<input type="radio" name="status" value="1" checked>
															Active
														</label>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="status" value="0">
															Inactive
														</label>
													</div>
												</div>
												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Type</label>
														<select class="form-control change_class" name="type">
															<option value=""> - Select Type - </option>
															<option value="Online">Online</option>
															<option value="Offline" >Offline</option>
														</select>
														<?php if($errors->has('type')): ?>
														<span class="text-danger"><?php echo e($errors->first('type')); ?> </span>
														<?php endif; ?>
													</div>
												</div>	
												
												<div class="col-md-6 col-12 capacity_div" style="display:none;">
													<div class="form-group">
														<label for="first-name-column">Capacity</label>
														<input type="number" class="form-control" placeholder="Capacity" name="capacity" value="<?php echo e(old('capacity')); ?>" min="0">
														<?php if($errors->has('capacity')): ?>
														<span class="text-danger"><?php echo e($errors->first('capacity')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-6 col-12 mt-2">
													<div class="form-group d-flex align-items-center">
														<label class="mr-2">IS OBS System :</label>
														<label>
															<input type="radio" name="is_obs" value="Yes" >
															Yes
														</label>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="is_obs" value="No" checked>
															No
														</label>
													</div>
												</div> 
												
												<div class="col-12">
													<button type="submit" class="btn btn-primary mr-1 mb-1 btn_submit">Submit</button>
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
			placeholder: "Select",
			allowClear: true
		});
		$('.select-multiple2').select2({
			placeholder: "Select",
			allowClear: true
		});
		$('.select-multiple3').select2({
			placeholder: "Select",
			allowClear: true
		});
	})
</script>
<script type="text/javascript">
	$(".branch_id").on("change", function () {
		var branch_id = $(".branch_id option:selected").attr('value');
		if (branch_id) {
			$.ajax({
				beforeSend: function(){
					$("#branch_loader i").show();
				},
				type : 'POST',
				url : '<?php echo e(route('studiomanager.get-branchwise-assistant')); ?>',
				data : {'_token' : '<?php echo e(csrf_token()); ?>', 'branch_id': branch_id},
				dataType : 'html',
				success : function (data){
					$("#branch_loader i").hide();
					$('.assistant_id').empty();
					$('.assistant_id').append(data);
				}
			});
		}
	});
</script>
<script type="text/javascript">
	$(".assistant_id").on("change", function () {
		var assistant_id = $(".assistant_id option:selected").attr('value');
		if (assistant_id) {
			$.ajax({
				beforeSend: function(){
					$("#chk_loader i").show();
				},
				type : 'POST',
				url : '<?php echo e(route('studiomanager.getassistantexits')); ?>',
				data : {'_token' : '<?php echo e(csrf_token()); ?>', 'assistant_id': assistant_id},
				dataType : 'json',
				success : function (data){
					if (data.status == true) {
						$("#chk_loader i").hide();
						$(".msg").text(data.data);
						$('.btn_submit').attr('disabled', 'disabled');
					} else {
						$("#chk_loader i").hide();
						$(".msg").text(data.data);
						$('.btn_submit').removeAttr('disabled');
					}
				}
			});
		}
	});
	
	$(".change_class").on("change", function () {
		if($(this).val()=='Offline'){
			$(".capacity_div").css('display','block');
		}
		else{			
			$(".capacity_div").css('display','none');
		}
	});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.studiomanager', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/studiomanager/studio/add.blade.php ENDPATH**/ ?>