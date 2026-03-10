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
						<h2 class="content-header-title float-left mb-0">Add Template</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Add Template</a>
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
									<form class="form" action="{{ route('admin.sendsms_templates_save') }}" method="post" enctype="multipart/form-data">
										@csrf
										<div class="form-body">
											<div class="row">
												<div class="col-md-12 col-12">
													<div class="form-group">
														<label for="first-name-column">Name</label>
														<input type="text" class="form-control" placeholder="Template Name" name="name" value="{{ old('name') }}" required>
														@if($errors->has('name'))
														<span class="text-danger">{{ $errors->first('name') }} </span>
														@endif
													</div>
												</div>												
												<div class="col-md-12 col-12">
													<div class="form-group">
														<label for="first-name-column">Body :</label>
														<textarea name="description" class="form-control" rows="5" required>{{ old('description') }}</textarea>
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

@section('scripts')
@endsection
