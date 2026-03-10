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
						<h2 class="content-header-title float-left mb-0">Reports</h2>
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
								<form action="{{ route('admin.reports.index') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-role">Branch</label>
											<fieldset class="form-group">
												<?php $branches = \App\Branch::where('status', '1')->orderBy('id', 'desc')->get(); ?>
												@if(count($branches) > 0)
												<select class="form-control branch_id get_branch select-multiple1" name="branch_id">
													<option value=""> - Select Branch - </option>
													@foreach($branches as $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('branch_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
												</select>												
												@endif
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Faculty</label>
											<fieldset class="form-group">
												<?php $faculty = \App\User::where('role_id', '2')->where('status', '1')->orderBy('id', 'desc')->get(); ?>
												<select class="form-control select-multiple2" name="faculty_id">
													<option value=""> - Select Faculty - </option>
													@if(count($faculty) > 0)
													@foreach($faculty as $value)
													<option value="{{ $value->id }}" @if(app('request')->input('faculty_id') == $value->id) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-verified">Month</label>
											<?php
											$monthArray = array(
												"1" => "January", "2" => "February", "3" => "March", "4" => "April",
												"5" => "May", "6" => "June", "7" => "July", "8" => "August",
												"9" => "September", "10" => "October", "11" => "November", "12" => "December",
											);
											?>
											<fieldset class="form-group">
												<select class="form-control select-multiple3" name="month">
													<option value=""> - Select Month - </option>
													@foreach ($monthArray as $monthNum => $month)
													<option value="{{ $monthNum }}" @if(app('request')->input('month') == $monthNum) selected="selected" @endif>{{ $month }}</option>
													@endforeach
												</select>
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-verified">From</label>
											<fieldset class="form-group">
												<input type="date" name="fdate" class="form-control" value="{{ app('request')->input('fdate') }}">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-verified">To</label>
											<fieldset class="form-group">
												<input type="date" name="tdate" class="form-control" value="{{ app('request')->input('tdate') }}">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<button type="submit" class="btn btn-primary mt-1">Search</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<form method="POST" action="{{ route('admin.report.export') }}">
						@csrf
						<div class="col-12 col-sm-6 col-lg-3">
							<button type="submit" class="btn btn-primary mt-1">Export</button>
						</div>
					</div>				
					<div class="table-responsive">
						<table class="table data-list-view">
							<thead>
								<tr>
									<th><input type="checkbox" name="check_all" id="check-all"></th>
									<th>S. No.</th>
									<th>Faculty</th>
									<th>Branch</th>
									<th>Working Hours</th>
									<th>Course</th>
									<th>Date</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>							
								@foreach($report_faculty as  $key => $value)
								<tr>
									<td><input type="checkbox" class="checkbox" name="id[]" value="{{ $value->id }}"></td>
									<td>{{ $key + 1 }}</td>
									<td>
										<?php if(!empty($value->timetable->faculty->name)){
											echo $value->timetable->faculty->name;
										}
										?>
									</td>
									<td>
										<?php if(!empty($value->timetable->studio->branch->name)){
											echo $value->timetable->studio->branch->name;
										}
										?>
									</td>
									<td>
										<?php
										$minutes =  round(abs(strtotime($value->start_time) - strtotime($value->end_time)) / 60,2);
										$hours = floor($minutes / 60).':'.($minutes -   floor($minutes / 60) * 60);
										echo $hours . ' hours';
										?>								
									</td>
									<?php 
									if(!empty($value->timetable->faculty_id)){
										$get_facultywise_batch_course = \App\Batchrelation::select('batch_id')->whereFacultyId($value->timetable->faculty_id)->groupBy('batch_id')->count();
									}
									
									?>
									<td>{{ isset($get_facultywise_batch_course) ? $get_facultywise_batch_course : '' }}</td>
									<td>{{ date('d-m-Y',strtotime($value->sc_date)) }}</td>
									<td class="product-action">
										<a href="{{ route('admin.reports.show', $value->id) }}">
											<span class="action-edit"><i class="feather icon-eye"></i></span>
										</a>
									</td>
								</tr>
								@endforeach			
							</tbody>
						</table>
					</div>
				</form>                   
			</section>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
	$(document).ready(function(){
		$('#check-all').on('click',function(){
			if(this.checked){
				$('.checkbox').each(function(){
					this.checked = true;
				});
			}else{
				$('.checkbox').each(function(){
					this.checked = false;
				});
			}
		});
	});
</script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple1').select2({
			placeholder: "Select Any",
			allowClear: true
		});
		$('.select-multiple2').select2({
			placeholder: "Select Any",
			allowClear: true
		});
		$('.select-multiple3').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
</script>
@endsection
