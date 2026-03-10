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
						<h2 class="content-header-title float-left mb-0">Batch Reports</h2>
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
								<form action="{{ route('admin.course-planner.batchReports') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Location Wise</label>
											<fieldset class="form-group">
												<?php 
													$branches = \App\Branch::where('status', '1')->orderBy('id', 'desc')->groupby('branch_location')->get(); ?>
												@if(count($branches) > 0)
												<select class="form-control branch_id get_branch" name="branch_location">
													<option value=""> - Select - </option>
													@foreach($branches as $value)
													<option value="{{ $value->branch_location }}" @if($value->branch_location == app('request')->input('branch_location')) selected="selected" @endif>{{ ucwords( $value->branch_location) }}</option>
													@endforeach
												</select>												
												@endif
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Course Wise</label>
											<fieldset class="form-group">
												<?php $course = \App\Course::where('status', '1')->orderBy('id', 'desc')->get(); ?>
												<select class="form-control select-multiple1" name="course_id">
													<option value=""> - Select Course - </option>
													@if(count($course) > 0)
													@foreach($course as $value)
													<option value="{{ $value->id }}" @if(app('request')->input('course_id') == $value->id) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Batch Wise</label>
											<fieldset class="form-group">
												<?php $batch = \App\Batch::where('status', '1')->where('course_planer_enable',1);
												if($designation=='CATEGORY HEAD'){
													$batch->whereIN('category',$erp_category);
												}
												$batch=$batch->orderBy('id', 'desc')->get(); ?>
												<select class="form-control select-multiple2" name="batch_id[]" multiple>
													<option value=""> - Select Batch - </option>
													@if(count($batch) > 0)
													@foreach($batch as $value)
													<option value="{{ $value->id }}" <?php if(in_array($value->id,$_GET['batch_id']??[])){ echo "selected"; } ?>>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Indicator Wise</label>
											<fieldset class="form-group">
												<select class="form-control" name="indicator">
													<option value=""> - Select - </option>													
													<option value="10" @if(app('request')->input('indicator') =='10') selected="selected" @endif>Red</option>
													<option value="-10" @if(app('request')->input('indicator') =='-10') selected="selected" @endif>BLue</option>
													<option value="1" @if(app('request')->input('indicator') =='1') selected="selected" @endif>Green</option>
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Batch Completion % (>=)</label>
											<fieldset class="form-group">
												<select class="form-control" name="percentage_batch_complete">
													<option value=""> - Select - </option>
													<?php for($i=5;$i<=100;$i=$i+5){ ?>											
													  <option value="<?=$i;?>" @if(app('request')->input('percentage_batch_complete') ==$i) selected="selected" @endif ><?=$i;?></option>
													<?php } ?>
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-verified">Delay in Days (>=)</label>
											<fieldset class="form-group">
												<input type="number" name="possible_delay" class="form-control" value="{{ app('request')->input('possible_delay') }}" placeholder=">=">
											</fieldset>
										</div>

										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Batch Status Wise</label>
											<fieldset class="form-group">
												<select class="form-control" name="batch_status">
													<option value=""> - Select - </option>													
													<option value="1" @if(app('request')->input('batch_status') =='1') selected="selected" @endif>Completed</option>
													<option value="2" @if(app('request')->input('batch_status') =='2') selected="selected" @endif>Running</option>
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-verified">ERP ID</label>
											<fieldset class="form-group">
												<input type="number" name="erp_id" class="form-control" value="{{ app('request')->input('erp_id') }}" placeholder="Enter ERP ID">
											</fieldset>
										</div>

										<div class="col-12 col-sm-6 col-lg-3">
											<button type="submit" class="btn btn-primary mt-1">Search</button>
											<a href="{{ route('admin.course-planner.batchReports') }}" class="btn btn-warning mt-1">Reset</a>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="row mx-0">
					<div class="table-responsive table-container">
						<style type="text/css">
							.table-container {
		            width: 100%;
		            margin: 0 auto;
		            overflow-y: auto;
		            height: 800px; /* Adjust this value as needed */
		            font-size:22px;
		        }
							thead th {
			            position: sticky;
			            top: 0;
			            background-color: #f2f2f2;
			            z-index: 1;
			        }
						</style>
						<table class="table data-list-view">
							<thead>
								<tr>
									<th>Action</th>
									<th>S. No.</th>
									<th>Course Name</th>
									<th>Batch Name</th>
									<th>ERP Course ID</th>
									<th>
										Total Batch Duration (in Hours)
										<button type="button" class="btn btn-secondary p-0 text-dark" data-toggle="tooltip" data-placement="right" title="The total duration of the batch in hours, calculated by summing the given hours for each subject in the course planner.">
											<i class="fa fa-info-circle" aria-hidden="true"></i>  
										</button>
									</th>
									<th>Schedule Hours
										<button type="button" class="btn btn-secondary p-0 text-dark" data-toggle="tooltip" data-placement="right" title="The total schedule duration of the batch in hours, calculated by summing the given hours for each subject in class by timetable team(schedule team).">
											<i class="fa fa-info-circle" aria-hidden="true"></i>  
										</button>
									</th>
									<th>
										Total Spent time (In hours) 
										<button type="button" class="btn btn-secondary p-0 text-dark" data-toggle="tooltip" data-placement="right" title="The total time spent in classes by faculty for this batch, calculated by summing the hours each faculty member has taken classes.">
											<i class="fa fa-info-circle" aria-hidden="true"></i>  
										</button>
									</th>
									<th>Topics (In hours) Completed+Partially Completed</th>
									<th>%age of Batch Completed</th>
									<th>
										Total Batch Duration (Days) 
										<button type="button" class="btn btn-secondary p-0 text-dark" data-toggle="tooltip" data-placement="right" title="The total duration of the batch in days, calculated from the start and end dates provided when the batch was created.">
											<i class="fa fa-info-circle" aria-hidden="true"></i>  
										</button>
									</th>
									<th>Duration Done (Days)</th>
									<th>%age of Duration Completed</th>
									<th>
										Daily Average Class Duration 
										<button type="button" class="btn btn-secondary p-0 text-dark" data-toggle="tooltip" data-placement="right" title="The average duration of classes per day, calculated by dividing the total scheduled class hours by the number of days classes are scheduled.">
											<i class="fa fa-info-circle" aria-hidden="true"></i>  
										</button>
									</th>
									<th>
										Duration Required (Days) 
										<button type="button" class="btn btn-secondary p-0 text-dark" data-toggle="tooltip" data-placement="right" title="The number of days required to complete the remaining topics, based on the pending hours and the average class schedule duration.">
											<i class="fa fa-info-circle" aria-hidden="true"></i>  
										</button>
									</th>
									<th>
										Possible Early/Delay (In Days)  
										<button type="button" class="btn btn-secondary p-0 text-dark" data-toggle="tooltip" data-placement="right" title="The estimated number of days the batch will finish early or be delayed, calculated by comparing the remaining topic duration to the average class schedule duration, minus the remaining days, holidays, and Sundays.">
											<i class="fa fa-info-circle" aria-hidden="true"></i>  
										</button>
									</th>
									<th>Expected End Date</th>
									<th>Additions Planner</th>
									<th>Indicator</th>
								</tr>
							</thead>
							<tbody>	
								<?php $i = 1; foreach($batch_report as $br){ ?>
								<tr>
									<td>
										<a href="javascript:void(0)"  data-id="{{ $br->batch_id }}" class="get_edit_data text-primary">Subject Status</a>
									</td>
									<td>{{ $i }}</td>
									<td>{{ $br->course_name }}</td>
									<td>{{ $br->batch_name }}</td>
									<td><?php if($br->erp_course_id!=0){ echo $br->erp_course_id; }else{ echo '-'; } ?></td>
									<td>{{ $br->batch_duration_hours }} Hours</td>
									<td>{{ $br->batch_schedule_hours }} Hours</td>
									<td>{{ $br->batch_spent_time_hours }} Hours</td>
									<td>{{ $br->batch_topic_completed_hours }} Hours</td>
									<td>{{ $br->percentage_batch_complete   }}% </td>
									<td>{{ $br->batch_duration_days }} Days</td>
									<td><?php  echo $done =  $br->batch_duration_days - $br->batch_remaining_days ;?>  Days</td>
									<td><?php echo round($done*100/$br->batch_duration_days,2); ?>%</td>
									<td>{{ $br->avg_class_duration }} Hours</td>
									<td>{{ $br->required_days }} Days</td>
									<td>{{ $br->possible_delay }} Days</td>
									<td>{{ $br->expected_end_date }}</td>
									<td>{{ $br->additions_planner }} Hours</td>
									<td>
										<?php if($br->possible_delay >= 10){
											echo '<i class="fa fa-caret-square-o-down text-danger" aria-hidden="true"></i>';
										}else if($br->possible_delay <= -10){
											echo '<i class="fa fa-caret-square-o-up text-primary" aria-hidden="true"></i>';
										}else{
											echo '<i class="fa fa-check-circle text-success" aria-hidden="true"></i>';
										}
										?>
									</td>
								</tr>
								<?php $i++; } ?>
							</tbody>
						</table>
						<div class="d-flex justify-content-center">					
						{!! $batch_report->appends($params)->links() !!}
						</div>
					</div>
				</form>                   
			</section>
		</div>
	</div>
</div>



<div class="modal fade bd-example-modal-lg" id="modalRaiseIssue" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" style="color:#FF6F0E">Report</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
			
			
		</div>
		<div class="text-right p-1">
			<a href="#" target="_blank" class="levelStatus">Batch Topic Level Status</a>
		</div>
		<table class="table data-list-view">
			<thead>
				<tr>
					<th>Faculty Name</th>
					<th>ID</th>
					<th>Subject</th>
					<th>Total Duration</th>
					<th>Schedule Hours</th>
					<th>Total Spent time</th>
					<th>Topics (In hours) Completed+Partially Completed</th>
					<th>%age of Subject Completed</th>
					<th>Required Days for Completion</th>
					<th>Indicator</th>					
				</tr>
			</thead>
			<tbody class="fill_html">
			
			</tbody>
		</table>
    </div>
  </div>
</div>
<style>
	.table thead th {
		font-size: 12px;
	}
</style>
@endsection

@section('scripts')
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
	});
</script>

<script type="text/javascript">
	$(".get_edit_data").on("click", function() {  
		var batch_id = $(this).attr("data-id"); 
		
		$('#modalRaiseIssue').modal({
				backdrop: 'static',
				keyboard: true, 
				show: true
		});
		
		$('.levelStatus').attr("href", '{{ route('admin.batch.view') }}/'+batch_id);
		
		
		$.ajax({
			type : 'GET',
			url : '{{ route('admin.course-planner.batchSubjectReports') }}/'+batch_id,
			//data : {'_token' : '{{ csrf_token() }}', 'batch_id': batch_id},
			dataType : 'json',
			success : function (data){
				console.log(data['report']);			
				var html = '';
				if(data['status']==true){
					var report=data['report'];

					report = report.sort((a, b) => b.subject_compelete - a.subject_compelete);
					$.each(report, function (i) {
						//if(report[i]['indicator']==''
						var indicator = "";
						if(report[i]['possible_delay'] >= 10){
							indicator=  '<i class="fa fa-caret-square-o-down text-danger" aria-hidden="true"></i>';
						}else if(report[i]['possible_delay'] <= -10){
							indicator=  '<i class="fa fa-caret-square-o-up text-primary" aria-hidden="true"></i>';
						}else if(report[i]['possible_delay'] == 0){
							indicator=  '<i class="fa fa-check-circle text-warning" aria-hidden="true"></i>';
						}else{
							indicator=  '<i class="fa fa-check-circle text-success" aria-hidden="true"></i>';
						}
						
						html  += `<tr>
							<td>`+report[i]['faculty']+`</td>
							<td>`+report[i]['faculty_id']+`</td>
							<td>`+report[i]['subject']+`</td>
							<td>`+report[i]['total_subject_duration']+` Hours</td>
							<td>`+report[i]['schedule_hrs']+` Hours</td>
							<td>`+report[i]['spent_hrs']+` Hours</td>
							<td>`+report[i]['topic_completed_hrs']+` Hours</td>
							<td>
							  ${report[i]['subject_compelete']==100?`<span class="text-success">100</span>`:''}
							  ${report[i]['subject_compelete']>0 && report[i]['subject_compelete']<100?`<span class="text-primary">`+report[i]['subject_compelete']+`</span>`:''}
							  ${report[i]['subject_compelete']==0?`<span class="text-warning">0</span>`:''}
							</td>
							<td>`+report[i]['required_days']+` Days</td>
							<td>`+indicator+`</td>
						</tr>`;
					});
				}else{
					html="No data found";
				}
				
				$('.fill_html').html(html);
			}
		});	

	}); 
</script>
@endsection
