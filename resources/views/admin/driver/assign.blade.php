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
						<h2 class="content-header-title float-left mb-0">Assign Faculties</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">Faculties
								</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
						<a href="{{ route('admin.drivers.index') }}" class="btn btn-primary mr-1">Back</a>
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
									<form class="form" action="{{ route('admin.drivers.update', $employee->id) }}" method="post" enctype="multipart/form-data">
										@csrf
										<div class="form-body">
											<div class="row">
												 
												
												<div class="col-md-10 col-12">
													<div class="form-group">
														<?php $users = \App\User::where('status', '1')->where('role_id','2')->where('register_id', '!=', NULL)->get(); ?>
														<label for="first-name-column">Faculties Name</label>
														@if(count($users) > 0)
														<select class="form-control select-multiple1" name="faculty_id[]" multiple>
															<option value=""> - Select Any - </option>
															@foreach($users as $value)
															<option value="{{ $value->id }}"<?php if( !empty($assign_faculties) && in_array($value->id,$assign_faculties)){ echo "selected";} ?>>{{ $value->name . ' ( ' .$value->register_id.' ) ' }}</option>
															@endforeach
														</select>
														@endif
														@if($errors->has('faculty_id'))
														<span class="text-danger">{{ $errors->first('faculty_id') }} </span>
														@endif
													</div>
												</div>
												
												 
												
											</div>
											
											 
											 
										</div>
										 
										 
										
										<div class="col-12">
											<button type="submit" class="btn btn-primary mr-1 mb-1 btn_submit">Update</button>
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
		
		$('.select-multiple1').select2({
			placeholder: "Select",
			allowClear: true
		});
		 
	});
	
</script>

@endsection
