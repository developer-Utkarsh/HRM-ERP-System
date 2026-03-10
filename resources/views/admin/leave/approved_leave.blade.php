@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Datewise Approved And Reject Leave</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">Approved Leave View</li>
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
								<form action="{{ route('admin.leave.update-approved-leave') }}" method="post" name="filtersubmit">
									@csrf
									<div class="row">
										<div class="col-md-3 col-12">
											<div class="form-group">
												<label for="first-name-column">From Date</label>
												<input type="date" class="form-control from_date" placeholder="Date" name="from_date" max="{{date('Y-m-d')}}" value="{{ old('from_date') }}">
												@if($errors->has('from_date'))
												<span class="text-danger">{{ $errors->first('from_date') }} </span>
												@endif
											</div>
										</div>
										<div class="col-md-3 col-12">
											<div class="form-group">
												<label for="first-name-column">To Date</label>
												<input type="date" class="form-control to_date" placeholder="Date" name="to_date" max="{{date('Y-m-d')}}" value="{{ old('to_date') }}">
												@if($errors->has('to_date'))
												<span class="text-danger">{{ $errors->first('to_date') }} </span>
												@endif
											</div>
										</div>
										<div class="col-md-3 col-12">
											<div class="form-group">
												<label for="">Status</label>
												<select class="form-control status" id="status" name="status">	
												<option value=""> Select</option>
												<option value="Approved"> Approved</option>
												<option value="Rejected"> Rejected</option>
												</select>
												@if($errors->has('status'))
												<span class="text-danger">{{ $errors->first('status') }} </span>
												@endif
											</div>
										</div>
										<div class="col-6 col-sm-6 col-lg-3 pt-2" >
											<fieldset class="form-group" style="float:right;">		
											<button type="submit" class="btn btn-primary">Update</button>
											<a href="{{ route('admin.leave.approved-leave') }}" class="btn btn-warning">Reset</a>
											</fieldset>
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
