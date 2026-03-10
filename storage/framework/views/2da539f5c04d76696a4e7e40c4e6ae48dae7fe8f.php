
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
						<h2 class="content-header-title float-left mb-0">Transfer Inventory Details</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Transfer Details</a>
								</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
			<!--div class="col-md-3"><a href="<?php echo e(route('admin.add-transfer-inventory', $product_id)); ?>" class="btn btn-outline-primary float-right">Add Transfer</a></div-->
			<div class="col-md-4"><a href="<?php echo e(route('admin.transfer-branch-inventory')); ?>" class="btn btn-outline-primary float-right">Back</a></div>
			<div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
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
										<th>Transfer From</th>
										<th>Transfer To</th>
										<th>Quantity</th>
										<th>Status</th>
										<th>Remark</th>
										<th>Created</th>
										<!--th>Action</th-->
									</tr>
								</thead>
								<tbody>
								<?php if(count($get_product_detail) > 0): ?>
									<?php $__currentLoopData = $get_product_detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<tr>
										<td><?php echo e($key + 1); ?></td>
										<td class="product-category"><?php echo e($value->transfer_from == 0 ? 'Warehouse' : $value->transfer_from_name); ?></td>
										<td class="product-category"><?php echo e($value->transfer_to_name); ?></td>
										<td class="product-category"><?php echo e($value->qty); ?></td>
										<td class="product-category"><?php echo e($value->status); ?></td>
										<td class="product-category"><?php echo e($value->remark); ?></td>
										<td class="product-category"><?php echo e(date('d-m-Y',strtotime($value->created_at))); ?></td>
										<?php if($value->status == 'Pending'): ?>
										<!--td class="product-category">
										
										<a title="Update Bill" href="<?php echo e(route('admin.edit-transfer-inventory', [ $product_id, $value->id ] )); ?>">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
										</a>
										
										<a title="Delete Bill" href="<?php echo e(route('admin.delete-transfer-inventory', $value->id)); ?>" onclick="return confirm('Are You Sure To Delete Transfer Product')">
											<span class="action-delete"><i class="feather icon-trash"></i></span>
										</a>
										</td-->
										<?php endif; ?>
									</tr>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>    
								<?php else: ?>
									<tr ><td class="text-center text-primary" colspan="9">No Record Found</td></tr>
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/product/transfer-inventory.blade.php ENDPATH**/ ?>