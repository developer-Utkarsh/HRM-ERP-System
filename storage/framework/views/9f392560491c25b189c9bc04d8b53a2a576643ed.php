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
			<div class="text-right pb-2">				
				<a href="<?php echo e(route('faculty-invoice-list',[$user_id])); ?>" class="btn-dark p-1">Invoice List</a>
			</div>
			
			<section id="data-list-view" class="data-list-view-header">				
				<div class="card">
					<div class="card-content">
						<div class="card-body">
							<form class="form" action="<?php echo e(route('faculty-invoice-save')); ?>" method="post" enctype="multipart/form-data">
								<?php echo csrf_field(); ?>					
								<input type="hidden" name="user_id" value="<?php echo e($user_id); ?>" />
								<div class="form-body">
									<div class="row">
										<div class="col-md-12 col-12">
											<div class="form-group">
												<label for="first-name-column">Month</label>
												<input type="month" class="form-control" name="month" value="" required>
												<?php if($errors->has('month')): ?>
												<span class="text-danger"><?php echo e($errors->first('month')); ?> </span>
												<?php endif; ?>
											</div>
										</div>	
										<div class="col-md-12 col-12">
											<div class="form-group">
												<label for="first-name-column">Upload Invoice</label>
												<input type="file" class="form-control" name="upload_file" value="" required>
												<?php if($errors->has('upload_file')): ?>
												<span class="text-danger"><?php echo e($errors->first('upload_file')); ?> </span>
												<?php endif; ?>
											</div>
										</div>	
										<div class="col-md-4 mt-2">
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
	$(document).ready(function() {
		$('.select-multiple').select2({
			placeholder: "Select",
			allowClear: true
		});
		
		$('.select-multiple1').select2({
			placeholder: "Select",
			allowClear: true
		});
	});
</script>

<?php echo $__env->make('layouts.notification', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>
</html><?php /**PATH /var/www/html/laravel/resources/views/admin/all_reports/faculty_invoice.blade.php ENDPATH**/ ?>