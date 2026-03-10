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
						<h2 class="content-header-title float-left mb-0">Employee time update</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">Leave View</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="content-body">
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				<?php if(Auth::user()->role_id == 29 || Auth::user()->role_id == 24){ ?>
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
						<p class="text-success"><b style="background: yellow;">{{$save_successfully}}</b></p>
							<div class="users-list-filter">
								<form action="{{ route('admin.employee.time_update') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-12 col-lg-12" >
											<label for="users-list-role">Employee ID(example : 2324,1244,3454)</label>
											<fieldset class="form-group">
												<input type="text" class="form-control" name="emp_code" placeholder="EMP ID" value="{{ app('request')->input('emp_code') }}">
											</fieldset>
										</div>
										<div class="col-md-2 col-12">
											<div class="form-group">
												<label for="first-name-column">In Timing Shift</label>
												<input type="time" class="form-control" name="timing_shift_in" value="{{ app('request')->input('timing_shift_in') }}">
												@if($errors->has('timing_shift_in'))
												<span class="text-danger">{{ $errors->first('timing_shift_in') }} </span>
												@endif
											</div>
										</div>
										
										<div class="col-md-2 col-12">
											<div class="form-group">
												<label for="first-name-column">Out Timing Shift</label>
												<input type="time" class="form-control" name="timing_shift_out" value="{{ app('request')->input('timing_shift_out') }}">
												@if($errors->has('timing_shift_out'))
												<span class="text-danger">{{ $errors->first('timing_shift_out') }} </span>
												@endif
											</div>
										</div>
										
										<div class="col-md-2 col-12">
											<div class="form-group">
												<label for="company-column">Total Time ( in minutes )</label>
												<input type="number" class="form-control" name="total_time" placeholder="Total Time ( in minutes )" value="{{app('request')->input('total_time')}}">
											</div>
										</div>
										
										<div class="col-2 col-sm-3 col-lg-3 pt-2" >
											<fieldset class="form-group" style="float:right;">		
											<button type="submit" class="btn btn-primary">Submit</button>
											<a href="{{ route('admin.employee.time_update') }}" class="btn btn-primary">Reset</a>
											</fieldset>
										</div>
									</div>
									
								</form>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
				                
			</section>
		</div>
	</div>
</div>
@endsection

@section('scripts')

@endsection
