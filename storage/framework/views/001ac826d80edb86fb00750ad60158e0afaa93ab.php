
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Add Branch</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">Add Branch
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
									<form class="form" action="<?php echo e(route('admin.branch.store')); ?>" method="post" enctype="multipart/form-data">
										<?php echo csrf_field(); ?>
										<div class="form-body">
											<div class="row">
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Branch Name</label>
														<input type="text" class="form-control" placeholder="Branch Name" name="name" value="<?php echo e(old('name')); ?>">
														<?php if($errors->has('name')): ?>
														<span class="text-danger"><?php echo e($errors->first('name')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Branch Address</label>
														<input type="text" class="form-control" placeholder="Branch Address" name="address" value="<?php echo e(old('address')); ?>">
														<?php if($errors->has('address')): ?>
														<span class="text-danger"><?php echo e($errors->first('address')); ?> </span>
														<?php endif; ?>
													</div>
												</div>	
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Nickname</label>
														<input type="text" class="form-control" placeholder="Nickname" name="nickname" value="<?php echo e(old('nickname')); ?>">
														<?php if($errors->has('nickname')): ?>
														<span class="text-danger"><?php echo e($errors->first('nickname')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<label class="mr-2">Branch Type :</label>
													<div class="form-group d-flex align-items-center">														
														<select class="form-control"  name="related" required>	
															<option value=""> Select</option>
															<option value="1">JODHPUR</option>
															<option value="2">JAIPUR</option>
															<option value="3">PRAYAGRAJ</option>
															<option value="4">DELHI</option>
															<option value="5">INDORE</option>
															<option value="6">LUCKNOW</option>
															<option value="7">PATNA</option>
														</select>
													</div>
												</div> 
												<div class="col-md-6 col-12">
													<div class="form-group d-flex align-items-center mt-2">
														<label class="mr-2">Status :</label>
														<label>
															<input type="radio" name="status" value="1" checked>
															Active
														</label>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="status" value="0">
															Inactive
														</label>
													</div>
												</div> 
												<div class="col-12">
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/branch/add.blade.php ENDPATH**/ ?>