
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
						<h2 class="content-header-title float-left mb-0">Add Inventory</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Add Inventory</a>
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
									<form class="form" action="<?php echo e(route('admin.inventory.store')); ?>" method="post" enctype="multipart/form-data">
										<?php echo csrf_field(); ?>
										<div class="form-body">
											<div class="row">
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Select Product</label>
														 <select class="form-control select-multiple10 product_id" name="product_id">
															<option value="">Select Poduct</option>
															<?php if(count($product) > 0): ?>
															<?php $__currentLoopData = $product; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($val->id); ?>" <?php echo e(old('product_id') ? 'selected' : ''); ?>><?php echo e($val->cat_name); ?> - <?php echo e($val->sub_cat_name); ?> - <?php echo e($val->name); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															<?php endif; ?>
														</select>
														<?php if($errors->has('product_id')): ?>
														<span class="text-danger"><?php echo e($errors->first('product_id')); ?> </span>
														<?php endif; ?>
													</div>
												</div>	
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Maintains</label>
														<select class="form-control select-multiple1 maintains" name="maintains">
															<option value="Yes" <?php echo e(old('maintains') == 'Yes' ? 'selected' : ''); ?>>Yes</option>
															<option value="No" <?php echo e(old('maintains') == 'No' ? 'selected' : ''); ?>>No</option>
														</select>
														<?php if($errors->has('maintains')): ?>
														<span class="text-danger"><?php echo e($errors->first('maintains')); ?> </span>
														<?php endif; ?>
													</div>
												</div>	
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Warranty</label>
														<select class="form-control select-multiple1 warranty" name="warranty">
															<option value="Yes" <?php echo e(old('warranty') == 'Yes' ? 'selected' : ''); ?>>Yes</option>
															<option value="No" <?php echo e(old('warranty') == 'No' ? 'selected' : ''); ?>>No</option>
														</select>
														<?php if($errors->has('warranty')): ?>
														<span class="text-danger"><?php echo e($errors->first('warranty')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												
												<div class="col-md-4 col-12 wperiod">
													<div class="form-group">
														<label for="first-name-column">Warranty Period</label>
														<input type="text" class="form-control" placeholder="Warranty Period" name="warranty_period" value="<?php echo e(old('warranty_period')); ?>">
														<?php if($errors->has('warranty_period')): ?>
														<span class="text-danger"><?php echo e($errors->first('warranty_period')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Expiry Date</label>
														<input type="date" class="form-control" name="expiry_date" value="<?php echo e(old('expiry_date')); ?>">
														<?php if($errors->has('expiry_date')): ?>
														<span class="text-danger"><?php echo e($errors->first('expiry_date')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Consumable</label>
														<select class="form-control select-multiple1 is_consumer" name="is_consumer">
															<option value="Yes" <?php echo e(old('is_consumer') == 'Yes' ? 'selected' : ''); ?>>Yes</option>
															<option value="No" <?php echo e(old('is_consumer') == 'No' ? 'selected' : ''); ?>>No</option>
														</select>
														<?php if($errors->has('is_consumer')): ?>
														<span class="text-danger"><?php echo e($errors->first('is_consumer')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Quantity</label>
														<input type="number" class="form-control" name="qty" placeholder="Quantity" value="<?php echo e(old('qty')); ?>" step="0.01">
														<?php if($errors->has('qty')): ?>
														<span class="text-danger"><?php echo e($errors->first('qty')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Measurement</label>
														<!--<input type="text" class="form-control" name="measurement" placeholder="Measurement" value="<?php echo e(old('measurement')); ?>">-->
														<select class="form-control select-multiple1 measurement" name="measurement">
															<option value="Piece" <?php echo e(old('measurement') == 'Piece' ? 'selected' : ''); ?>>Piece</option>
															<option value="Liter" <?php echo e(old('measurement') == 'Liter' ? 'selected' : ''); ?>>Liter</option>
															<option value="KG" <?php echo e(old('measurement') == 'KG' ? 'selected' : ''); ?>>KG</option>
															<option value="Mtr" <?php echo e(old('measurement') == 'Mtr' ? 'selected' : ''); ?>>Mtr</option>
															<option value="SQ Feet" <?php echo e(old('measurement') == 'SQ Feet' ? 'selected' : ''); ?>>SQ Feet</option>
															<option value="Dozen" <?php echo e(old('measurement') == 'Dozen' ? 'selected' : ''); ?>>Dozen</option>
															<option value="PKT" <?php echo e(old('measurement') == 'PKT' ? 'selected' : ''); ?>>PKT</option>
															<option value="CAN" <?php echo e(old('measurement') == 'CAN' ? 'selected' : ''); ?>>CAN</option>
														</select>
														<?php if($errors->has('measurement')): ?>
														<span class="text-danger"><?php echo e($errors->first('measurement')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Price</label>
														<input type="number" class="form-control" name="price" placeholder="Price" value="<?php echo e(old('price')); ?>">
														<?php if($errors->has('price')): ?>
														<span class="text-danger"><?php echo e($errors->first('price')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Buyer</label>
														<?php $buyer_data = \App\Buyer::where('is_deleted', '0')->orderBy('id','desc')->get(); ?>
														 <select class="form-control select-multiple2 buyer_id" name="buyer_id">
															<option value="">Select Buyer</option>
															<?php if(count($buyer_data) > 0): ?>
															<?php $__currentLoopData = $buyer_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $buyer_data_value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($buyer_data_value->id); ?>" <?php echo e(old('buyer_id') ? 'selected' : ''); ?>><?php echo e($buyer_data_value->name); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															<?php endif; ?>
														</select>
														<?php if($errors->has('buyer_id')): ?>
														<span class="text-danger"><?php echo e($errors->first('buyer_id')); ?> </span>
														<?php endif; ?>
													</div>
												</div>	
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Bill No</label>
														<input type="text" class="form-control" placeholder="Bill No" name="bill_no" value="<?php echo e(!empty($get_bill_detail->bill_no) ? $get_bill_detail->bill_no : old('bill_no')); ?>">
														
														<!--
														<select class="form-control select-multiple3 bill_no" name="bill_no">
															<?php if(!empty(old('bill_no'))): ?>
																<?php
																	$billData = DB::table('bill')->where('buyer_id', old('bill_no'))->get();
																?>
																<?php $__currentLoopData = $billData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $billDataValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																	<option value="<?php echo e($billDataValue->id); ?>" <?php echo e(old('bill_no', !empty(old('bill_no')) && $billDataValue->id == old('bill_no') ? 'selected' : '' )); ?>><?php echo e($billDataValue->bill_no); ?></option>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															<?php else: ?>
																<option value="">Select Bill</option>
															<?php endif; ?>
														</select>
														-->
														
														
														<?php if($errors->has('bill_no')): ?>
														<span class="text-danger"><?php echo e($errors->first('bill_no')); ?> </span>
														<?php endif; ?>
													</div>
												</div>	
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Mode</label>
														<select class="form-control select-multiple1 mode" name="mode">
															<option value="Credit" <?php echo e(old('mode') == 'Credit' ? 'selected' : ''); ?>>Credit</option>
															<option value="Cash" <?php echo e(old('mode') == 'Cash' ? 'selected' : ''); ?>>Cash</option>
														</select>
														<?php if($errors->has('mode')): ?>
														<span class="text-danger"><?php echo e($errors->first('mode')); ?> </span>
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
												
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Product Image 1</label>
														<input type="file" class="form-control" name="product_one" id="product_img_one">
														<?php if($errors->has('product_one')): ?>
														<span class="text-danger"><?php echo e($errors->first('product_one')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Product Image 2</label>
														<input type="file" class="form-control" name="product_two" id="product_img_two">
														<?php if($errors->has('product_two')): ?>
														<span class="text-danger"><?php echo e($errors->first('product_two')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Type</label>
														<!--<input type="text" class="form-control" name="type" placeholder="Type" value="<?php echo e(old('type')); ?>">-->
														<select class="form-control select-multiple1 type" name="type">
															<option value="New" <?php echo e(old('type') == 'New' ? 'selected' : ''); ?>>New</option>
															<option value="Expired" <?php echo e(old('type') == 'Expired' ? 'selected' : ''); ?>>Expired</option>
															<option value="Dead" <?php echo e(old('type') == 'Dead' ? 'selected' : ''); ?>>Dead</option>
														</select>
														<?php if($errors->has('type')): ?>
														<span class="text-danger"><?php echo e($errors->first('type')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Status</label>
														<select class="form-control select-multiple1 status" name="status">
															<option value="New" <?php echo e(old('status') == 'New' ? 'selected' : ''); ?>>New</option>
															<option value="Dead" <?php echo e(old('status') == 'Dead' ? 'selected' : ''); ?>>Dead</option>
															<option value="Lost" <?php echo e(old('status') == 'Lost' ? 'selected' : ''); ?>>Lost</option>
															<option value="Out of stock" <?php echo e(old('status') == 'Out of stock' ? 'selected' : ''); ?>>Out of stock</option>
														</select>
														<?php if($errors->has('status')): ?>
														<span class="text-danger"><?php echo e($errors->first('status')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Location</label>
														<?php if(Auth::user()->role_id == 25){ ?>
															<input type="text" name="location" class="form-control" readonly value="<?=Auth::user()->user_branches[0]->branch['branch_location'];?>"/> 
														<?php }else{ ?>														
															<?php $branch_location = \App\Branch::where('is_deleted', '0')->orderBy('id','asc')->groupby('branch_location')->get(); ?>
															 <select class="form-control select-multiple2 location" name="location">
																<option value="">Select Location</option>
																<?php if(count($branch_location) > 0): ?>
																<?php $__currentLoopData = $branch_location; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch_location_value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																<option value="<?php echo e($branch_location_value->branch_location); ?>" <?php echo e(old('location') ? 'selected' : ''); ?>><?php echo e($branch_location_value->branch_location); ?></option>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
																<?php endif; ?>
															</select>
															<?php if($errors->has('location')): ?>
															<span class="text-danger"><?php echo e($errors->first('location')); ?> </span>
															<?php endif; ?>
														<?php } ?>														
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Serial Number</label>
														<input type="text" class="form-control" name="model_no">
														<?php if($errors->has('model_no')): ?>
														<span class="text-danger"><?php echo e($errors->first('model_no')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Model Number</label>
														<input type="text" class="form-control" name="serial_no">
														<?php if($errors->has('serial_no')): ?>
														<span class="text-danger"><?php echo e($errors->first('serial_no')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												
												
												<div class="col-md-12 col-12">
													<div class="form-group">
														<label for="first-name-column">Remark</label>
														<textarea name="remark" class="form-control remark" placeholder="Remark" required></textarea>
														<?php if($errors->has('remark')): ?>
														<span class="text-danger"><?php echo e($errors->first('remark')); ?> </span>
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
 

<script type="text/javascript">

			
	$(document).ready(function() {
		$('.select-multiple10').select2({
			width: "100%",
			placeholder: "Select Product",
			allowClear: true
		});
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
			placeholder: "Select",
			allowClear: true
		});
	});
	
	$(document).ready(function() {
		$('.select-multiple3').select2({
			width: "100%",
			placeholder: "Select Bill",
			allowClear: true
		});
	});
	
	$(document).ready(function() {
		$('.select-multiple4').select2({
			width: "100%",
			placeholder: "Select Sub Category",
			allowClear: true
		});
	});
	
	$(".warranty").on("change", function(){ 
		var warranty_val = $('.warranty option:selected').attr('value');
		if(warranty_val == 'Yes'){
			$('.wperiod').show();
		}	
		else{
			$('.wperiod').hide();
		}		
	});
	
	$(".buyer_id").on("change", function () {
		var buyer_id = $(".buyer_id option:selected").attr('value'); 
		if (buyer_id) {
			$.ajax({
				type : 'POST',
				url : '<?php echo e(route('admin.product.get-bill')); ?>',
				data : {'_token' : '<?php echo e(csrf_token()); ?>', 'buyer_id': buyer_id},
				dataType : 'html',
				success : function (data){
					$('.bill_no').empty();
					$('.bill_no').append(data);
				}
			});
		}
	});
	
	$('#product_img_one').change(function () {
		var fileName=this.value;
		var ext =fileName.substr(fileName.lastIndexOf('.') + 1);// this.value.match(/\.(.+)$/)[1];
		ext=ext.toLowerCase();
		switch (ext) {
			case 'jpg':
			case 'jpeg':
			case 'png':
			case 'gif':
			break;
			default:
			alert('This is not an allowed file type.');
			this.value = '';
		}
	});
	
	$('#product_img_two').change(function () {
		var fileName=this.value;
		var ext =fileName.substr(fileName.lastIndexOf('.') + 1);// this.value.match(/\.(.+)$/)[1];
		ext=ext.toLowerCase();
		switch (ext) {
			case 'jpg':
			case 'jpeg':
			case 'png':
			case 'gif':
			break;
			default:
			alert('This is not an allowed file type.');
			this.value = '';
		}
	});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/inventory/add.blade.php ENDPATH**/ ?>