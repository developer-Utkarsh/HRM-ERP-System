
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Assign Coupon</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">Add Coupon</li>
							</ol>
						</div>
					</div>
                    <div class="col-4 text-right">
						<a href="<?php echo e(route('request-access.index')); ?>" class="btn btn-outline-primary mr-1">&#8592; Back</a>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="<?php echo e(route('coupon.store')); ?>" method="post" enctype="multipart/form-data">
										<?php echo csrf_field(); ?>
										<div class="form-body">
											<div class="row">
												<div class="col-md-5 col-12">
													<div class="form-group">
														<label for="mobile">Mobile Number</label>
														<input type="number" name="mobile" class="form-control" placeholder="Enter Mobile Number...">
														<?php if($errors->has('mobile')): ?>
														<span class="text-danger"><?php echo e($errors->first('mobile')); ?> </span>
														<?php endif; ?> 
													</div>
												</div>	
												
												<div class="col-md-5 col-12">
													<div class="form-group">
														<label for="coupon_code">Coupon Code</label>
														<input type="text" class="form-control" name="coupon_code" value="EXTRA10" readonly disabled>
														<?php if($errors->has('coupon_code')): ?>
														<span class="text-danger"><?php echo e($errors->first('coupon_code')); ?> </span>
														<?php endif; ?>
													</div>
												</div>	
												<div class="col-md-2 d-flex justify-content-center align-items-center">
													<button type="submit" class="btn font-weight-bold btn-primary mr-1 mb-1">Assign Now</button>
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


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/request-access/request.blade.php ENDPATH**/ ?>