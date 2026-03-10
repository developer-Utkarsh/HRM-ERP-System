@extends('layouts.studiomanager')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Edit Cancel Class</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('studiomanager.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Edit Cancel Class</a>
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
									<form class="form" action="{{ route('studiomanager.cancelclass.update', $cancelclass->id) }}" method="post" enctype="multipart/form-data">
										@method('PATCH')
										@csrf
										<div class="form-body">
											<div class="row">
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Days</label>
														<input type="text" class="form-control" placeholder="To Time" name="to_time" value="{{ old('to_time', $cancelclass->days) }}">
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Faculty Reason</label>
														<textarea class="form-control" name="faculty_reason">{{ old('faculty_reason', $cancelclass->faculty_reason) }}</textarea>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Admin Reason</label>
														<textarea class="form-control" name="admin_reason" placeholder="Admin Reason">{{ old('admin_reason', $cancelclass->admin_reason) }}</textarea>
													</div>
												</div>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Status</label>
														<select class="form-control" name="status">
															<option value=""> - Select Any - </option>
															<option value="Approved" @if($cancelclass->status == 'Approved') selected="selected" @endif>Approved</option>
															<option value="Reject" @if($cancelclass->status == 'Reject') selected="selected" @endif>Reject</option>
															<option value="pending" @if($cancelclass->status == 'pending') selected="selected" @endif>Pending</option>
														</select>
													</div>
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
@endsection
