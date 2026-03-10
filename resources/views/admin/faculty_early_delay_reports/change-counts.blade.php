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
						<h2 class="content-header-title float-left mb-0">TimeTable Changes Counts</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ route('admin.timetable-change-counts') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Class Date</label>
											<fieldset class="form-group">
												<input type="date" name="cdate" value="{{ app('request')->input('cdate') }}" class="form-control">
											</fieldset>									
										</div>
										<div class="col-12 col-sm-6 col-lg-3 pt-2">
											<fieldset class="form-group" style="float:right;">		
												<button type="submit" class="btn btn-primary">Search</button>
												<a href="{{ route('admin.timetable-change-counts') }}" class="btn btn-danger">Reset</a>
											</fieldset>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body pt-0">
							<div class="pt-2"><h5>Classes Changes Counts Dashboard</h5></div>
							<!-- <div class="pt-2"><h5>1. City Wise</h5></div> -->
							<div class="users-list-filter">								
								<div class="row text-center">
									<?php 		
										$total = 0;
										foreach($list as $gr){
											
									?>
									<div class="col-12 col-sm-6 col-lg-2">										
										<div class="border mt-1 p-1">
											<label for="users-list-status"><b class="text-primary"><?=ucwords($gr->branch_location);?></b></label></br>
											<b class="text-danger"><?=$gr->record;?></b>
										</div>
									</div>									
									<?php } ?>	
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


@endsection
