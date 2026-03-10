@extends('layouts.admin')
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
								<form action="{{ route('admin.timetable.index') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
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
										<div class="col-12 col-sm-6 col-lg-3">
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
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-verified">Date</label>
											<fieldset class="form-group">
												@if(!empty(app('request')->input('fdate')))
												<input type="date" name="fdate" placeholder="Date" class="form-control" value="{{ app('request')->input('fdate') }}">
												@else
												<input type="date" name="fdate" placeholder="Date" class="form-control" value="{{ date('Y-m-d') }}">
												@endif
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<button type="submit" class="btn btn-primary mt-1">Search</button>
											<a href="{{ route('admin.timetables.export') }}" class="btn btn-primary mt-1">Export</a>
										</div>
									</div>
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
											<th scope="col">Studio\Time</th>
											@if(count($timeslots))
											@foreach($timeslots as $time)
											<th scope="col">
												<div class="set_time" id="{{ $time->id }}">{{ $time->time_slot }}</div>
											</th>
											@endforeach
											@endif
										</tr>
									</thead>
									<tbody>
										@if(count($get_studios) > 0)
										@foreach($get_studios as $value)
										<tr>
											<td scope="row text-center">
												<p>{{ $value->name }}</p>						
												<a href="#myModal" data-toggle="modal" studioname="{{ $value->name  }} - Set Class" data-id="{{ $value->id }}" class="btn btn-sm btn-primary get_studio_data">
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
													?>
													<td colspan="{{ $number }}">
														<div class="faculty-set from-time to-time">
															<div class="row">
																<div class="col-lg-6">
																	<p>{{ isset($value->assistant->name) ? $value->assistant->name : '' }}</p>
																	<span>{{ isset($value->assistant->mobile) ? $value->assistant->mobile : '' }}</span>
																	<span>{{ isset($faculty->topic->name) ? $faculty->topic->name : '' }}</span>
																</div>
																<div class="col-lg-6">
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
															<a href="#swap" data-toggle="modal" studioname="{{ $value->name }} - Swap" data-id="{{ $faculty->id }}" data-fid="{{ $faculty->faculty_id }}" class="btn btn-sm btn-primary mt-1 get_timetable_data">
																Swap
															</a>
															<a href="#delete" data-toggle="modal" studioname="{{ $value->name }} - Delete" data-id="{{ $faculty->id }}" class="btn btn-sm btn-primary mt-1 get_timetable_data">
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
				<div id="myModal" class="modal fade">
					<div class="modal-dialog">
						<div class="modal-content">
							<form method="post" id="submit_timetable_form">
								<div class="modal-header">
									<h5 class="modal-title studioname"></h5>
								</div>
								<div class="modal-body">
									<div class="form-body">
										<div class="row pt-2">
											<div class="col-md-6 col-12" id="faculty_loader">
												<div class="form-label-group">
													<?php $faculty = \App\User::where('role_id', '2')->where('status', '1')->orderBy('id', 'desc')->get(); ?>
													<select class="form-control faculty_id select-multiple-3" name="faculty_id">
														<option value=""> - Select Faculty - </option>
														@if(count($faculty) > 0)
														@foreach($faculty as $value)
														<option value="{{ $value->id }}">{{ $value->name }}</option>
														@endforeach
														@endif
													</select>
													<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
												</div>
											</div>
											<div class="col-md-6 col-12">
												<div class="form-label-group" id="batch_loader">
													<select class="form-control batch_id select-multiple-4" name="batch_id">
														<option value=""> - Select Batch - </option>
													</select>
													<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
												</div>
											</div>
											<div class="col-md-6 col-12" id="course_loader">
												<div class="form-label-group">
													<select class="form-control course_id select-multiple-5" name="course_id">
														<option value=""> - Select Course - </option>
													</select>
													<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
												</div>
											</div>
											<div class="col-md-6 col-12" id="subject_loader">
												<div class="form-label-group">
													<select class="form-control subject_id select-multiple-6" name="subject_id">
														<option value=""> - Select Subject - </option>
													</select>
													<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
												</div>
											</div>
											<div class="col-md-6 col-12" id="chapter_loader">
												<div class="form-label-group">
													<select class="form-control chapter_id select-multiple-7" name="chapter_id">
														<option value=""> - Select Chapter - </option>
													</select>
													<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
												</div>
											</div>
											<div class="col-md-6 col-12">
												<div class="form-label-group">
													<select class="form-control topic_id select-multiple-8" name="topic_id">
														<option value=""> - Select Topic - </option>
													</select>
												</div>
											</div>
											<div class="col-md-6 col-12">
												<div class="form-label-group">
													<input type="text" class="form-control timepicker" placeholder="From Time" name="from_time" value="{{ old('from_time') }}" autocomplete="off">
													<label for="first-name-column">From Time</label>
												</div>
											</div>
											<div class="col-md-6 col-12">
												<div class="form-label-group">
													<input type="text" class="form-control timepicker" placeholder="To Time" name="to_time" value="{{ old('to_time') }}" autocomplete="off">
													<label for="first-name-column">To Time</label>
												</div>
											</div>
											<div class="col-md-6 col-12">
												<div class="form-label-group">
													<input type="date" class="form-control" placeholder="Date" name="cdate" value="{{ date('Y-m-d') }}" autocomplete="off">
													<label for="first-name-column">Date</label>
												</div>
											</div>
											<div class="col-md-6 col-12 show_remark" style="display: none;">
												<div class="form-label-group">
													<input type="text" name="remark" placeholder="Remark" class="form-control remark" value="">
												</div>
											</div>
										</div>
									</div>
								</div>
								<input type="hidden" name="studio_id" class="studio_id" value="">
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
									<button type="submit" id="timetable_btn" class="btn btn-primary">Submit <i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i></button>
								</div>
							</form>
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
													<input type="text" class="form-control timepicker" placeholder="To Time" name="to_time" value="{{ old('to_time') }}" autocomplete="off">
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

				<div id="delete" class="modal fade">
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
						$('.select-multiple3,.select-multiple4,.select-multiple5,.select-multiple6,.select-multiple7,.select-multiple8,.select-multiple9,.select-multiple10,.select-multiple11,.select-multiple12').select2({
							placeholder: "Select",
							allowClear: true
						});
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
				</script>
				<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
				<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
				<script src="{{ asset('laravel/public/admin/js/jquery.validate.min.js') }}"></script>
				<script type="text/javascript">
					$(document).ready(function(){
						$(document).on('focus', '.timepicker', function(){
							$(this).timepicker({
								interval: 5,
								timeFormat: 'HH:mm',
								minTime: '5:00',
							});
						});
					}); 
				</script>
				<script type="text/javascript">
					$(function() {
						$(".get_studio_data").on("click", function() {
							var studio_id = $(this).attr("data-id");
							var studioname = $(this).attr("studioname");
							$(".studio_id").val(studio_id);
							$(".studioname").text(studioname);
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
						var faculty_id = $(".faculty_id option:selected").attr('value');
						if (faculty_id) {
							$.ajax({
								beforeSend: function(){
									$("#faculty_loader i").show();
								},
								type : 'POST',
								url : '{{ route('admin.get-batch') }}',
								data : {'_token' : '{{ csrf_token() }}', 'faculty_id': faculty_id},
								dataType : 'html',
								success : function (data){
									$("#faculty_loader i").hide();
									$('.batch_id').empty();
									$('.batch_id').append(data);
								}
							});
						}
					});
				</script>
				<script type="text/javascript">
					$(".batch_id").on("change", function () {
						var batch_id = $(".batch_id option:selected").attr('value');
						if (batch_id) {
							$.ajax({
								beforeSend: function(){
									$("#batch_loader i").show();
								},
								type : 'POST',
								url : '{{ route('admin.get-course') }}',
								data : {'_token' : '{{ csrf_token() }}', 'batch_id': batch_id},
								dataType : 'html',
								success : function (data){
									$("#batch_loader i").hide();
									$('.course_id').empty();
									$('.course_id').append(data);
								}
							});
						}
					});
				</script>
				<script type="text/javascript">
					$(".course_id").on("change", function () {
						var course_id = $(".course_id option:selected").attr('value');
						var batch_id = $("input[name=batch_id]").val();
						//get_subject(batch_id);
						if (course_id) {
							$.ajax({
								beforeSend: function(){
									$("#course_loader i").show();
								},
								type : 'POST',
								url : '{{ route('admin.get-class-batch-subject') }}',
								data : {'_token' : '{{ csrf_token() }}', 'batch_id': batch_id},
								dataType : 'html',
								success : function (data){
									$("#course_loader i").hide();
									$('.subject_id').empty();
									$('.subject_id').append(data);
								}
							});
						}
					});
				</script>
				<script type="text/javascript">
					$(".subject_id").on("change", function () {
						var subject_id = $(".subject_id option:selected").attr('value');
						get_remark(subject_id);
						if (subject_id) {
							$.ajax({
								beforeSend: function(){
									$("#subject_loader i").show();
								},
								type : 'POST',
								url : '{{ route('admin.get-chapter') }}',
								data : {'_token' : '{{ csrf_token() }}', 'subject_id': subject_id},
								dataType : 'html',
								success : function (data){
									$("#subject_loader i").hide();
									$(".show_remark").show();
									$('.chapter_id').empty();
									$('.chapter_id').append(data);
								}
							});
						}
					});
				</script>
				<script type="text/javascript">
					function get_remark(subject_id){						
						if (subject_id) {
							$.ajax({
								type : 'POST',
								url : '{{ route('admin.get-remark') }}',
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
						var chapter_id = $(".chapter_id option:selected").attr('value');
						if (chapter_id) {
							$.ajax({
								beforeSend: function(){
									$("#chapter_loader i").show();
								},
								type : 'POST',
								url : '{{ route('admin.get-topic') }}',
								data : {'_token' : '{{ csrf_token() }}', 'chapter_id': chapter_id},
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
							chapter_id : {
								required: true,
							},
							topic_id : {
								required: true,
							},
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
								url : '{{ route('admin.timetable.store') }}',
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
								url : '{{ route('admin.reschedule.store') }}',
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
								url : '{{ route('admin.cancelclass.store') }}',
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
								url : '{{ route('admin.get-swap-faculty') }}',
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
								url : '{{ route('admin.get-swap-faculty-timetable') }}',
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
								url : '{{ route('admin.swap.store') }}',
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
							url : '{{ route('admin.timetable.export') }}',
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
