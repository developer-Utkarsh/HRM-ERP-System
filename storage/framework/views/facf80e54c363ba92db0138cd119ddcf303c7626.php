
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
			<div class="content-header-left col-md-8 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Transfer Asset Details</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Transfer Asset Details</a>
								</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">

			<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						
						
						<div class="table-responsive">
							<table class="table data-list-view">
								<thead>
									<tr>
										<th>S. No.</th>
										<th>Asset Name</th>
										<th>Employee Name</th>
										<th>Quantity</th>
										<th>Created</th>
									</tr>
								</thead>
								<tbody>
								<?php if(count($asset_history) > 0): ?>
									<?php $__currentLoopData = $asset_history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<tr>
										<td><?php echo e($key + 1); ?></td>
										<td class="product-category"><?php echo e($value->name); ?></td>
										<td class="product-category"><?php echo e($value->user_name); ?></td>
										<td class="product-category"><?php echo e($value->assign_asset_qty); ?></td>
										<td class="product-category"><?php echo e(date('d-m-Y',strtotime($value->assign_asset_created_at))); ?></td>
									</tr>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>    
								<?php else: ?>
									<tr ><td class="text-center text-primary" colspan="5">No Record Found</td></tr>
								<?php endif; ?>
								</tbody>
							</table>
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/asset/transfer-asset.blade.php ENDPATH**/ ?>