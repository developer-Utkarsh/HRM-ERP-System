
<style type="text/css">
	.hide {
		display: none!important;
	}
</style>
<?php $__env->startSection('content'); ?>

<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Edit Employee</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">Edit Employee
								</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
						<?php 
							$check = $_SERVER['QUERY_STRING'];
							if(!empty($check)){
								$nCheck	=	$check;
							}else{
								$nCheck	=	"";
							}
						?>
						
						<a href="<?php echo e(route('admin.employees.index', $nCheck)); ?>" class="btn btn-primary mr-1">Back</a>
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
									<form class="form" action="<?php echo e(route('admin.employees.update', $employee->id.'?'.$_SERVER['QUERY_STRING'])); ?>" method="post" enctype="multipart/form-data">
										<?php echo method_field('PATCH'); ?>
										<?php echo csrf_field(); ?>
										<div class="form-body">
											<div class="row">
												<div class="col-md-6 col-12">
													<div class="form-group">
														<?php $roles = \App\Role::where('status', '1')->get(); ?>
														<label for="first-name-column">Roles</label>
														<?php if(count($roles) > 0): ?>
														<select class="form-control get_role select-multiple1" name="role_id">
															<option value=""> - Select Any - </option>
															<?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($value->id); ?>" <?php if($value->id == $employee->role_id): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>
														<?php endif; ?>
														<?php if($errors->has('role_id')): ?>
														<span class="text-danger"><?php echo e($errors->first('role_id')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												
												<div class="col-md-6 col-12 course_category_div <?php if(!empty($employee->role_id) && ($employee->role_id == '27' || $employee->role_id == '4')): ?><?php echo e('show'); ?><?php else: ?><?php echo e('hide'); ?><?php endif; ?>">
													<div class="form-group">
														<?php $course_category = \App\CourseCategory::where('status', 'Active')->where('is_deleted','0')->get(); ?>
														<label for="first-name-column">Course Category</label>
														<?php if(count($course_category) > 0): ?>
														<select class="form-control select-multiple6 course_category" name="course_category[]" multiple>
															<option value=""> - Select Any - </option>
															<?php $__currentLoopData = $course_category; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($value->id); ?>" <?php if( !empty($employee->course_category) && in_array($value->id,explode(",",$employee->course_category))){ echo "selected";} ?>><?php echo e($value->name); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>
														<?php endif; ?>
														<?php if($errors->has('course_category')): ?>
														<span class="text-danger"><?php echo e($errors->first('course_category')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<?php $users = \App\User::where('status', '1')->where('role_id', '!=','1')->where('register_id', '!=', NULL)->get(); ?>
														<label for="first-name-column">Supervisor Name</label>
														<?php if(count($users) > 0): ?>
														<select class="form-control select-multiple1" name="supervisor_id[]" multiple>
															<option value=""> - Select Any - </option>
															<?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($value->id); ?>"<?php if( !empty($employee->supervisor_id) && in_array($value->id,json_decode($employee->supervisor_id))){ echo "selected";} ?>><?php echo e($value->name . ' ( ' .$value->register_id.' ) '); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>
														<?php endif; ?>
														<?php if($errors->has('supervisor_id')): ?>
														<span class="text-danger"><?php echo e($errors->first('supervisor_id')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<?php $department = \App\Department::where('status', 'Active')->get(); ?>
														<label for="first-name-column">Department Type</label>
														<?php if(count($department) > 0): ?>
														<select class="form-control get_role select-multiple1 department_type" name="department_type">
															<option value=""> - Select Any - </option>
															<?php $__currentLoopData = $department; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($value->id); ?>" <?php if($value->id == $employee->department_type): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>
														<?php endif; ?>
														<?php if($errors->has('department_type')): ?>
														<span class="text-danger"><?php echo e($errors->first('department_type')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<?php $sub_department = \App\SubDepartment::where('status', 'Active')->where('is_deleted', '0')->where('department_id', $employee->department_type)->get(); ?>
														<label for="first-name-column">Sub Department Type</label>
														<select class="form-control select-multiple1 sub_department_type" name="sub_department_type">
															<option value=""> - Select Any - </option>
															<?php $__currentLoopData = $sub_department; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($value->id); ?>" <?php if($value->id == $employee->sub_department_type): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>
													</div>
												</div>

												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Nickname</label>
														<input type="text" class="form-control" placeholder="Nickname" name="nickname" value="<?php echo e(old('nickname', $employee->nickname)); ?>">
														<?php if($errors->has('nickname')): ?>
														<span class="text-danger"><?php echo e($errors->first('nickname')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												
												<div class="col-md-6 col-12 show_fields1 subject_div" style="display: ;">
													<div class="form-group">
														<label for="first-name-column">Subjects</label>
														<?php $subjects = \App\Subject::where('status', '1')->orderBy('id', 'desc')->get(); ?>
														<?php if(count($subjects) > 0): ?>
														<select class="form-control select-multiple" multiple="multiple" name="subject_id[]">
															<option value=""> - Select Subjects - </option>
															<?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($value->id); ?>" <?php if( !empty($subject_ids) && in_array($value->id,$subject_ids)){ echo "selected";} ?> ><?php echo e($value->name); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-6 col-12 show_fields1" style="display:;">
													<div class="form-group">
														<label for="first-name-column">Agreement</label>														
														<select class="form-control get_hours" name="agreement">
															<option value=""> - Select Type - </option>
															<option value="Yes" <?php if('Yes' == old('agreement', $employee->agreement)): ?> selected="selected" <?php endif; ?>>Yes</option>
															<option value="No" <?php if('No' == old('agreement', $employee->agreement)): ?> selected="selected" <?php endif; ?>>No</option>
														</select>
													</div>
												</div>
												
												<div class="col-md-6 col-12 show_fields1 committed_fields" style="display:;">
													<div class="form-group">
														<label for="first-name-column">Committed Hours</label>
														<input type="text" class="form-control" placeholder="Committed Hours" name="committed_hours" value="<?php echo e(old('committed_hours', $employee->committed_hours)); ?>">
													</div>
												</div>
											</div>
											
											<div class="row show_fields" style="display: none;">
												<div class="col-md-12 col-12">
													<div class="input-group after-add-more">
														<input type="text" name="faculty[from_time][]" class="form-control timepicker" placeholder="Form Time" aria-describedby="button-addon2">
														<input type="text" name="faculty[to_time][]" class="form-control timepicker" placeholder="To Time" aria-describedby="button-addon2">
														<div class="input-group-append" id="button-addon2">
															<button class="btn btn-primary add-more" type="button">Add More</button>
														</div>
													</div>
													<?php if(isset($employee->faculty_relations) && !empty($employee->faculty_relations)): ?>
													<?php foreach($employee->faculty_relations as $key => $time) { if(!empty($time)) { ?>
													<div>
														<div class="control-group input-group" style="padding-top: 6px;">
															<input type="text" name="faculty[from_time][]" class="form-control timepicker" placeholder="Form Time" aria-describedby="button-addon2" value="<?php echo e($time->from_time); ?>">
															<input type="text" name="faculty[to_time][]" class="form-control timepicker" placeholder="To Time" aria-describedby="button-addon2" value="<?php echo e($time->to_time); ?>">
															<div class="input-group-append" id="button-addon2">
																<button class="btn btn-danger remove" type="button"><i class="glyphicon glyphicon-remove"></i>Remove</button>
															</div>
														</div>
													</div>
													<?php } } ?>
													<?php endif; ?>
												</div>
											</div>
											<div class="row">
												<div class="card-header">
													<h4 class="card-title pb-2">Basic Information</h4>
												</div>
											</div>
											<div class="row">
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Darwin Code</label>
														<input type="text" class="form-control" placeholder="Darwin Code" name="darwin_code" value="<?php echo e(old('darwin_code', $employee->darwin_code)); ?>">
														<?php if($errors->has('darwin_code')): ?>
														<span class="text-danger"><?php echo e($errors->first('darwin_code')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Full Name</label>
														<input type="text" class="form-control" placeholder="Full Name" name="name" value="<?php echo e(old('name', $employee->name)); ?>">
														<?php if($errors->has('name')): ?>
														<span class="text-danger"><?php echo e($errors->first('name')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Register Id (Employee ID)</label>
														<input type="text" class="form-control" placeholder="Register Id" name="register_id" value="<?php echo e(old('register_id', $employee->register_id)); ?>" readonly>
														<?php if($errors->has('register_id')): ?>
														<span class="text-danger"><?php echo e($errors->first('register_id')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Nominee Name</label>
														<input type="text" class="form-control" placeholder="Nominee Name" name="nominee_name" value="<?php echo e(old('nominee_name', $employee->nominee_name)); ?>">
														<?php if($errors->has('nominee_name')): ?>
														<span class="text-danger"><?php echo e($errors->first('nominee_name')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												
												<div class="col-md-6 col-12">
													<?php if($employee->user_details): ?>
													<?php 
													if(!empty($employee->user_details->dob)){
														$dob = date("Y-m-d", strtotime($employee->user_details->dob));
													}
													?>
													<?php endif; ?>
													<div class="form-group">
														<label for="company-column">DOB</label>
														<input type="date" class="form-control" placeholder="DOB" name="dob" value="<?php echo e(old('dob', isset($dob) ?  $dob : '')); ?>" max="<?=date('Y-m-d')?>">
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Father's Name</label>
														<input type="text" class="form-control" placeholder="Father's Name" name="fname" value="<?php echo e(old('fname', isset($employee->user_details->fname) ?  $employee->user_details->fname : '')); ?>">
														<?php if($errors->has('fname')): ?>
														<span class="text-danger"><?php echo e($errors->first('fname')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Mother's Name</label>
														<input type="text" class="form-control" placeholder="Mother's Name" name="mname" value="<?php echo e(old('mname', isset($employee->user_details->mname) ?  $employee->user_details->mname : '')); ?>">
														<?php if($errors->has('mname')): ?>
														<span class="text-danger"><?php echo e($errors->first('mname')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Personal Contact Number</label>
														<input type="number" class="form-control" placeholder="Personal Contact Number" name="contact_number" value="<?php echo e(old('contact_number', isset($employee->mobile) ?  $employee->mobile : '')); ?>">
														<?php if($errors->has('contact_number')): ?>
														<span class="text-danger"><?php echo e($errors->first('contact_number')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Personal Email ID</label>
														

														<input type="email" class="form-control" placeholder="Email" name="email" value="<?php echo e(old('email', isset($employee->email)&$employee->email!=NULL ?$employee->email :$employee->mobile.'@gmail.com')); ?>">

														<!-- <input type="email" class="form-control" placeholder="Email" name="email" value="<?php echo e(old('email', $employee->email)); ?>"> -->
														<?php if($errors->has('email')): ?>
														<span class="text-danger"><?php echo e($errors->first('email')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Official Number</label>
														<input type="text" class="form-control" placeholder="Official Number" name="official_no" value="<?php echo e(old('official_no', isset($employee->user_details->official_no) ? $employee->user_details->official_no : '')); ?>">
														<?php if($errors->has('official_no')): ?>
														<span class="text-danger"><?php echo e($errors->first('official_no')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Offical/Alternate Email</label>
														<input type="email" class="form-control" placeholder="Alternate Email" name="alternate_email" value="<?php echo e(old('alternate_email', isset($employee->user_details->alternate_email) ?  $employee->user_details->alternate_email : '')); ?>">
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Image</label>
														<input type="file" class="form-control" name="image" id="image-file">
														<?php if(!empty($employee->image)): ?>
														<img src="<?php echo e(asset('laravel/public/profile/'.$employee->image)); ?>" height="80" width="80">
														<?php endif; ?>
														<?php if($errors->has('image')): ?>
														<span class="text-danger"><?php echo e($errors->first('image')); ?> </span>
														<?php endif; ?>
														<small class="image-msg text-danger"></small>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Alternate Contact Number</label>
														<input type="number" class="form-control" placeholder="Alternate Contact Number" name="alternate_contact_number" value="<?php echo e(old('alternate_contact_number', isset($employee->user_details->alternate_contact_number) ?  $employee->user_details->alternate_contact_number : '')); ?>">
														<?php if($errors->has('alternate_contact_number')): ?>
														<span class="text-danger"><?php echo e($errors->first('alternate_contact_number')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												
												
												<div class="col-md-6 col-12">
													<div class="form-group d-flex align-items-center">
														<label class="mr-2">Gender :</label>
														<?php if($employee->user_details): ?>
														<label>
															<input type="radio" name="gender" value="Male" <?php echo e(($employee->user_details->gender == 'Male') ? "checked" : ""); ?>>
															Male
														</label>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="gender" value="Female" <?php echo e(($employee->user_details->gender == 'Female') ? "checked" : ""); ?>>
															Female
														</label>
														<?php else: ?>
														<label>
															<input type="radio" name="gender" value="Male">
															Male
														</label>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="gender" value="Female">
															Female
														</label>
														<?php endif; ?>
													</div>
													<?php if($errors->has('gender')): ?>
													<span class="text-danger"><?php echo e($errors->first('gender')); ?> </span>
													<?php endif; ?>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group d-flex align-items-center">
														<label class="mr-2">Marital  Status :</label>
														<?php if($employee->user_details): ?>
														<label>
															<input type="radio" name="material_status" value="Single" <?php echo e(($employee->user_details->material_status == 'Single') ? "checked" : ""); ?>>
															Single
														</label>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="material_status" value="Married" <?php echo e(($employee->user_details->material_status == 'Married') ? "checked" : ""); ?>>
															Married
														</label>
														<?php else: ?>
														<label>
															<input type="radio" name="material_status" value="Single">
															Single
														</label>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="material_status" value="Married">
															Married
														</label>
														<?php endif; ?>
														
													</div>
													<?php if($errors->has('material_status')): ?>
													<span class="text-danger"><?php echo e($errors->first('material_status')); ?> </span>
													<?php endif; ?>
												</div>
												
												<div class="col-md-6 col-12 anniversary_div <?php if(!empty($employee->user_details->material_status) && $employee->user_details->material_status == 'Married'): ?><?php echo e('show'); ?><?php else: ?><?php echo e('hide'); ?><?php endif; ?>">
													<?php if($employee->user_details): ?>
													<?php 
													if(!empty($employee->user_details->anniversary_date)){
														$anniversary_date = date("Y-m-d", strtotime($employee->user_details->anniversary_date));
													}
													?>
													<?php endif; ?>
													<div class="form-group">
														<label for="company-column">Anniversary Date</label>
														<input type="date" class="form-control" name="anniversary_date" value="<?php echo e(old('anniversary_date', isset($anniversary_date) ?  $anniversary_date : '')); ?>" max="<?=date('Y-m-d')?>">
													</div>
												</div>
												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Current Address</label>
														<textarea class="form-control" name="c_address" placeholder="Current Address"><?php echo e(old('c_address', isset($employee->user_details->c_address) ?  $employee->user_details->c_address : '')); ?></textarea>.
													<?php if($errors->has('c_address')): ?>
													<span class="text-danger"><?php echo e($errors->first('c_address')); ?> </span>
													<?php endif; ?>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Permanent Address</label>
														<textarea class="form-control" name="p_address" placeholder="Permanent Address"><?php echo e(old('p_address', isset($employee->user_details->p_address) ?  $employee->user_details->p_address : '')); ?></textarea>
													<?php if($errors->has('p_address')): ?>
													<span class="text-danger"><?php echo e($errors->first('p_address')); ?> </span>
													<?php endif; ?>
													</div>
												</div>
												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Employee Type</label>
														<select class="form-control select-multiple2" name="employee_type">
															<option value=""> - Select Employee Type - </option>
															<option value="Fulltime" <?php if('Fulltime' == old('employee_type', $employee->user_details->employee_type)): ?> selected="selected" <?php endif; ?>>Full Time</option>
															<option value="PartTime" <?php if('PartTime' == old('employee_type', $employee->user_details->employee_type)): ?> selected="selected" <?php endif; ?>>Part Time</option>
															<option value="Hourlybasis" <?php if('Hourlybasis' == old('employee_type', $employee->user_details->employee_type)): ?> selected="selected" <?php endif; ?>>Hourly Basis</option>

														</select>
													</div>
												</div>

												<?php 
												$designation_arr = \App\Designation::where('status', 'Active')->where('is_deleted','0')->orderBy('name')->get();
																							
												?>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Designation</label>
														<select class="form-control select-multiple3 desgination" name="degination">
														
														<option value=""> - Select Designation - </option>
														<?php $__currentLoopData = $designation_arr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
														<option value="<?php echo e($value->name); ?>" <?php if($value->name== $employee->user_details->degination): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
														<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


														</select>
													</div>
												</div>
												<div class="col-md-6 col-12 erpmain_category" style="display:none;">
													<div class="form-group">
														<label for="first-name-column">ERP Main Category</label>
														<select class="form-control select_category_name select-multiple6" name="erp_main_category[]" multiple>
															<option value=""> Select Category </option>
															<?php
															if(!empty($category_name)){ 
																foreach($category_name as $val){
																	?>
																	
																	<option value="<?php echo e($val); ?>"><?php echo e($val); ?></option>
																	<?php
																}
															} 
															?>
														</select>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Blood Group</label>
														<select class="form-control select-multiple4" name="blood_group">
															<option value=""> - Select Blood Group - </option>
															<option value="A+" <?php if(isset($employee->user_details->blood_group) &&  ('A+' == $employee->user_details->blood_group)): ?>? selected="selected" <?php endif; ?>>A+</option>
															<option value="A-" <?php if(isset($employee->user_details->blood_group) &&  ('A-' == $employee->user_details->blood_group)): ?>? selected="selected" <?php endif; ?>>A-</option>
															<option value="B+" <?php if(isset($employee->user_details->blood_group) &&  ('B+' == $employee->user_details->blood_group)): ?>? selected="selected" <?php endif; ?>>B+</option>
															<option value="B-" <?php if(isset($employee->user_details->blood_group) &&  ('B-' == $employee->user_details->blood_group)): ?>? selected="selected" <?php endif; ?>>B-</option>
															<option value="O+" <?php if(isset($employee->user_details->blood_group) &&  ('O+' == $employee->user_details->blood_group)): ?>? selected="selected" <?php endif; ?>>O+</option>
															<option value="O-" <?php if(isset($employee->user_details->blood_group) &&  ('O-' == $employee->user_details->blood_group)): ?>? selected="selected" <?php endif; ?>>O-</option>
															<option value="AB+" <?php if(isset($employee->user_details->blood_group) &&  ('AB+' == $employee->user_details->blood_group)): ?>? selected="selected" <?php endif; ?>>AB+</option>
															<option value="AB-" <?php if(isset($employee->user_details->blood_group) &&  ('AB-' == $employee->user_details->blood_group)): ?>? selected="selected" <?php endif; ?>>AB-</option>
														</select>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<?php $branches = \App\Branch::where('status', '1')->where('is_deleted', '0')->orderBy('id', 'desc')->get();
														$branch_id = 0;
														if(!empty($employee->user_details->branch_id)){
															$branch_id = $employee->user_details->branch_id;
														}
														
														$branch_ids = array();
														if(isset($employee->user_branches) && !empty($employee->user_branches)){
															foreach($employee->user_branches as $key => $val) { 
																if(!empty($val)) {
																	$branch_ids[] = $val->branch_id;
																}
															}
														}
														
														?>
														
														<label for="first-name-column">Branch</label>
														<?php if(count($branches) > 0): ?>
														<select class="form-control select-multiple5" name="branch_id[]" multiple>
															<option value=""> - Select Branch - </option>
															<?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($value->id); ?>" <?php if( !empty($branch_ids) && in_array($value->id,$branch_ids)){ echo "selected";} ?> ><?php echo e($value->name); ?></option>
															<!--option value="<?php echo e($value->id); ?>" <?php if($value->id == $branch_id): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option-->
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>
														<?php endif; ?>
														<?php if($errors->has('branch_id')): ?>
														<span class="text-danger"><?php echo e($errors->first('branch_id')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="company-column">Joining Date</label>
														<?php if($employee->user_details): ?>
														<?php 
														$readonly = "readonly";
														$min_date = "";
														$joining_date = date("Y-m-d", strtotime($employee->user_details->joining_date)); 
														if(date('Y') == date('Y',strtotime($joining_date)) ){
															$readonly ="";
															$min_date = date("Y-01-01");
														}
														?>
														<?php endif; ?>
														<input type="date" class="form-control joining_date" name="joining_date" placeholder="Joining Date" value="<?php echo e(old('joining_date', isset($joining_date) ? $joining_date : '')); ?>" onblur="joinTime()" min="<?php echo e($min_date); ?>" <?php echo e($readonly); ?>>
													</div>
												</div>
												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="company-column">Probation Period Type</label>
														<div class="form-group d-flex align-items-center mt-1">
														<?php
														$is_probation = $employee->user_details->probation;
														?>													
															<span>
																<input type="radio" name="probation" <?=($is_probation=='Yes')?'checked':'';?>  value="Yes" class="probationType">
																Yes
															</span>
															&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
															<span>
																<input type="radio" name="probation" <?=($is_probation=='No')?'checked':'';?> value="No" class="probationType">
																No
															</span>
														</div>
													</div>
												</div>
												<?php 
													$toDate		=	$joining_date;
													$fromDate	=	date('Y-m-d', strtotime ($toDate .'+90 days'));
													
													if(!empty($employee->user_details->probation_from)){
														$fromDate = $employee->user_details->probation_from;
													}
													
												?>
												
												<div class="col-md-6 col-12 probationTime" style="display:<?=($is_probation=='Yes')?'block':'none';?>">
													<div class="form-group">
														<label for="company-column">Probation Last Date</label>
														<input type="date" class="form-control probation_from" name="probation_from" placeholder="Joining Date" value="<?php echo e($fromDate); ?>" min="<?php echo e($min_date); ?>" onblur="callprobation(this.value)" <?php echo e($readonly); ?>>
														<?php if($errors->has('probation_from')): ?>
															<span class="text-danger"><?php echo e($errors->first('probation_from')); ?> </span>
														<?php endif; ?>
													</div>
												</div>
												
												<?php if(!empty($employee->reason_date)){ ?>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="company-column">Date Of Leave</label>
														<?php $reason_date = date("Y-m-d", strtotime($employee->reason_date)); ?>
														<input type="date" class="form-control reason_date" name="reason_date" placeholder="Date Of Leave" value="<?php echo e(old('reason_date', isset($reason_date) ? $reason_date : '')); ?>">
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="company-column">Reason</label>														
														<textarea class="form-control reason" name="reason"><?php echo e($employee->reason); ?></textarea>
													</div>
												</div>
												<?php } ?>
												
												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="company-column">PL</label>
														<input type="text" class="form-control pl" name="pl" placeholder="PL" value="<?php echo e(old('pl', $employee->user_details->pl)); ?>" readonly >
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="company-column">CL</label>
														<input type="text" class="form-control cl" name="cl" placeholder="CL" value="<?php echo e(old('cl', $employee->user_details->cl)); ?>" readonly >
													</div>
												</div>
												<div class="col-md-6 col-12" style="display:none;">
													<div class="form-group">
														<label for="company-column">SL</label>
														<input type="text" class="form-control sl" name="sl" placeholder="SL" value="<?php echo e(old('sl', $employee->user_details->sl)); ?>" readonly >
													</div>
												</div>
												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="company-column">Total Time ( in minutes )</label>
														<input type="number" class="form-control total_time" name="total_time" placeholder="Total Time ( in minutes )" value="<?php echo e(old('total_time', $employee->total_time)); ?>">
													</div>
												</div>
												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="company-column">Update Password</label>
														<input type="password" class="form-control password" name="password" placeholder="Update Password" value="" >
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="company-column">Asset Required</label>
														<input type="text" class="form-control" name="asset_requirement" placeholder="Asset Requirements" value="<?php echo e(old('asset_requirement', $employee->asset_requirement)); ?>" >
													</div>
												</div>
												
												<div class="row">
													<div class="col-md-6 col-12">
														<div class="form-group">
															<label for="company-column">Online Discount (%)</label>
															<input type="text" class="form-control" name="online_discount" placeholder="Online Discount (%)" value="<?php echo e(old('online_discount', $employee->online_discount)); ?>" >
														</div>
													</div>
													
													<div class="col-md-6 col-12">
														<div class="form-group">
															<label for="company-column">Offline Discount (%)</label>
															<input type="text" class="form-control" name="offline_discount" placeholder="Offline Discount (%)" value="<?php echo e(old('offline_discount', $employee->offline_discount)); ?>" >
														</div>
													</div>
													
												</div>
												
												<!--div class="col-md-6 col-12">
													<div class="form-group">
														<label for="company-column">Comp. Off Will Be Paid ?</label>
														<div class="form-group d-flex align-items-center mt-1">														
														<label>
															<input type="radio" name="extraPay" value="1" <?php echo e(($employee->is_extra_working_salary == '1') ? "checked" : ""); ?>>
															Yes
														</label>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="extraPay" value="0" <?php echo e(($employee->is_extra_working_salary == '0') ? "checked" : ""); ?>>
															No
														</label>
														</div>
													</div>
												</div-->

												
											</div>
											<div class="row">
												<div class="card-header">
													<h4 class="card-title pb-2">Documents</h4>
												</div>
											</div>
											<div class="row">
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Resume</label>
														<input type="file" class="form-control" name="resume" id="resume-file">
														<?php if(!empty($employee->user_details->resume)): ?>
														<a href="<?php echo e(asset('laravel/public/resume/'. $employee->user_details->resume)); ?>" target="_blank">View Resume</a>
														<?php endif; ?>
														<?php if($errors->has('resume')): ?>
														<span class="text-danger"><?php echo e($errors->first('resume')); ?> </span>
														<?php endif; ?>
														<small class="msg text-danger"></small>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="card-header">
												<h4 class="card-title pb-2">Account Information</h4>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6 col-12">
												<div class="form-group">
													<label for="first-name-column">Account Number</label>
													<input type="number" class="form-control" placeholder="Account Number" name="account_number" value="<?php echo e(old('account_number', isset($employee->user_details->account_number) ? $employee->user_details->account_number : '')); ?>">
													<?php if($errors->has('account_number')): ?>
													<span class="text-danger"><?php echo e($errors->first('account_number')); ?> </span>
													<?php endif; ?>
												</div>
											</div>
											<div class="col-md-6 col-12">
												<div class="form-group">
													<label for="first-name-column">Bank Name</label>
													<input type="text" class="form-control" placeholder="Bank Name" name="bank_name" value="<?php echo e(old('bank_name', isset($employee->user_details->bank_name) ?  $employee->user_details->bank_name : '0')); ?>" onkeypress="return blockSpecialChar(event)">
													<?php if($errors->has('bank_name')): ?>
													<span class="text-danger"><?php echo e($errors->first('bank_name')); ?> </span>
													<?php endif; ?>
												</div>
											</div>
											<div class="col-md-6 col-12">
												<div class="form-group">
													<label for="first-name-column">IFSC Code</label>
													<input type="text" class="form-control" placeholder="IFSC Code" name="ifsc_code" value="<?php echo e(old('ifsc_code', isset($employee->user_details->ifsc_code) ?  $employee->user_details->ifsc_code : '0')); ?>">
													<?php if($errors->has('ifsc_code')): ?>
													<span class="text-danger"><?php echo e($errors->first('ifsc_code')); ?> </span>
													<?php endif; ?>
												</div>
											</div>
											<div class="col-md-6 col-12">
												<div class="form-group">
													<label for="first-name-column">Branch</label>
													<input type="text" class="form-control" placeholder="Branch" name="bank_branch" value="<?php echo e(old('bank_branch', isset($employee->user_details->bank_branch) ?  $employee->user_details->bank_branch : '0')); ?>">
													<?php if($errors->has('bank_branch')): ?>
													<span class="text-danger"><?php echo e($errors->first('bank_branch')); ?> </span>
													<?php endif; ?>
												</div>
											</div>
											<div class="col-md-6 col-12">
												<div class="form-group">
													<label for="first-name-column">Gross Salary</label>
													<input type="number" class="form-control" placeholder="Gross Salary" name="net_salary" value="<?php echo e(old('net_salary', isset($employee->user_details->net_salary) ? $employee->user_details->net_salary : '0')); ?>">
													<?php if($errors->has('net_salary')): ?>
													<span class="text-danger"><?php echo e($errors->first('net_salary')); ?> </span>
													<?php endif; ?>
												</div>
											</div>
											<div class="col-md-6 col-12">
												<div class="form-group">
													<label for="first-name-column">TDS</label>
													<input type="number" class="form-control" placeholder="TDS" name="tds" value="<?php echo e(old('tds', isset($employee->user_details->tds) ? $employee->user_details->tds : '0')); ?>">
													<?php if($errors->has('tds')): ?>
													<span class="text-danger"><?php echo e($errors->first('tds')); ?> </span>
													<?php endif; ?>
												</div>
											</div>
											
										</div>
										
										<hr>
										<div class="row">
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">PF Amount</label>
													<input type="text" class="form-control" placeholder="PF Amount" name="pf_amount" value="<?php echo e(old('pf_amount', isset($employee->user_details->pf_amount) ? $employee->user_details->pf_amount : '0')); ?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
													<?php if($errors->has('pf_amount')): ?>
													<span class="text-danger"><?php echo e($errors->first('pf_amount')); ?> </span>
													<?php endif; ?>
												</div>
											</div>
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">PF Date</label>
													<input type="date" class="form-control" name="pf_date" value="<?php echo e(old('pf_date', isset($employee->user_details->pf_date) ? $employee->user_details->pf_date : '')); ?>">
													<?php if($errors->has('pf_date')): ?>
													<span class="text-danger"><?php echo e($errors->first('pf_date')); ?> </span>
													<?php endif; ?>
												</div>
											</div>
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">Is PF Deduct</label>
													<select class="form-control" name="is_pf">
														<option value=""> - Select - </option>
														<option value="Yes" <?php if(isset($employee->user_details->is_pf) &&  ('Yes' == $employee->user_details->is_pf)): ?>? selected="selected" <?php endif; ?>>Yes</option>
														<option value="No" <?php if(isset($employee->user_details->is_pf) &&  ('No' == $employee->user_details->is_pf)): ?>? selected="selected" <?php endif; ?>>No</option>
													</select>
													<?php if($errors->has('is_pf')): ?>
													<span class="text-danger"><?php echo e($errors->first('is_pf')); ?> </span>
													<?php endif; ?>
												</div>
											</div>
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">ESI Amount</label>
													<input type="text" class="form-control" placeholder="ESI Amount" name="esi_amount" value="<?php echo e(old('esi_amount', isset($employee->user_details->esi_amount) ? $employee->user_details->esi_amount : '0')); ?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
													<?php if($errors->has('esi_amount')): ?>
													<span class="text-danger"><?php echo e($errors->first('esi_amount')); ?> </span>
													<?php endif; ?>
												</div>
											</div>
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">ESI Date</label>
													<input type="date" class="form-control" name="esi_date" value="<?php echo e(old('esi_date', isset($employee->user_details->esi_date) ? $employee->user_details->esi_date : '0')); ?>">
													<?php if($errors->has('esi_date')): ?>
													<span class="text-danger"><?php echo e($errors->first('esi_date')); ?> </span>
													<?php endif; ?>
												</div>
											</div>
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">Is ESI Deduct</label>
													<select class="form-control" name="is_esi">
														<option value=""> - Select - </option>
														<option value="Yes" <?php if(isset($employee->user_details->is_esi) &&  ('Yes' == $employee->user_details->is_esi)): ?>? selected="selected" <?php endif; ?>>Yes</option>
														<option value="No" <?php if(isset($employee->user_details->is_esi) &&  ('No' == $employee->user_details->is_esi)): ?>? selected="selected" <?php endif; ?>>No</option>
													</select>
													<?php if($errors->has('is_esi')): ?>
													<span class="text-danger"><?php echo e($errors->first('is_esi')); ?> </span>
													<?php endif; ?>
												</div>
											</div>
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">ESIC No</label>
													<input type="text" class="form-control" placeholder="Esic No" name="esic_no" value="<?php echo e(old('esic_no', isset($employee->user_details->esic_no) ? $employee->user_details->esic_no : '')); ?>">
													<?php if($errors->has('esic_no')): ?>
													<span class="text-danger"><?php echo e($errors->first('esic_no')); ?> </span>
													<?php endif; ?>
												</div>
											</div>
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">Addhar Card No</label>
													<input type="text" class="form-control" placeholder="Addhar Card No" name="aadhar_card_no" value="<?php echo e(old('aadhar_card_no', isset($employee->user_details->aadhar_card_no) ? $employee->user_details->aadhar_card_no : '')); ?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
													<?php if($errors->has('aadhar_card_no')): ?>
													<span class="text-danger"><?php echo e($errors->first('aadhar_card_no')); ?> </span>
													<?php endif; ?>
												</div>
											</div>
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">Name As Per Aadhar</label>
													<input type="text" class="form-control" placeholder="Name As Per Aadhar" name="aadhar_name" value="<?php echo e(old('aadhar_name', isset($employee->user_details->aadhar_name) ? $employee->user_details->aadhar_name : '')); ?>">
													<?php if($errors->has('aadhar_name')): ?>
													<span class="text-danger"><?php echo e($errors->first('aadhar_name')); ?> </span>
													<?php endif; ?>
												</div>
											</div>
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">Pan No</label>
													<input type="text" class="form-control" placeholder="Pan No" name="pan_no" value="<?php echo e(old('pan_no', isset($employee->user_details->pan_no) ? $employee->user_details->pan_no : '')); ?>">
													<?php if($errors->has('pan_no')): ?>
													<span class="text-danger"><?php echo e($errors->first('pan_no')); ?> </span>
													<?php endif; ?>
												</div>
											</div>
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">Name As Per Pan</label>
													<input type="text" class="form-control" placeholder="Name As Per Pan" name="pan_name" value="<?php echo e(old('pan_name', isset($employee->user_details->pan_name) ? $employee->user_details->pan_name : '')); ?>">
													<?php if($errors->has('pan_name')): ?>
													<span class="text-danger"><?php echo e($errors->first('pan_name')); ?> </span>
													<?php endif; ?>
												</div>
											</div>
											
											
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">Previous Experience</label>
													<input type="text" class="form-control" placeholder="Previous Experience" name="previous_experience" value="<?php echo e(old('previous_experience', isset($employee->user_details->previous_experience) ? $employee->user_details->previous_experience : '')); ?>">
													<?php if($errors->has('previous_experience')): ?>
													<span class="text-danger"><?php echo e($errors->first('previous_experience')); ?> </span>
													<?php endif; ?>
												</div>
											</div>
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">UAN No</label>
													<input type="text" class="form-control" placeholder="UAN No" name="uan_no" value="<?php echo e(old('uan_no', isset($employee->user_details->uan_no) ? $employee->user_details->uan_no : '')); ?>">
													<?php if($errors->has('uan_no')): ?>
													<span class="text-danger"><?php echo e($errors->first('uan_no')); ?> </span>
													<?php endif; ?>
												</div>
											</div>
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">Name As Per Bank</label>
													<input type="text" class="form-control" placeholder="Name As Per Bamnk" name="bank_emp_name" value="<?php echo e(old('bank_emp_name', isset($employee->user_details->bank_emp_name) ? $employee->user_details->bank_emp_name : '')); ?>">
													<?php if($errors->has('bank_emp_name')): ?>
													<span class="text-danger"><?php echo e($errors->first('bank_emp_name')); ?> </span>
													<?php endif; ?>
												</div>
											</div>
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">In Timing Shift</label>
													<input type="time" class="form-control" name="timing_shift_in" value="<?php echo e(old('timing_shift_in', isset($employee->user_details->timing_shift_in) ? $employee->user_details->timing_shift_in : '')); ?>">
													<?php if($errors->has('timing_shift_in')): ?>
													<span class="text-danger"><?php echo e($errors->first('timing_shift_in')); ?> </span>
													<?php endif; ?>
												</div>
											</div>
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">Out Timing Shift</label>
													<input type="time" class="form-control" name="timing_shift_out" value="<?php echo e(old('timing_shift_out', isset($employee->user_details->timing_shift_out) ? $employee->user_details->timing_shift_out : '')); ?>">
													<?php if($errors->has('timing_shift_out')): ?>
													<span class="text-danger"><?php echo e($errors->first('timing_shift_out')); ?> </span>
													<?php endif; ?>
												</div>
											</div>
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">EMP File No</label>
													<input type="text" class="form-control" placeholder="EMP File No" name="emp_file_no" value="<?php echo e(old('emp_file_no', isset($employee->user_details->emp_file_no) ? $employee->user_details->emp_file_no : '')); ?>">
													<?php if($errors->has('emp_file_no')): ?>
													<span class="text-danger"><?php echo e($errors->first('emp_file_no')); ?> </span>
													<?php endif; ?>
												</div>
											</div>
										</div>
										
										<div class="col-12">
											<button type="submit" class="btn btn-primary mr-1 mb-1 btn_submit">Update</button>
										</div>
									</form>
									<div class="copy-fields hide">
										<div class="control-group input-group" style="padding-top: 6px;">
											<input type="text" name="faculty[from_time][]" class="form-control timepicker" placeholder="Form Time" aria-describedby="button-addon2">
											<input type="text" name="faculty[to_time][]" class="form-control timepicker" placeholder="To Time" aria-describedby="button-addon2">
											<div class="input-group-append" id="button-addon2">
												<button class="btn btn-danger remove" type="button">Remove</button>
											</div>
										</div>
									</div>
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
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$(".add-more").click(function(){ 
			var html = $(".copy-fields").html();
			$(".after-add-more").after(html);    
		});
		$("body").on("click",".remove",function(){ 
			$(this).parents(".control-group").remove();
		});
		$(document).on('focus', '.timepicker', function(){
			$(this).timepicker({
				interval: 30,
				timeFormat: 'HH:mm',
				minTime: '5:00',
			});
		});
	}); 
</script>
<script type="text/javascript">
	$(".get_role").on("change", function () {
		var role = $(".get_role option:selected").attr('value');
		if(role == '2'){
			$(".show_fields").show();
			$(".show_fields1").show();
		}else{
			$(".show_fields").hide();
			$(".show_fields1").hide();
		}
		
		if(role == '27'){
			$('.course_category_div').removeClass('hide');
			$('.course_category_div').addClass('show');
		}else{
			$('.course_category_div').removeClass('show');
			$('.course_category_div').addClass('hide');
		}
	});

	$(document).ready(function() {
		var role = $(".get_role option:selected").attr('value');
		if(role == '2'){
			$(".show_fields").show();
			$(".show_fields1").show();
		}else{
			$(".show_fields").hide();
			$(".show_fields1").hide();
		}		
	});
	
	$('input[name="material_status"]').on("click", function () {
		var material_val = $('input[name="material_status"]:checked').val();
		if(material_val == 'Single'){
			$('.anniversary_div').removeClass('show');
			$('.anniversary_div').addClass('hide');
		}
		else if(material_val == 'Married'){
			$('.anniversary_div').removeClass('hide');
			$('.anniversary_div').addClass('show');
		} 
		else{
			$('.anniversary_div').removeClass('show');
			$('.anniversary_div').addClass('hide');
		}
	});	
</script>

<?php  
$none = "none";
if( $employee->role_id =='2'){ 
	$none = "block";
} 
?>

<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple').select2({
			placeholder: " Select Subjects",
			allowClear: true
		});
		
		$('.select-multiple1').select2({
			placeholder: "Select",
			allowClear: true
		});
		$('.select-multiple2').select2({
			placeholder: "Select",
			allowClear: true
		});
		$('.select-multiple3').select2({
			placeholder: "Select",
			allowClear: true
		});
		$('.select-multiple4').select2({
			placeholder: "Select",
			allowClear: true
		});
		$('.select-multiple5').select2({
			placeholder: "Select",
			allowClear: true
		});
		$('.select-multiple6').select2({
			placeholder: "Select",
			width: '100%',
			allowClear: true
		});
		
		$(".subject_div").css("display","<?=$none?>");
	});
</script>
<script type="text/javascript">
	function blockSpecialChar(e){
		var k;
		document.all ? k = e.keyCode : k = e.which;
		return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57));
	}
	
	$('#resume-file').bind('change', function() {
		var resume_size = this.files[0].size/1024/1024;
		if(resume_size > 5){
			$(".msg").text('The resume may not be greater than 5 MB.');
			$('.btn_submit').attr('disabled', 'disabled');
		}
		else{
			$(".msg").text('');
			$('.btn_submit').removeAttr('disabled');
		}
	});
	
	$('#image-file').bind('change', function() {
		var image_size = this.files[0].size/1024/1024;
		if(image_size > 5){
			$(".image-msg").text('The image may not be greater than 5 MB.');
			$('.btn_submit').attr('disabled', 'disabled');
		}
		else{
			$(".image-msg").text('');
			$('.btn_submit').removeAttr('disabled');
		}
	});
	
	$(document).on("change",".joining_date",function(){
		var j_date = $(this).val();
		$.ajax({
			type : 'POST',
			url : '<?php echo e(route('admin.employee.get_leave_month')); ?>',
			data : {'_token' : '<?php echo e(csrf_token()); ?>', 'j_date': j_date},
			dataType : 'json',
			success : function (data){ //alert(data['id']);
				
			  $(".pl").val(data['pl']);
			  $(".cl").val(data['cl']);
			  $(".sl").val(data['sl']);
				
			}
		});
	});
	$(document).ready(function() {
		$(".probationType").click(function() {
			var test = $(this).val();
			if(test=="No"){
				$('.probationTime').hide();
			}else{
				$('.probationTime').show();
			}
			
		});
	});

	$(".department_type").on("change", function () {
		var department_type_id = $(".department_type option:selected").attr('value');
		if (department_type_id) {
			$.ajax({
				type : 'POST',
				url : '<?php echo e(route('admin.get-sub-department')); ?>',
				data : {'_token' : '<?php echo e(csrf_token()); ?>', 'department_type_id': department_type_id},
				dataType : 'html',
				success : function (data){
					$('.sub_department_type').empty();
					$('.sub_department_type').append(data);
					$(".sub_department_type").trigger("change");
					
				}
			});
		}
	});
	
	function joinTime(){
		var nDate = $('input[name=joining_date]').val();		
		var date = new Date(nDate);		
		date.setDate(date.getDate() + 90);
		var dateString = date.toISOString().split('T')[0];
		
		$('input[name=probation_from]').val(dateString);
		
		callprobation(dateString);
	}
	
	function callprobation(probation_date){
		$.ajax({
			type : 'POST',
			url : '<?php echo e(route('admin.employee.get_leave_month')); ?>',
			data : {'_token' : '<?php echo e(csrf_token()); ?>', 'probation_date': probation_date},
			dataType : 'json',
			success : function (data){ //alert(data['id']);
				
			  $(".pl").val(data['pl']);
			  $(".cl").val(data['cl']);
			  $(".sl").val(data['sl']);
				
			}
		});
	}
	
	$('.desgination').on('change', function() {
		var newval = $(this).val();
		
		$('.erpmain_category').hide();
		if(newval=="CATEGORY HEAD"){
			$('.erpmain_category').css('display','block');
		}
	});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/employee/edit.blade.php ENDPATH**/ ?>