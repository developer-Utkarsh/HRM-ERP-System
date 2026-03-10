@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-9">
						<h2 class="content-header-title float-left mb-0">Edit Meeting Place</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Edit Meeting Place</a>
								</li>
							</ol>
						</div>
					</div>
					<div class="col-md-3">
						<a href="{{ route('admin.meeting-places') }}" class="btn btn-primary float-right ">Back</a>
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
									<form class="form" action="{{ route('admin.meeting-places.update', $meeting_place->id) }}" method="post" enctype="multipart/form-data">
										<!-- @method('PATCH') -->
										@csrf
										<div class="form-body">
											<div class="row">
												<div class="col-12 col-md-6">
														<label for="users-list-role">Branch</label>
														<fieldset class="form-group">												
															<select class="form-control select-multiple branch" name="branch">
																<option value="">Select Any</option>
																	<?php
																		$branch = DB::table('branches')->select('id','branch_location')->where('is_deleted','0')->groupby('branch_location')->get();
																		if(count($branch) > 0){
																			foreach($branch as $value){
																	?>
																	<option value="{{ $value->id }}" @if($value->id == $meeting_place->branch) selected="selected" @endif>{{ ucwords($value->branch_location) }}</option>
																	<?php } } ?>
															</select>
															@if($errors->has('branch'))
															<span class="text-danger">{{ $errors->first('branch') }} </span>
															@endif												
														</fieldset>
													</div>

												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Name</label>
														<input class="form-control" placeholder="Name" name="name" value="{{ old('name', $meeting_place->name) }}"/>
														@if($errors->has('name'))
														<span class="text-danger">{{ $errors->first('name') }} </span>
														@endif
													</div>
												</div>

												<div class="col-12 col-md-6">
													<label for="users-list-status">Status</label>
													<fieldset class="form-group">												
														<select class="form-control select-multiple status" name="status">
															@php $status = ['Active', 'Inactive']; @endphp
															<option value="">Select Any</option>
															@if(count($status) > 0)
																@foreach($status as $key => $value)
																<option value="{{ $value }}" @if((!empty(old('status')) && $value == old('status')) || (!empty($meeting_place->status) && $value == $meeting_place->status)) selected="selected" @endif>{{ $value }}</option>
																@endforeach]
															@endif
														</select>
														@if($errors->has('status'))
														<span class="text-danger">{{ $errors->first('status') }} </span>
														@endif												
													</fieldset>
												</div>

												<div class="col-12">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Update</button>
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
