
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-md-8">
						<h2 class="content-header-title float-left mb-0">Vendor History</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>					
				</div>
			</div>
		</div>
		<div class="content-body">		
			<section id="data-list-view" class="data-list-view-header">				
				<div class="table-responsive">
					<table class="table data-list-view">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Request Type</th>
								<th>PO No.</th>
								<th>PO Date</th>
								<th>Pay Amount</th>
								<th>Advance Amount</th>
							</tr>
						</thead>
						<tbody>
							<?php if(count($buyer_history) > 0): ?>
							<?php $i = 1; ?>
							<?php $__currentLoopData = $buyer_history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $bh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td><?php echo e($i++); ?></td>
									<td>
										<?php
											if ($bh->request_type == '1') { 
												$powoText = 'WRL'; 
												$pwText = 'WO'; 
											} else { 
												$powoText = 'MRL'; 
												$pwText = 'PO'; 
											} 
										?>
										<?php echo e($powoText); ?>

									</td>
									<td>
										<?php
											if (!empty($bh->po_month)) {
												$po_month = $bh->po_location . '-' . $bh->po_no . "/" . $bh->po_month;
											} else {
												$po_month = $bh->po_no;
											}
										?>
										<?php echo e('UTK' . $pwText . '-' . $po_month); ?>

									</td>
									<td><?php echo e(date('d-m-Y', strtotime($bh->pdate))); ?></td>
									<td><?php echo e($bh->final_amt); ?></td>
									<td><?php echo e($bh->advance_amt); ?></td>
								</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						<?php else: ?>
							<tr>
								<td colspan="6" class="text-center">No Record Found</td>
							</tr>
						<?php endif; ?>

						</tbody>
					</table>
				</div>                   
			</section>
			

		</div>
	</div>
</div>


<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/buyer/vendor-view.blade.php ENDPATH**/ ?>