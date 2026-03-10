
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
			<div class="content-header-left col-md-10 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Asset Transfer Details</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Asset Transfer Details</a>
								</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-2"><a href="<?php echo e(route('admin.asset.index')); ?>" class="btn btn-outline-primary float-right"><i class="feather icon-arrow-left"></i></a></div>
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
									<form class="form" action="<?php echo e(route('admin.asset.store-transfer-asset', $id)); ?>" method="post" enctype="multipart/form-data">
										<?php echo csrf_field(); ?>
										<h3>Asset Transfer</h3>
										<div class="form-body">
											<div class="row">
												
												<div class="col-md-4">
													<label for="users-list-status">Serial No.</label>
													<?php $serial = DB::table('asset_child')->where('asset_id',$id)->whereNull('assign')->get(); ?>
													<fieldset class="form-group">												
														<select class="form-control select-multiple1 serial_no" name="serial_no" required>
															<option value="">Select Any</option>
															<?php if(count($serial) > 0): ?>
															<?php $__currentLoopData = $serial; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($value->id); ?>" <?php echo e(old('serial_no') == $value->id ? 'selected' : ''); ?>><?php echo e($value->serial_no); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															<?php endif; ?>
														</select>
														<?php if($errors->has('serial_no')): ?>
														<span class="text-danger"><?php echo e($errors->first('serial_no')); ?> </span>
														<?php endif; ?>												
													</fieldset>
												</div>
												<div class="col-md-4">
													<label for="users-list-status">Employee</label>
													<?php $user = \App\User::select('id','name','register_id')->where('status', '1')->orderBy('id','desc')->get(); ?>
													<fieldset class="form-group">												
														<select class="form-control select-multiple1 emp_id" name="emp_id" required>
															<option value="">Select Any</option>
															<?php if(count($user) > 0): ?>
															<?php $__currentLoopData = $user; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($value->id); ?>" <?php echo e(old('emp_id') == $value->id ? 'selected' : ''); ?>><?php echo e($value->name); ?><?php if(!empty($value->register_id)): ?><?php echo e(' - ('.$value->register_id.')'); ?><?php endif; ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															<?php endif; ?>
														</select>
														<?php if($errors->has('emp_id')): ?>
														<span class="text-danger"><?php echo e($errors->first('emp_id')); ?> </span>
														<?php endif; ?>												
													</fieldset>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Quantity</label>
														<input type="number" class="form-control" placeholder="Quantity" name="qty" value="<?php echo e(!empty($get_product_transfer_detail->qty) ? $get_product_transfer_detail->qty : old('qty')); ?>" required>
														<?php if($errors->has('qty')): ?>
														<span class="text-danger"><?php echo e($errors->first('qty')); ?> </span>
														<?php endif; ?>
													</div>
												</div>	
												<div class="col-md-12 col-12">
													<div class="form-group">
														<label for="first-name-column">Remark</label>
														<textarea class="form-control" placeholder="Remark" name="remark" required><?php echo e(!empty($get_product_transfer_detail->remark) ? $get_product_transfer_detail->remark : old('remark')); ?></textarea>
														<?php if($errors->has('remark')): ?>
														<span class="text-danger"><?php echo e($errors->first('remark')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												
											
												<div class="col-md-12 col-12">
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
		$('.select-multiple1').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/asset/add-transfer-asset.blade.php ENDPATH**/ ?>