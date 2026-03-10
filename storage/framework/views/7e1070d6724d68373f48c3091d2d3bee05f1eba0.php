<!DOCTYPE html>
<html class="loading" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="description" content="">
    <meta name="keywords" content="">   

   <link href="<?php echo e(url('../laravel/public/logo.png')); ?>" rel="icon" type="image/ico" />
    <title><?php echo e(config('app.name')); ?> - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('laravel/public/admin/css/vendors.min.css')); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('laravel/public/admin/css/bootstrap.css')); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('laravel/public/admin/css/bootstrap-extended.css')); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('laravel/public/admin/css/colors.css')); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('laravel/public/admin/css/components.css')); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('laravel/public/admin/css/vertical-menu.css')); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('laravel/public/admin/css/dashboard-analytics.css')); ?>">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css">
	<style>
		.m_body {
			width: 50%;
		}

		@media  only screen and (max-width: 600px) {
			.m_body {
				width: 100%;
			}
		}
	</style>

</head>

<body class="vertical-layout vertical-menu-modern 2-columns  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">

<div class="app-content content m_body" style="margin: 0 auto;">
	<div class="content-wrapper" style="margin-top: 2px;">
		<div class="content-body">
			<div class="text-right pb-2">
				<?php if($sess_emp_id==8799 || $sess_emp_id==6859){?>
					<a href="<?php echo e(route('po-approval-list',[$sess_emp_id,$type])); ?>" class="btn-dark p-1">PO Approval</a>
				<?php } ?>
				
				<?php //if($user_details->mrl_raise == 0){ ?>
				<a href="<?php echo e(route('employee-requisition-list',[$sess_emp_id,$type])); ?>" class="btn-dark p-1">Requisition List</a>
				<?php //} ?>
			</div>
			
			<section id="data-list-view" class="data-list-view-header">
				<!--
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								
								<div class="table-responsive">
									<table class="table data-list-view">
										<tr>
											<th>Name</th>
											<td><?php echo e(!empty($user_details->name) ? $user_details->name : ''); ?></td>
										</tr>
										<tr>
											<th>Emp Code</th>
											<td><?php echo e(!empty($user_details->register_id) ? $user_details->register_id : ''); ?></td>
										</tr>
										<tr>
											<th>Mobile No</th>
											<td><?php echo e(!empty($user_details->mobile) ? $user_details->mobile : ''); ?></td>
										</tr>
										<tr>
											<th>Department</th>
											<td><?php echo e(!empty($user_details->departments_name) ? $user_details->departments_name : ''); ?></td>
										</tr>
										<tr>
											<th>Center name</th>
											<td><?php echo e(!empty($user_details->branches_name) ? $user_details->branches_name : ''); ?></td>
										</tr>
									</table>
								</div> 
								

							</div>
						</div>
					</div>
				</div>
				-->
				
				<div class="card">
					<div class="card-content">
						<div class="card-body">
							<?php 								
								if($user_details->mrl_raise == 0){ ?>
								<form class="form" action="<?php echo e(route('store-material-requisition',[$sess_emp_id,$type])); ?>" method="post" enctype="multipart/form-data">
									<?php echo csrf_field(); ?>					
									<div class="cRecord" style="display:none">0</div>
									<div class="form-body">
										<div class="row">
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
											
											<div class="col-md-12 col-12">
												<div class="form-group">
													<label>Quantity</label>
													<input type="number" name="qty[]" class="form-control" value="" required />													
												</div>
											</div>
											<div class="col-md-12 col-12">
												<div class="form-group">
													<label>Branch</label>
													<select class="form-control select-multiple" name="branch_id[]">
														<option value="">Select</option>
														<?php
															$branch = DB::table('branches')->where('status', 1)->get();
														?>
														<?php $__currentLoopData = $branch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>															
															<option value="<?php echo e($b->id); ?>" <?php if($b->id==$user_details->branch_id){ echo 'selected'; } ?>><?php echo e($b->name); ?></option>
														<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													</select>													
												</div>
											</div>
											<div class="col-md-12 col-12">
												<div class="form-group">
													<label>For which Employee the asset is requested</label>
													<select class="form-control select-multiple" name="emp_id[]" required>
														<option value="">Select</option>
														<?php $__currentLoopData = $employee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>															
															<option value="<?php echo e($value->id); ?>" <?php if($value->id==$sess_emp_id){ echo 'selected'; } ?>><?php echo e($value->name); ?> - <?php echo e($value->register_id); ?></option>
														<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													</select>													
												</div>
											</div>
											<div class="col-md-12 col-12">
												<div class="form-group">
													<label for="first-name-column">Mention the name of relevant person who approved this requisition.</label>
													<select name="remark[]" class="form-control select-multiple" required>
														<option value="">-- Select --</option>
														<?php foreach($dhemployee as $key => $dvalue){ ?>
														<option value="<?php echo e($dvalue->id); ?>"><?php echo e($dvalue->name); ?> - <?php echo e($dvalue->register_id); ?></option>
														<?php } ?>
													</select>
													<!-- <textarea name="" class="form-control"></textarea> -->
													<?php if($errors->has('remark')): ?>
													<span class="text-danger"><?php echo e($errors->first('remark')); ?> </span>
													<?php endif; ?>
												</div>
											</div>
											<div class="col-md-12 col-12">
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
											<div class="col-md-12 col-12">
												<div class="form-group">
													<label>Type Of Business</label>
													<select class="form-control" name="type_of_business[]" required>
														<option value="">Select</option>
														<option value="Offline">Offline</option>
														<option value="Online">Online</option>
														<option value="Both">Both</option>
													</select>													
												</div>
											</div>
											<div class="col-md-12 col-12">
												<label>Request Type :</label>
												&nbsp;&nbsp;&nbsp;
												<label>
													<input type="radio" name="request_type[0]" value="0" checked /> MRL 															
												</label>
												&nbsp;&nbsp;&nbsp;
												<label>
													<input type="radio" name="request_type[0]" value="1"/> WRL 															
												</label>
											</div>
											
											<span class="append_div w-100">
											
											</span>
											<div class="col-md-12 col-12">
												<div class="form-group text-right">
													<label for="">&nbsp;</label>
													<button class="btn btn-primary add-more px-1" type="button">Add More</button>
												</div>
											</div>
											
											
											
											
											<div class="col-md-4 mt-2">
												<button type="submit" class="btn btn-dark mr-1 mb-1 w-100">Submit</button>
											</div>
										</div>
									</div>
								</form>
								
								<!-- Hidden Coloum -->
								<div class="copy-fields w-100" style="display:none;">
									
									<div class="remove_row">
										<hr style="background:#000;">
												
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
												<textarea name="requirement[]" class="form-control" placeholder="Please enter your product description"></textarea>
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
										<div class="modal-body">
											<label>Quantity</label>
											<input type="number" name="qty[]" class="form-control" value="" />												
										</div>
										<div class="col-md-12 col-12">
											<div class="form-group">
												<label>Branch</label>
												<select class="form-control select-multiple_2" name="branch_id[]">
													<option value="">Select</option>
													<?php
														$branch = DB::table('branches')->where('status', 1)->get();
													?>
													<?php $__currentLoopData = $branch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>															
														<option value="<?php echo e($b->id); ?>" <?php if($b->id==$user_details->branch_id){ echo 'selected'; } ?>><?php echo e($b->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>													
											</div>
										</div>
										<div class="col-md-12 col-12">
											<div class="form-group">
												<label>For which Employee the asset is requested</label>
												<select class="form-control select-multiple_2" name="emp_id[]">
													<option value="">Select</option>
													<?php $__currentLoopData = $employee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>															
														<option value="<?php echo e($value->id); ?>" <?php if($value->id==$sess_emp_id){ echo 'selected'; } ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>													
											</div>
										</div>
										<div class="col-md-12 col-12">
											<div class="form-group">
												<label for="first-name-column">Mention the name of relevant person who approved this requisition.</label>
												<select name="remark[]" class="form-control select-multiple_2" required>
													<option value="">-- Select --</option>
													<?php foreach($dhemployee as $key => $dvalue){ ?>
													<option value="<?php echo e($dvalue->id); ?>"><?php echo e($dvalue->name); ?> - <?php echo e($dvalue->register_id); ?></option>
													<?php } ?>
												</select>
												<!-- <textarea name="" class="form-control"></textarea> -->
												<?php if($errors->has('remark')): ?>
												<span class="text-danger"><?php echo e($errors->first('remark')); ?> </span>
												<?php endif; ?>
											</div>
										</div>
										<div class="col-md-12 col-12">
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
										<div class="col-md-12 col-12">
											<div class="form-group">
												<label>Type Of Business</label>
												<select class="form-control" name="type_of_business[]" required>
													<option value="">Select</option>
													<option value="Offline">Offline</option>
													<option value="Online">Online</option>
													<option value="Both">Both</option>
												</select>													
											</div>
										</div>
										<div class="col-md-12 col-12">
											<label>Request Type :</label>
											&nbsp;&nbsp;&nbsp;
											<label>
												<input type="radio" name="request_type[]" value="0" checked /> MRL 															
											</label>
											&nbsp;&nbsp;&nbsp;
											<label>
												<input type="radio" name="request_type[]" value="1"/> WRL 															
											</label> 
										</div>
										<div class="col-md-12 col-12 text-right">
											<div class="form-group">
												<label for="">&nbsp;</label>
												<button class="btn btn-danger remove px-1" type="button">Remove</button>
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

			</section>
			<p>&nbsp;</p>
			<p>&nbsp;</p>
		</div>
	</div>

<script src="<?php echo e(asset('laravel/public/admin/js/vendors.min.js')); ?>"></script>
<script src="<?php echo e(asset('laravel/public/admin/js/app-menu.js')); ?>"></script>
<script src="<?php echo e(asset('laravel/public/admin/js/app.js')); ?>"></script>
<script src="<?php echo e(asset('laravel/public/admin/js/components.js')); ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>	
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple').select2({
			placeholder: "Select",
			allowClear: true
		});
		
		$('.select-multiple1').select2({
			placeholder: "Select",
			allowClear: true
		});
	});
	
	$("#generate_salary_otp").submit(function(e) { 
		var form = document.getElementById('generate_salary_otp');
		var dataForm = new FormData(form);
		e.preventDefault();
		$.ajax({
			beforeSend: function(){
				
			},
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},      
			type: "POST",
			url : '<?php echo e(route('material-send-otp')); ?>',
			data : dataForm,
			processData : false, 
			contentType : false,
			dataType : 'json',
			success : function(data){
				if(data.status == false){
					swal("Error!", data.message, "error");
				} 
				else if(data.status == true){
					$("#generate_salary_otp").css('display','none');
					$("#check_salary_otp").css('display','block');
				}
			}
		}); 
	});
	
	$("#check_salary_otp").submit(function(e) {
		$('.msg-success').text('');
		var form1 = document.getElementById('generate_salary_otp');
		var form = document.getElementById('check_salary_otp');
		var dataForm1 = new FormData(form1);
		var dataForm = new FormData(form);
		dataForm.append('mobile_no', dataForm1.get('mobile_no'));
		e.preventDefault();
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},      
			type: "POST",
			url : '<?php echo e(route('material-access-otp')); ?>',
			data : dataForm,
			processData : false, 
			contentType : false,
			dataType : 'json',
			success : function(data){ alert(data.status);
				if(data.status == false){
					swal("Error!", data.message, "error");
				}else if(data.status == true){	 
					window.location.href = "<?php echo e(route('employee-details')); ?>/"+data.material_id+"/web";
				}
			}
		}); 
	});
</script>

<script type="text/javascript">
	$(".cat_id").on("change", function () {
		var cat_id = $(".cat_id option:selected").attr('value'); 
		if (cat_id) {
			$.ajax({
				type : 'POST',
				url : '<?php echo e(route('get-sub-cat')); ?>',
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
			i 			=	parseInt(cRecord)+1;
			var html 	= 	$(".copy-fields").html();
			html 		= 	html.replaceAll('request_type[]', 'request_type['+i+']');
			
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
<?php echo $__env->make('layouts.notification', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>
</html><?php /**PATH /var/www/html/laravel/resources/views/material-details.blade.php ENDPATH**/ ?>