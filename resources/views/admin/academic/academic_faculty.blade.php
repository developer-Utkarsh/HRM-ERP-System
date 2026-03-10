@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-md-8">
						<h2 class="content-header-title float-left mb-0">Academic Faculty Report</h2>
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
			<section id="multiple-column-form">
				<div class="match-height">
					<div class="card">
						<div class="card-content">
							<div class="card-body">
								<div class="users-list-filter">
									<form action="{{ route('admin.academic-faculty-report') }}" method="get">
										@csrf
										<div class="row mx-0">								
											<div class="col-3">
												<label>Subject</label>
												<fieldset class="form-group">
													<select class="form-control subject select-multiple1" name="subject">
														<option value=""> -- Select -- </option>
														@foreach($subject as $su)
														<option value="{{ $su->id }}" @if($su->id == app('request')->input('subject')) selected="selected" @endif>{{ $su->name }}</option>
														@endforeach
													</select>
												</fieldset>
											</div>
											<div class="col-3">
												<label>Faculty</label>
												<fieldset class="form-group">
													<select class="form-control faculty select-multiple1" name="faculty">
														<option value=""> -- Select -- </option>
														@foreach($faculty as $fa)
														<option value="{{ $fa->id }}" @if($fa->id == app('request')->input('faculty')) selected="selected" @endif>{{ $fa->name }}</option>
														@endforeach
													</select>
												</fieldset>
											</div>
											<div class="col-3">
												<label>Location</label>
												<fieldset class="form-group">
													<select class="form-control location select-multiple1" name="location">
														<option value=""> -- Select -- </option>
														@foreach($location as $la)
														<option value="{{ $la->branch_location }}" @if($la->branch_location == app('request')->input('location')) selected="selected" @endif>{{ ucwords($la->branch_location) }}</option>
														@endforeach
													</select>
												</fieldset>
											</div>
											<div class="col-3">
												<label>Agreement</label>
												<fieldset class="form-group">
													<select class="form-control agreement select-multiple1" name="agreement">
														<option value=""> -- Select -- </option>
														<option value="Yes" @if('Yes' == app('request')->input('agreement')) selected="selected" @endif>Fixed</option>
														<option value="No" @if('No' == app('request')->input('agreement')) selected="selected" @endif>Variable</option>
														<option value="Both" @if('Both' == app('request')->input('agreement')) selected="selected" @endif>Fixed+Variable</option>
													</select>
												</fieldset>
											</div>
											<div class="col-3">
												<label for="users-list-verified">Month</label>
												<fieldset class="form-group">
													<input type="month" name="month" value="{{ app('request')->input('month') ?: now()->format('Y-m') }}" class="form-control"/>
												</fieldset>
											</div>
											<div class="col-4 pt-2">
												<button type="submit" class="btn btn-primary">Search</button>
												<a href="{{ route('admin.academic-faculty-report') }}" class="btn btn-warning">Reset</a>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
					
					<div class="">												
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<div class="table-responsive">
										<table class="table data-list-view">
											<thead>
												<tr>
													<th>S.No.</th>
													<th>Subject</th>
													<th>Faculty Name</th>
													<th>City</th>
													<th>Agreement Type</th>
													<th>Agreement Hours</th>
													<th>Spent Hours</th>
													<th>Status</th>
													<th>NPS 5.0</th>
													<th>NPS 4.0</th>
													<th>NPS 3.0</th>
													<th>Average Raiting</th>
													<th>Subject Average</th>
													<th>Company Average</th>
												</tr>
											</thead>
											<tbody>
												<?php 
													if(count($report) >0){
													$i =1;
													foreach($report as $re){ 
												?>
												<tr>
													<td>{{ $i++ }}</td>
													<td>{{ $re->subject_name }}</td>
													<td>{{ $re->name }}</td>
													<td>{{ ucwords($re->city) }}</td>
													<td>
														<?php 
															if($re->agreement=='Yes'){
																$agreement =  'Fixed';
															}else if($re->agreement=='No'){
																$agreement =  'Variable';
															}else{
																$agreement =  'Fixed + Variable';
															}
															
															echo $agreement;
														?>
													</td>
													<td>{{ $re->committed_hours }}</td>
													<td>{{ round($re->spent_hrs_1,2) }}</td>
													<td>
														<?php 
															if($re->committed_hours > round($re->spent_hrs_1,2)){
																echo '<i class="fa fa-arrow-circle-down text-info" aria-hidden="true"></i>';
															}else{
																echo '<i class="fa fa-arrow-circle-up text-danger" aria-hidden="true"></i>';
															}
														?>
													</td>
													<td>{{ round($re->avg_rating,2) }}</td>
													<td>{{ $re->avg_nps_4 }}</td>
													<td>{{ $re->avg_nps_3 }}</td>
													<td>{{ round(((float)$re->avg_rating + (float)$re->avg_nps_4 + (float) $re->avg_nps_3 )/3,2) }}</td>
													<td>{{ round($re->subject_avg,2) }}</td>
													<td>4.52</td>
												</tr>
												<?php } }else{ ?>
												<tr>
													<td colspan="15">No Record Found</td>
												</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>
</div>

<style>
	.dashboard b{
		font-size:22px;
		padding-bottom:10px;
	}
	
	.daywise thead th{
		font-size:11px !important; 
	}
</style>
@endsection
@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>


<script type="text/javascript">
$(document).ready(function() {
	$('.select-multiple1').select2({
		width: '100%',
		placeholder: "Select",
		allowClear: true
	});
});
</script>
@endsection
