
<?php $__env->startSection('content'); ?>

<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Discount Category</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Add Discount Category</a>
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
									<h5 class="mb-2">Add Discount Category</h5>
									<form class="form" action="<?php echo e(route('admin.store-paternity-leave')); ?>" method="post" enctype="multipart/form-data" onsubmit="return confirm('Are you sure to want to add paternity leave?');">
										<?php echo csrf_field(); ?>
										<div class="form-body"> 
											<div class="row">
												<div class="col-md-8 col-12">
													<div class="form-group">
														<label for="first-name-column">Category Name</label>
														<input type="text" class="form-control " id=""  name="cat_name" required>
														<?php if($errors->has('cat_name')): ?>
														<span class="text-danger"><?php echo e($errors->first('cat_name')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												
												<div class="col-md-4 col-12 mt-2">
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

<?php $__env->startSection('scripts'); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple').select2({
			placeholder: "Select",
			allowClear: true
		});
	});
	
	$('#dropdownYear').each(function() {

	  var year = (new Date()).getFullYear();
	  var current = year;
	  year -= 3;
	  for (var i = 3; i < 8; i++) {
		if ((year+i) == current)
		  $(this).append('<option selected value="' + (year + i) + '">' + (year + i) + '</option>');
		else
		  $(this).append('<option value="' + (year + i) + '">' + (year + i) + '</option>');
	  }

	})
	
 </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/discountApprovel/discount_category_add.blade.php ENDPATH**/ ?>