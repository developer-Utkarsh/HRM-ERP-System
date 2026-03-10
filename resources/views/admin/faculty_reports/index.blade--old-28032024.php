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
						<h2 class="content-header-title float-left mb-0">Faculty Report</h2>
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
								<form action="{{ route('admin.faculty-reports') }}" method="get" name="filtersubmit">
									<div class="row">
										<?php if( Auth::user()->role_id != 3){ ?>
										<div class="col-12 col-sm-6 col-lg-3 branch_loader">
											<label for="users-list-status">Location</label>
											<fieldset class="form-group">												
												<select class="form-control branch_location" name="branch_location" id="">
													<option value="">Select Any</option>
													<option value="jaipur" @if('jaipur' == app('request')->input('branch_location')) selected="selected" @endif>jaipur</option>
													<option value="jodhpur" @if('jodhpur' == app('request')->input('branch_location')) selected="selected" @endif>jodhpur</option>
													<option value="prayagraj" @if('prayagraj' == app('request')->input('branch_location')) selected="selected" @endif>prayagraj</option>
													<option value="indore" @if('indore' == app('request')->input('branch_location')) selected="selected" @endif>indore</option>
												</select>
											</fieldset>
										</div>
										
										<div class="col-12 col-sm-6 col-lg-3 branch_loader">
											<label for="users-list-status">Branch</label>
											<?php $branches = \App\Branch::where('status', '1')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 branch_id" name="branch_id" id="">
													<option value="">Select Any</option>
													@if(count($branches) > 0)
													@foreach($branches as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('branch_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>
												<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
											</fieldset>
										</div>
										<?php } ?>
										
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
										
										<div class="col-12 col-md-3">
											<label for="users-list-status">Batch</label>
											<?php $batchs = \App\Batch::where('status', '1')->where('is_deleted', '0')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2 batch_id" name="batch_id">
													<option value="">Select Any</option>
													@if(count($batchs) > 0)
													@foreach($batchs as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('batch_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										
										<div class="col-12 col-md-3">
											<label for="users-list-status">Assistant</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple3 select_assistant_id" name="assistant_id">
													<option value="">Select Any</option>
													@if(count($get_assistant) > 0)
													@foreach($get_assistant as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('assistant_id')) selected="selected" @endif>{{ $value->name }} ({{ $value->register_id }})</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										
										<div class="col-12 col-md-3">
											<label for="users-list-status">Status</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2 status" name="status">
													<option value="">Select Status</option>
													<option value="all" @if('all' == app('request')->input('status')) selected="selected" @endif>All</option>
													<option value="cancel" @if('cancel' == app('request')->input('status')) selected="selected" @endif>Cancelled Classes</option>
													<option value="not_fill_spent_time" @if('not_fill_spent_time' == app('request')->input('status')) selected="selected" @endif>Not Fill Spent Time</option>
												</select>												
											</fieldset>
										</div>
										
										<div class="col-12 col-sm-6 col-lg-3">											
											<label for="users-list-status">From Date</label>								
											<fieldset class="form-group">																					
												<input type="date" name="fdate"  placeholder="DD-MM-YYYY" value="{{ app('request')->input('fdate') }}" class="form-control StartDateClass fdate" >	
											</fieldset>	
										</div>	
										
										<div class="col-12 col-sm-6 col-lg-3" style="display:;" >
											<label for="users-list-status">To Date</label>
											<fieldset class="form-group">
												<input type="date" name="tdate" placeholder="DD-MM-YYYY" value="{{ app('request')->input('tdate') }}" class="form-control EndDateClass tdate">
											</fieldset>									
										</div>										
											
									</div>
									
									<fieldset class="form-group" style="float:right;">		
										<button type="submit" class="btn btn-primary">Search</button>
										<a href="{{ route('admin.faculty-reports') }}" class="btn btn-warning">Reset</a>
										<?php if( Auth::user()->role_id != 3){ ?>
										<a href="javascript:void(0)" id="download_pdf" class="btn btn-primary">Export in PDF</a>
										<?php } ?>
									</fieldset>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
				
				<?php 
				$final_total_schedule = new DateTime('00:00');
				$final_total_schedule_last = new DateTime('00:00');
				
				$final_total_spent = new DateTime('00:00');
				$final_total_spent_last = new DateTime('00:00');
				
				
				if (count($get_faculty) > 0) {
					foreach ($get_faculty as $key2=>$get_faculty_value) {
						$total_cancel_class = 0;
				?>
					<table class="table data-list-view" style=''>
					 
						<head>
							<tr style="">
								<th colspan="11"><b>Faculty Name : <?php echo isset($get_faculty_value->faculty_name)?$get_faculty_value->faculty_name:''; ?></b> 
									&nbsp;&nbsp;&nbsp;&nbsp; 
									<?php
									$driver_data = DB::table('driver_faculties')
												->select('users.name','users.mobile','driver_faculties.*')
												->leftJoin('users', 'users.id', '=', 'driver_faculties.driver_id')
												->where('driver_faculties.faculty_id',$get_faculty_value->faculty_id)
												->whereRaw("DATE(driver_faculties.assign_date) = '$selectFromDate' AND driver_faculties.driver_id IS NOT NULL")
												->get();
									if(count($driver_data) > 0){
										foreach($driver_data as $d_detail){
											?>
											<?php if( Auth::user()->role_id != 3 && Auth::user()->id != 1172){ ?>
											<button class="btn btn-sm btn-success assigned_driver" data-assign-date="<?=$selectFromDate?>" data-faculty-id="<?=$get_faculty_value->faculty_id?>" data-driver-id="<?=$d_detail->driver_id?>">Assigned Driver ( <?=$d_detail->name?> - <?=$d_detail->mobile?>)</button>
											<?php
											}
										}
									}
									else{
									?>
										<?php if( Auth::user()->role_id != 3 && Auth::user()->id != 1172){ ?>
										<a href="javascript:void(0);" class="btn btn-sm btn-warning assign_driver" data-assign-date="<?=$selectFromDate?>" data-faculty-id="<?=$get_faculty_value->faculty_id?>">Driver Assign</a>
										<?php } ?>
									<?php
									}
									?>
								</th>
								<!--th colspan="3"><b>Assistant Name : 
								<?php //echo isset($get_faculty_value->assistant_name) ?  $get_faculty_value->assistant_name : ''; ?>
								</b></th-->
							</tr>
						</head>
						<head>
							<tr style="">
								<th scope="col">From Time</th>
								<th scope="col">To Time</th>
								<th scope="col">Date</th>
								<th scope="col">Branch Name</th>
								<th scope="col">Batch Name</th>
								<th scope="col">Subject Name</th>
								<th scope="col">Assistant Name</th>
								<th scope="col">Schedule Time</th>
								<th scope="col">Spent Time</th>
								<th scope="col">Topic</th>
								<th scope="col">Action</th>
							</tr>
						</head>
						<body>
							<?php
							$whereCond = '1=1';;
							/* if(!empty($selectFromDate) && !empty($selectToDate)){
									$whereCond .= ' AND timetables.cdate >= "'.$selectFromDate.'" AND timetables.cdate <= "'.$selectToDate.'"';
							} */
							if(!empty($selectFromDate) && !empty($selectToDate)){
								$whereCond .= ' AND timetables.cdate >= "'.$selectFromDate.'" AND timetables.cdate <= "'.$selectToDate.'"';
							}
							else{
								$whereCond .= ' AND timetables.cdate >= "'.date('Y-m-d').'" AND timetables.cdate <= "'.date('Y-m-d') .'"';
							}	
							
							$base_time = new DateTime('00:00');
							$total     = new DateTime('00:00');
							
							$total_schedule = new DateTime('00:00');
							$total_base_schedule = new DateTime('00:00');
							$total_base_cancel = new DateTime('00:00');
							
							// $whereCond .= ' AND timetables.assistant_id = "'.$get_faculty_value->assistant_id.'"';
							if(!empty($get_faculty_value->faculty_name)){
							$get_faculty_timetable = DB::table('timetables')
													  ->select('timetables.*','studios.name as studios_name','branches.name as branches_name','branches.id as branches_id','batch.name as batch_name','course.name as course_name','subject.name as subject_name','chapter.name as chapter_name','start_classes.status as start_classes_status','start_classes.start_time as start_classes_start_time','start_classes.end_time as start_classes_end_time','start_classes.topic_name','users_assistant.name as assistant_name','users_assistant.mobile as assistant_mobile')
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
													  ->where('timetables.is_publish', '1')
													  ->whereRaw("(timetables.is_deleted='0' OR timetables.is_deleted='2')");
													  
							if(!empty($status) && $status == 'cancel'){
								$get_faculty_timetable->where('timetables.is_cancel', '1');
							}
							else if(!empty($status) && $status == 'not_fill_spent_time'){
								$get_faculty_timetable->whereRaw('start_classes.id is null');
							}
							if(!empty(Auth::user()->id) &&  Auth::user()->user_details->degination == "CENTER HEAD"){
								$user_branch_id = Auth::user()->user_branches[0]->branch_id;
								$get_faculty_timetable->whereIn('timetables.studio_id',$studio_arr);
							}
							else if(!empty(Auth::user()->id) &&  Auth::user()->user_details->degination == "STUDIO INCHARGE"){
								$user_branch_id = Auth::user()->user_branches[0]->branch_id;
								$get_faculty_timetable->whereIn('timetables.studio_id',$studio_arr);
							}
							else if(Auth::user()->role_id == 3){
								$get_faculty_timetable->where('timetables.assistant_id', Auth::user()->id);
							}
							if(!empty(app('request')->input('batch_id'))){
								$get_faculty_timetable->where('batch.id', app('request')->input('batch_id'));
							}
							if(!empty(app('request')->input('branch_id'))){
								$get_faculty_timetable->where('studios.branch_id', app('request')->input('branch_id'));
							}
							if(!empty(app('request')->input('assistant_id'))){
								$get_faculty_timetable->where('timetables.assistant_id', app('request')->input('assistant_id'));
							}
							$get_faculty_timetable = $get_faculty_timetable->whereRaw($whereCond)
													  ->orderBy('timetables.cdate', 'ASC')
													  ->orderBy('timetables.from_time', 'ASC')
													  ->get();
													  
													  //echo "<pre>"; print_r($get_faculty_timetable); die;
							$duration  = "00 : 00 Hours"; 
							$schedule_duration  = "00 : 00 Hours"; 
							
							
							if(count($get_faculty_timetable) > 0){ 
							foreach($get_faculty_timetable as $key => $get_faculty_timetable_value){
								
								$from_time         = new DateTime($get_faculty_timetable_value->from_time);
								$to_time           = new DateTime($get_faculty_timetable_value->to_time);
								$schedule_interval = $from_time->diff($to_time);
								$schedule_duration = $schedule_interval->format('%H : %I Hours');
								$total_base_schedule->add($schedule_interval); 
								$final_total_schedule->add($schedule_interval);
								
								if($get_faculty_timetable_value->is_cancel != '1'){
									$first_date = new DateTime($get_faculty_timetable_value->start_classes_start_time);
									$second_date = new DateTime($get_faculty_timetable_value->start_classes_end_time);
									$interval = $first_date->diff($second_date);
									$duration = $interval->format('%H : %I Hours');
									$base_time->add($interval);
									$final_total_spent->add($interval);
								}
								else{
									$total_cancel_class++;
									$duration = 'Cancelled Classes';
									
									$total_base_cancel->add($schedule_interval); 
								}
								
								
								
								 
								
							?>
								<tr>
									<td><?php echo isset($get_faculty_timetable_value->from_time) ?  date("h:i A", strtotime($get_faculty_timetable_value->from_time)) : '' ?></td>
									<td><?php echo isset($get_faculty_timetable_value->to_time) ?  date("h:i A", strtotime($get_faculty_timetable_value->to_time)) : '' ?></td>
									<td><?php echo isset($get_faculty_timetable_value->cdate) ?  date('d-m-Y',strtotime($get_faculty_timetable_value->cdate)) : '' ?></td>
									<td><?php echo isset($get_faculty_timetable_value->branches_name) ?  $get_faculty_timetable_value->branches_name : '' ?>
									<?php
									if(!empty($get_faculty_timetable_value->branches_id)){
										$get_data = DB::table('users')
										->leftJoin('userbranches','users.id','=','userbranches.user_id')
										->leftJoin('userdetails','users.id','=','userdetails.user_id')
										->select('users.name as user_name','users.mobile as mobile')
										->where('userbranches.branch_id',$get_faculty_timetable_value->branches_id)
										->where('userdetails.degination','CENTER HEAD')
										->where('users.status',1)
										->get();
										$center_heads = "";
										if(count($get_data) > 0){
											foreach($get_data as $center_data){
												$center_heads .= $center_data->user_name."( ".$center_data->mobile." ) ,";
											}
											echo "<b>CH.-</b> ".rtrim($center_heads,',');
										}
									}
									
									?>
									</td>
									<td>
									<?php 
									$get_batches_name = "";
									$get_batches = DB::table('timetables')->select("batch.name as b_name")->leftJoin('batch','batch.id','=','timetables.batch_id')->where('timetables.is_deleted', '0')->where('timetables.time_table_parent_id', $get_faculty_timetable_value->id)->get();
									if(count($get_batches) > 0){
										foreach($get_batches as $vallll){
											$get_batches_name .= ', '.$vallll->b_name;
										}
									}
									echo isset($get_faculty_timetable_value->batch_name) ?  $get_faculty_timetable_value->batch_name : '';
									echo $get_batches_name;
									?>
									</td>
									<td><?php echo isset($get_faculty_timetable_value->subject_name) ?  $get_faculty_timetable_value->subject_name : '' ?></td>									
									<td><?php echo isset($get_faculty_timetable_value->assistant_name) ?  $get_faculty_timetable_value->assistant_name : '' ?>
									( <?php echo isset($get_faculty_timetable_value->assistant_mobile) ?  $get_faculty_timetable_value->assistant_mobile : '' ?> )</td> 
									
									
									
									<td><?=$schedule_duration?></td>
									<td class="spd_time{{$key2}}{{$key}}"><?=$duration?></td>
									<td class=""><?=$get_faculty_timetable_value->topic_name?></td>
									<td data-id="{{ $get_faculty_timetable_value->id }}">
										<?php
										if(strtotime(date('Y-m-d')) <= strtotime($get_faculty_timetable_value->cdate.'+1 day') || Auth::user()->user_details->degination == 'PRODUCTION INCHARGE' || Auth::user()->user_details->degination == 'CENTER HEAD' || Auth::user()->id == 5760){
											if(Auth::user()->id != 1172){
										?>
											<a href="javascript:void(0);" data-toggle="modal" data-id="{{ $get_faculty_timetable_value->id }}" data-spent-id="{{$key2}}{{$key}}" class="btn btn-sm btn-outline-primary get_start_class_data"><span class="action-edit"><i class="feather icon-edit"></i></span></a>
										<?php
											}
										}
										?>
									</td>
								</tr>
							<?php
							}
							}
							}
							?>
							
							<tr>
								<td colspan="3"><b>Total Schedule Time:</b> 
								<?php
								$totalDays = $total_schedule->diff($total_base_schedule)->format("%a");
								$totalHours = $total_schedule->diff($total_base_schedule)->format("%H");
								$totalMinute = $total_schedule->diff($total_base_schedule)->format("%I");
								echo ($totalDays*24)+$totalHours. ":" . $totalMinute;
								?> Hours
								</td> 
								<td colspan="3"><b>Total Spent Time:</b> 
								<?php
								$baseDays = $total->diff($base_time)->format("%a");
								$baseHours = $total->diff($base_time)->format("%H");
								$baseMinute = $total->diff($base_time)->format("%I");
								echo ($baseDays*24)+$baseHours. ":" . $baseMinute;
								?> 
								Hours</td>
								<td colspan="5"><b>Total Cancel Class:</b> 
								<strong style="background: red;border-radius: 50%;padding: 6px;color: #fff;"><?=$total_cancel_class?></strong>
								<br/>
								<b>Total Cancel Time:</b>
								<?php
								$totalDays = $total_schedule->diff($total_base_cancel)->format("%a");
								$totalHours = $total_schedule->diff($total_base_cancel)->format("%H");
								$totalMinute = $total_schedule->diff($total_base_cancel)->format("%I");
								echo ($totalDays*24)+$totalHours. ":" . $totalMinute;
								?>Hours
								</td> 								
							</tr>
								
						</body>
					
					</table>
					<p><hr/></p>
					
				<?php 
					}
				}
				?>
				<p> <b>
					<?php
					$totalDays = $final_total_schedule_last->diff($final_total_schedule)->format("%a");
					$totalHours = $final_total_schedule_last->diff($final_total_schedule)->format("%H");
					$totalMinute = $final_total_schedule_last->diff($final_total_schedule)->format("%I");
					?>
					Search According Total Schedule Time: <?php echo ($totalDays*24)+$totalHours. ":" . $totalMinute;?> Hours 
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
					
					<?php
					$totalDays = $final_total_spent_last->diff($final_total_spent)->format("%a");
					$totalHours = $final_total_spent_last->diff($final_total_spent)->format("%H");
					$totalMinute = $final_total_spent_last->diff($final_total_spent)->format("%I");
					?>
					
					Search According Total Spent Time: <?php echo ($totalDays*24)+$totalHours. ":" . $totalMinute;?> Hours </b></p>
		<style>
		hr{background:#000;}
		</style>
					 
				</div>       
				
			</section>
		</div>
	</div>
</div>


<div id="s_class" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form method="post" id="submit_start_class_form">
				<div class="modal-header">
					<h5 class="modal-title">Timetable - <span class="subject_name_set"></span></h5>
				</div>
				<div class="modal-body">
					<div class="form-body">
						<div class="row pt-2 htmlset">
						</div>
						<div class="row pt-2">
							
							@if(Auth::user()->user_details->degination == "CENTER HEAD")
								@php
								$user_branch_id = Auth::user()->user_branches[0]->branch_id;
								
								$userdeatils = \App\Userbranches::with([
									'user' => function($q){
										$q->where('role_id', '3')->where('status', '1')->where('is_deleted', '0');
									}
								]);

								$userdeatils->WhereHas('user', function ($q) {
												 $q->where('role_id', '3')->where('status', '1')->where('is_deleted', '0');
											});
					
								$userdeatils = $userdeatils->where('branch_id', $user_branch_id)->get();					
								@endphp
								
								<div class="col-md-12 col-12">
									<div class="form-label-group">												
										<select class="form-control select-multiple4 assistant_id" name="assistant_id" required>
											<option value="">Select Assistant</option>
											@if(count($userdeatils) > 0)
												@foreach($userdeatils as $key => $value)
												<option value="{{ $value->user->id }}">{{$value->user->name }}</option>
												@endforeach
											@endif
										</select>												
									</div>
								</div>
										
							@endif
							
							<div class="col-md-6 col-12">
								<div class="form-label-group">
									<input type="text" class="form-control timepicker start_time" placeholder="Start Time" name="start_time" autocomplete="off">
									<label for="first-name-column">Start Time</label>
								</div>
							</div>
							
							<div class="col-md-6 col-12">
								<div class="form-label-group">
									<input type="text" class="form-control timepicker end_time" placeholder="End Time" name="end_time" autocomplete="off">
									<label for="first-name-column">End Time</label>
								</div>
							</div>
							<div class="col-md-12 col-12">
								<div class="form-label-group">
									<strong>Total Time (H:M) : <span class="total_time"></span> </strong>
								</div>
							</div>
							<div class="col-md-12 col-12">
								<div class="form-label-group">
									<input type="text" class="form-control remark" placeholder="Remark" name="remark" autocomplete="off" >
									<label for="first-name-column">Remark</label>
								</div>
							</div>

							<div class="col-md-12 col-12 div_delay d-none">
								<div class="form-label-group">
									<label for="first-name-column">Delay Type</label>
								    <select type="text" class="form-control delay_type" name="delay_type">
								    	<option value="">Select Delay Type</option>
								    	<option value="Technical Issue">Due to Technical</option>
								    	<option value="Due to Faculty">Due to Faculty</option>
								    	<option value="Due to Managment">Due to Managment</option>
								    </select>
								</div>
							</div>

							<div class="col-md-12 col-12 div_early_delay d-none">
								<div class="form-label-group">
									<input type="text" class="form-control early_delay_reason" placeholder="Early/Delay Reason" name="early_delay_reason" autocomplete="off" >
									<label for="first-name-column">Early/Delay Reason</label>
								</div>
							</div>
						</div>
					</div>
				</div>
				<input type="hidden" name="timetable_id" class="timetable_id" value="">
				<input type="hidden" name="spent_id" class="spent_id" value="">
				<div class="modal-footer" style="display: block;">
					<div class="row">
						<div class="col-md-3">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
						<div class="col-md-5">
						<button type="button" class="btn btn-warning cancel-cls float-right" >Cancel Class</button>
						</div>
						<div class="col-md-4">
						<button type="submit" id="start_class_btn" class="btn btn-primary float-right">Submit</button>
						</div>
					</div>
					
					
					
				</div>
			</form>
		</div>
	</div>
</div>

<div id="myModal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="post" id="submit_assign_driver_form">
				<input type="hidden" name="faculty_id" class="faculty_id">
				<input type="hidden" name="assign_date" class="assign_date">
				<div class="modal-header">
					<h5 class="modal-title">Assign Driver</h5>
				</div>
				<div class="modal-body">
					<div class="form-body">
						<div class="row pt-2">
							<div class="col-md-12 col-12">
								<div class="form-label-group">
									<select class="form-control driver_id" name="driver_id">
										<option value=""> - Select Driver - </option>
										<option value="0"> Remove Driver </option>
										@if(count($drivers) > 0)
											@foreach($drivers as $value)
												<option value="{{ $value->id }}">{{ $value->name }}</option>
											@endforeach
										@endif
									</select>
									<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
								</div>
							</div>
							
							 
						</div>
					</div>
				</div>
				<input type="hidden" name="timetable_id" class="timetable_id" value="">
				
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					<button type="submit" id="start_class_btn" class="btn btn-primary">Submit</button>
				</div>
			</form>
		</div>
	</div>
</div>


				
@endsection

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<link href="{{ asset('laravel/public/css/jquery.timepicker.css') }}" rel="stylesheet"/>
<script src="{{ asset('laravel/public/js/jquery.timepicker.js') }}"></script>
<script src="{{ asset('laravel/public/admin/js/jquery.validate.min.js') }}"></script>


<script type="text/javascript">
	$("body").on("change", ".start_time,.end_time", function (e) {
		var start_time = $('.start_time').val();
		var end_time = $('.end_time').val();
		var str0="01/01/1970 " + start_time;
		var str1="01/01/1970 " + end_time;

		var diff=(Date.parse(str1)-Date.parse(str0))/1000/60;
		var hours=String(100+Math.floor(diff/60)).substr(1);
		var mins=String(100+diff%60).substr(1);
		$(".total_time").text(hours+':'+mins);

		if($(".total_time").text()!=$(".total_time").attr("data-time")){
			$(".div_early_delay").removeClass("d-none");
			if(start_time!=$(".start_time").attr("data-time")){
				$(".div_delay").removeClass("d-none");
				$(".early_delay_reason").attr("required",true);
				$(".delay_type").attr("required",true);
			}
		}else{
			$(".div_delay").addClass("d-none");
			$(".div_early_delay").addClass("d-none");
			$(".early_delay_reason").attr("required",false);
			$(".delay_type").attr("required",false);
		}
	});
</script>
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
		$('.select-multiple4').select2({
			width: '100%',
			placeholder: "Select Any",
			allowClear: true
		});
		
		$('.timepicker').timepicker({ 'step': 1, 'timeFormat': 'h:i A' });
	});
	
	
					
	
	$("body").on("click", "#download_pdf", function (e) {
		/* if ($userTable.data().count() == 0) {
			swal("Warning!", "Not have any data!", "warning");
			return;
		} */
		var data = {};
			//data.studio_id = $('.studio_id').val(),
			data.branch_id = $('.branch_id').val(),
			data.branch_location = $('.branch_location').val(),
			data.faculty_id = $('.faculty_id').val(),
			data.fdate = $('.fdate').val(),
			data.tdate = $('.tdate').val(),
			data.batch_id = $('.batch_id').val(), 
			data.status = $('.status').val(),
			data.assistant_id = $('.select_assistant_id').val(),
			window.open("<?php echo URL::to('/admin/'); ?>/faculty-report-pdf?" + Object.keys(data).map(function (k) {
				return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
			}).join('&'));
		/* window.location.href = "<?php echo URL::to('/admin/'); ?>/faculty-report-pdf?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&'); */
	});
</script>
<script type="text/javascript">

	$(".cancel-cls").on("click", function(){
		var remark = $(".remark").val();
		var early_delay_reason = $(".early_delay_reason").val();
		var start_time = $(".start_time").val();
		var end_time = $(".end_time").val();
		var tt_id = $(".timetable_id").val();
		var spent_id = $(".spent_id").val();
		if(remark != ''){
			 $.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},      
				type: "POST",
				url : '{{ route('admin.update-cancel-class') }}',
				data : {'_token' : '{{ csrf_token() }}', 'remark': remark,'early_delay_reason': early_delay_reason, 'start_time': start_time, 'end_time': end_time, 'timetable_id': tt_id, 'spent_id': spent_id},
				dataType : 'json',
				success : function(data){
					if(data.status == false){
						swal("Error!", data.message, "error");
						
					} else if(data.status == true){
						$('.spd_time'+data.spent_id).text(data.spd_time);
						$('#submit_start_class_form').trigger("reset");	
						$('#s_class').modal('hide');
						swal("Done!", data.message, "success").then(function(){ 
							// location.reload();
						});
					}
				}
			});
		
		}
		else{
			alert("Remark are required")
		}
	});
	
	$(".get_start_class_data").on("click", function() {
		$(".timetable_id").val('');
		$(".start_time").val('');
		$(".end_time").val('');
		$(".topic_name").val('');
		$(".remark").val('');
		$(".early_delay_reason").val('');
		var tt_id = $(this).attr("data-id");
		var spnt_id = $(this).attr("data-spent-id");
		$(".timetable_id").val(tt_id);
		$(".spent_id").val(spnt_id);
		
		
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},      
			type: "POST",
			url : '{{ route('admin.edit-start-class') }}',
			data : {'_token' : '{{ csrf_token() }}', 'tt_id': tt_id},
			//processData : false, 
			//contentType : false,
			dataType : 'json',
			success : function(data){
				if(data.status == false){
					swal("Error!", data.message, "error");
				} else if(data.status == true){
					$(".total_time").text(data.total_spent_time);
					$(".total_time").attr("data-time",data.total_spent_time);
					
					if(data.start_time != ''){
						$('.start_time').val(data.start_time);
						$(".start_time").attr("data-time",data.start_time);
					}

					if(data.end_time != ''){
						$('.end_time').val(data.end_time);
					}
					if(data.topic_name != ''){
						$('.topic_name').val(data.topic_name);
					}
					if(data.remark != ''){
						$('.remark').val(data.remark);
					}
					if(data.early_delay_reason != ''){
						$('.early_delay_reason').val(data.early_delay_reason);
					}
					if(data.html != ''){
						$('.htmlset').html(data.html);
					}
					if(data.subject_name != ''){
						$('.subject_name_set').text(data.subject_name);
					}
					if(data.res != ''){
						$('.assistant_id').html('');
						$('.assistant_id').html(data.res);
					}
					
					$('#s_class').modal({
						backdrop: 'static',
						keyboard: true, 
						show: true
					});
				}
			}
		});
	});  

	var $form = $('#submit_start_class_form');
	validatereschedule = $form.validate({
		ignore: [],
		rules: {
			topic_name : {
				required: true,
			},
			start_time : {
				required: true,
			},
			end_time : {
				required: true,
			},         
		},

		/* errorElement : "span",*/
		errorClass : 'border-danger',
		errorPlacement: function(error, element) {
			if (element.is(':input') || element.is(':select')) {
				$(this).addClass('border-danger');
			}
			else {
				return true;
			}
		}
	});
	
	$("#submit_start_class_form").submit(function(e) {
		var form = document.getElementById('submit_start_class_form');
		var dataForm = new FormData(form);
		e.preventDefault();
		if(validatereschedule.valid()){
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},      
				type: "POST",
				url : '{{ route('admin.update-start-class') }}',
				data : dataForm,
				processData : false,  
				contentType : false,
				dataType : 'json',
				success : function(data){
					if(data.status == false){
						swal("Error!", data.message, "error");
					} else if(data.status == true){
						$('.spd_time'+data.spent_id).text(data.spd_time);
						$('#submit_start_class_form').trigger("reset");	
						$('#s_class').modal('hide');
						swal("Done!", data.message, "success").then(function(){ 
							// location.reload();
						});
					}
				}
			});
		}       
	});
	
	$(document).ready(function() {
		var branch_id = $(".branch_id option:selected").attr('value');
		var faculty_id = $(".faculty_id_get").val();
	});
	
	$(".assign_driver").on("click", function() {
		var faculty_id = $(this).attr("data-faculty-id");
		var assign_date = $(this).attr("data-assign-date");
		$(".faculty_id").val(faculty_id);
		$(".assign_date").val(assign_date);
		$('#myModal').modal({
				backdrop: 'static',
				keyboard: true, 
				show: true
		});
	});
	
	$("#submit_assign_driver_form").submit(function(e) {
		var form = document.getElementById('submit_assign_driver_form');
		var dataForm = new FormData(form);
		e.preventDefault();
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},      
				type: "POST",
				url : '{{ route('admin.driver.update_driver') }}',
				data : dataForm,
				processData : false,  
				contentType : false,
				dataType : 'json',
				success : function(data){
					if(data.status == false){
						swal("Error!", data.message, "error");
					} else if(data.status == true){
						// $('#submit_assign_driver_form').trigger("reset");						
						swal("Done!", data.message, "success").then(function(){ 
							location.reload();
						});
					}
				}
			});
	});
	
	$(".assigned_driver").on("click", function() {
		$('#submit_assign_driver_form').trigger("reset");
		var driver_id = $(this).attr("data-driver-id");
		var faculty_id = $(this).attr("data-faculty-id");
		var assign_date = $(this).attr("data-assign-date");
		$(".faculty_id").val(faculty_id);
		$(".assign_date").val(assign_date);
		$(".driver_id").val(driver_id);
		
		$('#myModal').modal({
				backdrop: 'static',
				keyboard: true, 
				show: true
		});
	});
	
	$(document).on("change",".chapter_data", function () {
		var option = $('option:selected', this).attr('data-chname');
		$(this).parents('.row').find('.topic_name').val(option);
	});
	
</script>


<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>$('.dateddmmyyy').datepicker({ dateFormat: 'dd-mm-yy' }).val();


</script>


@endsection
