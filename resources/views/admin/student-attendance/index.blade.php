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
						<h2 class="content-header-title float-left mb-0">Student Attendance</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
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
								<form action="{{ route('admin.student-attendance') }}" method="get" name="filtersubmit">
									<div class="row pt-2">
										<div class="col-12 col-sm-6 col-lg-3">
											<fieldset class="form-group">
												<input type="text" class="form-control" name="Reg_No" placeholder="Reg. No." value="{{ app('request')->input('title') }}">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('admin.student-attendance') }}" class="btn btn-warning">Reset</a>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table class="table data-list-view">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Reg. No.</th>
								<th>Name</th>
								<th>Batch</th>
								<th>RFID No.</th>
								<th>Date</th>										
							</tr>
						</thead>
						<tbody>		
							<?php 
								if(count($attendance) > 0){
									$i = 1;
									foreach($attendance as $at){
							?>
							<tr>
								<td><?=$i++;?></td>
								<td><?=$at->reg_no;?></td>
								<td>-</td>
								<td>-</td>
								<td><?=$at->rfid_no;?></td>
								<td><?=$at->date;?></td>										
							</tr>
							<?php 
									 } 
								}else{
							?>
							<tr>
								<td colspan="6" class="text-center">No Record Found</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
				                  
			</section>
		</div>
	</div>
</div>
@endsection
