
<?php $__env->startSection('content'); ?>


<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Edit Asset Request</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Edit Asset Request</a>
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
									<form class="form" action="<?php echo e(route('admin.request.update', $getRecord->id)); ?>" method="post" enctype="multipart/form-data">
										<?php echo csrf_field(); ?>
										<div class="form-body">
											<div class="row">
												<div class="col-md-12 col-12">
													<div class="form-group">
														<label for="first-name-column">Title</label>
														<input type="text" class="form-control" placeholder="Title" id="" name="title" value="<?php echo e($getRecord->title); ?>">
														<?php if($errors->has('title')): ?>
														<span class="text-danger"><?php echo e($errors->first('title')); ?> </span>
														<?php endif; ?>
													</div>
												</div>		
												<div class="col-md-12 col-12">
													<div class="form-group">
														<label for="first-name-column">Requirements</label>
														<textarea name="requirement" class="form-control" placeholder="Please enter your requirements"><?php echo e($getRecord->requirement); ?></textarea>
														<?php if($errors->has('requirement')): ?>
														<span class="text-danger"><?php echo e($errors->first('requirement')); ?> </span>
														<?php endif; ?>
													</div>
												</div>		
												<div class="col-md-12 col-12">
													<label>Quantity</label>
													<input type="number" name="qty" class="form-control" value="<?php echo e($getRecord->qty); ?>" required />
													<?php if($errors->has('qty')): ?>
													<span class="text-danger"><?php echo e($errors->first('qty')); ?> </span>
													<?php endif; ?>										
												</div>
												<div class="col-md-12 mt-2">
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
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/request/edit_request.blade.php ENDPATH**/ ?>