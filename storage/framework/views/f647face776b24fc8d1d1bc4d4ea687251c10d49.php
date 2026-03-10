
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
						<h2 class="content-header-title float-left mb-0">Add Asset</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Add Asset</a>
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
									<form class="form" action="<?php echo e(route('admin.asset.store')); ?>" method="post" enctype="multipart/form-data">
										<?php echo csrf_field(); ?>
										<div class="form-body">
											<div class="row">
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Product Name</label>
														
														<select name="name" class="form-control select-multiple" id="name">
															<option value="">-- Select Asset --</option>
															<?php $__currentLoopData = $asset; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $as => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>{
															<option value="<?php echo e($value->id); ?>" <?php echo e(old('name') == $value->id  ? 'selected' : ''); ?>><?php echo e($value->name); ?><?php if(!empty($value->qty)): ?><?php echo e(' - ('.($value->qty - $value->transfer_qty).')'); ?><?php endif; ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>
														<?php if($errors->has('name')): ?>
														<span class="text-danger"><?php echo e($errors->first('name')); ?> </span>
														<?php endif; ?> 
														
													</div>
												</div>	
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Quantity</label>
														<input type="number" class="form-control" placeholder="Quantity" name="qty" value="<?php echo e(!empty($get_product_transfer_detail->qty) ? $get_product_transfer_detail->qty : old('qty')); ?>">
														<?php if($errors->has('qty')): ?>
														<span class="text-danger"><?php echo e($errors->first('qty')); ?> </span>
														<?php endif; ?>
													</div>
												</div>	
												<div class="col-md-4">
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
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
	$('.select-multiple').select2({
		width: '100%',
		placeholder: "Select",
		allowClear: true
	});

	/*
	$(document).ready(function() {            
		$('#asset_name').autocomplete({			
			source:function(request, response) {
				$.get('asset-autocomplete', {'term':request.term}, function(recv){response(recv);});				
			},
			minLength: 0,
			select:function(event, ui) {
				$('#asset_name').attr('value', ui.item.label);
				$('#asset_name').val(ui.item.label);						
			}	
		}).focus(function(){$(this).data("uiAutocomplete").search($(this).val());});
	});	
	*/
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/asset/add.blade.php ENDPATH**/ ?>