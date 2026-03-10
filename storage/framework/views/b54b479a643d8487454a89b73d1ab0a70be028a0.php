
<?php $__env->startSection('content'); ?>
<?php //echo '<pre>'; print_r('http://'.request()->getHttpHost().'/');die;?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">OTP</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">Salary OTP</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								
								<form id="generate_salary_otp" method="post">
									<p class="msg-success text-success">Generate OTP for view SALARY Module</p>
									<div class="row">
										<div class="col-md-6">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary submit_generate_otp">Generate OTP</button>
											</fieldset>
										</div>
									</div>
								</form>
								
								<form id="check_salary_otp" method="post" style="display:none;">
									<p class="msg-success text-success">Plesae check OTP on your registered Mobile Number.</p>
									<div class="row">
										
									    <div class="col-md-3">
											<label for="users-list-role"></label>
											 <input type="text" class="form-control" placeholder="Enter OTP" name="otp" value="<?php if(!empty(app('request')->input('otp'))): ?><?php echo e(app('request')->input('otp')); ?><?php endif; ?>">
										</div>
										
										
										<div class="col-md-6">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary submit_salary_btn">Submit</button>
											</fieldset>
										</div>
									</div>
								</form>
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

	$("#generate_salary_otp").submit(function(e) {
		var form = document.getElementById('generate_salary_otp');
		e.preventDefault();
		$('.submit_generate_otp').attr('disabled', 'disabled');
		$.ajax({
			beforeSend: function(){
				
			},
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},      
			type: "POST",
			url : '<?php echo e(route('admin.salary-send-otp')); ?>',
			data : {},
			processData : false, 
			contentType : false,
			dataType : 'json',
			success : function(data){
				if(data.status == false){
					swal("Error!", data.message, "error");
					$('.submit_generate_otp').removeAttr('disabled');
				} 
				else if(data.status == true){
					$("#generate_salary_otp").css('display','none');
					$("#check_salary_otp").css('display','block');
				}
			}
		}); 
	});
	
	$("#check_salary_otp").submit(function(e) {
		$('.msg-success').text('');
		var form = document.getElementById('check_salary_otp');
		var dataForm = new FormData(form);
		e.preventDefault();
		$('.submit_salary_btn').attr('disabled', 'disabled');
		$.ajax({
			beforeSend: function(){
				
			},
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},      
			type: "POST",
			url : '<?php echo e(route('admin.salary-access-otp')); ?>',
			data : dataForm,
			processData : false, 
			contentType : false,
			dataType : 'json',
			success : function(data){
				if(data.status == false){
					swal("Error!", data.message, "error");
					$('.submit_salary_btn').removeAttr('disabled');
				} else if(data.status == true){	
					location.reload();
					/* swal("Done!", data.message, "success").then(function(){ 
						location.reload();
					}); */
					// $('.submit_salary_btn').removeAttr('disabled');
				}
			}
		}); 
	});
</script>



<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/salary/otp.blade.php ENDPATH**/ ?>