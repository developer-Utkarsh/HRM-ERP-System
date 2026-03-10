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
						<h2 class="content-header-title float-left mb-0">Add Job Role</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Add Job Role</a>
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
									<form class="form" action="{{ route('admin.store-job-role', !empty($job_result->id) ? $job_result->id : '') }}" method="post" enctype="multipart/form-data">
										@csrf
										<div class="form-body"> 
											<div class="row">
												<div class="col-md-12 col-12">
													<div class="form-group">
														<label for="first-name-column">Employees</label>
														<select class="form-control select-multiple user_id" name="user_id">
															<option value="">Select Employee</option>
															@if(count($employees_list) > 0)
																@foreach($employees_list as $value)
																	<option value="{{$value->id}}"   @if($value->id == old('category_name') || (!empty($job_result->user_id) && $job_result->user_id == $value->id)) selected="selected" @endif>{{$value->name}}({{!empty($value->register_id) ? $value->register_id : '--'}})</option>
																@endforeach
															@endif
														</select>
														@if($errors->has('user_id'))
														<span class="text-danger">{{ $errors->first('user_id') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-12 col-12">
													<div class="form-group">
														<label for="first-name-column">Description</label>
														<textarea class="form-control" id="editor1" name="description" placeholder="Description">{{ old('description', !empty($job_result->description) ? $job_result->description : '') }}</textarea>
													@if($errors->has('description'))
													<span class="text-danger">{{ $errors->first('description') }} </span>
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
<script type="text/javascript" src="https://cdn.ckeditor.com/4.5.11/standard/ckeditor.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple').select2({
			placeholder: "Select",
			allowClear: true
		});
	});
	
	CKEDITOR.replace( 'editor1', {height: 240} );
</script>
@endsection
