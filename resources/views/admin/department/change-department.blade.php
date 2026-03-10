@extends('layouts.admin')
@section('content')

@if (Auth::viaRemember())
    {{666}}
@else
    {{777}}
@endif
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Change Department</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Change Department</a>
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
									<h5 class="mb-2">Department Change</h5>
									<form class="form" action="{{ route('admin.store-change-department') }}" method="post" enctype="multipart/form-data" onsubmit="return confirm('Are you sure to want to change deparment?');">
										@csrf
										<div class="form-body"> 
											<div class="row">
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">From Department</label>
														<select class="form-control select-multiple from_department" name="from_department">
															
															<option value="">Select From Department</option>
															@if(count($department_list) > 0)
																@foreach($department_list as $value)
																	<option value="{{$value->id}}" @if($value->id == old('from_department')) selected="selected" @endif>{{$value->name}}</option>
																@endforeach
															@endif
														</select>
														@if($errors->has('from_department'))
														<span class="text-danger">{{ $errors->first('from_department') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Change To Department</label>
														<select class="form-control select-multiple to_department" name="to_department">
															<option value="">Select To Department</option>
															@if(count($department_list) > 0)
																@foreach($department_list as $value)
																	<option value="{{$value->id}}" @if($value->id == old('to_department')) selected="selected" @endif>{{$value->name}}</option>
																@endforeach
															@endif
														</select>
														@if($errors->has('to_department'))
														<span class="text-danger">{{ $errors->first('to_department') }} </span>
														@endif
													</div>
												</div>	                                      
												<div class="col-md-4 col-12 mt-2">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Submit</button>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>


					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<h5 class="mb-2">Employee Department Change</h5>
									<form class="form" action="{{ route('admin.store-change-employee-department') }}" method="post" enctype="multipart/form-data" onsubmit="return confirm('Are you sure to want to change deparment?');">
										@csrf
										<div class="form-body"> 
											<div class="row">
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Employee</label>
														<select class="form-control select-multiple employee_name" name="employee_name[]" multiple>
															
															<option value="">Select From Department</option>
															@if(count($employees_list) > 0)
																@foreach($employees_list as $value)
																	<option value="{{$value->id}}" @if(!empty(old('employee_name')) && in_array($value->id, old('employee_name'))) selected="selected" @endif>{{$value->name}}({{!empty($value->register_id) ? $value->register_id : '--'}})</option>
																@endforeach
															@endif
														</select>
														@if($errors->has('employee_name'))
														<span class="text-danger">{{ $errors->first('employee_name') }} </span>
														@endif
													</div>
												</div> 
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Change To Department</label>
														<select class="form-control select-multiple employee_to_department" name="employee_to_department">
															<option value="">Select To Department</option>
															@if(count($department_list) > 0)
																@foreach($department_list as $value)
																	<option value="{{$value->id}}" @if($value->id == old('employee_to_department')) selected="selected" @endif>{{$value->name}}</option>
																@endforeach
															@endif
														</select>
														@if($errors->has('employee_to_department'))
														<span class="text-danger">{{ $errors->first('employee_to_department') }} </span>
														@endif
													</div>
												</div>	                                      
												<div class="col-md-4 col-12 mt-2">
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
	})
</script>
@endsection
