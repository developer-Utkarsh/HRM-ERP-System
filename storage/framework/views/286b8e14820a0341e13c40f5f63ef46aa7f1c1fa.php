
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Import Invoice</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">Import Invoice
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
									<form class="form" action="<?php echo e(route('admin.invoice.store')); ?>" method="post" enctype="multipart/form-data">
										<?php echo csrf_field(); ?>
										<div class="form-body">
											<div class="row">
												
												<div class="col-md-6 col-12">
													<div class="form-group">
													    <h3>Tax Invoice</h3>
														<label for="first-name-column">Import File</label>
														<input type="file" class="form-control" name="import_file">
														<?php if($errors->has('import_file')): ?>
														<span class="text-danger"><?php echo e($errors->first('import_file')); ?></span>
														<?php endif; ?>
														<br>
														<!--<a href="javascript:void(0);" class="download_sample">Download Sample</a>-->
													</div>
												</div>                                 
												<div class="col-md-6 col-12 mt-4">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Submit</button>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
						
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="<?php echo e(route('admin.invoice.credit-store')); ?>" method="post" enctype="multipart/form-data">
										<?php echo csrf_field(); ?>
										<div class="form-body">
											<div class="row">
												
												<div class="col-md-6 col-12">
													<div class="form-group">
													    <h3>Credit Note</h3>
														<label for="first-name-column">Import File</label>
														<input type="file" class="form-control" name="credit_import_file">
														<?php if($errors->has('credit_import_file')): ?>
														<span class="text-danger"><?php echo e($errors->first('credit_import_file')); ?></span>
														<?php endif; ?>
														<br>
														<!--<a href="javascript:void(0);" class="download_sample">Download Sample</a>-->
													</div>
												</div>                                 
												<div class="col-md-6 col-12 mt-4">
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

<script>

$(document).on("click",".download_sample",function(){
	var course_id = $(".course_id").val();
	if(course_id!=''){
		document.location.href = "download_sample?course_id="+course_id;	
	}
	else{
		alert('Please select course');
	}
});


</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/invoice/add.blade.php ENDPATH**/ ?>