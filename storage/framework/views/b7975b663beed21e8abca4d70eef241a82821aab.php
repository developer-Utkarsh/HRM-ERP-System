
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Online Courses</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">Online Courses
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
									<form class="form" action="<?php echo e(route('admin.onlinecourses.store')); ?>" method="post" enctype="multipart/form-data">
										<?php echo csrf_field(); ?>
										<h3>Add Online Course</h3>
										<div class="form-body">
											<div class="row">
												
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">User</label>
														<?php if(count($employees) > 0): ?>
														<select class="form-control select-multiple1" name="emp_id" required>
															<option value="">Select Employee</option>
															<?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($value->id); ?>" <?php if(!empty(old('emp_id')) && old('emp_id') ==  $value->id): ?><?php echo e('selected'); ?><?php endif; ?>><?php echo e($value->name.'-'.$value->register_id.'-'.$value->mobile); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>
														<?php endif; ?>
														<?php if($errors->has('emp_id')): ?>
														<span class="text-danger"><?php echo e($errors->first('emp_id')); ?> </span>
														<?php endif; ?>
													</div>
												</div> 

												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Course Type</label>
														<select class="form-control select-multiple1" name="course_type" required>
															<option value="">Select Couese Type</option>
															<option value="Online Course">Online Course</option>
															<option value="Batch Course">Batch Course</option>
															
														</select>
													</div>
													<?php if($errors->has('course_type')): ?>
														<span class="text-danger"><?php echo e($errors->first('course_type')); ?> </span>
													<?php endif; ?>
												</div>   

												<div class="col-md-4 col-12 select_online d-none">
													<div class="form-group">
														<label for="first-name-column">Online Course ID</label>
														<input type="text" class="form-control" placeholder="Course ID" name="course_id" value="<?php echo e(old('course_id')); ?>">
														<?php if($errors->has('course_id')): ?>
														<span class="text-danger"><?php echo e($errors->first('course_id')); ?> </span>
														<?php endif; ?>
													</div>
												</div> 

												<div class="col-md-4 col-12 select_batch d-none">
													<div class="form-group">
														<label for="first-name-column">Batch Course</label>
														<?php 
														  $batchcourses = json_decode($batchcourses);
														  $batchcourses=$batchcourses->data;
														?>
														<?php if(count($batchcourses) > 0): ?>
														<select class="form-control select-multiple1" name="batch_id[]" style="width:100%;" multiple>
															<option value="">Select Batch Course</option>
															<?php $__currentLoopData = $batchcourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($value->id); ?>" <?php if(!empty(old('batch_id')) && old('batch_id') ==  $value->id): ?><?php echo e('selected'); ?><?php endif; ?>><?php echo e($value->title); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>
														<?php endif; ?>
														<?php if($errors->has('batch_id')): ?>
														<span class="text-danger"><?php echo e($errors->first('batch_id')); ?> </span>
														<?php endif; ?>
													</div>
												</div> 
												
												<div class="col-md-4 col-12 mt-2">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Submit</button>
												</div>

												<div class="table-responsive">
												<table class="table">
													<tr id="course_list"></tr>
												</table>
											    </div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
						
						<?php if(count($online_course_result) > 0): ?>
						<div class="table-responsive">
							<h3>Online Course List</h3>
							<table class="table data-list-view">
								<thead>
									<tr>
										<th>S. No.</th>
										<th>Employee Name</th>
										<th>Mobile No</th>
										<th>Course ID</th>
										<th>Status</th>
										<th>Added By</th>
										<th>Created Date</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									<?php $__currentLoopData = $online_course_result; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<tr class="rw<?php echo e($key); ?>">
										<td><strong><?php echo e($key + 1); ?></strong></td>
										<td class="product-category"><?php echo e(!empty($value->emp_name) ? $value->emp_name : ''); ?></td>
										<td class="product-category">
										<a href="javascript::void(0);" mobile_no="<?php echo e($value->mobile_no); ?>" data-id="<?php echo e($key); ?>" class="btn btn-sm btn-primary mt-1 get_course_details"><?php echo e($value->mobile_no); ?></a>
										</td>
										<td class="product-category"><?php echo e($value->course_id); ?></td>
										<td class="product-category"><?php echo e(!empty($value->course_status) ? $value->course_status : ''); ?></td>
										<td class="product-category"><?php echo e(!empty($value->addby_name) ? $value->addby_name : ''); ?></td>
										<td><?php echo e(date('d-m-Y', strtotime($value->created_at))); ?></td>
										<td colspan="2">
											<a href="javascript:void(0)" class="btn btn-danger btn-sm mt-1 waves-effect waves-light delete_course" data-course-id="<?php echo e($value->course_id); ?>" data-contact="<?php echo e($value->mobile_no); ?>" data-course-type="<?php echo e($value->course_status == 'Error' ? $value->course_status : ''); ?>" data-id="<?php echo e($key); ?>" title="Delete" style="padding: 0.5rem 0.5rem;"><i class="feather icon-trash"></i></a>
										</td>
									</tr>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</tbody>
							</table>
						</div> 
						<?php endif; ?>
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

<script>
$('.select-multiple1').select2({
	placeholder: "Select",
	allowClear: true
});

$('input[name="course_id"]').on('change, keyup', function() {
    var currentInput = $(this).val();
    var fixedInput = currentInput.replace(/[A-Za-z!@#$%^&*()]/g, '');
    $(this).val(fixedInput);
    console.log(fixedInput);
});

$(".get_course_details").on("click", function() {
	var unique_id = $(this).attr("data-id");
	var mobile_no = $(this).attr("mobile_no");
	$.ajax({type : 'POST',
		url : '<?php echo e(route('admin.get-course-list-by-mobile')); ?>',
		data : {'_token' : '<?php echo e(csrf_token()); ?>', 'mobile_no': mobile_no, 'unique_id': unique_id},
		dataType : 'json',
		success : function (data){
			if(data.status == false){
				swal("Error!", data.message, "error");
			} else if(data.status == true){
				$(".subtr"+unique_id).remove();
				$(".rw"+unique_id).after(data.data);
			}
		}
	});
})

$("select[name='emp_id']").on("change", function() {
	var data=$("select[name='emp_id'] :selected").text();
	data = data.split('-');
    mobile_no=data['2'];
	var unique_id ="333";
	$.ajax({type : 'POST',
		url : '<?php echo e(route('admin.get-course-list-by-mobile')); ?>',
		data : {'_token' : '<?php echo e(csrf_token()); ?>', 'mobile_no': mobile_no, 'unique_id': unique_id},
		dataType : 'json',
		success : function (data){
			if(data.status == false){
				swal("Error!", data.message, "error");
			} else if(data.status == true){
				$("#course_list").html(data.data);
			}
		}
	});
})

$("select[name='course_type']").on("change", function() {
	if(this.value=='Online Course'){
		$(".select_online").removeClass('d-none');
		$(".select_batch").addClass('d-none');
	}else if(this.value=='Batch Course'){
     $(".select_online").addClass('d-none');
	 $(".select_batch").removeClass('d-none');
	}
})



$(document).on("click", ".destroy_course", function () {  
	if (!confirm("Do you want to delete")){
      return false;
    }   
	var course_id = $(this).attr('data-course-id');
	var contact = $(this).attr('data-contact');
	var u_id = $(this).attr('data-id');
	var course_type = $(this).attr('data-course-type'); 
	
	$.ajax({type : 'POST',
		url : '<?php echo e(route('admin.delete-course')); ?>',
		data : {'_token' : '<?php echo e(csrf_token()); ?>', 'course_id': course_id, 'contact': contact, 'course_type': course_type},
		dataType : 'json',
		success : function (data){
			if(data.status == false){
				swal("Error!", data.message, "error");
			} else if(data.status == true){
				if(data.message != 'error'){
					if(course_type == "free"){
						$(".offsubstrrm"+u_id).remove();
					}
					if(course_type == "paid"){
						$(".paidsubstrrm"+u_id).remove();
					}
					if(course_type == "batch"){
						$(".batchsubstrrm"+u_id).remove();
					}
					
					swal("Success!", data.message, "success");
				}
				else{
					swal("Error!", data.message, "error");
				}
				
			}
		}
	});
	
});

$(document).on("click", ".delete_course", function () { 
	if (!confirm("Do you want to delete")){
      return false;
    }  
	var course_id = $(this).attr('data-course-id');
	var contact = $(this).attr('data-contact');
	var u_id = $(this).attr('data-id');
	var course_type = $(this).attr('data-course-type'); 
	
	$.ajax({
		type : 'POST',
		url : '<?php echo e(route('admin.delete-course')); ?>',
		data : {'_token' : '<?php echo e(csrf_token()); ?>', 'course_id': course_id, 'contact': contact, 'course_type': course_type},
		dataType : 'json',
		success : function (data){
			if(data.status == false){
				swal("Error!", data.message, "error");
			} else if(data.status == true){
				if(data.message != 'error'){
					$(".rw"+u_id).remove();
					swal("Success!", data.message, "success");
				}
				else{
					$(".rw"+u_id).remove();
					swal("Error!", data.message, "error");
				}
				
			}
		}
	});
	
});

$(document).on("click", ".remove_all", function () { 
	var u_id = $(this).attr('data-id');
	$(".subtr"+u_id).remove();
	
});

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/onlinecourses/index.blade.php ENDPATH**/ ?>