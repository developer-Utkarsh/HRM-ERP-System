
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
						<h2 class="content-header-title float-left mb-0">Add Product</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Add Product</a>
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
									<form class="form" action="<?php echo e(route('admin.product.store')); ?>" method="post" enctype="multipart/form-data">
										<?php echo csrf_field(); ?>
										<div class="form-body">
											<div class="row">
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Product Name</label>
														<input type="text" class="form-control" placeholder="Name" name="name" id="pro_name" value="<?php echo e(old('name')); ?>">
														<?php if($errors->has('name')): ?>
														<span class="text-danger"><?php echo e($errors->first('name')); ?> </span>
														<?php endif; ?>
													</div>
												</div>		
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Product Code</label>
														<input type="text" class="form-control" placeholder="Product Code" name="pcode"  value="<?php echo e(old('pcode')); ?>" required>
														<?php if($errors->has('pcode')): ?>
														<span class="text-danger"><?php echo e($errors->first('pcode')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Category</label>
														 <select class="form-control select-multiple1 cat_id" name="cat_id">
															<option value="">Select Category</option>
															<?php if(count($category) > 0): ?>
															<?php $__currentLoopData = $category; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoryvalue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($categoryvalue->id); ?>" <?php echo e(old('cat_id') ? 'selected' : ''); ?>><?php echo e($categoryvalue->name); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															<?php endif; ?>
														</select>
														<?php if($errors->has('cat_id')): ?>
														<span class="text-danger"><?php echo e($errors->first('cat_id')); ?> </span>
														<?php endif; ?>
													</div>
												</div>	
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Sub Category</label>
														<select class="form-control select-multiple4 sub_cat_id" name="sub_cat_id">
															<?php if(!empty(old('cat_id'))): ?>
																<?php
																	$subCatData = DB::table('category')->where('parent', old('cat_id'))->get();
																?>
																<?php $__currentLoopData = $subCatData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $subCatDataValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																	<option value="<?php echo e($subCatDataValue->id); ?>" <?php echo e(old('sub_cat_id', !empty(old('cat_id')) && $subCatDataValue->id == old('cat_id') ? 'selected' : '' )); ?>><?php echo e($subCatDataValue->name); ?></option>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															<?php else: ?>
																<option value="">Select Sub Category</option>
															<?php endif; ?>
														</select>
														<?php if($errors->has('sub_cat_id')): ?>
														<span class="text-danger"><?php echo e($errors->first('sub_cat_id')); ?> </span>
														<?php endif; ?>
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
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<script type="text/javascript">
	$(document).ready(function() {            
		$('#pro_name').autocomplete({			
			source:function(request, response) {
				$.get('product-autocomplete', {'term':request.term}, function(recv){response(recv);});				
			},
			minLength: 0,
			select:function(event, ui) {
				$('#pro_name').attr('value', ui.item.label);
				$('#pro_name').val(ui.item.label);						
			}	
		}).focus(function(){$(this).data("uiAutocomplete").search($(this).val());});
	});	
			
	$(document).ready(function() {
		$('.select-multiple1').select2({
			width: "100%",
			placeholder: "Select Category",
			allowClear: true
		});
	});
	
	$(document).ready(function() {
		$('.select-multiple2').select2({
			width: "100%",
			placeholder: "Select Buyer",
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/product/add.blade.php ENDPATH**/ ?>