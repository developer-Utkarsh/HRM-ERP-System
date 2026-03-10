@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Send Links</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">Send Links
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
									<form class="form" action="{{ route('admin.links.faculty_link') }}" method="post" enctype="multipart/form-data">
										@csrf
										<div class="form-body">
											<div class="row">
												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<h3>Faculty Links</h3>
														<label for="first-name-column">Faculty Name</label>
														@if(count($faculties) > 0)
														<select class="form-control select-multiple1" name="employee_id[]" multiple>
															<option value=""> - Select Any - </option>
															@foreach($faculties as $value)
															<option value="{{ $value->id }}">{{ $value->name . ' ( ' .$value->register_id.' ) ' }}</option>
															@endforeach
														</select>
														@endif
														@if($errors->has('employee_id'))
														<span class="text-danger">{{ $errors->first('employee_id') }} </span>
														@endif
													</div>
												</div>                                 
												<div class="col-md-6 col-12 mt-4">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Send Link</button>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
						
						
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="{{ route('admin.links.faculty_link') }}" method="post" enctype="multipart/form-data">
										@csrf
										<div class="form-body">
											<div class="row">
												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<h3>Studio Manager / TimeTable Manager Links</h3>
														<label for="first-name-column">Studio Manager / TimeTable Manager Name</label>
														@if(count($studiomanager) > 0)
														<select class="form-control select-multiple1" name="employee_id[]" multiple>
															<option value=""> - Select Any - </option>
															@foreach($studiomanager as $value)
															<option value="{{ $value->id }}">{{ $value->name . ' ( ' .$value->register_id.' ) ' }}</option>
															@endforeach
														</select>
														@endif
													</div>
												</div>                                 
												<div class="col-md-6 col-12 mt-4">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Send Link</button>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
						
						
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="{{ route('admin.links.faculty_link') }}" method="post" enctype="multipart/form-data">
										@csrf
										<div class="form-body">
											<div class="row">
												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<h3>Studio Assistant Links</h3>
														<label for="first-name-column">Studio Assistant Name</label>
														@if(count($assistants) > 0)
														<select class="form-control select-multiple1" name="employee_id[]" multiple>
															<option value=""> - Select Any - </option>
															@foreach($assistants as $value)
															<option value="{{ $value->id }}">{{ $value->name . ' ( ' .$value->register_id.' ) ' }}</option>
															@endforeach
														</select>
														@endif
													</div>
												</div>                                 
												<div class="col-md-6 col-12 mt-4">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Send Link</button>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
						
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="{{ route('admin.links.faculty_link') }}" method="post" enctype="multipart/form-data">
										@csrf
										<div class="form-body">
											<div class="row">
												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<h3>Drivers Links</h3>
														<label for="first-name-column">Drivers Name</label>
														@if(count($drivers) > 0)
														<select class="form-control select-multiple1" name="employee_id[]" multiple>
															<option value=""> - Select Any - </option>
															@foreach($drivers as $value)
															<option value="{{ $value->id }}">{{ $value->name . ' ( ' .$value->register_id.' ) ' }}</option>
															@endforeach
														</select>
														@endif
													</div>
												</div>                                 
												<div class="col-md-6 col-12 mt-4">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Send Link</button>
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

<script>
$('.select-multiple1').select2({
	placeholder: "Select",
	allowClear: true
});
</script>
@endsection
