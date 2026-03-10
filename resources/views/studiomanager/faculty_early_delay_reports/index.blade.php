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
						<h2 class="content-header-title float-left mb-0">Faculty Early Delay Report</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('studiomanager.dashboard') }}">Home</a>
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
								<form action="{{ route('studiomanager.faculty-early-delay-reports') }}" method="get" name="filtersubmit">
									<div class="row">
										
										<input type="hidden" class="faculty_id_get" value="{{ json_encode(app('request')->input('faculty_id')) }}" multiple>
										
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Faculty</label>
											<?php $faculty = \App\User::where('role_id', '2')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2 faculty_id" name="faculty_id[]" multiple>
													@if(count($faculty) > 0)
													@foreach($faculty as $key => $value)
													<option value="{{ $value->id }}" @if(!empty(app('request')->input('faculty_id')) && in_array($value->id, app('request')->input('faculty_id'))) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">											
											<label for="users-list-status">From Date</label>								
											<fieldset class="form-group">																					
												<input type="date" name="fdate" placeholder="Date" value="{{ app('request')->input('fdate') }}" class="form-control StartDateClass fdate">	
											</fieldset>	
										</div>	
										
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">To Date</label>
											<fieldset class="form-group">
												<input type="date" name="tdate" placeholder="Date" value="{{ app('request')->input('tdate') }}" class="form-control EndDateClass tdate">
											</fieldset>									
										</div>

										<?php
										    $delay_type="";
											if(!empty(app('request')->input('delay_type'))){
												$delay_type=app('request')->input('delay_type');
											} 
										?>

										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Delay Type</label>
											<fieldset class="form-group">							<select class="form-control delay_type" name="delay_type">
													<option value=""> Select Delay Type</option>
													<option value="Due to Faculty" @if($delay_type=='Due to Faculty') selected  @endif>DuetoFaculty</option>
													<option value="Due to Managment" @if($delay_type=='Due to Managment') selected @endif>Due to Managment</option>
													<option value="Technical Issue" @if($delay_type=='Technical Issue') selected @endif>Technical Issue</option>
												</select>												
											</fieldset>
										</div>

										<div class="col-md-2">
											<label for="users-list-status">Location</label>											
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 branch_location" name="branch_location">
													<option value="">Select Any</option>																									
													<option value="jodhpur" @if(!empty(app('request')->input('branch_location')) && 'jodhpur' == app('request')->input('branch_location')) selected="selected" @endif>Jodhpur</option>
													<option value="jaipur" @if(!empty(app('request')->input('branch_location')) && 'jaipur' == app('request')->input('branch_location')) selected="selected" @endif>Jaipur</option>
													<option value="delhi" @if(!empty(app('request')->input('branch_location')) && 'delhi' == app('request')->input('branch_location')) selected="selected" @endif>Delhi</option>
													<option value="prayagraj" @if(!empty(app('request')->input('branch_location')) && 'prayagraj' == app('request')->input('branch_location')) selected="selected" @endif>Prayagraj</option>
													<option value="indore" @if(!empty(app('request')->input('branch_location')) && 'indore' == app('request')->input('branch_location')) selected="selected" @endif>Indore</option>
												</select>												
											</fieldset>
										</div>
									</div>
									
									<fieldset class="form-group" style="float:right;">		
										<button type="submit" class="btn btn-primary" name="search" value="search">Search</button>
										<a href="{{ route('studiomanager.faculty-early-delay-reports') }}" class="btn btn-warning">Reset</a>
										<?php if( Auth::user()->role_id != 3){ ?>
										<a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a>
										<?php } ?>
									</fieldset>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
				
				<?php 
				if (count($get_faculty) > 0) {
					foreach ($get_faculty as $key2=>$get_faculty_value) {
				?>
					<table class="table data-list-view" style=''>
					 
						<head>
							<tr style="">
								<th colspan="10"><b>Faculty Name : <?php echo isset($get_faculty_value->faculty_name)?$get_faculty_value->faculty_name:''; ?></b> </th>
							</tr>
						</head>
						<head>
							<tr style="">
								<th scope="col">Date</th>
								<th scope="col">Schedule From</th>
								<th scope="col">Schedule To</th>
								<th scope="col">Spent From</th>
								<th scope="col">Spent To</th>
								<th scope="col">Early</th>
								<th scope="col">Delay</th>
								<th scope="col">Early/Delay Reason</th>
								<th scope="col">Delay Type</th>
								<th scope="col">Faculty Delay Reason</th>
							</tr>
						</head>
						<body>
							<?php
							$whereCond = '1=1';
							if(!empty($selectFromDate) || !empty($selectToDate)){
									$whereCond .= ' AND timetables.cdate >= "'.$selectFromDate.'" AND timetables.cdate <= "'.$selectToDate.'"';
							}
							else{
								$whereCond .= ' AND timetables.cdate = "'.date('Y-m-d').'"';
							}

							if(!empty(app('request')->input('delay_type'))){
								$delay_type=app('request')->input('delay_type');
                               $whereCond .= ' AND start_classes.delay_type= "'.$delay_type.'"';
							}	
							
							if(!empty($get_faculty_value->faculty_name)){
							$get_faculty_timetable = DB::table('timetables')
							->select('timetables.*','studios.name as studios_name','branches.name as branches_name','branches.id as branches_id','batch.name as batch_name','course.name as course_name','subject.name as subject_name','chapter.name as chapter_name','start_classes.status as start_classes_status','start_classes.start_time as start_classes_start_time','start_classes.end_time as start_classes_end_time','users_assistant.name as assistant_name','users_assistant.mobile as assistant_mobile','start_classes.early_delay_reason',
								'start_classes.delay_type',
								'start_classes.delay_status',
								'start_classes.delay_faculty_reason')
							 ->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
							  ->leftJoin('branches', 'branches.id', '=', 'studios.branch_id')
							  ->leftJoin('batch', 'batch.id', '=', 'timetables.batch_id')
							  ->leftJoin('course', 'course.id', '=', 'timetables.course_id')
							  ->leftJoin('subject', 'subject.id', '=', 'timetables.subject_id')
							  ->leftJoin('chapter', 'chapter.id', '=', 'timetables.chapter_id')
							  ->leftJoin('start_classes', 'start_classes.timetable_id', '=', 'timetables.id')
							  ->leftJoin('users as users_assistant', 'users_assistant.id', '=', 'timetables.assistant_id')
							  ->where('timetables.faculty_id', $get_faculty_value->faculty_id)
							  ->where('timetables.time_table_parent_id', '0')
							  ->where('timetables.is_deleted', '0');
							if(Auth::user()->role_id == 3){
								$get_faculty_timetable->where('timetables.assistant_id', Auth::user()->id);
							 }						  
							$get_faculty_timetable = $get_faculty_timetable->whereRaw($whereCond)
													  ->orderBy('timetables.cdate', 'ASC')
													  ->orderBy('timetables.from_time', 'ASC')
													  ->get();
													  
							//echo "<pre>"; print_r($get_faculty_timetable); die;
							$duration  = "00 : 00 Hours"; 
							$schedule_duration  = "00 : 00 Hours"; 
							
							
							$total_early_schedule = new DateTime('00:00');
							$total_base_early_schedule = new DateTime('00:00');
							
							$total_delay_schedule = new DateTime('00:00');
							$total_base_delay_schedule = new DateTime('00:00');
							
							if(count($get_faculty_timetable) > 0){ 
							foreach($get_faculty_timetable as $key => $get_faculty_timetable_value){
								$early = 0;
								if(!empty($get_faculty_timetable_value->start_classes_end_time) && $get_faculty_timetable_value->to_time > $get_faculty_timetable_value->start_classes_end_time){
									$early_from_time         = new DateTime($get_faculty_timetable_value->to_time);
									$early_to_time           = new DateTime($get_faculty_timetable_value->start_classes_end_time);
									$early_schedule_interval = $early_from_time->diff($early_to_time); 
									$early       = $early_schedule_interval->format('%H : %I');
									$total_base_early_schedule->add($early_schedule_interval); 
								}
								
								$delay = 0;
								if(!empty($get_faculty_timetable_value->start_classes_start_time) && $get_faculty_timetable_value->from_time < $get_faculty_timetable_value->start_classes_start_time){
									$delay_from_time         = new DateTime($get_faculty_timetable_value->start_classes_start_time);
									$delay_to_time           = new DateTime($get_faculty_timetable_value->from_time);
									$delay_schedule_interval = $delay_from_time->diff($delay_to_time); 
									$delay       = $delay_schedule_interval->format('%H : %I');
									$total_base_delay_schedule->add($delay_schedule_interval); 
								}
								
							?>
								<tr>
									<td><?php echo isset($get_faculty_timetable_value->cdate) ?  date('d-m-Y',strtotime($get_faculty_timetable_value->cdate)) : '' ?></td>
									<td><?php echo isset($get_faculty_timetable_value->from_time) ?  date("h:i A", strtotime($get_faculty_timetable_value->from_time)) : '0' ?></td>
									<td><?php echo isset($get_faculty_timetable_value->to_time) ?  date("h:i A", strtotime($get_faculty_timetable_value->to_time)) : '0' ?></td>
									
									
									<td><?php echo isset($get_faculty_timetable_value->start_classes_start_time) ?  date("h:i A", strtotime($get_faculty_timetable_value->start_classes_start_time)) : '0' ?></td>
									<td><?php echo isset($get_faculty_timetable_value->start_classes_end_time) ?  date("h:i A", strtotime($get_faculty_timetable_value->start_classes_end_time)) : '0' ?></td>
									<td>{{$early}}</td>
									<td>{{$delay}}</td>
									<td><?=$get_faculty_timetable_value->early_delay_reason?></td>
									<td><?=$get_faculty_timetable_value->delay_type?></td>
									<td><?=$get_faculty_timetable_value->delay_faculty_reason?></td>
								</tr>
							<?php
							}
							}
							}
							?>
							
							<tr>
								<td colspan="4"><b>Total Early Time:</b> 
								<?php
								$totalDays = $total_early_schedule->diff($total_base_early_schedule)->format("%a");
								$totalHours = $total_early_schedule->diff($total_base_early_schedule)->format("%H");
								$totalMinute = $total_early_schedule->diff($total_base_early_schedule)->format("%I");
								echo ($totalDays*24)+$totalHours. ":" . $totalMinute;
								?>
								</td> 
								<td colspan="4"><b>Total Delay Time:</b> 
								<?php
								$baseDays = $total_delay_schedule->diff($total_base_delay_schedule)->format("%a");
								$baseHours = $total_delay_schedule->diff($total_base_delay_schedule)->format("%H");
								$baseMinute = $total_delay_schedule->diff($total_base_delay_schedule)->format("%I");
								echo ($baseDays*24)+$baseHours. ":" . $baseMinute;
								?> 
								</td> 
							</tr>
								
						</body>
					
					</table>
					<p><hr/></p>
				<?php 
					}
				}
				?>
		<style>
		hr{background:#000;}
		</style>
					 
				</div>       
				
			</section>
		</div>
	</div>
</div>

			
@endsection

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<link href="{{ asset('laravel/public/css/jquery.timepicker.css') }}" rel="stylesheet"/>
<script src="{{ asset('laravel/public/js/jquery.timepicker.js') }}"></script>

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
		
		$('.timepicker').timepicker({ 'step': 1, 'timeFormat': 'h:i A' });
	});
	
		$("body").on("click", "#download_excel", function (e) {
			var data = {};
			data.faculty_id = $("[name='faculty_id[]']").val(),
			data.fdate = $('.fdate').val(),
			data.tdate = $('.tdate').val(),
			data.branch_location = $('.branch_location').val(),
			data.online_class_type = $('.online_class_type').val(),
			window.location.href = "<?php echo URL::to('/studiomanager/'); ?>/faculty-early-delay-report-excel?" + Object.keys(data).map(function (k) {
				return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
			}).join('&');
		});
					
	
</script>

@endsection
