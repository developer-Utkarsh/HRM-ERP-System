@extends('layouts.admin')
<style type="text/css">
	.hide {
		display: none!important;
	}
</style>
@section('content')

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
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
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
						
						<a href="{{ route('admin.employees.index', $nCheck) }}" class="btn btn-primary mr-1">Back</a>
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
									<form class="form" action="{{ route('admin.employees.update', $employee->id.'?'.$_SERVER['QUERY_STRING']) }}" method="post" enctype="multipart/form-data">
										@method('PATCH')
										@csrf
										<div class="form-body">
											<div class="row">
												<div class="col-md-6 col-12">
													<div class="form-group">
														<?php $roles = \App\Role::where('status', '1')->get(); ?>
														<label for="first-name-column">Roles</label>
														@if(count($roles) > 0)
														<select class="form-control get_role select-multiple1" name="role_id">
															<option value=""> - Select Any - </option>
															@foreach($roles as $value)
															<option value="{{ $value->id }}" @if($value->id == $employee->role_id) selected="selected" @endif>{{ $value->name }}</option>
															@endforeach
														</select>
														@endif
														@if($errors->has('role_id'))
														<span class="text-danger">{{ $errors->first('role_id') }} </span>
														@endif
													</div>
												</div>
												
												<div class="col-md-6 col-12 course_category_div @if(!empty($employee->role_id) && ($employee->role_id == '27' || $employee->role_id == '4')){{'show'}}@else{{'hide'}}@endif">
													<div class="form-group">
														<?php $course_category = \App\CourseCategory::where('status', 'Active')->where('is_deleted','0')->get(); ?>
														<label for="first-name-column">Course Category</label>
														@if(count($course_category) > 0)
														<select class="form-control select-multiple6 course_category" name="course_category[]" multiple>
															<option value=""> - Select Any - </option>
															@foreach($course_category as $value)
															<option value="{{ $value->id }}" <?php if( !empty($employee->course_category) && in_array($value->id,explode(",",$employee->course_category))){ echo "selected";} ?>>{{ $value->name }}</option>
															@endforeach
														</select>
														@endif
														@if($errors->has('course_category'))
														<span class="text-danger">{{ $errors->first('course_category') }} </span>
														@endif
													</div>
												</div>
												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<?php $users = \App\User::where('status', '1')->where('role_id', '!=','1')->where('register_id', '!=', NULL)->get(); ?>
														<label for="first-name-column">Supervisor Name</label>
														@if(count($users) > 0)
														<select class="form-control select-multiple1" name="supervisor_id[]" multiple>
															<option value=""> - Select Any - </option>
															@foreach($users as $value)
															<option value="{{ $value->id }}"<?php if( !empty($employee->supervisor_id) && in_array($value->id,json_decode($employee->supervisor_id))){ echo "selected";} ?>>{{ $value->name . ' ( ' .$value->register_id.' ) ' }}</option>
															@endforeach
														</select>
														@endif
														@if($errors->has('supervisor_id'))
														<span class="text-danger">{{ $errors->first('supervisor_id') }} </span>
														@endif
													</div>
												</div>
												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<?php $department = \App\Department::where('status', 'Active')->get(); ?>
														<label for="first-name-column">Department Type</label>
														@if(count($department) > 0)
														<select class="form-control get_role select-multiple1 department_type" name="department_type">
															<option value=""> - Select Any - </option>
															@foreach($department as $value)
															<option value="{{ $value->id }}" @if($value->id == $employee->department_type) selected="selected" @endif>{{ $value->name }}</option>
															@endforeach
														</select>
														@endif
														@if($errors->has('department_type'))
														<span class="text-danger">{{ $errors->first('department_type') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<?php $sub_department = \App\SubDepartment::where('status', 'Active')->where('is_deleted', '0')->where('department_id', $employee->department_type)->get(); ?>
														<label for="first-name-column">Sub Department Type</label>
														<select class="form-control select-multiple1 sub_department_type" name="sub_department_type">
															<option value=""> - Select Any - </option>
															@foreach($sub_department as $value)
															<option value="{{ $value->id }}" @if($value->id == $employee->sub_department_type) selected="selected" @endif>{{ $value->name }}</option>
															@endforeach
														</select>
													</div>
												</div>

												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Nickname</label>
														<input type="text" class="form-control" placeholder="Nickname" name="nickname" value="{{ old('nickname', $employee->nickname) }}">
														@if($errors->has('nickname'))
														<span class="text-danger">{{ $errors->first('nickname') }} </span>
														@endif
													</div>
												</div>
												
												<div class="col-md-6 col-12 show_fields1 subject_div" style="display: ;">
													<div class="form-group">
														<label for="first-name-column">Subjects</label>
														<?php $subjects = \App\Subject::where('status', '1')->orderBy('id', 'desc')->get(); ?>
														@if(count($subjects) > 0)
														<select class="form-control select-multiple" multiple="multiple" name="subject_id[]">
															<option value=""> - Select Subjects - </option>
															@foreach($subjects as $value)
															<option value="{{ $value->id }}" <?php if( !empty($subject_ids) && in_array($value->id,$subject_ids)){ echo "selected";} ?> >{{ $value->name }}</option>
															@endforeach
														</select>
														@endif
													</div>
												</div>
												<div class="col-md-6 col-12 show_fields1" style="display:;">
													<div class="form-group">
														<label for="first-name-column">Agreement</label>														
														<select class="form-control get_hours" name="agreement">
															<option value=""> - Select Type - </option>
															<option value="Yes" @if('Yes' == old('agreement', $employee->agreement)) selected="selected" @endif>Yes</option>
															<option value="No" @if('No' == old('agreement', $employee->agreement)) selected="selected" @endif>No</option>
														</select>
													</div>
												</div>
												
												<div class="col-md-6 col-12 show_fields1 committed_fields" style="display:;">
													<div class="form-group">
														<label for="first-name-column">Committed Hours</label>
														<input type="text" class="form-control" placeholder="Committed Hours" name="committed_hours" value="{{ old('committed_hours', $employee->committed_hours) }}">
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
													@if(isset($employee->faculty_relations) && !empty($employee->faculty_relations))
													<?php foreach($employee->faculty_relations as $key => $time) { if(!empty($time)) { ?>
													<div>
														<div class="control-group input-group" style="padding-top: 6px;">
															<input type="text" name="faculty[from_time][]" class="form-control timepicker" placeholder="Form Time" aria-describedby="button-addon2" value="{{ $time->from_time }}">
															<input type="text" name="faculty[to_time][]" class="form-control timepicker" placeholder="To Time" aria-describedby="button-addon2" value="{{ $time->to_time }}">
															<div class="input-group-append" id="button-addon2">
																<button class="btn btn-danger remove" type="button"><i class="glyphicon glyphicon-remove"></i>Remove</button>
															</div>
														</div>
													</div>
													<?php } } ?>
													@endif
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
														<input type="text" class="form-control" placeholder="Darwin Code" name="darwin_code" value="{{ old('darwin_code', $employee->darwin_code) }}">
														@if($errors->has('darwin_code'))
														<span class="text-danger">{{ $errors->first('darwin_code') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Full Name</label>
														<input type="text" class="form-control" placeholder="Full Name" name="name" value="{{ old('name', $employee->name) }}">
														@if($errors->has('name'))
														<span class="text-danger">{{ $errors->first('name') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Register Id (Employee ID)</label>
														<input type="text" class="form-control" placeholder="Register Id" name="register_id" value="{{ old('register_id', $employee->register_id) }}" readonly>
														@if($errors->has('register_id'))
														<span class="text-danger">{{ $errors->first('register_id') }} </span>
														@endif
													</div>
												</div>
												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Nominee Name</label>
														<input type="text" class="form-control" placeholder="Nominee Name" name="nominee_name" value="{{ old('nominee_name', $employee->nominee_name) }}">
														@if($errors->has('nominee_name'))
														<span class="text-danger">{{ $errors->first('nominee_name') }} </span>
														@endif
													</div>
												</div>
												
												<div class="col-md-6 col-12">
													@if($employee->user_details)
													<?php 
													if(!empty($employee->user_details->dob)){
														$dob = date("Y-m-d", strtotime($employee->user_details->dob));
													}
													?>
													@endif
													<div class="form-group">
														<label for="company-column">DOB</label>
														<input type="date" class="form-control" placeholder="DOB" name="dob" value="{{ old('dob', isset($dob) ?  $dob : '') }}" max="<?=date('Y-m-d')?>">
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Father's Name</label>
														<input type="text" class="form-control" placeholder="Father's Name" name="fname" value="{{ old('fname', isset($employee->user_details->fname) ?  $employee->user_details->fname : '') }}">
														@if($errors->has('fname'))
														<span class="text-danger">{{ $errors->first('fname') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Mother's Name</label>
														<input type="text" class="form-control" placeholder="Mother's Name" name="mname" value="{{ old('mname', isset($employee->user_details->mname) ?  $employee->user_details->mname : '') }}">
														@if($errors->has('mname'))
														<span class="text-danger">{{ $errors->first('mname') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Personal Contact Number</label>
														<input type="number" class="form-control" placeholder="Personal Contact Number" name="contact_number" value="{{ old('contact_number', isset($employee->mobile) ?  $employee->mobile : '') }}">
														@if($errors->has('contact_number'))
														<span class="text-danger">{{ $errors->first('contact_number') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Personal Email ID</label>
														

														<input type="email" class="form-control" placeholder="Email" name="email" value="{{ old('email', isset($employee->email)&$employee->email!=NULL ?$employee->email :$employee->mobile.'@gmail.com')}}">

														<!-- <input type="email" class="form-control" placeholder="Email" name="email" value="{{ old('email', $employee->email) }}"> -->
														@if($errors->has('email'))
														<span class="text-danger">{{ $errors->first('email') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Official Number</label>
														<input type="text" class="form-control" placeholder="Official Number" name="official_no" value="{{ old('official_no', isset($employee->user_details->official_no) ? $employee->user_details->official_no : '') }}">
														@if($errors->has('official_no'))
														<span class="text-danger">{{ $errors->first('official_no') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Offical/Alternate Email</label>
														<input type="email" class="form-control" placeholder="Alternate Email" name="alternate_email" value="{{ old('alternate_email', isset($employee->user_details->alternate_email) ?  $employee->user_details->alternate_email : '') }}">
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Image</label>
														<input type="file" class="form-control" name="image" id="image-file">
														@if(!empty($employee->image))
														<img src="{{ asset('laravel/public/profile/'.$employee->image) }}" height="80" width="80">
														@endif
														@if($errors->has('image'))
														<span class="text-danger">{{ $errors->first('image') }} </span>
														@endif
														<small class="image-msg text-danger"></small>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Alternate Contact Number</label>
														<input type="number" class="form-control" placeholder="Alternate Contact Number" name="alternate_contact_number" value="{{ old('alternate_contact_number', isset($employee->user_details->alternate_contact_number) ?  $employee->user_details->alternate_contact_number : '') }}">
														@if($errors->has('alternate_contact_number'))
														<span class="text-danger">{{ $errors->first('alternate_contact_number') }} </span>
														@endif
													</div>
												</div>
												
												
												<div class="col-md-6 col-12">
													<div class="form-group d-flex align-items-center">
														<label class="mr-2">Gender :</label>
														@if($employee->user_details)
														<label>
															<input type="radio" name="gender" value="Male" {{ ($employee->user_details->gender == 'Male') ? "checked" : ""}}>
															Male
														</label>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="gender" value="Female" {{ ($employee->user_details->gender == 'Female') ? "checked" : ""}}>
															Female
														</label>
														@else
														<label>
															<input type="radio" name="gender" value="Male">
															Male
														</label>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="gender" value="Female">
															Female
														</label>
														@endif
													</div>
													@if($errors->has('gender'))
													<span class="text-danger">{{ $errors->first('gender') }} </span>
													@endif
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group d-flex align-items-center">
														<label class="mr-2">Marital  Status :</label>
														@if($employee->user_details)
														<label>
															<input type="radio" name="material_status" value="Single" {{ ($employee->user_details->material_status == 'Single') ? "checked" : ""}}>
															Single
														</label>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="material_status" value="Married" {{ ($employee->user_details->material_status == 'Married') ? "checked" : ""}}>
															Married
														</label>
														@else
														<label>
															<input type="radio" name="material_status" value="Single">
															Single
														</label>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="material_status" value="Married">
															Married
														</label>
														@endif
														
													</div>
													@if($errors->has('material_status'))
													<span class="text-danger">{{ $errors->first('material_status') }} </span>
													@endif
												</div>
												
												<div class="col-md-6 col-12 anniversary_div @if(!empty($employee->user_details->material_status) && $employee->user_details->material_status == 'Married'){{'show'}}@else{{'hide'}}@endif">
													@if($employee->user_details)
													<?php 
													if(!empty($employee->user_details->anniversary_date)){
														$anniversary_date = date("Y-m-d", strtotime($employee->user_details->anniversary_date));
													}
													?>
													@endif
													<div class="form-group">
														<label for="company-column">Anniversary Date</label>
														<input type="date" class="form-control" name="anniversary_date" value="{{ old('anniversary_date', isset($anniversary_date) ?  $anniversary_date : '') }}" max="<?=date('Y-m-d')?>">
													</div>
												</div>
												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Current Address</label>
														<textarea class="form-control" name="c_address" placeholder="Current Address">{{ old('c_address', isset($employee->user_details->c_address) ?  $employee->user_details->c_address : '') }}</textarea>.
													@if($errors->has('c_address'))
													<span class="text-danger">{{ $errors->first('c_address') }} </span>
													@endif
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Permanent Address</label>
														<textarea class="form-control" name="p_address" placeholder="Permanent Address">{{ old('p_address', isset($employee->user_details->p_address) ?  $employee->user_details->p_address : '') }}</textarea>
													@if($errors->has('p_address'))
													<span class="text-danger">{{ $errors->first('p_address') }} </span>
													@endif
													</div>
												</div>
												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Employee Type</label>
														<select class="form-control select-multiple2" name="employee_type">
															<option value=""> - Select Employee Type - </option>
															<option value="Fulltime" @if('Fulltime' == old('employee_type', $employee->user_details->employee_type)) selected="selected" @endif>Full Time</option>
															<option value="PartTime" @if('PartTime' == old('employee_type', $employee->user_details->employee_type)) selected="selected" @endif>Part Time</option>
															<option value="Hourlybasis" @if('Hourlybasis' == old('employee_type', $employee->user_details->employee_type)) selected="selected" @endif>Hourly Basis</option>

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
														@foreach($designation_arr as $key=>$value)
														<option value="{{ $value->name }}" @if($value->name== $employee->user_details->degination) selected="selected" @endif>{{ $value->name }}</option>
														@endforeach


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
																	{{-- <option value="{{$val['category name']}}">{{$val['category name']}}</option> --}}
																	<option value="{{$val}}">{{$val}}</option>
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
															<option value="A+" @if(isset($employee->user_details->blood_group) &&  ('A+' == $employee->user_details->blood_group))? selected="selected" @endif>A+</option>
															<option value="A-" @if(isset($employee->user_details->blood_group) &&  ('A-' == $employee->user_details->blood_group))? selected="selected" @endif>A-</option>
															<option value="B+" @if(isset($employee->user_details->blood_group) &&  ('B+' == $employee->user_details->blood_group))? selected="selected" @endif>B+</option>
															<option value="B-" @if(isset($employee->user_details->blood_group) &&  ('B-' == $employee->user_details->blood_group))? selected="selected" @endif>B-</option>
															<option value="O+" @if(isset($employee->user_details->blood_group) &&  ('O+' == $employee->user_details->blood_group))? selected="selected" @endif>O+</option>
															<option value="O-" @if(isset($employee->user_details->blood_group) &&  ('O-' == $employee->user_details->blood_group))? selected="selected" @endif>O-</option>
															<option value="AB+" @if(isset($employee->user_details->blood_group) &&  ('AB+' == $employee->user_details->blood_group))? selected="selected" @endif>AB+</option>
															<option value="AB-" @if(isset($employee->user_details->blood_group) &&  ('AB-' == $employee->user_details->blood_group))? selected="selected" @endif>AB-</option>
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
														@if(count($branches) > 0)
														<select class="form-control select-multiple5" name="branch_id[]" multiple>
															<option value=""> - Select Branch - </option>
															@foreach($branches as $value)
															<option value="{{ $value->id }}" <?php if( !empty($branch_ids) && in_array($value->id,$branch_ids)){ echo "selected";} ?> >{{ $value->name }}</option>
															<!--option value="{{ $value->id }}" @if($value->id == $branch_id) selected="selected" @endif>{{ $value->name }}</option-->
															@endforeach
														</select>
														@endif
														@if($errors->has('branch_id'))
														<span class="text-danger">{{ $errors->first('branch_id') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="company-column">Joining Date</label>
														@if($employee->user_details)
														<?php 
														$readonly = "readonly";
														$min_date = "";
														$joining_date = date("Y-m-d", strtotime($employee->user_details->joining_date)); 
														if(date('Y') == date('Y',strtotime($joining_date)) ){
															$readonly ="";
															$min_date = date("Y-01-01");
														}
														?>
														@endif
														<input type="date" class="form-control joining_date" name="joining_date" placeholder="Joining Date" value="{{ old('joining_date', isset($joining_date) ? $joining_date : '') }}" onblur="joinTime()" min="{{$min_date}}" {{$readonly}}>
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
														<input type="date" class="form-control probation_from" name="probation_from" placeholder="Joining Date" value="{{ $fromDate }}" min="{{$min_date}}" onblur="callprobation(this.value)" {{$readonly}}>
														@if($errors->has('probation_from'))
															<span class="text-danger">{{ $errors->first('probation_from') }} </span>
														@endif
													</div>
												</div>
												
												<?php if(!empty($employee->reason_date)){ ?>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="company-column">Date Of Leave</label>
														<?php $reason_date = date("Y-m-d", strtotime($employee->reason_date)); ?>
														<input type="date" class="form-control reason_date" name="reason_date" placeholder="Date Of Leave" value="{{ old('reason_date', isset($reason_date) ? $reason_date : '') }}">
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="company-column">Reason</label>														
														<textarea class="form-control reason" name="reason">{{ $employee->reason }}</textarea>
													</div>
												</div>
												<?php } ?>
												
												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="company-column">PL</label>
														<input type="text" class="form-control pl" name="pl" placeholder="PL" value="{{ old('pl', $employee->user_details->pl) }}" readonly >
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="company-column">CL</label>
														<input type="text" class="form-control cl" name="cl" placeholder="CL" value="{{ old('cl', $employee->user_details->cl) }}" readonly >
													</div>
												</div>
												<div class="col-md-6 col-12" style="display:none;">
													<div class="form-group">
														<label for="company-column">SL</label>
														<input type="text" class="form-control sl" name="sl" placeholder="SL" value="{{ old('sl', $employee->user_details->sl) }}" readonly >
													</div>
												</div>
												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="company-column">Total Time ( in minutes )</label>
														<input type="number" class="form-control total_time" name="total_time" placeholder="Total Time ( in minutes )" value="{{ old('total_time', $employee->total_time) }}">
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
														<input type="text" class="form-control" name="asset_requirement" placeholder="Asset Requirements" value="{{ old('asset_requirement', $employee->asset_requirement) }}" >
													</div>
												</div>
												
												<div class="row">
													<div class="col-md-6 col-12">
														<div class="form-group">
															<label for="company-column">Online Discount (%)</label>
															<input type="text" class="form-control" name="online_discount" placeholder="Online Discount (%)" value="{{ old('online_discount', $employee->online_discount) }}" >
														</div>
													</div>
													
													<div class="col-md-6 col-12">
														<div class="form-group">
															<label for="company-column">Offline Discount (%)</label>
															<input type="text" class="form-control" name="offline_discount" placeholder="Offline Discount (%)" value="{{ old('offline_discount', $employee->offline_discount) }}" >
														</div>
													</div>
													
												</div>
												
												<!--div class="col-md-6 col-12">
													<div class="form-group">
														<label for="company-column">Comp. Off Will Be Paid ?</label>
														<div class="form-group d-flex align-items-center mt-1">														
														<label>
															<input type="radio" name="extraPay" value="1" {{ ($employee->is_extra_working_salary == '1') ? "checked" : ""}}>
															Yes
														</label>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="extraPay" value="0" {{ ($employee->is_extra_working_salary == '0') ? "checked" : ""}}>
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
														@if(!empty($employee->user_details->resume))
														<a href="{{ asset('laravel/public/resume/'. $employee->user_details->resume) }}" target="_blank">View Resume</a>
														@endif
														@if($errors->has('resume'))
														<span class="text-danger">{{ $errors->first('resume') }} </span>
														@endif
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
													<input type="number" class="form-control" placeholder="Account Number" name="account_number" value="{{ old('account_number', isset($employee->user_details->account_number) ? $employee->user_details->account_number : '') }}">
													@if($errors->has('account_number'))
													<span class="text-danger">{{ $errors->first('account_number') }} </span>
													@endif
												</div>
											</div>
											<div class="col-md-6 col-12">
												<div class="form-group">
													<label for="first-name-column">Bank Name</label>
													<input type="text" class="form-control" placeholder="Bank Name" name="bank_name" value="{{ old('bank_name', isset($employee->user_details->bank_name) ?  $employee->user_details->bank_name : '0') }}" onkeypress="return blockSpecialChar(event)">
													@if($errors->has('bank_name'))
													<span class="text-danger">{{ $errors->first('bank_name') }} </span>
													@endif
												</div>
											</div>
											<div class="col-md-6 col-12">
												<div class="form-group">
													<label for="first-name-column">IFSC Code</label>
													<input type="text" class="form-control" placeholder="IFSC Code" name="ifsc_code" value="{{ old('ifsc_code', isset($employee->user_details->ifsc_code) ?  $employee->user_details->ifsc_code : '0') }}">
													@if($errors->has('ifsc_code'))
													<span class="text-danger">{{ $errors->first('ifsc_code') }} </span>
													@endif
												</div>
											</div>
											<div class="col-md-6 col-12">
												<div class="form-group">
													<label for="first-name-column">Branch</label>
													<input type="text" class="form-control" placeholder="Branch" name="bank_branch" value="{{ old('bank_branch', isset($employee->user_details->bank_branch) ?  $employee->user_details->bank_branch : '0') }}">
													@if($errors->has('bank_branch'))
													<span class="text-danger">{{ $errors->first('bank_branch') }} </span>
													@endif
												</div>
											</div>
											<div class="col-md-6 col-12">
												<div class="form-group">
													<label for="first-name-column">Gross Salary</label>
													<input type="number" class="form-control" placeholder="Gross Salary" name="net_salary" value="{{ old('net_salary', isset($employee->user_details->net_salary) ? $employee->user_details->net_salary : '0') }}">
													@if($errors->has('net_salary'))
													<span class="text-danger">{{ $errors->first('net_salary') }} </span>
													@endif
												</div>
											</div>
											<div class="col-md-6 col-12">
												<div class="form-group">
													<label for="first-name-column">TDS</label>
													<input type="number" class="form-control" placeholder="TDS" name="tds" value="{{ old('tds', isset($employee->user_details->tds) ? $employee->user_details->tds : '0') }}">
													@if($errors->has('tds'))
													<span class="text-danger">{{ $errors->first('tds') }} </span>
													@endif
												</div>
											</div>
											
										</div>
										
										<hr>
										<div class="row">
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">PF Amount</label>
													<input type="text" class="form-control" placeholder="PF Amount" name="pf_amount" value="{{ old('pf_amount', isset($employee->user_details->pf_amount) ? $employee->user_details->pf_amount : '0') }}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
													@if($errors->has('pf_amount'))
													<span class="text-danger">{{ $errors->first('pf_amount') }} </span>
													@endif
												</div>
											</div>
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">PF Date</label>
													<input type="date" class="form-control" name="pf_date" value="{{ old('pf_date', isset($employee->user_details->pf_date) ? $employee->user_details->pf_date : '') }}">
													@if($errors->has('pf_date'))
													<span class="text-danger">{{ $errors->first('pf_date') }} </span>
													@endif
												</div>
											</div>
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">Is PF Deduct</label>
													<select class="form-control" name="is_pf">
														<option value=""> - Select - </option>
														<option value="Yes" @if(isset($employee->user_details->is_pf) &&  ('Yes' == $employee->user_details->is_pf))? selected="selected" @endif>Yes</option>
														<option value="No" @if(isset($employee->user_details->is_pf) &&  ('No' == $employee->user_details->is_pf))? selected="selected" @endif>No</option>
													</select>
													@if($errors->has('is_pf'))
													<span class="text-danger">{{ $errors->first('is_pf') }} </span>
													@endif
												</div>
											</div>
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">ESI Amount</label>
													<input type="text" class="form-control" placeholder="ESI Amount" name="esi_amount" value="{{ old('esi_amount', isset($employee->user_details->esi_amount) ? $employee->user_details->esi_amount : '0') }}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
													@if($errors->has('esi_amount'))
													<span class="text-danger">{{ $errors->first('esi_amount') }} </span>
													@endif
												</div>
											</div>
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">ESI Date</label>
													<input type="date" class="form-control" name="esi_date" value="{{ old('esi_date', isset($employee->user_details->esi_date) ? $employee->user_details->esi_date : '0') }}">
													@if($errors->has('esi_date'))
													<span class="text-danger">{{ $errors->first('esi_date') }} </span>
													@endif
												</div>
											</div>
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">Is ESI Deduct</label>
													<select class="form-control" name="is_esi">
														<option value=""> - Select - </option>
														<option value="Yes" @if(isset($employee->user_details->is_esi) &&  ('Yes' == $employee->user_details->is_esi))? selected="selected" @endif>Yes</option>
														<option value="No" @if(isset($employee->user_details->is_esi) &&  ('No' == $employee->user_details->is_esi))? selected="selected" @endif>No</option>
													</select>
													@if($errors->has('is_esi'))
													<span class="text-danger">{{ $errors->first('is_esi') }} </span>
													@endif
												</div>
											</div>
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">ESIC No</label>
													<input type="text" class="form-control" placeholder="Esic No" name="esic_no" value="{{ old('esic_no', isset($employee->user_details->esic_no) ? $employee->user_details->esic_no : '') }}">
													@if($errors->has('esic_no'))
													<span class="text-danger">{{ $errors->first('esic_no') }} </span>
													@endif
												</div>
											</div>
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">Addhar Card No</label>
													<input type="text" class="form-control" placeholder="Addhar Card No" name="aadhar_card_no" value="{{ old('aadhar_card_no', isset($employee->user_details->aadhar_card_no) ? $employee->user_details->aadhar_card_no : '') }}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
													@if($errors->has('aadhar_card_no'))
													<span class="text-danger">{{ $errors->first('aadhar_card_no') }} </span>
													@endif
												</div>
											</div>
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">Name As Per Aadhar</label>
													<input type="text" class="form-control" placeholder="Name As Per Aadhar" name="aadhar_name" value="{{ old('aadhar_name', isset($employee->user_details->aadhar_name) ? $employee->user_details->aadhar_name : '') }}">
													@if($errors->has('aadhar_name'))
													<span class="text-danger">{{ $errors->first('aadhar_name') }} </span>
													@endif
												</div>
											</div>
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">Pan No</label>
													<input type="text" class="form-control" placeholder="Pan No" name="pan_no" value="{{ old('pan_no', isset($employee->user_details->pan_no) ? $employee->user_details->pan_no : '') }}">
													@if($errors->has('pan_no'))
													<span class="text-danger">{{ $errors->first('pan_no') }} </span>
													@endif
												</div>
											</div>
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">Name As Per Pan</label>
													<input type="text" class="form-control" placeholder="Name As Per Pan" name="pan_name" value="{{ old('pan_name', isset($employee->user_details->pan_name) ? $employee->user_details->pan_name : '') }}">
													@if($errors->has('pan_name'))
													<span class="text-danger">{{ $errors->first('pan_name') }} </span>
													@endif
												</div>
											</div>
											
											
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">Previous Experience</label>
													<input type="text" class="form-control" placeholder="Previous Experience" name="previous_experience" value="{{ old('previous_experience', isset($employee->user_details->previous_experience) ? $employee->user_details->previous_experience : '') }}">
													@if($errors->has('previous_experience'))
													<span class="text-danger">{{ $errors->first('previous_experience') }} </span>
													@endif
												</div>
											</div>
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">UAN No</label>
													<input type="text" class="form-control" placeholder="UAN No" name="uan_no" value="{{ old('uan_no', isset($employee->user_details->uan_no) ? $employee->user_details->uan_no : '') }}">
													@if($errors->has('uan_no'))
													<span class="text-danger">{{ $errors->first('uan_no') }} </span>
													@endif
												</div>
											</div>
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">Name As Per Bank</label>
													<input type="text" class="form-control" placeholder="Name As Per Bamnk" name="bank_emp_name" value="{{ old('bank_emp_name', isset($employee->user_details->bank_emp_name) ? $employee->user_details->bank_emp_name : '') }}">
													@if($errors->has('bank_emp_name'))
													<span class="text-danger">{{ $errors->first('bank_emp_name') }} </span>
													@endif
												</div>
											</div>
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">In Timing Shift</label>
													<input type="time" class="form-control" name="timing_shift_in" value="{{ old('timing_shift_in', isset($employee->user_details->timing_shift_in) ? $employee->user_details->timing_shift_in : '') }}">
													@if($errors->has('timing_shift_in'))
													<span class="text-danger">{{ $errors->first('timing_shift_in') }} </span>
													@endif
												</div>
											</div>
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">Out Timing Shift</label>
													<input type="time" class="form-control" name="timing_shift_out" value="{{ old('timing_shift_out', isset($employee->user_details->timing_shift_out) ? $employee->user_details->timing_shift_out : '') }}">
													@if($errors->has('timing_shift_out'))
													<span class="text-danger">{{ $errors->first('timing_shift_out') }} </span>
													@endif
												</div>
											</div>
											
											<div class="col-md-4 col-12">
												<div class="form-group">
													<label for="first-name-column">EMP File No</label>
													<input type="text" class="form-control" placeholder="EMP File No" name="emp_file_no" value="{{ old('emp_file_no', isset($employee->user_details->emp_file_no) ? $employee->user_details->emp_file_no : '') }}">
													@if($errors->has('emp_file_no'))
													<span class="text-danger">{{ $errors->first('emp_file_no') }} </span>
													@endif
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
@endsection

@section('scripts')
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
			url : '{{ route('admin.employee.get_leave_month') }}',
			data : {'_token' : '{{ csrf_token() }}', 'j_date': j_date},
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
				url : '{{ route('admin.get-sub-department') }}',
				data : {'_token' : '{{ csrf_token() }}', 'department_type_id': department_type_id},
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
			url : '{{ route('admin.employee.get_leave_month') }}',
			data : {'_token' : '{{ csrf_token() }}', 'probation_date': probation_date},
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
@endsection
