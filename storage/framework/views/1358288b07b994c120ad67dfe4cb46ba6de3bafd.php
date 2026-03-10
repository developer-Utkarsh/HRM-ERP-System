<!DOCTYPE html>
<html class="loading" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="description" content="">
    <meta name="keywords" content="">   

   <link href="<?php echo e(url('../laravel/public/logo.png')); ?>" rel="icon" type="image/ico" />
    <title><?php echo e(config('app.name')); ?> - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('laravel/public/admin/css/vendors.min.css')); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('laravel/public/admin/css/bootstrap.css')); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('laravel/public/admin/css/bootstrap-extended.css')); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('laravel/public/admin/css/colors.css')); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('laravel/public/admin/css/components.css')); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('laravel/public/admin/css/vertical-menu.css')); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('laravel/public/admin/css/dashboard-analytics.css')); ?>">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css">
	<style>
		.m_body {
			width: 50%;
		}

		@media  only screen and (max-width: 600px) {
			.m_body {
				width: 100%;
			}
		}
	</style>

</head>

<body class="vertical-layout vertical-menu-modern 2-columns  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">

<div class="app-content content m_body" style="margin: 0 auto;">
	<div class="content-wrapper" style="margin-top: 2px;">
		<div class="content-body">
			<section id="data-list-view" class="data-list-view-header">
				<div class="text-right pb-2">
					<a href="<?php echo e(route('faculty-invoice-add')); ?>?user_id=<?php echo e($user_id); ?>" class="btn-dark p-1">Back</a>
				</div>
				<div class="card mb-1">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter ">
								<h3 class="mb-0">Invoice List</h3>
							</div>
						</div>
					</div>
				</div>
				<div class="card mb-1">
					<div class="card-content collapse show">
						<div class="card-body">
							<form class="" action="<?php echo e(route('faculty-invoice-list',[$user_id])); ?>" method="get">
								<div class="form-group">
									<input type="month" class="form-control month" name="month" value="<?php echo e(app('request')->input('month')); ?>">
								</div>
								<div class="text-right">
									<button type="submit" class="btn btn-success">Submit</button> &nbsp;
									<button type="button" class="btn btn-primary" onclick="location.href = '<?php echo e(route('faculty-invoice-list',[$user_id])); ?>';">Reset</button>
								</div>
							</form>
						</div>
					</div>
				</div>
				
				<?php if(count($record) > 0){ ?>
						<?php $__currentLoopData = $record; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<div class="card mb-1" style="font-size:13px;">
					<div class="card-content">
						
						<div class="card-body">
							<div class="row">
								<div class="col-5 font-weight-bold">Invoice Month</div>
								<div class="col-7">: <?php echo e(date('F Y', strtotime($value->month . '-01'))); ?></div>
							</div>
							<div class="row" style="padding-top:5px;">
								<div class="col-5 font-weight-bold">Invoice</div>
								<div class="col-7 font-weight-bold">: 
									<?php if(!empty($value->invoice)){ ?>
										<a href="<?php echo e(asset('laravel/public/invoice/' . $value->invoice)); ?>" download class="text-primary">Preview</a>

									<?php }else{ echo 'No File Available'; } ?>
								</div>
							</div>
							<div class="row" style="padding-top:5px;">
								<div class="col-5 font-weight-bold">Status</div>
								<div class="col-7">: 
									<?php 
										if($value->status==1){
											echo 'Approved'; 
										}else if($value->status==2){
											echo 'Rejected';
										}else{
											echo 'Pending';
										}
									?>
								</div>
							</div>
							<div class="row" style="padding-top:5px;">
								<div class="col-5 font-weight-bold">Remark</div>
								<div class="col-7">: <?php echo e($value->reason??'-'); ?></div>
							</div>
							<div class="row" style="padding-top:5px;">
								<div class="col-5 font-weight-bold">Uploaded Date</div>
								<div class="col-7">: <?php echo date('d-m-Y h:i:s', strtotime($value->created_at)); ?></div>
							</div>							
						</div>
					</div>
				</div>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>									
				<?php }else{ ?>
					
					<div class="text-center">No Data Found</div>	
				<?php } ?>
				
			</section>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
		</div>
	</div>

<script src="<?php echo e(asset('laravel/public/admin/js/vendors.min.js')); ?>"></script>
<script src="<?php echo e(asset('laravel/public/admin/js/app-menu.js')); ?>"></script>
<script src="<?php echo e(asset('laravel/public/admin/js/app.js')); ?>"></script>
<script src="<?php echo e(asset('laravel/public/admin/js/components.js')); ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>	
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">

	$("#generate_salary_otp").submit(function(e) { 
		var form = document.getElementById('generate_salary_otp');
		var dataForm = new FormData(form);
		e.preventDefault();
		$.ajax({
			beforeSend: function(){
				
			},
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},      
			type: "POST",
			url : '<?php echo e(route('material-send-otp')); ?>',
			data : dataForm,
			processData : false, 
			contentType : false,
			dataType : 'json',
			success : function(data){
				if(data.status == false){
					swal("Error!", data.message, "error");
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
		var form1 = document.getElementById('generate_salary_otp');
		var form = document.getElementById('check_salary_otp');
		var dataForm1 = new FormData(form1);
		var dataForm = new FormData(form);
		dataForm.append('mobile_no', dataForm1.get('mobile_no'));
		e.preventDefault();
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},      
			type: "POST",
			url : '<?php echo e(route('material-access-otp')); ?>',
			data : dataForm,
			processData : false, 
			contentType : false,
			dataType : 'json',
			success : function(data){ alert(data.status);
				if(data.status == false){
					swal("Error!", data.message, "error");
				}else if(data.status == true){	 
					window.location.href = "<?php echo e(route('employee-details')); ?>/"+data.material_id+"/web";
				}
			}
		}); 
	});

	$(".product_accept").on("click", function() {  
		var request_id = $(this).attr("data-id"); 
		var product_id = $(this).attr("data-product-id"); 
	
		// alert(request_id);
		
		$.ajax({
			type : 'POST',
			url : '<?php echo e(route('web-view-product-accept')); ?>',
			data : {'_token' : '<?php echo e(csrf_token()); ?>', 'request_id': request_id,'product_id':product_id},
			dataType : 'json',
			success : function (data){
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


	$(".product_return").on("click", function() {  
		var request_id = $(this).attr("data-id"); 
	
		$.ajax({
			type : 'POST',
			url : '<?php echo e(route('request.web-view-product-return')); ?>',
			data : {'_token' : '<?php echo e(csrf_token()); ?>', 'request_id': request_id},
			dataType : 'json',
			success : function (data){
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
<?php echo $__env->make('layouts.notification', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>
</html><?php /**PATH /var/www/html/laravel/resources/views/admin/all_reports/faculty_invoice_list.blade.php ENDPATH**/ ?>