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
					<a href="<?php echo e(route('employee-enquiry-history',[$user_id])); ?>" class="btn-dark p-1" style="border-radius:3px;">History</a>
				</div> 
				<div class="card">
					<div class="card-content">
						
						<div class="card-body">
							<div>
								<b>Your Enquiry </b></br>
							</div>
							<hr>
							<form class="form" action="<?php echo e(route('employee-enquiry-store')); ?>" method="post" enctype="multipart/form-data">
								<?php echo csrf_field(); ?>
								<input type="hidden" name="user_id" class="form-control" value="<?=$user_id;?>"/>
								<div class="form-body">
									<div class="row">
										<div class="col-md-12 col-12 modal-body">
											<label><b>Select Category</b> <span style="color:red;">*</span></label>
											<select class="form-control select-multiple" name="category_id" style="margin-bottom: 20px;">
												<option value="">Select Category</option>
												<?php if(count($category_list) > 0): ?>
												<?php $__currentLoopData = $category_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<option value="<?php echo e($value->id); ?>" <?php if(!empty(old('category_id')) && old('category_id') == $value->id): ?><?php echo e('selected'); ?><?php endif; ?>><?php echo e($value->name); ?></option>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												<?php endif; ?>
											</select>
										</div>
										<div class="col-md-12 col-12 modal-body">
											<label><b>Select Priority</b> <span style="color:red;">*</span></label>
											<select class="form-control select-multiple1" name="priority" style="margin-bottom: 20px;">
												<option value="">Select Priority</option>
												<option value="low" <?php if(!empty(old('priority')) && old('priority') == 'low'): ?><?php echo e('selected'); ?><?php endif; ?>>Low</option>
												<option value="medium" <?php if(!empty(old('priority')) && old('priority') == 'medium'): ?><?php echo e('selected'); ?><?php endif; ?>>Medium</option>
												<option value="high" <?php if(!empty(old('priority')) && old('priority') == 'high'): ?><?php echo e('selected'); ?><?php endif; ?>>High</option>
											</select>
										</div>
										<div class="col-md-12 col-12 modal-body">
											<label><b>Description</b> <span style="color:red;">*</span></label>
											<textarea name="description" class="form-control" rows="5" required> </textarea>
											
										</div> 
										
										<div class="col-md-12 mt-2">
											<button type="submit" class="btn btn-dark mr-1 mb-1 w-100">Submit</button>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>

			</section>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
		</div>
	</div>

<style type="text/css">
	.input {
		position: relative;
		bottom: -3px;
	}
</style>
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
	function getissue(id){
		if(id==1){
			$('#employee').show();
		}else{
			$('#employee').hide();
		}
	}
	
</script>
<?php echo $__env->make('layouts.notification', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>
</html><?php /**PATH /var/www/html/laravel/resources/views/admin/app_enquiry/employee-enquiry.blade.php ENDPATH**/ ?>