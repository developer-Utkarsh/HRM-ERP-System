@extends('layouts.studioassistant')
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
								<li class="breadcrumb-item"><a href="{{ route('faculty.dashboard') }}">Home</a>
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
								<form action="{{ route('studioassistant.timetable.index') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Faculty</label>
											<fieldset class="form-group">
												<?php $faculty = \App\User::where('role_id', '2')->orderBy('id', 'desc')->get(); ?>
												<select class="form-control" name="faculty_id">
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
															$startclass = \App\StartClass::where('timetable_id', $faculty->id)->first();
															?>
															@if(isset($startclass) && !empty($startclass))
															@if($startclass->status == 'Start Class')
															<a href="#" data-id="{{ $faculty->id }}" class_status="Partially" class="btn btn-sm btn-primary mt-1 end_class">
																Partially End Class
															</a>
															<a href="#" data-id="{{ $faculty->id }}" class_status="End Class" class="btn btn-sm btn-primary mt-1 end_class">
																End Class
															</a>
															@else
															@if($startclass->status == 'Partially')
															<a href="#" class="btn btn-sm btn-primary mt-1">
																Partially Class Completed
															</a>
															@else
															<a href="#" class="btn btn-sm btn-primary mt-1">
																Class Completed
															</a>
															@endif
															@endif
															@else
															<a href="#" data-id="{{ $faculty->id }}" class="btn btn-sm btn-primary mt-1 start_class">
																Start Time
															</a>
															@endif
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
				<div id="reschedule" class="modal fade">
					<div class="modal-dialog">
						<div class="modal-content">
							<form method="post" id="submit_timetable_form">
								<div class="modal-header">
									<h5 class="modal-title studioname"></h5>
								</div>
								<div class="modal-body">
									<div class="form-body">
										<div class="row pt-2">
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
				@endsection

				@section('scripts')				
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
					// $(function() {
					// 	$(".get_studio_data").on("click", function() {
					// 		var studio_id = $(this).attr("data-id");
					// 		var studioname = $(this).attr("studioname");
					// 		$(".studio_id").val(studio_id);
					// 		$(".studioname").text(studioname);
					// 	})     
					// })
				</script>
				<script type="text/javascript">
					$(".start_class").on("click", function() {

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
								url : '{{ route('studioassistant.startclass.store') }}',
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
					$(".end_class").on("click", function() {

						var timetable_id = $(this).attr("data-id");
						var class_status = $(this).attr("class_status");

						if(class_status == 'Partially'){
							job=confirm("Are you sure to partially end class?");
						}else{
							job=confirm("Are you sure to end class?");
						}						

						if(job != true){
							return false;
						}
						
						if(timetable_id){
							$.ajax({
								headers: {
									'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
								},      
								type: "POST",
								url : '{{ route('studioassistant.endclass.update') }}',
								data : {'timetable_id': timetable_id, 'class_status': class_status},
								dataType : 'json',
								success : function(data){
									if(data.status == false){
										swal("Error!", data.message, "error").then(function(){ 
											location.reload();
										});;
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
				@endsection
