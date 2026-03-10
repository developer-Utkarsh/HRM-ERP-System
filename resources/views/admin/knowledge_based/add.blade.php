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
						<h2 class="content-header-title float-left mb-0">Add Knowledge Base</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Add Knowledge Base</a>
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
									<form class="form" action="{{ route('admin.knowledge_based.store') }}" method="post" enctype="multipart/form-data">
										@csrf
										<div class="form-body">
											<div class="row">
												<div class="col-12 col-md-6">
													<label for="users-list-status">Employee</label>
													<fieldset class="form-group">												
														<select class="form-control select-multiple emp_id" name="emp_id">
															<option value="">Select Any</option>
															@if(count($employee) > 0)
																@foreach($employee as $key => $value)
																<option value="{{ $value->id }}" @if($value->id == old('emp_id')) selected="selected" @endif>{{ $value->name }}</option>
																@endforeach]
															@endif
														</select>
														@if($errors->has('emp_id'))
														<span class="text-danger">{{ $errors->first('emp_id') }} </span>
														@endif												
													</fieldset>
												</div>

												<div class="col-12 col-md-6">
													<label for="users-list-status">Category</label>
													<fieldset class="form-group">												
														<select class="form-control select-multiple cat_id" name="cat_id">
															<option value="">Select Any</option>
															@if(count($kb_category) > 0)
																@foreach($kb_category as $key => $value)
																<option value="{{ $value->id }}" @if($value->id == old('cat_id')) selected="selected" @endif>{{ $value->name }}</option>
																@endforeach]
															@endif
														</select>
														@if($errors->has('cat_id'))
														<span class="text-danger">{{ $errors->first('cat_id') }} </span>
														@endif												
													</fieldset>
												</div>

												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Title</label>
														<input type="text" class="form-control" placeholder="Title" name="title" value="{{ old('title') }}">
														@if($errors->has('title'))
														<span class="text-danger">{{ $errors->first('title') }} </span>
														@endif
													</div>
												</div>	

												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Reference Link</label>
														<input type="text" class="form-control" placeholder="Reference Link" name="reference_link" value="{{ old('reference_link') }}">
														@if($errors->has('reference_link'))
														<span class="text-danger">{{ $errors->first('reference_link') }} </span>
														@endif
													</div>
												</div>

												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Description</label>
														<textarea class="form-control" placeholder="Description" name="description" >{{ old('description') }}</textarea>
														@if($errors->has('description'))
														<span class="text-danger">{{ $errors->first('description') }} </span>
														@endif
													</div>
												</div>

												<div class="col-12 col-md-6">
													<label for="users-list-status">Status</label>
													<fieldset class="form-group">												
														<select class="form-control select-multiple status" name="status">
															@php $status = ['Pending', 'Approved','Reject']; @endphp
															<option value="">Select Any</option>
															@if(count($status) > 0)
																@foreach($status as $key => $value)
																<option value="{{ $value }}" @if($value == old('status')) selected="selected" @endif>{{ $value }}</option>
																@endforeach]
															@endif
														</select>
														@if($errors->has('status'))
														<span class="text-danger">{{ $errors->first('status') }} </span>
														@endif												
													</fieldset>
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
			placeholder: "Select Any",
			allowClear: true
		});
	});
</script>
@endsection
