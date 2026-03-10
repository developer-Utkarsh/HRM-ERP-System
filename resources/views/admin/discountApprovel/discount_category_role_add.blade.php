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
						<h2 class="content-header-title float-left mb-0">Role and Category Wise Discount</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Add Discount</a>
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
									<h5 class="mb-2">Add Discount</h5>
									<form class="form" action="{{ route('admin.store-discount-category-role') }}" method="post" enctype="multipart/form-data" onsubmit="return confirm('Are you sure to want to add discount?');">
										@csrf
										<div class="form-body"> 
											<div class="row">
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Role</label>
														<select class="form-control select-multiple" name="role_id" required>
															
															<option value="">Select Role</option>
															@if(count($role_list) > 0)
																@foreach($role_list as $value)
																	<option value="{{$value->id}}" @if(!empty(old('role_id')) && in_array($value->id, old('role_id'))) selected="selected" @endif>{{$value->name}}</option>
																@endforeach
															@endif
														</select>
														@if($errors->has('role_id'))
														<span class="text-danger">{{ $errors->first('role_id') }} </span>
														@endif
													</div>
												</div>
												<?php
												$category_list = array("One Time Payment","Previous Online Student(Same Course)","Previous Online Student(Different Course)","Previous Offline Student(Same Course)","Previous Offline Student(Different Course)","Employee/Faculty Relative/Reference","Widow/Divorcee","Ex Army/Airforce/Navy","Army/Airforce/Navy Child","Orphan","Blind/Handicapped","Early Bird Discount","Economically Weak Student","Special Consideration Reference","Founder & Cofounder reference","Employee & Faculty Relative(Same Blood)","Employee & Faculty Relative/Reference(Non Blood)","Tablet/BPPS/Pathshala/Navodaya Yojana","Handicapped","Reference","Old Student Same Batch","Old Student Other Batch","Economically Weak","Widow","Online to Offline","Anuprati Yojana","Anupriti Yojna-2022-23","Anupriti Yojna-2023-24","Anupriti Yojna-2023-24 (Ph-2)","Navodaya Vidyalaya Samiti","UCSET-2024","Other","REET1000","Discount @ Utkarsh |RSS/AVM others","Free/Discount | Online Course","Utk Tablets Distri. Team");
												?>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Category</label>
														<select class="form-control select-multiple" name="category" id="select_category" required>
															
															<option value="">Select Category</option>
															@if(count($category_list) > 0)
																@foreach($category_list as $cat_value)
																	<option value="{{$cat_value}}" @if(!empty(old('category')) && in_array($cat_value, old('category'))) selected="selected" @endif>{{$cat_value}}</option>
																@endforeach
															@endif
														</select>
														@if($errors->has('category'))
														<span class="text-danger">{{ $errors->first('category') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-2 col-12">
													<div class="form-group">
														<label for="first-name-column">Online Discount</label>
														<input type="number" class="form-control" min="0" name="online" required>
														@if($errors->has('paternity_leave'))
														<span class="text-danger">{{ $errors->first('paternity_leave') }} </span>
														@endif
													</div>
												</div>	
												<div class="col-md-2 col-12">
													<div class="form-group">
														<label for="first-name-column">Offline Discount</label>
														<input type="number" class="form-control" min="0" name="offline" required>
													</div>
												</div>
												
												<div class="col-md-3 col-12 mt-2">
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
@endsection

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple').select2({
			placeholder: "Select",
			allowClear: true
		});
	});
	
	$('#dropdownYear').each(function() {

	  var year = (new Date()).getFullYear();
	  var current = year;
	  year -= 3;
	  for (var i = 3; i < 8; i++) {
		if ((year+i) == current)
		  $(this).append('<option selected value="' + (year + i) + '">' + (year + i) + '</option>');
		else
		  $(this).append('<option value="' + (year + i) + '">' + (year + i) + '</option>');
	  }

	})
	
 </script>
@endsection