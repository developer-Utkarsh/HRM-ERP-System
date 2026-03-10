
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Add Faculty Leave</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('studiomanager.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">Add Faculty Leave</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
						<a href="<?php echo e(route('studiomanager.faculty-leave')); ?>" class="btn btn-primary mr-1">Back</a>
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
									<form id="leave_add" class="form" action="" method="post" enctype="multipart/form-data">
										<?php echo csrf_field(); ?>
										
										<div class="form-body">
											<div class="row">
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Faculty</label>
														
														<select class="form-control emp_id select-multiple1" id="emp_id" name="emp_id" required>
															<!--<option value="<?php echo e(Auth::user()->id); ?>">Assign To Self </option>
															<option value="0">Open Task </option>-->
															<option value="">Select</option>
															<?php if(count($users) > 0): ?>
															<?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($value['id']); ?>" <?php if($value['id'] == old('task_added_to')): ?> selected="selected" <?php endif; ?>><?=$value['name']." (".$value['mobile'].")";?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
															<?php endif; ?>
														</select>
													</div>
												</div>	
												
												
												
												
											</div>
											<hr>
											<div class="">											
											<div class="row fil">
											<?php
											// $todayDate = date('Y-m-d',strtotime("first day of last month"));
											$todayDate = date('Y-m-01');
											?>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">From Date</label>
														<input type="date" class="form-control from_date" placeholder="Date" name="from_date" value="" min="<?php echo e($todayDate); ?>" required>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">To Date</label>
														<input type="date" class="form-control to_date" placeholder="Date" name="to_date" value="" min="<?php echo e($todayDate); ?>" required>
													</div>
												</div>
												
												<span class="col-md-12">
													<hr>
												</span>
											</div>
											</div>
											<div class="row">
												<div class="col-md-12 col-12">
													<div class="form-group">
														<label for="">Reason</label>
														<textarea class="form-control reason" id="reason" name="reason" value="" required></textarea>	
													</div>
												</div>
											</div>
											
											<div class="row">	                                      
												<div class="col-12">
													<p class="text-danger please_wait"></p>
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
	})
</script>

<script type="text/javascript">
	
	var TodayDate = new Date();
	$(document).on("change",".from_date, .to_date", function () {
		var thisval = $(this);
		var from_date = $(".from_date").val();
		var to_date = $(".to_date").val();
		if(from_date && to_date){
			var from_date= new Date(Date.parse(from_date));
			var to_date= new Date(Date.parse(to_date));
			if (from_date > to_date) {
				$(".to_date").val('');
				alert('From date less then To date');
			}	
		}
		else{
			$(".to_date").val("");
		}
	});
	
	$("#leave_add").submit(function(e) {
		$(".btn_submit").attr('disabled',true);
		$(".please_wait").text('Please Wait...');
		var form = document.getElementById('leave_add');
		var dataForm = new FormData(form);
		e.preventDefault(); 
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},      
			type: "POST",
			url : '<?php echo e(route('studiomanager.faculty_leave_add_save')); ?>',
			data : dataForm,
			processData : false,  
			contentType : false,
			dataType : 'json',
			success : function(data){
				$(".btn_submit").attr('disabled',false);
				$(".please_wait").text('');
				if(data.status == false){
					swal("Error!", data.message, "error");
				} else if(data.status == true){						
					swal("Done!", data.message, "success").then(function(){ 
						location.reload();
					});
				}
			}
		});   
	});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.studiomanager', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/studiomanager/faculty_leave/add.blade.php ENDPATH**/ ?>