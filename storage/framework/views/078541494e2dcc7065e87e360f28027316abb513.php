
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-11 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-md-8">
						<h2 class="content-header-title float-left mb-0">Inventory History</h2>
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
			<div class="col-md-1"><a href="<?php echo e(route('admin.branch-product-inventory')); ?>" class="btn btn-outline-primary float-right"><i class="feather icon-arrow-left"></i></a></div>
		</div>
		<div class="content-body">
			<section id="data-list-view" class="data-list-view-header">
				
				<div class="table-responsive">
					<table class="table data-list-view">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Name</th>								
								<th>Qty</th>
								<th>Status</th>
								<th>Date</th>
							</tr>
						</thead>
						<tbody>
						<?php if(count($product) > 0): ?>
							<?php $__currentLoopData = $product; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr>
								<td><?php echo e($key + 1); ?></td>
								<td class="product-category"><?php echo e($value->name); ?></td>
								<td class="product-category"><?php echo e($value->qty); ?></td>
								<td class="product-category"><?php echo e($value->status); ?></td>
								<td><?php echo e($value->created_at->format('d-m-Y')); ?></td>
							</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						<?php else: ?>
						<tr ><td class="text-center text-primary" colspan="8">No Record Found</td></tr>
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
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple1').select2({
			width: "100%",
			placeholder: "Select Category",
			allowClear: true
		});
	});
	
	$(".cat_id").on("change", function () {
		var cat_id = $(".cat_id option:selected").attr('value'); 
		if (cat_id) {
			$.ajax({
				type : 'POST',
				url : '<?php echo e(route('admin.product.get-sub-cat')); ?>',
				data : {'_token' : '<?php echo e(csrf_token()); ?>', 'cat_id': cat_id},
				dataType : 'html',
				success : function (data){
					$('.sub_cat_id').empty();
					$('.sub_cat_id').append(data);
				}
			});
		}
	});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/product/inventory-history.blade.php ENDPATH**/ ?>