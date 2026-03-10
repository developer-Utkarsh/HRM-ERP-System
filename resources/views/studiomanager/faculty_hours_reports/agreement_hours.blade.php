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
						<h2 class="content-header-title float-left mb-0">Faculty Agreement Hours</h2>
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
								<form action="{{ route('studiomanager.faculty-agreement-hours') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-2 branch_loader">
											<label for="users-list-status">Location</label>
											<fieldset class="form-group">												
												<select class="form-control branch_location" name="branch_location" id="" required>
													<option value="">Select Any</option>
													<option value="jaipur" @if('jaipur' == app('request')->input('branch_location')) selected="selected" @endif>jaipur</option>
													<option value="jodhpur" @if('jodhpur' == app('request')->input('branch_location')) selected="selected" @endif>jodhpur</option>
													<option value="prayagraj" @if('prayagraj' == app('request')->input('branch_location')) selected="selected" @endif>prayagraj</option>
													<option value="indore" @if('indore' == app('request')->input('branch_location')) selected="selected" @endif>indore</option>
												</select>
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-4">
											<label for="users-list-status">Faculty</label>
											<?php $faculty = \App\User::where('role_id', '2')->where('status', '1')->where('is_deleted', '0')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2 faculty_id" name="faculty_id[]" multiple>
													<option value="">Select Any</option>
													@if(count($faculty) > 0)
													@foreach($faculty as $key => $value)
													<option value="{{ $value->id }}"  <?php if(!empty( app('request')->input('faculty_id')) && in_array($value->id, app('request')->input('faculty_id'))){ echo "selected"; } ?>>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>																				
										
										<div class="col-12 col-sm-6 col-lg-2">											
											<label for="users-list-status">Month</label>								
											<fieldset class="form-group">															
												<input type="month" name="fmonth" placeholder="Month" value="" class="form-control fmonth">	
											</fieldset>	
										</div>	
										<div class="col-12 col-sm-6 col-lg-2">											
											<label for="users-list-status">From Date</label>								
											<fieldset class="form-group">																					
												<input type="date" name="fdate" placeholder="Date" value="{{ app('request')->input('fdate') }}" class="form-control fdate">	
											</fieldset>	
										</div>	
										
										<div class="col-12 col-sm-6 col-lg-2">											
											<label for="users-list-status">To Date</label>									
											<fieldset class="form-group">																					
												<input type="date" name="tdate" placeholder="Date" value="{{ app('request')->input('tdate') }}" class="form-control tdate">	
											</fieldset>									
										</div>
										
										<!--
										<div class="col-12 col-sm-6 col-lg-2">											
											<label for="users-list-status">Year</label>									
											<fieldset class="form-group">																					
												<input type="year" name="tdate" placeholder="Date" value="{{ app('request')->input('tdate') }}" class="form-control tdate">	
											</fieldset>									
										</div>	
										-->
											
									</div>
									
									<fieldset class="form-group" style="float:right;">		
										<button type="submit" class="btn btn-primary">Search</button>
										<a href="{{ route('studiomanager.faculty-agreement-hours') }}" class="btn btn-warning">Reset</a>
										<!--<a href="javascript:void(0)" id="download_pdf" class="btn btn-primary">Export in PDF</a>-->
										<a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a>
									</fieldset>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
				
					<table class="table data-list-view">
						<thead>
							<tr>
								<th scope="col">S No.</th>
								<th scope="col">Name</th>
								<th scope="col">Contact No</th>
								<th scope="col">Agreement Hour</th>
								<th scope="col">Schedule Hour</th>
								<th scope="col">Spent Hour</th>
								<th scope="col">Offline Spent Hour</th>
								<th scope="col">Online Spent Hour</th>
								<!--th scope="col">From Date</th>
								<th scope="col">To Date</th-->
							</tr>
						</thead>
						<tbody>
							@if(count($get_faculty) > 0)
							@php $s_no = 0; @endphp
								@foreach($get_faculty as $key=>$get_faculty_val)
								<?php
									// $f_date = date('Y-m-d'); $t_date = date('Y-m-d');
									// if(!empty($selectFromDate)){
										// $f_date = $selectFromDate;
									// }
									// if(!empty($selectToDate)){
										// $t_date = $selectToDate;
									// }
									/*$get_total_time = DB::table('timetables')
													->select('start_classes.start_time','start_classes.end_time','subject.name')
													->leftJoin('start_classes', 'start_classes.timetable_id', '=', 'timetables.id')
													->leftJoin('subject', 'subject.id', '=', 'timetables.subject_id')
													->where('timetables.faculty_id', $get_faculty_val->id)
													->whereRaw(' timetables.cdate >= "'.$f_date.'" AND timetables.cdate <= "'.$t_date.'"')
													->get();
									//echo "<pre>"; print_r($get_total_time); die;					
									$duration           = "00 : 00 Hours"; 
									$schedule_duration  = "00 : 00 Hours"; 
									$base_time          = new DateTime('00:00');
									$total              = new DateTime('00:00');
									$subject_arr        = array();
									if(count($get_total_time) > 0){
										foreach($get_total_time as $get_total_time_value){
											array_push($subject_arr, $get_total_time_value->name);
											$first_time = new DateTime($get_total_time_value->start_time);
											$second_time = new DateTime($get_total_time_value->end_time);
											$interval = $first_time->diff($second_time);
											$duration = $interval->format('%H : %I Hours');
											$base_time->add($interval); 
										}
									}*/

									$whereCond  	= ' 1=1';
									//$monCondition 	= " (MONTH(timetables.cdate) = $mt and YEAR(timetables.cdate) = $yr)";
									
									// if(!empty($branch_location)){
										// $whereCond .= ' AND branches.branch_location = "'.$branch_location.'"';
									// }
									
									$get_total_time = DB::table('timetables')
													->select('timetables.from_time as start_time','timetables.to_time as end_time','subject.name','start_classes.start_time as start_classes_start_time','start_classes.end_time as start_classes_end_time','timetables.online_class_type')
													->leftJoin('subject', 'subject.id', '=', 'timetables.subject_id')
													->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
													->leftJoin('branches', 'branches.id', '=', 'studios.branch_id')
													->leftJoin('start_classes', 'start_classes.timetable_id', '=', 'timetables.id')
													->where('timetables.faculty_id', $get_faculty_val->id)
													->where('timetables.time_table_parent_id', '0')
													->where('timetables.is_deleted', '0')
													->where('timetables.online_class_type', '!=', 'Test')
													->where('branches.branch_location', $branch_location)
													->whereRaw($whereCond);
													//->whereRaw($monCondition);
													
									if (!empty($selectFromDate) && !empty($selectToDate)) {
										$get_total_time->whereRaw('timetables.cdate >= ? AND timetables.cdate <= ?', [$selectFromDate, $selectToDate]);
									}
									
									if (!empty($year_wise_month)) {
										$get_total_time->whereRaw('MONTH(timetables.cdate) = ? AND YEAR(timetables.cdate) = ?', [$mt, $yr]);
									}
			
													
									$get_total_time= $get_total_time->get();
													
												
									$base_time4          = new DateTime('00:00');
									$base_time3          = new DateTime('00:00');
									$base_time2          = new DateTime('00:00');
									$base_time          = new DateTime('00:00');
									$total              = new DateTime('00:00');
									$total2              = new DateTime('00:00');
									$subject_arr        = array();
									$schedule_total_tt           = "00 : 00 Hours"; 
									$total_tt           = "00 : 00 Hours"; 
									if(count($get_total_time) > 0){
										foreach($get_total_time as $get_total_time_value){
											array_push($subject_arr, $get_total_time_value->name);
											$first_time = new DateTime($get_total_time_value->start_time);
											$second_time = new DateTime($get_total_time_value->end_time);
											$interval = $first_time->diff($second_time);
											$base_time->add($interval);
											
											
											$first_date = new DateTime($get_total_time_value->start_classes_start_time);
											$second_date = new DateTime($get_total_time_value->start_classes_end_time);
											$interval = $first_date->diff($second_date);
											$base_time2->add($interval);

											if($get_total_time_value->online_class_type=='Offline' || $get_total_time_value->online_class_type=='Offline & App live'){
												$base_time3->add($interval);
											}
											else{
												$base_time4->add($interval);
											}
										}
										
										$baseDays = $total->diff($base_time)->format("%a");
										$baseHours = $total->diff($base_time)->format("%H");
										$baseMinute = $total->diff($base_time)->format("%I");
										
										$schedule_total_tt = ($baseDays*24)+$baseHours. ":" . $baseMinute;
										
										$totalDays = $total2->diff($base_time2)->format("%a");
										$totalHours = $total2->diff($base_time2)->format("%H");
										$totalMinute = $total2->diff($base_time2)->format("%I");
										
										$total_tt = ($totalDays*24)+$totalHours. ":" . $totalMinute;
										
										
										$totalOfflineDays = $total2->diff($base_time3)->format("%a");
										$totalOfflineHours = $total2->diff($base_time3)->format("%H");
										$totalOfflineMinute = $total2->diff($base_time3)->format("%I");										
										$total_Offline_tt = ($totalOfflineDays*24)+$totalOfflineHours. ":" . $totalOfflineMinute;
										
										$totalOnlineDays = $total2->diff($base_time4)->format("%a");
										$totalOnlineHours = $total2->diff($base_time4)->format("%H");
										$totalOnlineMinute = $total2->diff($base_time4)->format("%I");										
										$total_Online_tt = ($totalOnlineDays*24)+$totalOnlineHours. ":" . $totalOnlineMinute;
									}
									
									
									if(count($get_total_time) > 0){
										$s_no++;
									?>
									<tr>
										<td class="product-category">{{ $s_no }}</td>
										<td class="product-category">{{ $get_faculty_val->name }}</td>
										<td class="product-category">{{ $get_faculty_val->mobile }}</td>
										<td class="product-category">{{ $get_faculty_val->committed_hours }} </td>
										<!--td class="product-category">{{ $total->diff($base_time)->format("%H:%I") }} </td-->
										<td class="product-category">{{ $schedule_total_tt }} </td>
										<td class="product-category">{{ $total_tt }} </td>
										<td class="product-category">{{ $total_Offline_tt }} </td>
										<td class="product-category">{{ $total_Online_tt }} </td>
									</tr>
									<?php
									}
								?>
								@endforeach
							@else
								<tr>
									<td class="text-center" colspan="9">No Record Found</td>
								</tr>								
							@endif
						</tbody>
					</table>
					 
				</div>       
				
			</section>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		 
		$('.select-multiple2').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
	
	$("body").on("click", "#download_excel", function (e) {
		var data = {};
		data.faculty_id = $("[name='faculty_id[]']").val(),
		// data.fdate = $('.fdate').val(),
		// data.tdate = $('.tdate').val(),
		data.year_wise_month    = $('.fmonth').val(), 
		data.branch_location = $('.branch_location').val(),
		window.location.href = "<?php echo URL::to('/studiomanager/'); ?>/faculty-agreement-hours-report-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});
	
	$("body").on("click", "#download_pdf", function (e) {
		var data = {}; 
		data.faculty_id = $("[name='faculty_id[]']").val(),
		data.fdate = $('.fdate').val(),
		data.tdate = $('.tdate').val(),
		window.location.href = "<?php echo URL::to('/studiomanager/'); ?>/faculty-hours-report-pdf?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});
</script>
@endsection
