
<?php $__env->startSection('content'); ?>


<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Add Asset Request</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a></li>
								<li class="breadcrumb-item active"><a href="#">Add Asset Request</a></li>
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
									<?php if(Auth::user()->mrl_raise == 0){ ?>
										<form class="form" action="<?php echo e(route('admin.request.store')); ?>" method="post" enctype="multipart/form-data">
											<?php echo csrf_field(); ?>
											<div class="form-body">
												<div class="cRecord" style="display:none">1</div>
												<div class="row">
													<?php if(Auth::user()->role_id == 25){ ?>
													<div class="col-md-4 col-4">
														<div class="form-group">
															<label for="first-name-column">Category</label>
															 <select class="form-control select-multiple cat_id" name="category[]">
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
													
													<div class="col-md-4 col-4">
														<div class="form-group">
															<label for="first-name-column">Sub Category</label>
															<select class="form-control select-multiple sub_cat_id" name="scategory[]">
																<?php if(!empty(old('cat_id'))): ?>
																	<?php
																		$subCatData = DB::table('category')->where('parent', old('cat_id'))->get();
																	?>
																	<?php $__currentLoopData = $subCatData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $subCatDataValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																		<option value="<?php echo e($subCatDataValue->id); ?>" <?php echo e(old('sub_cat_id', !empty(old('cat_id')) && $subCatDataValue->id == old('cat_id') ? 'selected' : '' )); ?>><?php echo e($subCatDataValue->name); ?></option>
																	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
																<?php else: ?>
																	<option value="">Select Category</option>
																<?php endif; ?>
															</select>
															<?php if($errors->has('sub_cat_id')): ?>
															<span class="text-danger"><?php echo e($errors->first('sub_cat_id')); ?> </span>
															<?php endif; ?>
														</div>	
													</div>	
													<div class="col-md-4 col-4">
														<div class="form-group">
															<label>Type of Demand</label>
															<select class="form-control" name="type[]" required>
																<option value="">Select</option>
																<option value="Asset">Asset</option>
																<option value="Non Asset">Non Asset</option>
															</select>													
														</div>
													</div>
													<?php } ?>
													<div class="col-md-12 col-12">
														<div class="form-group">
															<label for="first-name-column">Product Name</label>
															<input type="text" class="form-control" placeholder="Product Name" id="" name="title[]" value="" required>
															<?php if($errors->has('title')): ?>
															<span class="text-danger"><?php echo e($errors->first('title')); ?> </span>
															<?php endif; ?>
														</div>
													</div>		
													<div class="col-md-12 col-12">
														<div class="form-group">
															<label for="first-name-column">Product Description</label>
															<textarea name="requirement[]" class="form-control" placeholder="Please enter your product description" required></textarea>
															<?php if($errors->has('requirement')): ?>
															<span class="text-danger"><?php echo e($errors->first('requirement')); ?> </span>
															<?php endif; ?>
														</div>
													</div>	
													
													<div class="col-md-12 col-12">
														<div class="form-group">
															<label>Attach screenshot proof of requisition approval from relevent person.</label>
															<input type="file" name="proImg[]" class="form-control" value="" required />										
														</div>
													</div>
													
													
													<div class="col-md-4 col-4">
														<div class="form-group">
															<label>Quantity</label>
															<input type="number" name="qty[]" class="form-control" value="" required />								
														</div>
													</div>
													<div class="col-md-4 col-4">
														<label>Branch</label>
														<select class="form-control select-multiple" name="branch_id[]">
															<option value="">Select</option>
															<?php
																$branch = DB::table('branches')->where('status', 1)->get();
															?>
															<?php $__currentLoopData = $branch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>															
																<option value="<?php echo e($b->id); ?>" <?php if($b->id==$user_branch){ echo 'selected'; } ?>><?php echo e($b->name); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>													
													</div>
													<div class="col-md-4 col-4">
														<label>For which Employee the asset is requested</label>
														<select class="form-control select-multiple" name="emp_id[]" required>
															<option value="">Select</option>
															<option value="0">New Employee</option>
															<?php $__currentLoopData = $employee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>															
																<option value="<?php echo e($value->id); ?>" <?php if($value->id==Auth::user()->id){ echo 'selected'; } ?>><?php echo e($value->name); ?> - <?php echo e($value->register_id); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>													
													</div>
													<div class="col-md-6 col-6">
														<label>Select the name of relevant person who approved this requisition.</label>
														<select name="remark[]" class="form-control select-multiple" required>
															<option value="">-- Select --</option>
															<?php foreach($dhemployee as $key => $dvalue){ ?>
															<option value="<?php echo e($dvalue->id); ?>"><?php echo e($dvalue->name); ?> - <?php echo e($dvalue->register_id); ?></option>
															<?php } ?>
														</select>
														<!-- <textarea name="remark[]" class="form-control" required></textarea> -->
														<?php if($errors->has('remark')): ?>
														<span class="text-danger"><?php echo e($errors->first('remark')); ?> </span>
														<?php endif; ?>
													</div>	
													<div class="col-md-6 col-6">
														<div class="form-group">
															<label for="first-name-column">For which Category the asset is requested</label>
															<?php 
																$material_category = app('request')->input('material_category');
																$mcategory = DB::table('material_category')->where('status',1); 
																if(!empty($material_category)){
																	$mcategory->where('id', $material_category);
																}
																$mcategory = $mcategory->orderBy('id','asc')->get();											
															?>
															<select name="material_category[]" class="form-control select-multiple" required>
																<option value="">-- Select --</option>																
																<?php if(count($mcategory) > 0): ?>
																<?php $__currentLoopData = $mcategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																<option value="<?php echo e($value->id); ?>" <?php if($value->id == app('request')->input('material_category')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
																<?php endif; ?>
															</select>
															<?php if($errors->has('material_category')): ?>
															<span class="text-danger"><?php echo e($errors->first('material_category')); ?> </span>
															<?php endif; ?>
														</div>
													</div>
													
													<div class="col-md-6 col-12 pt-2">
														<label>Type Of Business</label>
														<select class="form-control" name="type_of_business[]" required>
															<option value="">Select</option>
															<option value="Offline">Offline</option>
															<option value="Online">Online</option>
															<option value="Both">Both</option>
														</select>													
													</div>
													
													<div class="col-md-6 col-12 pt-2">
														<label>Request Type :</label>
														&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="request_type[1]" value="0" class="reqType" checked /> MRL 															
														</label>
														&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="request_type[1]" value="1" class="reqType"/> WRL 															
														</label>
													</div>
													
													<div class="append_div w-100">
													
													</div>
													
													<div class="col-md-12 col-12 rAddmore">
														<div class="form-group text-right">
															<label for="">&nbsp;</label>
															<button class="btn btn-primary add-more" type="button" style="margin-top:10px;">Add More</button>
														</div>
													</div>												
													<div class="col-md-12 mt-2">
														<button type="submit" class="btn btn-primary mr-1 mb-1">Submit</button>
													</div>
												</div>
											</div>
										</form>
										
										<!-- Hidden Coloum -->
										<div class="copy-fields w-100" style="display:none;">													
											<div class="remove_row">
												<hr style="background:#000;">
												<?php if(Auth::user()->role_id == 25){ ?>
												<div class="row mx-0">
													<div class="col-md-4 col-4">
														<div class="form-group">
															<label for="first-name-column">Category</label>
															 <select class="form-control select-multiple1 cat_id" name="category[]">
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
													
													<div class="col-md-4 col-4">
														<div class="form-group">
															<label for="first-name-column">Sub Category</label>
															<select class="form-control select-multiple4 sub_cat_id" name="scategory[]">
																<?php if(!empty(old('cat_id'))): ?>
																	<?php
																		$subCatData = DB::table('category')->where('parent', old('cat_id'))->get();
																	?>
																	<?php $__currentLoopData = $subCatData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $subCatDataValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																		<option value="<?php echo e($subCatDataValue->id); ?>" <?php echo e(old('sub_cat_id', !empty(old('cat_id')) && $subCatDataValue->id == old('cat_id') ? 'selected' : '' )); ?>><?php echo e($subCatDataValue->name); ?></option>
																	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
																<?php else: ?>
																	<option value="">Select Category</option>
																<?php endif; ?>
															</select>
															<?php if($errors->has('sub_cat_id')): ?>
															<span class="text-danger"><?php echo e($errors->first('sub_cat_id')); ?> </span>
															<?php endif; ?>
														</div>	
													</div>	
													<div class="col-md-4 col-4">
														<div class="form-group">
															<label>Type of Demand</label>
															<select class="form-control" name="type[]" required>
																<option value="">Select</option>
																<option value="Asset">Asset</option>
																<option value="Non Asset">Non Asset</option>
															</select>													
														</div>
													</div>
												</div>
												<?php } ?>
												<div class="col-md-12 col-12">
													<div class="form-group">
														<label for="first-name-column">Product Name</label>
														<input type="text" class="form-control" placeholder="Product Name" id="" name="title[]" value="">
														<?php if($errors->has('title')): ?>
														<span class="text-danger"><?php echo e($errors->first('title')); ?> </span>
														<?php endif; ?>
													</div>
												</div>		
												<div class="col-md-12 col-12">
													<div class="form-group">
														<label for="first-name-column">Product Description</label>
														<textarea name="requirement[]" class="form-control" placeholder="Please enter your product description" ></textarea>
														<?php if($errors->has('requirement')): ?>
														<span class="text-danger"><?php echo e($errors->first('requirement')); ?> </span>
														<?php endif; ?>
													</div>
												</div>	
												
												<div class="col-md-12 col-12">
													<div class="form-group">
														<label>Attach screenshot proof of requisition approval from relevent person.</label>
														<input type="file" name="proImg[]" class="form-control" value="" />									
													</div>
												</div>
												<div class="row mx-0">
													<div class="col-md-4 col-4">
														<div class="form-group">
															<label>Quantity</label>
															<input type="number" name="qty[]" class="form-control" value="" required />									
														</div>
													</div>
													<div class="col-md-4 col-4">
														<label>Branch</label>
														<select class="form-control select-multiple_2" name="branch_id[]">
															<option value="">Select</option>
															<?php
																$branch = DB::table('branches')->where('status', 1)->get();
															?>
															<?php $__currentLoopData = $branch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>															
																<option value="<?php echo e($b->id); ?>" <?php if($b->id==$user_branch){ echo 'selected'; } ?>><?php echo e($b->name); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>													
													</div>
													<div class="col-md-4 col-4">
														<label>For which Employee the asset is requested</label>
														<select class="form-control select-multiple_2" name="emp_id[]" required>
															<option value="">Select</option>
															<?php $__currentLoopData = $employee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>															
																<option value="<?php echo e($value->id); ?>" <?php if($value->id==Auth::user()->id){ echo 'selected'; } ?>><?php echo e($value->name); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>													
													</div>
												</div>
												<div class="row mx-0">
													<div class="col-md-6 col-6">
														<div class="form-group">
															<label for="first-name-column">Select the name of relevant person who approved this requisition.</label>
															<select name="remark[]" class="form-control select-multiple_2" required>
																<option value="">-- Select --</option>
																<?php foreach($dhemployee as $key => $dvalue){ ?>
																<option value="<?php echo e($dvalue->id); ?>"><?php echo e($dvalue->name); ?> - <?php echo e($dvalue->register_id); ?></option>
																<?php } ?>
															</select>
															<!-- <textarea name="remark[]" class="form-control" required></textarea> -->
															<?php if($errors->has('remark')): ?>
															<span class="text-danger"><?php echo e($errors->first('remark')); ?> </span>
															<?php endif; ?>
														</div>
													</div>
													<div class="col-md-6 col-6">
														<div class="form-group">
															<label for="first-name-column">For which Category the asset is requested</label>
															<?php 
																$material_category = app('request')->input('material_category');
																$mcategory = DB::table('material_category')->where('status',1); 
																if(!empty($material_category)){
																	$mcategory->where('id', $material_category);
																}
																$mcategory = $mcategory->orderBy('id','asc')->get();											
															?>
															<select name="material_category[]" class="form-control select-multiple_2" required>
																<option value="">-- Select --</option>																
																<?php if(count($mcategory) > 0): ?>
																<?php $__currentLoopData = $mcategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																<option value="<?php echo e($value->id); ?>" <?php if($value->id == app('request')->input('material_category')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
																<?php endif; ?>
															</select>
															<?php if($errors->has('material_category')): ?>
															<span class="text-danger"><?php echo e($errors->first('material_category')); ?> </span>
															<?php endif; ?>
														</div>
													</div>
												</div>
												<div class="row mx-0">
													<div class="col-md-6 col-12">
														<label>Type Of Business</label>
														<select class="form-control" name="type_of_business[]" required>
															<option value="">Select</option>
															<option value="Offline">Offline</option>
															<option value="Online">Online</option>
															<option value="Both">Both</option>
														</select>													
													</div>
													<div class="col-md-6 col-12 pt-2">
														<label>Request Type :</label>
														&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="request_type[]" value="0" class="reqType" checked /> MRL 															
														</label>
														&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="request_type[]" value="1" class="reqType"/> WRL 															
														</label>
													</div>
												</div>
												<div class="col-md-12 col-12 text-right">
													<div class="form-group mb-0">
														<label for="">&nbsp;</label>
														<button class="btn btn-danger remove" type="button" style="margin-top:10px;">Remove</button>
													</div>
												</div>
											</div>
										</div>
									<?php }else { ?>
										<div class="form-body text-center ">
											<p class="mb-0">You are not eligible to raise any requistion.</p>
										</div>
									
									
									<?php } ?>
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
	$(document).ready(function() {
		$('.select-multiple').select2({
			placeholder: "Select",
			allowClear: true,
			
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
	
	
	$(document).ready(function() {
		$(".add-more").click(function(){ 
			cRecord		=	$('.cRecord').html();
			var i 			=	parseInt(cRecord)+1;
			
			var html 	= 	$(".copy-fields").html();
			html 		= 	html.replaceAll('request_type[]', 'request_type['+i+']');
			//html 		= 	html.replaceAll('select-multiple', 'select-multiple'+i);
			
			$(".append_div").append(html); 
			$('.cRecord').html(i);
			
			$('.append_div .select-multiple_2').select2({				
				width:'100%',
				placeholder: "Select",
			    allowClear: true
			});
		});
		
		
		$("body").on("click",".remove",function(){ 
			$(this).parents(".remove_row").remove();
		});
	});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/request/add_request.blade.php ENDPATH**/ ?>