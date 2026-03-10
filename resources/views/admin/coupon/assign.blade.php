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
						<h2 class="content-header-title float-left mb-0">Assign Coupon</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">Add Coupon</li>
							</ol>
						</div>
					</div>
                    <div class="col-4 text-right">
						<a href="{{ route('coupon.index') }}" class="btn btn-outline-primary mr-1">&#8592; Back</a>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="{{ route('coupon.store') }}" method="post" enctype="multipart/form-data">
										@csrf
										<div class="form-body">
											<div class="row">
												<div class="col-md-5 col-12">
													<div class="form-group">
														<label for="mobile">Mobile Number</label>
														<input type="number" name="mobile" class="form-control" placeholder="Enter Mobile Number...">
														@if($errors->has('mobile'))
														<span class="text-danger">{{ $errors->first('mobile') }} </span>
														@endif 
													</div>
												</div>	
												
												<div class="col-md-5 col-12">
													<div class="form-group">
														<label for="coupon_code">Coupon Code</label>
														<input type="text" class="form-control" name="coupon_code" value="EXTRA10" readonly disabled>
														@if($errors->has('coupon_code'))
														<span class="text-danger">{{ $errors->first('coupon_code') }} </span>
														@endif
													</div>
												</div>	
												<div class="col-md-2 d-flex justify-content-center align-items-center">
													<button type="submit" class="btn font-weight-bold btn-primary mr-1 mb-1">Assign Now</button>
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

