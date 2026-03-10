
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-md-8">
						<h2 class="content-header-title float-left mb-0">Inventory</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					
					<div class="col-md-4"> <a href="<?php echo e(route('admin.inventory.create')); ?>" data-id="" class="btn btn-outline-primary float-right">Add Inventory</a></div>
					
				</div>
			</div>
		</div>
		<div class="content-body">
		
		<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="<?php echo e(route('admin.inventory.index')); ?>" method="get" enctype="multipart/form-data">
										<?php echo csrf_field(); ?>
										<div class="form-body">
											<div class="row">
												<!--
												<div class="col-md-3">
													<div class="form-group">
														<label>Product Name</label>
														<input type="text" class="form-control" placeholder="Product Name" name="name" value="<?php echo e(app('request')->input('name')); ?>">
														<?php if($errors->has('name')): ?>
														<span class="text-danger"><?php echo e($errors->first('name')); ?> </span>
														<?php endif; ?>
													</div>
												</div>	
												-->
												<div class="col-md-3 col-12">
													<div class="form-group">
														<label for="first-name-column">Select Product</label>
														 <select class="form-control select-multiple10 product_id" name="product_id">
															<option value="">Select Poduct</option>
															<?php if(count($product_list) > 0): ?>
															<?php $__currentLoopData = $product_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($val->id); ?>" <?php if($val->id == app('request')->input('product_id')): ?> selected="selected" <?php endif; ?>><?php echo e($val->name); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															<?php endif; ?>
														</select>
														<?php if($errors->has('name')): ?>
														<span class="text-danger"><?php echo e($errors->first('name')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<!--div class="col-md-3 col-12">
													<div class="form-group">
														<label for="first-name-column">Category</label>
														 <select class="form-control select-multiple1 cat_id" name="cat_id">
															<option value="">Select Category</option>
															<?php if(count($category) > 0): ?>
															<?php $__currentLoopData = $category; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoryvalue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($categoryvalue->id); ?>" <?php if($categoryvalue->id == app('request')->input('cat_id')): ?> selected="selected" <?php endif; ?>><?php echo e($categoryvalue->name); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															<?php endif; ?>
														</select>
													</div>
												</div>	
												<?php $gtSubCat = array(); ?>
												<div class="col-md-3 col-12">
													<div class="form-group">
														<label for="first-name-column">Sub Category</label>
														<select class="form-control select-multiple1 sub_cat_id" name="sub_cat_id">
															<option value="">Select Sub Category</option>
															<?php if(!empty(app('request')->input('cat_id'))): ?>
																<?php
																$gtSubCat  = DB::table('category')->where('parent', app('request')->input('cat_id'))->where('is_deleted', '0')->get();
																 
																?>
																<?php if(count($gtSubCat) > 0): ?>
																<?php $__currentLoopData = $gtSubCat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gtSubCatvalue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																<option value="<?php echo e($gtSubCatvalue->id); ?>" <?php if($gtSubCatvalue->id == app('request')->input('sub_cat_id')): ?> selected="selected" <?php endif; ?>><?php echo e($gtSubCatvalue->name); ?></option>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
																<?php endif; ?>
															<?php endif; ?>
														</select>
													</div>
												</div-->
												<div class="col-md-3">
													<label for="users-list-status">Status</label>
													<?php //$status = array('In Stock','Out of stock'); ?>
													<fieldset class="form-group">												
														<select class="form-control select-multiple1 status" name="status">
															<option value="">Select Any</option>															
															<option value="In Stock" <?php if('In Stock' == app('request')->input('status')): ?> selected="selected" <?php endif; ?>>In Stock</option>
															<option value="Out of stock" <?php if('Out of stock' == app('request')->input('status')): ?> selected="selected" <?php endif; ?>>Out of stock</option>
															
														</select>												
													</fieldset>
												</div>
												<div class="col-md-6 mt-2">
													<fieldset class="form-group">		
														<button type="submit" class="btn btn-primary">Search</button>
														<a href="<?php echo e(route('admin.inventory.index')); ?>" class="btn btn-warning">Reset</a>
														<a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a>
													</fieldset>
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
			
			<section id="data-list-view" class="data-list-view-header">
				
				<div class="table-responsive">
					<table class="table data-list-view">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Category</th>
								<th>Sub Category</th>
								<th>Product Name</th>
								<th>Quantity</th> 
								<th>In Stock</th>
								<th>Created</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						<?php 
						if(count($product) > 0){
							$i = 1;
							foreach($product as  $key => $value){	
								$rem_pro = 0;									
								$location 	= Auth::user()->user_branches[0]->branch['branch_location'];
								
								$transfer_qty = DB::table('transfer')
														  ->where('product_id', $value->product_id)
														  ->where('transfer_from', '0')
														  ->where('is_deleted','=','0')
														  ->where('status','Accept')
														  ->where('location',$location)
														  ->get()
														  ->sum("qty");
								
								if(!empty($transfer_qty)){
									$rem_pro = $value->total_qty - $transfer_qty;
								}
								else{
									$rem_pro = $value->total_qty;
								}
								
								if($rem_pro > 0){								
						?>
							<tr>
								<td><?php echo e($pageNumber++); ?></td>
								<td class="product-category"><?php echo e($value->cat_name); ?></td>
								<td class="product-category"><?php echo e($value->sub_cat_name); ?></td>
								<td class="product-category">
									<?php echo e($value->p_name); ?>

									
									<?php if(!empty($value->product_one)){ ?><a href="<?php echo e(asset('laravel/public/bill/' . $value->product_one)); ?>" download >Preview-1</a></br><?php } ?>
									
									<?php if(!empty($value->product_two)){ ?><a href="<?php echo e(asset('laravel/public/bill/' . $value->product_two)); ?>" download >Preview-2</a><?php } ?>
								</td>
								<td class="product-category"><?php echo e($value->total_qty); ?></td>
								<td class="product-category"><?php echo e($rem_pro); ?></td>
								<td><?php echo e($value->created_at->format('d-m-Y')); ?></td>
								<td class="product-action">
									
									<!--
									<a title="Transfer" href="<?php echo e(route('admin.inventory.inventory-transfer', [$value->product_id])); ?>">
										<span class="action-transfer"><i class="feather icon-repeat"></i></span>
									</a> &nbsp;
									-->
									<a title="Transfer List" href="<?php echo e(route('admin.inventory.inventory-transfer-list', [$value->product_id])); ?>">
										<span class="action-transfer"><i class="feather icon-clock"></i></span>
									</a> &nbsp;
									<!--
									<a title="Product Inventory List" href="<?php echo e(route('admin.inventory.product-inventory-list', $value->product_id)); ?>">
										<span class="action-edit"><i class="feather icon-list"></i></span>
									</a>
									-->
									<!--a title="Delete Inventory" href="<?php echo e(route('admin.inventory.delete', $value->id)); ?>" onclick="return confirm('Are You Sure To Delete Inventory')">
										<span class="action-delete"><i class="feather icon-trash"></i></span>
									</a-->
									
									<!--
									<a title="Product Inventory List" href="<?php echo e(route('admin.inventory.product-dead-status', $value->product_id)); ?>" class="btn btn-primary">
										Dead 
									</a>
									-->
								</td>
							</tr>
						
						<?php
								}
							}
						}else{
						?>
						<tr><td class="text-center text-primary" colspan="8">No Record Found</td></tr>
						<?php } ?>
						</tbody>
					</table>
					<div class="d-flex justify-content-center">					
					<?php echo $product->appends($params)->links(); ?>

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
		$('.select-multiple10').select2({
			width: "100%",
			placeholder: "Select Product",
			allowClear: true
		});
	});
	
	
	$(document).ready(function() {
		$('.select-multiple3').select2({
			width: "100%",
			placeholder: "Select Status",
			allowClear: true
		});
	});
	
	$(document).ready(function() {
		$('.select-multiple1').select2({
			width: "100%",
			placeholder: "Select",
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
	
	
	$("body").on("click", "#download_excel", function (e) {
		var data = {};					
		data.product_id  = $('.product_id').val(),
		data.status 	 = $('.status').val(),
		window.location.href = "<?php echo URL::to('/admin/'); ?>/warehouse-report-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	}); 
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/inventory/index.blade.php ENDPATH**/ ?>