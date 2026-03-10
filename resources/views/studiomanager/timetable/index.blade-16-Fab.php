@extends('layouts.studiomanager')
<style type="text/css"> 
	.set_time{
		-webkit-transform: rotate(90deg);
		-moz-transform: rotate(90deg);
		-ms-transform: rotate(90deg);
		-o-transform: rotate(90deg);
		transform: rotate(-90deg);
		display: inline-block;
		font-size: 9px;
		margin: -4.7px;
		margin-top: 2px;
	}
	.ui-timepicker-container.ui-timepicker-standard.ui-timepicker-no-scrollbar {
		z-index: 9999999 !important;
	}
	.table thead th {
		border-bottom: 0px solid #f8f8f8 !important;
	}
	.table th, .table td {
		padding: 8px 6px !important;
	}
	.btn-sm, .btn-group-sm > .btn {
		padding: 8px 8px !important;
	}
	.faculty-set{
		/*width: 7%;*/
		/*float: left;*/
		border: 1px solid #ececec;
		padding: 8px 8px;

	}
	.faculty-set p{
		font-size: 12px;
		font-weight: bold;
		text-align: center;
		margin-bottom: 2px;
	}

	.faculty-set span{
		font-size: 8px;
		font-weight: bold;
		width: 100%;
		text-align: center;
		display: inline-block;
	}

	.from-time{
		border-left: 2px solid #7367f0 !important;
	}
	.to-time{
		border-right: 2px solid #7367f0 !important;
	}
</style>
@section('content')
<?php 
$onlinebatch = \App\Batch::where('status', '1')->where('type', 'online')->orderBy('id', 'asc')->get(); 
$offlinebatch = \App\Batch::where('status', '1')->where('type', 'offline')->orderBy('id', 'asc')->get(); 
?>

<select class="form-control online_batches" style="display:none;">
	<option value=""> - Select Batch - </option>
	@if(count($onlinebatch) > 0)
	@foreach($onlinebatch as $value)
	<option value="{{ $value->id }}">{{ $value->name }}</option>
	@endforeach
	@endif
</select>

<select class="form-control offline_batches" style="display:none;">
	<option value=""> - Select Batch - </option>
	@if(count($offlinebatch) > 0)
	@foreach($offlinebatch as $value)
	<option value="{{ $value->id }}">{{ $value->name }}</option>
	@endforeach
	@endif
</select>

<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Time Table</h2>
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
								<form action="{{ route('studiomanager.timetable.index') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-4 col-lg-2">
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


										<div class="col-12 col-sm-4 col-lg-2">
											<label for="users-list-role">Studio</label>
											<fieldset class="form-group">
												<?php $studios = \App\Studio::where('status', '1');
												if(app('request')->input('branch_id')){
													$studios->where('branch_id',app('request')->input('branch_id')); 
												}
												$studios = $studios->orderBy('id','desc')->get();
												?>
												
												<select class="form-control studio_id get_studio select-multiple1" name="studio_id">
													<option value=""> - Select Studio - </option>
													@if(count($studios) > 0)
													@foreach($studios as $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('studio_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
												
											</fieldset>
										</div>

										
										<div class="col-12 col-sm-4 col-lg-2">
											<label for="users-list-status">Faculty</label>
											<fieldset class="form-group">
												<?php $faculty = \App\User::where('role_id', '2')->where('status', '1')->orderBy('id', 'desc')->get(); ?>
												<select class="form-control get_faculty select-multiple2" name="faculty_id">
													<option value=""> - Select Faculty - </option>
													@if(count($faculty) > 0)
													@foreach($faculty as $value)
													<option value="{{ $value->id }}" @if(app('request')->input('faculty_id') == $value->id) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										
										<div class="col-12 col-sm-4 col-lg-2">
											<label for="users-list-status">Batch</label>
											<fieldset class="form-group">
												<?php $batch = \App\Batch::where('status', '1')->orderBy('id', 'desc')->get(); ?>
												<select class="form-control get_batch select-multiple2" name="batch_id">
													<option value=""> - Select Batch - </option>
													@if(count($batch) > 0)
													@foreach($batch as $value)
													<option value="{{ $value->id }}" @if(app('request')->input('batch_id') == $value->id) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										
										
										<div class="col-12 col-sm-4 col-lg-2">
											<label for="users-list-verified">Date</label>
											<fieldset class="form-group">
												@if(!empty(app('request')->input('fdate')))
												<input type="date" name="fdate" placeholder="Date" class="form-control get_fdate" value="{{ app('request')->input('fdate') }}">
												@else
												<input type="date" name="fdate" placeholder="Date" class="form-control" value="">
												@endif
											</fieldset>
										</div>
										
									</div>
									
									<fieldset class="form-group" style="float:right;">		
										<button type="submit" class="btn btn-primary">Search</button>
										<a href="{{ route('studiomanager.timetable.index') }}" class="btn btn-warning">Reset</a>
										<a href="{{ route('studiomanager.timetables.export') }}" class="btn btn-primary">Export</a>
									</fieldset>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="row" id="table-responsive">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<table class="table table-responsive mb-0">
									<thead>
										<tr>											
											<th scope="col"  style="position: sticky;left: 0;background-color: #fff;z-index: 9;">Studio\Time</th>
											@if(count($timeslots))
											@foreach($timeslots as $time)
											<th scope="col">
												<div class="set_time" id="{{ $time->id }}"  style="position: unset !important;">{{ $time->time_slot }}</div>
											</th>
											@endforeach
											@endif
										</tr>
									</thead>
									<tbody>
										@if(count($get_studios) > 0)
										@foreach($get_studios as $value)
										@if(!empty($value->branch_id))
										@php
										 $branch_name = DB::table('branches')->where('id', $value->branch_id)->first();
										 
										@endphp
										@endif
										
										<tr>
											<td scope="row text-center" style="position: sticky;left: 0;background-color: #fff;">
												<p>{{ $value->name }} ({{ $branch_name->name }})</p>						
												<a href="javascript:void(0);" data-toggle="modal" studioname="{{ $value->name  }} - Set Class" data-id="{{ $value->id }}" data-assistant_id="{{ $value->assistant_id }}" class="btn btn-sm btn-primary get_studio_data">
													Set Class
												</a>
											</td>
											<?php $faculty_timetables = $value->timetable; ?>
											@if(count($faculty_timetables) > 0)										

											<?php 

											$from_time = date('H:i', strtotime($faculty_timetables[0]->from_time));

											$timeslot = \App\TimeSlot::where('time_slot', $from_time)->first();

											for($i=1;$i<$timeslot->id;$i++){
												?>
												<td></td>
												<?php } ?>
												<?php
												$faculty = [];											
												for ($g=0;$g<count($faculty_timetables);$g++)											
												{

													$faculty = $faculty_timetables[$g];

													$start=strtotime(date('H:i', strtotime($faculty->from_time)));
													$end=strtotime(date('H:i', strtotime($faculty->to_time)));
													$number = 0;
													for ($i=$start;$i<=$end;$i = $i + 5*60)
													{
														$number++;
													}
													
													$online = true;
													$topic_name = "";
													if($faculty->class_type=="offline"){
														$ii = 1;
														$online = false;
														$topic_name .= isset($faculty->chapter->name) ? "($ii) " .$faculty->chapter->name. ' - ' : "($ii) ";
														$topic_name .= isset($faculty->topic->name) ? $faculty->topic->name : '';
														$parent_timetables = \App\Timetable::with(['chapter','topic'])->where('time_table_parent_id', $faculty->id)->get();
														if(count($parent_timetables) > 0 ){
															foreach($parent_timetables as $pTimetable){
																$ii++;
																$topic_name .= isset($pTimetable->chapter->name) ? "($ii) " .$pTimetable->chapter->name. ' - ' : "($ii) ";
																$topic_name .= isset($pTimetable->topic->name) ? $pTimetable->topic->name : '';
															}
														}
														
													}
													else{
														$topic_name = isset($faculty->topic->name) ? $faculty->topic->name : '';
													}
													?>
													<td colspan="{{ $number }}">
														<div class="faculty-set from-time to-time">
															<div class="row">
																<div class="col-lg-6" style="position: unset;">
																	<p>{{ isset($value->assistant->name) ? $value->assistant->name : '' }}</p>
																	<span>{{ isset($value->assistant->mobile) ? $value->assistant->mobile : '' }}</span>
																	<span>{{ $topic_name }}</span>
																</div>
																<div class="col-lg-6" style="position: unset;">
																	<p>{{ $faculty->faculty->name }}</p>
																	<span>{{ date('H:i', strtotime($faculty->from_time)) }} - {{ date('H:i', strtotime($faculty->to_time)) }}</span>
																	<span>{{ $faculty->faculty->mobile }}</span>
																</div>
															</div>
															<?php
															if($faculty->cdate >= date('Y-m-d')){
															?>
																<a href="#reschedule" data-toggle="modal" studioname="{{ $value->name }} - Reshedule" data-id="{{ $faculty->id }}" class="btn btn-sm btn-primary mt-1 get_timetable_data">
																	Reschedule
																</a>
																<?php
																if($online){
																?>
																<a href="#swap" data-toggle="modal" studioname="{{ $value->name }} - Swap" data-id="{{ $faculty->id }}" data-fid="{{ $faculty->faculty_id }}" class="btn btn-sm btn-primary mt-1 get_timetable_data">
																	Swap
																</a>
																<a href="#cancel" data-toggle="modal" studioname="{{ $value->name }} - Cancel" data-id="{{ $faculty->id }}" class="btn btn-sm btn-primary mt-1 get_timetable_data">
																	Cancel
																</a>
																<?php 
																}
																?>
																<a href="javascript:void(0)" data-toggle="modal" data-id="{{ $faculty->id }}" data-classtype="{{ $faculty->class_type }}" studioname="{{ $value->name  }} - {{ $faculty->class_type  }}" class="btn btn-sm btn-primary mt-1 get_edit_data">
																	Edit
																</a>
																<a href="javascript:void(0)" data-id="{{ $faculty->id }}" class="btn btn-sm btn-primary mt-1 delete_class">
																	Delete
																</a>
																
															<?php 
															}
															?>
														</div>											
													</td>

													<?php
													if($g<count($faculty_timetables)-1){
														$az = date('H:i', strtotime($faculty_timetables[$g+1]->from_time));
														$timeslot1 = \App\TimeSlot::where('time_slot', $az)->first();
														$timeslot2 = \App\TimeSlot::where('time_slot', date('H:i', strtotime($faculty_timetables[$g]->from_time)))->first();

														for($s=0;$s<$timeslot1->id-($number+$timeslot2->id);$s++) {
															?>
															<td></td>

															<?php } } ?>

															<?php } ?>
															@endif
														</tr>
														@endforeach
														@endif
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>              
							</section>
						</div>
					</div>
				</div>
				
				
				
				<div id="edit" class="modal fade">
					<div class="modal-dialog modal-xl">
						<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title studioname"></h5>
						</div>
						<form method="post" id="submit_timetable_online_form" class="online-form">
						<div class="modal-body">
							<div class="form-body">
								<div class="row pt-2 edit_form_data">
								
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<input type="hidden" name="online_timetable_id" class="online_timetable_id" value="">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
							<button type="submit" id="timetable_online_btn" class="btn btn-primary onlinedsabl">Submit <i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i></button>
						</div>
						</form>
						
						</div>
					</div>
				</div>
				
				
				<div id="myModal" class="modal fade">
					<div class="modal-dialog modal-xl">
						<div class="modal-content">
							<form method="post" id="submit_timetable_form">
								<div class="modal-header">
									<h5 class="modal-title studioname"></h5>
								</div>
								<div class="modal-body">
								<ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
									<li class="nav-item">
										<a class="nav-link active classess_type" data-id="1" id="home-tab-fill" data-toggle="tab" href="#online_classess" role="tab" aria-controls="home-fill" aria-selected="true">Online</a>
									</li>
									<li class="nav-item">
										<a class="nav-link classess_type" data-id="0" id="profile-tab-fill" data-toggle="tab" href="#offline_classess" role="tab" aria-controls="profile-fill" aria-selected="false">Offline</a>
									</li>
								</ul>
								<div style="display:none;">
									<input type="radio" class="online_class" name="class_type" value="online" checked>
									<input type="radio" class="offline_class" name="class_type" value="offline">
								</div>
									<div class="form-body">
										<div class="row pt-2">
											<div class="col-md-4 col-12">
												<div class="form-label-group" id="batch_loader">
													<select class="form-control batch_id select-multiple-11" name="batch_id">
														<option value=""> - Select Batch - </option>
														@if(count($onlinebatch) > 0)
														@foreach($onlinebatch as $value)
														<option value="{{ $value->id }}">{{ $value->name }}</option>
														@endforeach
														@endif
													</select>
													<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
												</div>
											</div>
											<div class="col-md-4 col-12" id="course_loader" style="display: none;">
												<div class="form-label-group">
													<select class="form-control course_id select-multiple-5" name="course_id">
														<option value=""> - Select Course - </option>
													</select>
													<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
												</div>
											</div>
											
											<div class="col-md-4 col-12" id="subject_loader">
												<div class="form-label-group">
													<select class="form-control subject_id select-multiple-21" name="subject_id">
														<option value=""> - Select Subject - </option>
													</select>
													<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
												</div>
											</div>
											
											<div class="col-md-4 col-12" id="faculty_loader">
												<div class="form-label-group">
													<?php //$faculty = \App\User::where('role_id', '2')->where('status', '1')->orderBy('id', 'desc')->get(); ?>
													<select class="form-control faculty_id select-multiple-3" name="faculty_id">
														<option value=""> - Select Faculty - </option>
													</select>
													<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
													<span class="text-danger" id="nt_av"></span>
												</div>
											</div>
											
											<div class="col-md-4 col-12 select_chapter" id="chapter_loader">
												<div class="form-label-group">
													<select class="form-control chapter_id select-multiple-31" name="chapter_id">
														<option value=""> - Select Chapter - </option>
													</select>
													<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
												</div>
											</div>
											<div class="col-md-4 col-12 select_topic">
												<div class="form-label-group">
													<select class="form-control topic_id select-multiple-41" name="topic_id">
														<option value=""> - Select Topic - </option>
														<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
													</select>
												</div>
											</div>
											
											<div class="col-md-8 col-12 select_chapter_topic" id="chapter_loader_offline" style="display:none;">
												<div class="form-label-group">
													<select class="form-control chapter_id_offline select-multiple-81" name="chapter_id_offline[]" multiple>
														<option value=""> - Select Chapter && Topic </option>
													</select>
													<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
												</div>
											</div>
											
											<div class="col-md-4 col-12 online_class_type">
												<div class="form-label-group">
													<select class="form-control" name="online_class_type">
														<option value=""> - Select Class Type - </option>
														<option value="youtube_live">YouTube Live Classes</option>
														<option value="youtube_premium">YouTube Premium Classes</option>
														<option value="youtube_free_live">YouTube Free Live Classes</option>
														<option value="offline_recorded">Offline Recorded Classes</option>
													</select>
													<span class="text-danger"></span>
												</div>
											</div>
											
											
											
											<div class="col-md-4 col-12">
												<div class="form-label-group">
													<input type="date" class="form-control" placeholder="Date" name="cdate" value="{{ date('Y-m-d', strtotime(' +1 day')) }}" autocomplete="off">
													<!--label for="first-name-column">Date</label-->
												</div>
											</div>
											<div class="col-md-4 col-12">
												<div class="form-label-group">
													<input type="text" class="form-control timepicker" placeholder="From Time" name="from_time" value="{{ old('from_time') }}" autocomplete="off">
													<label for="first-name-column">From Time</label>
												</div>
											</div>
											<div class="col-md-4 col-12">
												<div class="form-label-group">
													<input type="text" class="form-control timepicker" placeholder="To Time" name="to_time" value="{{ old('to_time') }}" autocomplete="off">
													<label for="first-name-column">To Time</label>
												</div>
											</div>
											
											<div class="col-md-12 col-12 show_remark" style="display: none;">
												<div class="form-label-group">
													<input type="text" name="remark" placeholder="Remark" class="form-control remark" value="">
												</div>
											</div>
											<div class="row col-md-12 allAddMoreFields">
												<div class="col-md-12 col-12 show_add_icon" style="display: none;">
													<a href='javascript:void(0);' title='Add' class='add-more' onClick='addMore()' style='float: right;'><i class="ficon feather icon-plus"></i></a>
												</div>
												
												<div class="col-md-12 col-12 accrd_topic" style="display: none;">
												
												
												</div>
												
												<div class="col-md-12 col-12 after_add_more">
												
												
												</div>
											</div>
											
										</div>
									</div>
								</div>
								<input type="hidden" name="studio_id" class="studio_id" value="">
								<input type="hidden" name="assistant_id" class="assistant_id" value="">
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
									<button type="submit" id="timetable_btn" class="btn btn-primary dsabl">Submit <i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i></button>
								</div>
							</form>
							
							<div class="copy-fields hide" style="display:none;">
											
								<div class="row remove_rows">
								<div class="col-md-12 col-12">
								<a  href='javascript:void(0);' title='Remove' class="remove text-danger" style='float: right;'><i class="ficon feather icon-delete"></i></a>
								</div>
									<div class="col-md-3 col-12">
										<div class="form-label-group">
											<select class="form-control batch_accord_subject" name="batch_accord_subject[]">
												<option value=""> Batch </option>
												<?php
												foreach ($onlinebatch as $key => $value) {
													if(!empty($value->id) && !empty($value->name)){
														?>
														<option value={{ $value->id }}>{{ $value->name }}</option>
														<?php
													}
												}
												?>
											</select>
										</div>
									</div>
									<div class="col-md-3 col-12" style="display: none;">
										<div class="form-label-group">
											<select class="form-control batch_accord_course select-multiple-61" name="batch_accord_course[]">
												<option value="">Course</option>
											</select>
										</div>
									</div>
									<div class="col-md-3 col-12">
										<div class="form-label-group">
											<select class="form-control batch_accord_subjects select-multiple-61" name="batch_accord_subjects[]">
												<option value=""> Subject </option>
											</select>
										</div>
									</div>
									<div class="col-md-3 col-12">
										<div class="form-label-group">
											<select class="form-control batch_accord_chapter select-multiple-61" name="batch_accord_chapter[]">
												<option value=""> Chapter </option>
											</select>
										</div>
									</div>
									<div class="col-md-3 col-12">
										<div class="form-label-group">
											<select class="form-control batch_accord_topic select-multiple-71" name="batch_accord_topic[]">
												<option value=""> Topic </option>
											</select>
										</div>
									</div>
								</div>
							</div>
											
						</div>
					</div>
				</div>

				<div id="reschedule" class="modal fade">
					<div class="modal-dialog">
						<div class="modal-content">
							<form method="post" id="submit_reschedule_form">
								<div class="modal-header">
									<h5 class="modal-title studioname"></h5>
								</div>
								<div class="modal-body">
									<div class="form-body">
										<div class="row pt-2">
											<div class="col-md-6 col-12">
												<div class="form-label-group">
													<input type="text" class="form-control timepicker" placeholder="From Time" name="from_time" value="{{ old('from_time') }}" autocomplete="off">
													<label for="first-name-column">To Time</label>
												</div>
											</div>
											<div class="col-md-12 col-12">
												<div class="form-group">
													<label for="first-name-column">Faculty Reason</label>
													<textarea class="form-control" name="faculty_reason" placeholder="Faculty Reason"></textarea>
												</div>
											</div>
										</div>
									</div>
								</div>
								<input type="hidden" name="timetable_id" class="timetable_id" value="">
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
									<button type="submit" id="reschedule_btn" class="btn btn-primary">Submit <i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i></button>
								</div>
							</form>
						</div>
					</div>
				</div>

				<div id="swap" class="modal fade">
					<div class="modal-dialog">
						<div class="modal-content">
							<form method="post" id="submit_swap_form">
								<div class="modal-header">
									<h5 class="modal-title studioname"></h5>
								</div>
								<div class="modal-body">
									<div class="form-body">
										<div class="row pt-2">
											<div class="col-md-6 col-12">
												<div class="form-label-group" id="swap_faculty_loader">
													<select class="form-control swap_faculty_id select-multiple9" name="swap_faculty_id">
														<option value=""> - Select Faculty - </option>
													</select>
													<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
												</div>
											</div>
											<div class="col-md-6 col-12">
												<div class="form-label-group">
													<select class="form-control swap_timetable_id select-multiple10" name="swap_timetable_id">
														<option value=""> - Select From Time - </option>
													</select>
												</div>
											</div>
										</div>
									</div>
								</div>
								<input type="hidden" name="timetable_id" class="timetable_id" value="">
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
									<button type="submit" id="swap_btn" class="btn btn-primary">Submit <i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i></button>
								</div>
							</form>
						</div>
					</div>
				</div>

				<div id="cancel" class="modal fade">
					<div class="modal-dialog">
						<div class="modal-content">
							<form method="post" id="submit_cancilclass_form">
								<div class="modal-header">
									<h5 class="modal-title studioname"></h5>
								</div>
								<div class="modal-body">
									<div class="form-body">
										<div class="row pt-2">
											<div class="col-md-6 col-12">
												<div class="form-group">
													<label for="first-name-column">Days</label>
													<select class="form-control select-multiple11" name="days">
														<option value=""> - Select Day - </option>
														<?php for($i=1;$i<=30;$i++) { ?>
														<option value="{{ $i }}">{{ $i }}</option>
														<?php } ?>
													</select>
												</div>
											</div>
											<div class="col-md-6 col-12">
												<div class="form-group">
													<label for="first-name-column">Reason</label>
													<select class="form-control get_reason select-multiple12" name="faculty_reason">
														<option value=""> - Select Reason - </option>
														<option value="Reason 1">Reason 1</option>
														<option value="Reason 2">Reason 2</option>
														<option value="Reason 3">Reason 3</option>
														<option value="Other">Other</option>
													</select>
												</div>
											</div>
											<div class="col-md-12 col-12 show_other_reason" style="display: none;">
												<div class="form-group">
													<label for="first-name-column">Other Reason</label>
													<input type="text" name="other_reason" class="form-control" placeholder="Other Reason">
												</div>
											</div>
										</div>
									</div>
								</div>
								<input type="hidden" name="timetable_id" class="timetable_id" value="">
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
									<button type="submit" id="cancilclass_btn" class="btn btn-primary">Submit <i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i></button>
								</div>
							</form>
						</div>
					</div>
				</div>
				{{-- <div id="export" class="modal fade">
					<div class="modal-dialog">
						<div class="modal-content">
							<form method="post" id="submit_export_form">
								<div class="modal-header">
									<h5 class="modal-title">Export</h5>
								</div>
								<div class="modal-body">
									<table class="table data-list-view">
										<thead>
											<tr>
												<th><input type="checkbox" name="check_all" id="check-all"></th>
												<th>S. No.</th>
												<th>Name</th>
												<th>Status</th>
												<th>Created</th>
											</tr>
										</thead>
										<tbody>
											@foreach($branches as  $key => $value)
											<tr>
												<td><input type="checkbox" class="checkbox" name="id[]" value="{{ $value->id }}"></td>
												<td>{{ $key + 1 }}</td>
												<td class="product-category">{{ $value->name }}</td>
												<td>@if($value->status == 1) Active @else Inactive @endif</td>
												<td>{{ $value->created_at->format('d-m-Y') }}</td>
											</tr>
											@endforeach
										</tbody>
									</table>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
									<button type="submit" id="export_btn" class="btn btn-primary">Export <i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i></button>
								</div>
							</form>
						</div>
					</div>
				</div> --}}
				@endsection

				@section('scripts')
				<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
				<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
				<script src="{{ asset('laravel/public/studiomanager/js/jquery.validate.min.js') }}"></script>
				

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
						$('.select-multiple-11').select2({
							width: '100%',
							placeholder: "Select Batch",
							allowClear: true
						});
						$('.select-multiple-21').select2({
							width: '100%',
							placeholder: "Select Subject",
							allowClear: true
						});
						$('.select-multiple-31').select2({
							width: '100%',
							placeholder: "Select Chapter",
							allowClear: true
						});
						$('.select-multiple-41').select2({
							width: '100%',
							placeholder: "Select Topic",
							allowClear: true
						});
						$('.select-multiple-51').select2({
							width: '100%',
							placeholder: "Select Batch",
							allowClear: true
						});
						// $('.select-multiple-3').select2({
						// 	placeholder: "Select Any",
						// 	allowClear: true
						// });
						$('.select-multiple3,.select-multiple4,.select-multiple5,.select-multiple6,.select-multiple7,.select-multiple8,.select-multiple9,.select-multiple10,.select-multiple11,.select-multiple12').select2({
							width: '100%',
							placeholder: "Select",
							allowClear: true
						});
						
						$('.select-multiple-81').select2({
							width: '100%',
							placeholder: "Select Chapter && Topic",
							allowClear: true
						});
					});
				</script>
				<script>
				$(document).on("change",".batch_accord_chapter", function () { 
						var thisVal= $(this);
						var chapter_id = $(this).val();
						if (chapter_id) {
							$.ajax({
								beforeSend: function(){
									$("#batch_loader i").show();
								},
								type : 'POST',
								url : '{{ route('studiomanager.get-topic-by-chapter') }}',
								data : {'_token' : '{{ csrf_token() }}', 'chapter_id': chapter_id},
								dataType : 'html',
								success : function (data){
									$("#batch_loader i").hide();
									thisVal.parents('.subject_batches').children('div').children('div').children('.batch_accord_topic').empty();
									thisVal.parents('.subject_batches').children('div').children('div').children('.batch_accord_topic').append(data);
									// $('.batch_accord_topic').empty();
									// $('.batch_accord_topic').append(data);
									// $(".batch_accord_topic").trigger("change");
									
								}
							});
						}
					});
				</script>
				
				<script type="text/javascript">
					$(".get_reason").on("change", function () {
						var reason = $(".get_reason option:selected").attr('value');

						if(reason == 'Other'){
							$(".show_other_reason").show();
						}else{
							$(".show_other_reason").hide();
						}						
					});
				</script>
				<script type="text/javascript">
					 $('.get_branch').on('change', function() {
					 	$("form[name='filtersubmit']").submit();
					 });
					 
					 $('.get_studio').on('change', function() {
					 	$("form[name='filtersubmit']").submit();
					 });
					 
					 $('.get_faculty').on('change', function() {
					 	$("form[name='filtersubmit']").submit();
					 });
					 
					 $('.get_batch').on('change', function() {
					 	$("form[name='filtersubmit']").submit();
					 });
					 $('.get_fdate').on('change', function() {
					 	$("form[name='filtersubmit']").submit();
					 });
				</script>
				<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
				<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
				<script src="{{ asset('laravel/public/studiomanager/js/jquery.validate.min.js') }}"></script>
				<script type="text/javascript">
					$(document).ready(function(){
						$(document).on('focus', '.timepicker', function(){
							$(this).timepicker({
								interval: 5,
								timeFormat: 'hh:mm p',
								minTime: '5:00',
							});
						});
					}); 
				</script>
				<script type="text/javascript">
					$(function() {
						$(".get_studio_data").on("click", function() {
							var studio_id = $(this).attr("data-id");
							var assistant_id = $(this).attr("data-assistant_id");
							if(assistant_id){
								var studioname = $(this).attr("studioname");
								$(".studio_id").val(studio_id);
								$(".assistant_id").val(assistant_id);
								$(".studioname").text(studioname);
								//$('#myModal').modal('show');
								$('#myModal').modal({
				                        backdrop: 'static',
				                        keyboard: true, 
				                        show: true
				                });
							}
							else{
								alert('Please first assign studio assistant.');
							}
							
						})     
					})
				</script>
				<script type="text/javascript">
					$(function() {
						$(".get_timetable_data").on("click", function() {
							var timetable_id = $(this).attr("data-id");
							var faculty_id = $(this).attr("data-fid");
							var studioname = $(this).attr("studioname");
							$(".timetable_id").val(timetable_id);
							$(".studioname").text(studioname);
							get_swap_faculty(faculty_id);
						})     
					})
				</script>
				<script type="text/javascript">
					$(".faculty_id").on("change", function () {
						/*var faculty_id = $(".faculty_id option:selected").attr('value');
						if (faculty_id) {
							$.ajax({
								beforeSend: function(){
									$("#faculty_loader i").show();
								},
								type : 'POST',
								url : '{{ route('studiomanager.get-batch') }}',
								data : {'_token' : '{{ csrf_token() }}', 'faculty_id': faculty_id},
								dataType : 'html',
								success : function (data){
									$("#faculty_loader i").hide();
									$('.batch_id').empty();
									$('.batch_id').append(data);
								}
							});
						}*/
					});
				</script>
				<script type="text/javascript">

					$(".branch_id").on("change", function () {
						var branch_id = $(".branch_id option:selected").attr('value');
						var assistant_id = $("input[name=assistant_id]").val();
						if (branch_id) {
							$.ajax({
								beforeSend: function(){
									// $(".branch_loader i").show();
								},
								type : 'POST',
								url : '{{ route('studiomanager.get-branchwise-studio') }}',
								data : {'_token' : '{{ csrf_token() }}', 'branch_id': branch_id},
								dataType : 'html',
								success : function (data){
									// $(".branch_loader i").hide();
									$('.studio_id').empty();
									$('.studio_id').append(data);
								}
							});
							
							$.ajax({
								beforeSend: function(){
									// $(".branch_loader i").show();
								},
								type : 'POST',
								url : '{{ route('studiomanager.get-branchwise-assistant') }}',
								data : {'_token' : '{{ csrf_token() }}', 'branch_id': branch_id, 'assistant_id': assistant_id},
								dataType : 'html',
								success : function (data){
									// $(".branch_loader i").hide();
									$('.assistant_id').empty();
									$('.assistant_id').append(data);
								}
							});
							
							
						}
					});

					$(".batch_id").on("change", function () {
						var batch_id = $(".batch_id option:selected").attr('value');
						if (batch_id) {
							$.ajax({
								beforeSend: function(){
									$("#batch_loader i").show();
								},
								type : 'POST',
								url : '{{ route('studiomanager.get-course') }}',
								data : {'_token' : '{{ csrf_token() }}', 'batch_id': batch_id},
								dataType : 'html',
								success : function (data){
									$("#batch_loader i").hide();
									$('.course_id').empty();
									$('.course_id').append(data);
									$(".course_id").trigger("change");
									
								}
							});
						}
					});
					
					
					
					
				</script>
				
				<script>
				$(document).on("change",".batch_accord_subject", function () { 
					var thisVal = $(this);
					var batch_id = $(this).val();
					if (batch_id) {
						$.ajax({
							type : 'POST',
							url : '{{ route('studiomanager.get-course') }}',
							data : {'_token' : '{{ csrf_token() }}', 'batch_id': batch_id},
							dataType : 'html',
							success : function (data){
								// $('.batch_accord_course').empty();
								thisVal.parents('.remove_rows').children('div').children('div').children('.batch_accord_course').html(data);
								thisVal.parents('.remove_rows').children('div').children('div').children('.batch_accord_course').trigger("change");
								
							}
						});
					}
				});
				
				$(document).on("change", ".batch_accord_course", function () { 
					var thisVal = $(this);
					var course_id =  $(this).val();
					var batch_id = thisVal.parents('.remove_rows').children('div').children('div').children('.batch_accord_subject').val();
					
					if (course_id) {
						$.ajax({
							type : 'POST',
							url : '{{ route('studiomanager.get-class-batch-subject') }}',
							data : {'_token' : '{{ csrf_token() }}', 'batch_id': batch_id},
							dataType : 'html',
							success : function (data){
								thisVal.parents('.remove_rows').children('div').children('div').children('.batch_accord_subjects').html(data);
							}
						});
					}
				});
				
				$(document).on("change", ".batch_accord_subjects", function () {
					var thisVal    = $(this);
					var subject_id = $(this).val();
					var batch_id   = thisVal.parents('.remove_rows').children('div').children('div').children('.batch_accord_subject').val();
					var course_id  = thisVal.parents('.remove_rows').children('div').children('div').children('.batch_accord_course').val();
					
					if (subject_id && course_id) {
						$.ajax({
							type : 'POST',
							url : '{{ route('studiomanager.get-chapter') }}',
							data : {'_token' : '{{ csrf_token() }}', 'subject_id': subject_id,'course_id':course_id},
							dataType : 'html',
							success : function (data){
								thisVal.parents('.remove_rows').children('div').children('div').children('.batch_accord_chapter').html(data);
							}
						});
					}
				});
				
				$(document).on("change", ".batch_accord_chapter", function () {
					var thisVal    = $(this);
					var chapter_id = $(this).val();
					var batch_id   = thisVal.parents('.remove_rows').children('div').children('div').children('.batch_accord_subject').val();
					var course_id  = thisVal.parents('.remove_rows').children('div').children('div').children('.batch_accord_course').val();
					var subject_id = thisVal.parents('.remove_rows').children('div').children('div').children('.batch_accord_subjects').val();
					
					if (chapter_id) {
						$.ajax({
							type : 'POST',
							url : '{{ route('studiomanager.get-topic') }}',
							data : {'_token' : '{{ csrf_token() }}', 'batch_id': batch_id, 'course_id': course_id, 'subject_id': subject_id, 'chapter_id': chapter_id},
							dataType : 'html',
							success : function (data){
								thisVal.parents('.remove_rows').children('div').children('div').children('.batch_accord_topic').html(data);
							}
						});
					}
				});
				</script>
				
				
				<script type="text/javascript">
					$(".course_id").on("change", function () { 
						var course_id = $(".course_id option:selected").attr('value');
						var batch_id = $(".batch_id").val();
						// var batch_id = $("input[name=batch_id]").val();
						//get_subject(batch_id);
						if (course_id) {
							$.ajax({
								beforeSend: function(){
									// $("#course_loader i").show();
									$("#batch_loader i").show();
								},
								type : 'POST',
								url : '{{ route('studiomanager.get-class-batch-subject') }}',
								data : {'_token' : '{{ csrf_token() }}', 'batch_id': batch_id},
								dataType : 'html',
								success : function (data){
									// $("#course_loader i").hide();
									$("#batch_loader i").hide();
									$('.subject_id').empty();
									$('.subject_id').append(data);
								}
							});
						}
						else{
							$('.subject_id').empty();
						}
					});
				</script>
				<script type="text/javascript">
					$(".subject_id").on("change", function () {
						var subject_id = $(".subject_id option:selected").attr('value');
						var batch_id = $(".batch_id").val();
						var course_id = $(".course_id").val();
						get_faculty(subject_id,batch_id);
						get_remark(subject_id);
						if (subject_id && course_id) {
							$.ajax({
								beforeSend: function(){
									$("#subject_loader i").show();
								},
								type : 'POST',
								url : '{{ route('studiomanager.get-chapter') }}',
								data : {'_token' : '{{ csrf_token() }}', 'subject_id': subject_id,'course_id':course_id},
								dataType : 'html',
								success : function (data){
									$("#subject_loader i").hide();
									$(".show_remark").show();
									$('.chapter_id').empty();
									$('.chapter_id').append(data);
								}
							});
							
							$.ajax({
								beforeSend: function(){
									$("#subject_loader i").show();
								},
								type : 'POST',
								url : '{{ route('studiomanager.get-chapter-and-topic') }}',
								data : {'_token' : '{{ csrf_token() }}', 'subject_id': subject_id,'course_id':course_id,'batch_id':batch_id},
								dataType : 'html',
								success : function (data){
									$("#subject_loader i").hide();
									$(".show_remark").show();
									$('.chapter_id_offline').empty();
									$('.chapter_id_offline').append(data);
								}
							});
							
						}
					});
					
					function get_faculty(subject_id,batch_id){
						if (subject_id && batch_id) {
							$.ajax({
								type : 'POST',
								url : '{{ route('studiomanager.get-faculty') }}',
								data : {'_token' : '{{ csrf_token() }}', 'subject_id': subject_id, 'batch_id': batch_id},
								dataType : 'html',
								success : function (data){  console.log(JSON.parse(data));
									var resp = JSON.parse(data);
									$('.dsabl').prop( "disabled", false);
									$('#nt_av').text('');
									$('.faculty_id').empty();
									$('.faculty_id').append(resp.response);
									if(resp.today_leave){
										$('#nt_av').text('faculty today on leave');
										$('.dsabl').prop( "disabled", true );
									}
									
								}
							});
						}
					}
				</script>
				<script type="text/javascript">
					function get_remark(subject_id){
						if (subject_id) {
							$.ajax({
								type : 'POST',
								url : '{{ route('studiomanager.get-remark') }}',
								data : {'_token' : '{{ csrf_token() }}', 'subject_id': subject_id},
								dataType : 'json',
								success : function (data){
									console.log(data);
									$('.remark').val(data.data);
								}
							});
						}
					}
				</script>
				<script type="text/javascript">
					$(".chapter_id").on("change", function () {
						var batch_id   = $(".batch_id option:selected").attr('value');
						var course_id  = $(".course_id option:selected").attr('value');
						var subject_id = $(".subject_id option:selected").attr('value');
						var chapter_id = $(".chapter_id option:selected").attr('value');
						if (chapter_id) {
							$.ajax({
								beforeSend: function(){
									$("#chapter_loader i").show();
								},
								type : 'POST',
								url : '{{ route('studiomanager.get-topic') }}',
								data : {'_token' : '{{ csrf_token() }}', 'batch_id': batch_id, 'course_id': course_id, 'subject_id': subject_id, 'chapter_id': chapter_id},
								dataType : 'html',
								success : function (data){
									$("#chapter_loader i").hide();
									$('.topic_id').empty();
									$('.topic_id').append(data);
								}
							});
						}
					});
				</script>
				
				<script type="text/javascript">
					$(".topic_id").on("change", function () { 
						
							$.ajax({
								type : 'POST',
								url : '{{ route('studiomanager.get-data-by-topic') }}',
								data : {'_token' : '{{ csrf_token() }}'},
								dataType : 'html',
								success : function (data){
									$(".accrd_topic").show();
									$(".show_add_icon").show();
									$("#topic_id i").hide();
									$('.accrd_topic').empty();
									$('.accrd_topic').append(data);
								}
							});
						
					});
				</script>
				
				<script type="text/javascript">
					function addMore(){  
					
					var html = $(".copy-fields").html();
					    $(".after_add_more").append(html);  
					}
					
					$("body").on("click",".remove",function(){ 
						$(this).parents(".after_add_more .remove_rows").remove();
					});
				</script>
				
				<script type="text/javascript">
					$(".subject_ids").on("change", function () { 
						var batch_id = $(".batch_id option:selected").attr('value');
						var subject_id = $(".subject_id option:selected").attr('value');
						if (subject_id) {
							$.ajax({
								beforeSend: function(){
									$("#topic_id i").show();
								},
								type : 'POST',
								url : '{{ route('studiomanager.get-batch-by-subject') }}',
								data : {'_token' : '{{ csrf_token() }}', 'batch_id': batch_id, 'subject_id': subject_id},
								dataType : 'html',
								success : function (data){
									$(".accrd_topic").show();
									$("#topic_id i").hide();
									$('.accrd_topic').empty();
									$('.accrd_topic').append(data);
								}
							});
						}
					});
				</script>
				
				<script type="text/javascript">
					var $form = $('#submit_timetable_form');
					validatorprice = $form.validate({
						ignore: [],
						rules: {
							faculty_id : {
								required: true,                
							},
							batch_id : {
								required: true,               
							},
							course_id : {
								required: true,
							},
							subject_id : {
								required: true,
							},
							/* chapter_id : {
								required: true,
							},
							topic_id : {
								required: true,
							}, */
							from_time : {
								required: true,
							},
							to_time : {
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

					$("#submit_timetable_form").submit(function(e) {
						var form = document.getElementById('submit_timetable_form');
						var dataForm = new FormData(form); 
						e.preventDefault();
						if(validatorprice.valid()){
							$('#timetable_btn').attr('disabled', 'disabled');
							$.ajax({
								beforeSend: function(){
									$("#timetable_btn i").show();
								},
								headers: {
									'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
								},      
								type: "POST",
								url : '{{ route('studiomanager.timetable.store') }}',
								data : dataForm,
								processData : false, 
								contentType : false,
								dataType : 'json',
								success : function(data){
									if(data.status == false){
										swal("Error!", data.message, "error");
										$('#timetable_btn').removeAttr('disabled');
										$("#timetable_btn i").hide();
									} else if(data.status == true){
										$('#submit_timetable_form').trigger("reset");						
										swal("Done!", data.message, "success").then(function(){ 
											location.reload();
										});
										$('#timetable_btn').removeAttr('disabled');
										$("#timetable_btn i").hide();
									}
								}
							});
						}       
					});
				</script>
				<script type="text/javascript">
					var $form = $('#submit_reschedule_form');
					validatereschedule = $form.validate({
						ignore: [],
						rules: {
							to_time : {
								required: true,
							},
							faculty_reason : {
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

					$("#submit_reschedule_form").submit(function(e) {
						var form = document.getElementById('submit_reschedule_form');
						var dataForm = new FormData(form);
						e.preventDefault();
						if(validatereschedule.valid()){
							$('#reschedule_btn').attr('disabled', 'disabled');
							$.ajax({
								beforeSend: function(){
									$("#reschedule_btn i").show();
								},
								headers: {
									'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
								},      
								type: "POST",
								url : '{{ route('studiomanager.reschedule.store') }}',
								data : dataForm,
								processData : false, 
								contentType : false,
								dataType : 'json',
								success : function(data){
									if(data.status == false){
										swal("Error!", data.message, "error");
										$('#reschedule_btn').removeAttr('disabled');
										$("#reschedule_btn i").hide();
									} else if(data.status == true){
										$('#submit_reschedule_form').trigger("reset");						
										swal("Done!", data.message, "success").then(function(){ 
											location.reload();
										});
										$('#reschedule_btn').removeAttr('disabled');
										$("#reschedule_btn i").hide();
									}
								}
							});
						}       
					});
				</script>
				<script type="text/javascript">
					var $form = $('#submit_cancilclass_form');
					validatecancelclass = $form.validate({
						ignore: [],
						rules: {
							days : {
								required: true,
							},
							faculty_reason : {
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

					$("#submit_cancilclass_form").submit(function(e) {
						var form = document.getElementById('submit_cancilclass_form');
						var dataForm = new FormData(form);
						e.preventDefault();
						if(validatecancelclass.valid()){
							$('#cancilclass_btn').attr('disabled', 'disabled');
							$.ajax({
								beforeSend: function(){
									$("#cancilclass_btn i").show();
								},
								headers: {
									'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
								},      
								type: "POST",
								url : '{{ route('studiomanager.cancelclass.store') }}',
								data : dataForm,
								processData : false, 
								contentType : false,
								dataType : 'json',
								success : function(data){
									if(data.status == false){
										swal("Error!", data.message, "error");
										$('#cancilclass_btn').removeAttr('disabled');
										$("#cancilclass_btn i").hide();
									} else if(data.status == true){
										$('#submit_cancilclass_form').trigger("reset");						
										swal("Done!", data.message, "success").then(function(){ 
											location.reload();
										});
										$('#cancilclass_btn').removeAttr('disabled');
										$("#cancilclass_btn i").hide();
									}
								}
							});
						}       
					});
				</script>
				<script type="text/javascript">
					function get_swap_faculty(faculty_id){
						if (faculty_id) {
							$.ajax({
								// beforeSend: function(){
								// 	$("#faculty_loader i").show();
								// },
								type : 'POST',
								url : '{{ route('studiomanager.get-swap-faculty') }}',
								data : {'_token' : '{{ csrf_token() }}', 'faculty_id': faculty_id},
								dataType : 'html',
								success : function (data){
									//$("#faculty_loader i").hide();
									$('.swap_faculty_id').empty();
									$('.swap_faculty_id').append(data);
								}
							});
						}
					};
				</script>
				<script type="text/javascript">
					$(".swap_faculty_id").on("change", function () {
						var swap_faculty_id = $(".swap_faculty_id option:selected").attr('value');
						if (swap_faculty_id) {
							$.ajax({
								beforeSend: function(){
									$("#swap_faculty_loader i").show();
								},
								type : 'POST',
								url : '{{ route('studiomanager.get-swap-faculty-timetable') }}',
								data : {'_token' : '{{ csrf_token() }}', 'swap_faculty_id': swap_faculty_id},
								dataType : 'html',
								success : function (data){
									$("#swap_faculty_loader i").hide();
									$('.swap_timetable_id').empty();
									$('.swap_timetable_id').append(data);
								}
							});
						}
					});
				</script>
				<script type="text/javascript">
					var $form = $('#submit_swap_form');
					validateswap = $form.validate({
						ignore: [],
						rules: {
							swap_faculty_id : {
								required: true,
							},
							swap_timetable_id : {
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

					$("#submit_swap_form").submit(function(e) {
						var form = document.getElementById('submit_swap_form');
						var dataForm = new FormData(form);
						e.preventDefault();
						if(validateswap.valid()){
							$('#swap_btn').attr('disabled', 'disabled');
							$.ajax({
								beforeSend: function(){
									$("#swap_btn i").show();
								},
								headers: {
									'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
								},      
								type: "POST",
								url : '{{ route('studiomanager.swap.store') }}',
								data : dataForm,
								processData : false, 
								contentType : false,
								dataType : 'json',
								success : function(data){
									if(data.status == false){
										swal("Error!", data.message, "error");
										$('#swap_btn').removeAttr('disabled');
										$("#swap_btn i").hide();
									} else if(data.status == true){
										$('#submit_swap_form').trigger("reset");						
										swal("Done!", data.message, "success").then(function(){ 
											location.reload();
										});
										$('#swap_btn').removeAttr('disabled');
										$("#swap_btn i").hide();
									}
								}
							});
						}       
					});
				
				var already = 'online';
				$(".classess_type").on("click",function(){
					var type = $(this).attr('data-id');
					if(type==1 && already !='online'){ //Online
						$("#submit_timetable_form")[0].reset();
						$(".batch_id,.course_id,.subject_id,.faculty_id,.chapter_id,.topic_id,.chapter_id_offline").val(null).trigger('change');
						already = 'online';
						$(".online_class").prop('checked',true);
						$(".select_topic,.select_chapter").show();
						$(".select_chapter_topic").hide();
						$(".allAddMoreFields").show();
						$(".online_class_type").show();
						$(".batch_id").html($(".online_batches").html());
					}
					else if(type==0 && already !='offline'){
						$("#submit_timetable_form")[0].reset();
						$(".batch_id,.course_id,.subject_id,.faculty_id,.chapter_id,.topic_id,.chapter_id_offline").val(null).trigger('change');
						already = 'offline';
						$(".offline_class").prop('checked',true);
						$(".select_topic,.select_chapter").hide();
						$(".select_chapter_topic").show();
						$(".allAddMoreFields").hide();
						$(".online_class_type").hide();
						$(".batch_id").html($(".offline_batches").html());
					}
				});
				
				$(".delete_class").on("click", function() {

					var timetable_id = $(this).attr("data-id");

					job=confirm("Are you sure to start class?");

					if(job != true){
						return false;
					}
					
					if(timetable_id){
						$.ajax({
							headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							},      
							type: "POST",
							url : '{{ route('studiomanager.timetable.delete_class') }}',
							data : {'timetable_id': timetable_id},
							dataType : 'json',
							success : function(data){
								if(data.status == false){
									swal("Error!", data.message, "error");
								} else if(data.status == true){
									swal("Done!", data.message, "success").then(function(){ 
										location.reload();
									});
								}
							}
						});
					}       
				});
				</script>
				
				<script type="text/javascript">
				$(document).on("change",".online_batch_id", function () {  
					var batch_id = $(".online_batch_id option:selected").attr('value');
					if (batch_id) {
						$.ajax({
							beforeSend: function(){
								$("#online_batch_loader i").show();
							},
							type : 'POST',
							url : '{{ route('studiomanager.get-course') }}',
							data : {'_token' : '{{ csrf_token() }}', 'batch_id': batch_id},
							dataType : 'html',
							success : function (data){
								$("#online_batch_loader i").hide();
								$('.online_course_id').empty();
								$('.online_course_id').append(data);
								$(".online_course_id").trigger("change");
								
							}
						});
					}
				});
				
				$(document).on("change",".online_course_id", function () {   
					var course_id = $(".online_course_id option:selected").attr('value');
					var batch_id = $(".online_batch_id").val();
					if (course_id) { 
						$.ajax({
							beforeSend: function(){
								// $("#course_loader i").show();
								$("#online_course_loader i").show();
							},
							type : 'POST',
							url : '{{ route('studiomanager.get-class-batch-subject') }}',
							data : {'_token' : '{{ csrf_token() }}', 'batch_id': batch_id},
							dataType : 'html',
							success : function (data){
								$("#online_course_loader i").hide();
								$('.online_subject_id').empty();
								$('.online_subject_id').append(data);
							}
						});
					}
				});
				
				$(document).on("change",".online_subject_id", function () {
					var subject_id = $(".online_subject_id option:selected").attr('value');
					var batch_id = $(".online_batch_id").val();
					var course_id = $(".online_course_id").val();
					get_online_faculty(subject_id,batch_id);
					//get_remark(subject_id);
					if (subject_id && course_id) {
						$.ajax({
							beforeSend: function(){
								$("#online_subject_loader i").show();
							},
							type : 'POST',
							url : '{{ route('studiomanager.get-chapter') }}',
							data : {'_token' : '{{ csrf_token() }}', 'subject_id': subject_id,'course_id':course_id},
							dataType : 'html',
							success : function (data){
								$("#online_subject_loader i").hide();
								//$(".show_remark").show();
								$('.online_chapter_id').empty();
								$('.online_chapter_id').append(data);
							}
						});
					}
				});
				
				
				function get_online_faculty(subject_id,batch_id){
					if (subject_id && batch_id) {
						$.ajax({
							type : 'POST',
							url : '{{ route('studiomanager.get-faculty') }}',
							data : {'_token' : '{{ csrf_token() }}', 'subject_id': subject_id, 'batch_id': batch_id},
							dataType : 'html',
							success : function (data){  console.log(JSON.parse(data));
								var resp = JSON.parse(data);
								$('.onlinedsabl').prop( "disabled", false);
								$('#online_nt_av').text('');
								$('.online_faculty_id').empty();
								$('.online_faculty_id').append(resp.response);
								if(resp.today_leave){
									$('#online_nt_av').text('faculty today on leave');
									$('.onlinedsabl').prop( "disabled", true );
								}
								
							}
						});
					}
				}
				
				$(document).on("change",".online_chapter_id", function () {
					var batch_id   = $(".online_batch_id option:selected").attr('value');
					var course_id  = $(".online_course_id option:selected").attr('value');
					var subject_id = $(".online_subject_id option:selected").attr('value');
					var chapter_id = $(".online_chapter_id option:selected").attr('value');
					if (chapter_id) {
						$.ajax({
							beforeSend: function(){
								$("#online_chapter_loader i").show();
							},
							type : 'POST',
							url : '{{ route('studiomanager.get-topic') }}',
							data : {'_token' : '{{ csrf_token() }}', 'batch_id': batch_id, 'course_id': course_id, 'subject_id': subject_id, 'chapter_id': chapter_id},
							dataType : 'html',
							success : function (data){
								$("#online_chapter_loader i").hide();
								$('.online_topic_id').empty();
								$('.online_topic_id').append(data);
							}
						});
					}
				});
				
				$("#submit_timetable_online_form").submit(function(e) {
					var form = document.getElementById('submit_timetable_online_form');
					var dataForm = new FormData(form); 
					e.preventDefault();
					if(validatorprice.valid()){
						$('#timetable_online_btn').attr('disabled', 'disabled');
						$.ajax({
							beforeSend: function(){
								$("#timetable_online_btn i").show();
							},
							headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							},      
							type: "POST",
							url : '{{ route('studiomanager.timetable.edit-online-store') }}',
							data : dataForm,
							processData : false, 
							contentType : false,
							dataType : 'json',
							success : function(data){
								if(data.status == false){
									swal("Error!", data.message, "error");
									$('#timetable_online_btn').removeAttr('disabled');
									$("#timetable_online_btn i").hide();
								} else if(data.status == true){
									$('#submit_timetable_form').trigger("reset");						
									swal("Done!", data.message, "success").then(function(){ 
										location.reload();
									});
									$('#timetable_online_btn').removeAttr('disabled');
									$("#timetable_online_btn i").hide();
								}
							}
						});
					}       
				});
				
				$(function() {
						$(".get_edit_data").on("click", function() { 
							var timetable_id = $(this).attr("data-id");
							if(timetable_id){
								var studioname = $(this).attr("studioname");
								$(".studioname").text(studioname);
								$(".online_timetable_id").val(timetable_id);
								var typ = $(this).attr("data-classtype"); 
								$('#edit').modal({
				                        backdrop: 'static',
				                        keyboard: true, 
				                        show: true
				                });
								
								$.ajax({
									beforeSend: function(){
										$("#form_loader i").show();
									},
									type : 'POST',
									url : '{{ route('studiomanager.get-studio-edit') }}',
									data : {'_token' : '{{ csrf_token() }}', 'timetable_id': timetable_id, 'typ': typ},
									dataType : 'html',
									success : function (data){
										//$("#form_loader i").hide();
										$('.edit_form_data').empty();
										
										$('.edit_form_data').html(data);
									}
								});
								
							}
							else{
								alert('Please first assign studio assistant.');
							}
							
							
							
						});     
					});
					
				</script>
				
				
				{{-- <script type="text/javascript">
					$("#submit_export_form").submit(function(e) {
						var form = document.getElementById('submit_export_form');
						var dataForm = new FormData(form);
						e.preventDefault();
						$('#export_btn').attr('disabled', 'disabled');
						$.ajax({
							beforeSend: function(){
								$("#export_btn i").show();
							},
							headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							},      
							type: "POST",
							url : '{{ route('studiomanager.timetable.export') }}',
							data : dataForm,
							processData : false, 
							contentType : false,
							dataType : 'json',
							success : function(data){
								if(data.status == false){
									swal("Error!", data.message, "error");
									$('#export_btn').removeAttr('disabled');
									$("#export_btn i").hide();
								} else if(data.status == true){
									$('#submit_export_form').trigger("reset");						
									swal("Done!", data.message, "success").then(function(){ 
										location.reload();
									});
									$('#export_btn').removeAttr('disabled');
									$("#export_btn i").hide();
								}
							}
						});
					});
				</script> --}}
				@endsection
