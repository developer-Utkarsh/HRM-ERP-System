
<?php $__env->startSection('content'); ?>

<?php if(Auth::viaRemember()): ?>
    <?php echo e(666); ?>

<?php else: ?>
    <?php echo e(777); ?>

<?php endif; ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Bill Details</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Bill Details</a>
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
					<div class="col-md-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="<?php echo e(route('admin.buyer.bill-store')); ?>" method="post" enctype="multipart/form-data">
										<?php echo csrf_field(); ?>
										<?php 
										
										?>
										<h3><?php echo e($bill_txt); ?> Bill</h3>
										<div class="form-body">
											<div class="row">
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Bill No</label>
														<input type="text" class="form-control" placeholder="Bill No" name="bill_no" value="<?php echo e(!empty($get_bill_detail->bill_no) ? $get_bill_detail->bill_no : old('bill_no')); ?>">
														<?php if($errors->has('bill_no')): ?>
														<span class="text-danger"><?php echo e($errors->first('bill_no')); ?> </span>
														<?php endif; ?>
													</div>
												</div>	

												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Bill File</label>
														<input type="file" class="form-control" name="bill_file">
														<?php if($errors->has('bill_file')): ?>
														<span class="text-danger"><?php echo e($errors->first('bill_file')); ?> </span>
														<?php endif; ?>
													</div>
												</div>													

												                                    
												<div class="col-md-4 col-12 mt-2">
											    	<input type="hidden" name="buyer_id" value="<?php echo e($buyer_id); ?>">
													<input type="hidden" name="bill_id" value="<?php echo e(!empty($get_bill_detail->id) ? $get_bill_detail->id : ''); ?>">
													<input type="hidden" name="prev_bill_file" value="<?php echo e(!empty($get_bill_detail->bill_file) ? $get_bill_detail->bill_file : ''); ?>">
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/buyer/add-bill.blade.php ENDPATH**/ ?>