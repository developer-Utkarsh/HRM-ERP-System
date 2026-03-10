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
						<h2 class="content-header-title float-left mb-0">Student Invalid Punch</h2>
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
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ route('admin.student-invalid-punch') }}" method="get" name="filtersubmit">
									<div class="row">										
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Date</label>
											<fieldset class="form-group">
												<input type="date" class="form-control" name="sdate" placeholder="Search" value="{{ app('request')->input('sdate') }}">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('admin.student-invalid-punch') }}" class="btn btn-warning">Reset</a>
											</fieldset>
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
								<th>Branch Name</th>
								<th>Total Invalid Punch</th>								
							</tr>
						</thead>
						<tbody>
							<?php 
								if(count($getpunch) > 0){ 
									$i = 1;
									foreach($getpunch as $gp){ 
							?>
							<tr>
								<td>{{ $i }}</td>
								<td>{{ $gp->name }}</td>
								<td>{{ $gp-> countbranch}}</td>								
							</tr>
							<?php  $i++;   }	
								}else{
							?>
							<tr>
								<td class="text-center" colspan="3">No Record Found</td>
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
