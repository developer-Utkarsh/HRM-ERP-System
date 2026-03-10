@extends('layouts.admin')
@section('content')
<?php //echo '<pre>'; print_r('http://'.request()->getHttpHost().'/');die;?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Salary</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">Import Salary</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								
								<form class="form" action="{{ route('admin.store-salary') }}" method="post" enctype="multipart/form-data">
								@csrf
									<h5>Import Salary</h5>
									<div class="row pt-2">
										<div class='col-md-4 col-12'>	
											<div class='form-label-group'>	
												<input type="file" class="form-control" name="import_file">
												<span><a class="float-right" href="{{asset('laravel/public/ImportSalary.xlsx')}}"><span>Sample Download File</span></a></span>
												@if($errors->has('import_file'))
												<span class="text-danger">{{ $errors->first('import_file') }} </span>
												@endif
											</div>
										</div>	
										<div class='col-md-4 col-12'><button type="submit" id="import_btn" class="btn btn-primary dsabl">Submit</button>
										</div>										
									</div>
									
								</form>
				
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

@endsection
