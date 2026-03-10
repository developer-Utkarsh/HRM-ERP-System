@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Add Notification</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Add Notification</a>
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
									<form class="form" action="{{ route('admin.notification.store') }}" method="post" enctype="multipart/form-data">
										@csrf
										<div class="form-body">
											<div class="row">

												<div class="col-12 col-sm-6 col-lg-4">
													<label for="users-list-status">Branch</label>
													<?php $branches = \App\Branch::where('status', '1')->orderBy('id','desc')->get(); ?>
													<fieldset class="form-group">												
														<select class="form-control select-multiple1 branch_id" name="branch_id" id="">
															<option value="">Select Any</option>
															@if(count($branches) > 0)
															@foreach($branches as $key => $value)
															<option value="{{ $value->id }}" @if($value->id == app('request')->input('branch_id')) selected="selected" @endif>{{ $value->name }}</option>
															@endforeach
															@endif
														</select>
														<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
													</fieldset>
												</div>

												<div class="col-12 col-sm-6 col-lg-4">
													<label for="users-list-status">Employee</label>
													<?php $employee = \App\User::orderBy('id','desc')->get(); ?>
													<fieldset class="form-group">												
														<select class="form-control select-multiple2 employee_id" name="employee_id">
															<option value="">Select Any</option>
															@if(count($employee) > 0)
															@foreach($employee as $key => $value)
															<option value="{{ $value->id }}" @if($value->id == app('request')->input('employee_id')) selected="selected" @endif>{{ $value->name }}</option>
															@endforeach
															@endif
														</select>												
													</fieldset>
												</div>

												@php
												$designation_arr = array(
												'Designation' => 'Designation',
												'ACADEMIC COORDINATOR' => 'ACADEMIC COORDINATOR',
												'ACADEMIC DIRECTOR' => 'ACADEMIC DIRECTOR',
												'ACADEMIC HEAD' => 'ACADEMIC HEAD',
												'IT (DEO) HOD' => 'IT (DEO) HOD',
												'IT (DEO) INCHARGE' => 'IT (DEO) INCHARGE',
												'IT (DEO)' => 'IT (DEO)',
												'ACCOUNTANT' => 'ACCOUNTANT',
												'ADVISOR TO CEO' => 'ADVISOR TO CEO',
												'APP COURSE TESTER' => 'APP COURSE TESTER',
												'APP COURSE TESTING HEAD' => 'APP COURSE TESTING HEAD',
												'ASSISTANT' => 'ASSISTANT',
												'Cordinator HR' => 'Cordinator HR',
												'ASSISTANT HR' => 'ASSISTANT HR',
												'ASSISTANT QUALITY ANALYST' => 'ASSISTANT QUALITY ANALYST',
												'BRANCH HEAD' => 'BRANCH HEAD',
												'BUSINESS DEVELOPMENT MANAGER' => 'BUSINESS DEVELOPMENT MANAGER',
												'CALL CENTER' => 'CALL CENTER',
												'CALL CENTER (UB)' => 'CALL CENTER (UB)',
												'CALL CENTER EXECUTIVE' => 'CALL CENTER EXECUTIVE',
												'CALL CENTER ZOHO' => 'CALL CENTER ZOHO',
												'CALL CENTRE (UT)' => 'CALL CENTRE (UT)',
												'CALL CENTRE/ ZOHO' => 'CALL CENTRE/ ZOHO',
												'CAMERA MAN' => 'CAMERA MAN',
												'CENTRAL HEAD' => 'CENTRAL HEAD',
												'CEO & FOUNDER' => 'CEO & FOUNDER',
												'CLASS ASSISTANT' => 'CLASS ASSISTANT',
												'CO FOUNDER' => 'CO FOUNDER',
												'COMP. FACULTY' => 'COMP. FACULTY',
												'COMPUTER OPERATOR' => 'COMPUTER OPERATOR',
												'CONTENT + FACULTY' => 'CONTENT + FACULTY',
												'CONTENT HEAD' => 'CONTENT HEAD',
												'CONTENT WRITER' => 'CONTENT WRITER',
												'CONTENT WRITER (UB)' => 'CONTENT WRITER (UB)',
												'COOK' => 'COOK',
												'COUNSELOR' => 'COUNSELOR',
												'DATA ENTRY OPERATOR' => 'DATA ENTRY OPERATOR',
												'DEO' => 'DEO',
												'DIGITAL MARKETING' => 'DIGITAL MARKETING',
												'DO-IT' => 'DO-IT',
												'DOP & EDITING' => 'DOP & EDITING',
												'DRIVER' => 'DRIVER',
												'ELECTRICIAN' => 'ELECTRICIAN',
												'FACULTIES' => 'FACULTIES',
												'FB & INSTA MANAGER' => 'FB & INSTA MANAGER',
												'GOOGLE PLAY STORE' => 'GOOGLE PLAY STORE',
												'GRAPHIC DESIGNER' => 'GRAPHIC DESIGNER',
												'HOUSE KEAPING' => 'HOUSE KEAPING',
												'HR' => 'HR',
												'IT & SUPPORTS' => 'IT & SUPPORTS',
												'IT HEAD' => 'IT HEAD',
												'IT OPERATOR' => 'IT OPERATOR',
												'IT SECTION (RE)' => 'IT SECTION (RE)',
												'JUNIOR ANDROID DEVELOPER' => 'JUNIOR ANDROID DEVELOPER',
												'LAB ASSISTANT' => 'LAB ASSISTANT',
												'LIBRARIAN' => 'LIBRARIAN',
												'LIVE INCHARGE' => 'LIVE INCHARGE',
												'MARKETING' => 'MARKETING',
												'NETWORKING & E-SURVEILLANCE' => 'NETWORKING & E-SURVEILLANCE',
												'NETWORKING ASSISTANT' => 'NETWORKING ASSISTANT',
												'NETWORKING MANAGER' => 'NETWORKING MANAGER',
												'OFFICE BOY' => 'OFFICE BOY',
												'ONLINE WEB CHAT' => 'ONLINE WEB CHAT',
												'ONLINE WEB CHAT HEAD' => 'ONLINE WEB CHAT HEAD',
												'PRODUCT MANAGER' => 'PRODUCT MANAGER',
												'PRODUCTION HEAD' => 'PRODUCTION HEAD',
												'PROJECT MANAGER' => 'PROJECT MANAGER',
												'PROOF READER' => 'PROOF READER', 
												'PS TO CEO' => 'PS TO CEO',
												'PSYCHOLOGIST' => 'PSYCHOLOGIST',
												'QUALITY ANALYST' => 'QUALITY ANALYST',
												'RECEPTION' => 'RECEPTION',
												'SCHOOL CONTENT WRITER' => 'SCHOOL CONTENT WRITER',
												'SCHOOL FACULTY' => 'SCHOOL FACULTY',
												'SCHOOL TYPIST' => 'SCHOOL TYPIST',
												'SCHOOL WEB CHAT HEAD' => 'SCHOOL WEB CHAT HEAD',
												'SENIOR ANDROID DEVELOPER' => 'SENIOR ANDROID DEVELOPER',
												'SOCIAL MEDIA' => 'SOCIAL MEDIA',
												'STORE ASSISTANT' => 'STORE ASSISTANT',
												'STORE MANAGER' => 'STORE MANAGER',
												'STUDIO ASSISTANT' => 'STUDIO ASSISTANT',
												'STUDIO ASSISTANT & OFFICE BOY' => 'STUDIO ASSISTANT & OFFICE BOY',
												'STUDIO ASSISTANT HEAD' => 'STUDIO ASSISTANT HEAD',
												'STUDIO ASSISTANT MANAGER' => 'STUDIO ASSISTANT MANAGER',
												'SWEEPER' => 'SWEEPER',
												'TIME TABLE INCHARGE' => 'TIME TABLE INCHARGE',
												'TIME TABLE MANAGER' => 'TIME TABLE MANAGER',
												'TYPIST' => 'TYPIST',
												'TYPIST (UT)' => 'TYPIST (UT)',
												'TYPIST SCHOOL' => 'TYPIST SCHOOL',
												'TYPIST(NEET/JEE)' => 'TYPIST(NEET/JEE)',
												'UI/UX APP DESIGNING' => 'UI/UX APP DESIGNING',
												'VICE PRESIDENT' => 'VICE PRESIDENT',
												'VIDEO EDITOR' => 'VIDEO EDITOR',
												'WATCHMAN' => 'WATCHMAN',
												'WEB CHAT EXECUTIVE' => 'WEB CHAT EXECUTIVE',
												'WEB CHAT ONLINE' => 'WEB CHAT ONLINE',
												'WEB CHAT REPALIER' => 'WEB CHAT REPALIER',
												'WEB DEVELOPER' => 'WEB DEVELOPER',
												'WEB REPLY' => 'WEB REPLY',
												'YOU TUBE OPERATOR' => 'YOU TUBE OPERATOR',
												'YOUTUBE EXECUTIVE' => 'YOUTUBE EXECUTIVE',
												'YOUTUBE MANAGER' => 'YOUTUBE MANAGER',
												'ZOHO SUPPORT HEAD' => 'ZOHO SUPPORT HEAD',
												'ZOHO TEAM' => 'ZOHO TEAM',
												'New Course Incharge' => 'New Course Incharge',
												'Compititive Exam Faculty Head' => 'Compititive Exam Faculty Head',
												'New Course Incharge' => 'New Course Incharge',
												'Typist Head' => 'Typist Head',
												);
												@endphp

												<div class="col-12 col-sm-6 col-lg-4">
													<label for="first-name-column">Designation</label>
													<select class="form-control select-multiple3" name="degination">
														<option value=""> - Select Designation - </option>
														@foreach($designation_arr as $key=>$value)
														<option value="{{ $value }}" @if($value == old('degination')) selected="selected" @endif>{{ $key }}</option>
														@endforeach
													</select>
												</div>

												<div class="col-12 col-sm-6 col-lg-4">
													<label for="users-list-role">Role</label>
													<?php $role = \App\Role::where('status', '1')->orderBy('id','desc')->get(); ?>
													<fieldset class="form-group">												
														<select class="form-control select-multiple4 role_id" name="role_id" id="">
															<option value="">Select Any</option>
															@if(count($role) > 0)
															@foreach($role as $key => $value)
															<option value="{{ $value->id }}" @if($value->id == app('request')->input('role_id')) selected="selected" @endif>{{ $value->name }}</option>
															@endforeach
															@endif
														</select>
														<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
													</fieldset>
												</div>
												<div class="col-12 col-sm-6 col-lg-4">
													<label for="users-list-role">Department</label>
													<?php $department = \App\Department::where('status', 'Active')->get(); ?>
													<fieldset class="form-group">												
														<select class="form-control select-multiple4 department" name="department" id="">
															<option value="">Select Any</option>
															@if(count($department) > 0)
															@foreach($department as $key => $value)
															<option value="{{ $value->id }}" @if($value->id == app('request')->input('role_id')) selected="selected" @endif>{{ $value->name }}</option>
															@endforeach
															@endif
														</select>
														<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
													</fieldset>
												</div>

												<div class="col-md-12 col-12">
													<div class="form-group">
														<label for="first-name-column">Title</label>
														<input type="text" class="form-control" placeholder="Title" name="title" value="{{ old('title') }}">
														@if($errors->has('title'))
														<span class="text-danger">{{ $errors->first('title') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-12 col-12">
													<div class="form-group">
														<label for="first-name-column">Description</label>
														<textarea class="form-control" name="description" id="editor">{{ old('description') }}</textarea>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Image</label>
														<input type="file" class="form-control" name="image">
														{{-- @if($errors->has('image'))
														<span class="text-danger">{{ $errors->first('image') }} </span>
														@endif --}}
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
@endsection

<style type="text/css">
	.ck-content {
		height : 150px;
	}
</style>

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

<script src="https://cdn.ckeditor.com/ckeditor5/11.0.1/classic/ckeditor.js"></script>
<script type="text/javascript">
	ClassicEditor
		.create( document.querySelector( '#editor' ) )
		.catch( error => {
			console.error( error );
		} );
</script>

<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple1').select2({
			placeholder: "Select Any",
			allowClear: true
		});
		$('.select-multiple2').select2({
			placeholder: "Select Any",
			allowClear: true
		});
		$('.select-multiple3').select2({
			width: '100%',
			placeholder: "Select Batch",
			allowClear: true
		});
		$('.select-multiple4').select2({
			width: '100%',
			placeholder: "Select Any",
			allowClear: true
		});
	});
</script>
@endsection

