@extends('layouts.studiomanager')
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
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-6">
						<h2 class="content-header-title float-left mb-0">Add Enquiry</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('studiomanager.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Add Enquiry</a>
								</li>
							</ol>
						</div>
					</div>
					<div class="col-6">
						<a href="{{ route('studiomanager.enquiry.index') }}" class="btn btn-primary" style="float: right;">Back</a>
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
								
									<form method="post" action="{{ route('studiomanager.enquiry.store') }}">
										@csrf
										<div class="row"> 
											
												<div class="col-md-3">
													<div class="radio">
														<label><input type="radio" style="margin-bottom: 20px;" name="course_type" value="Online" @if(!empty(old('course_type')) && old('course_type') == 'Online'){{ 'checked' }}@else{{'checked'}}@endif>Online</label>
														@if($errors->has('course_type'))
														<span class="text-danger">{{ $errors->first('course_type') }} </span>
														@endif	
													</div>
												</div>

												<div class="col-md-3">
													<div class="radio">
														<label><input type="radio" style="margin-bottom: 20px;" name="course_type" value="Offline" @if(!empty(old('course_type')) && old('course_type') == 'Offline'){{ 'checked' }}@endif>Offline</label>
														@if($errors->has('course_type'))
														<span class="text-danger">{{ $errors->first('course_type') }} </span>
														@endif	
													</div>
												</div>

												<div class="col-md-6">
													<input type="text" class="form-control" placeholder="Mobile Number" name="mobile_no" style="margin-bottom: 20px;" value="{{ old('mobile_no') }}">
													@if($errors->has('mobile_no'))
													<span class="text-danger">{{ $errors->first('mobile_no') }} </span>
													@endif	
												</div>

												<div class="col-md-6">
													<input type="text" class="form-control" placeholder="Course Name" name="course_name" style="margin-bottom: 20px;" value="{{ old('course_name') }}">
													@if($errors->has('course_name'))
													<span class="text-danger">{{ $errors->first('course_name') }} </span>
													@endif	
												</div>

												<div class="col-md-6">
													<select class="form-control select-multiple" name="category_id" style="margin-bottom: 20px;">
														<option value="">Select Category</option>
														@if(count($category_list) > 0)
														@foreach($category_list as $value)
														<option value="{{$value->id}}" @if(!empty(old('category_id')) && old('category_id') == $value->id){{ 'selected' }}@endif>{{$value->name}}</option>
														@endforeach
														@endif
													</select>
													@if($errors->has('category_id'))
													<span class="text-danger">{{ $errors->first('category_id') }} </span>
													@endif	
												</div>
												
												<div class="col-md-6">
													<select class="form-control select-multiple1" name="priority" style="margin-bottom: 20px;">
														<option value="">Select Priority</option>
														<option value="low" @if(!empty(old('priority')) && old('priority') == 'low'){{ 'selected' }}@endif>Low</option>
														<option value="medium" @if(!empty(old('priority')) && old('priority') == 'medium'){{ 'selected' }}@endif>Medium</option>
														<option value="high" @if(!empty(old('priority')) && old('priority') == 'high'){{ 'selected' }}@endif>High</option>
													</select>
													@if($errors->has('priority'))
													<span class="text-danger">{{ $errors->first('priority') }} </span>
													@endif	
												</div>

												<div class="col-md-12">
													<textarea class="form-control" placeholder="Description" name="description" style="margin-bottom: 20px;">{{ old('description') }}</textarea>
													@if($errors->has('description'))
													<span class="text-danger">{{ $errors->first('description') }} </span>
													@endif	
												</div>

												<div class="col-md-3">
													<button type="submit" class="btn btn-primary">Submit</button>
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

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple').select2({
			placeholder: "Select Category",
			allowClear: true
		});
	});
</script>
@endsection
