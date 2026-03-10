@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<?php 
					$urlRedirect =  Request::segment(2);
					
					if($urlRedirect=="incomplete-attendance-edit"){ 
						$headingText	=	"Incomplete";
					}else{
						$headingText	=	"Full";
					}
				?>
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Edit <?=$headingText;?> Attendance</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">Edit <?=$headingText;?> Attendance
								</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
						<?php 
							$check = $_SERVER['QUERY_STRING'];
							if(!empty($check)){
								$nCheck	=	$check;
							}else{
								$nCheck	=	"";
							}
						?>
						
						<?php if($urlRedirect=="incomplete-attendance-edit"){ ?>
							<a href="{{ route('admin.attendance.incompleteattendence', $nCheck) }}" class="btn btn-primary mr-1">Back</a>
						<?php }else{ ?>
							<a href="{{ route('admin.attendance.fullattendence', $nCheck) }}" class="btn btn-primary mr-1">Back</a>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
			</div>
		</div>
		<div class="content-body">
			<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="{{ route('admin.attendance.update-full-attendence') }}" method="post" enctype="multipart/form-data">
										
										@csrf
										<div class="form-body">
											<div class="row">
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Employee Name</label>
														<input type="text" class="form-control" placeholder="Name" name="emp_name" value="{{$attendance_data['name']}}" readonly>
														@if($errors->has('date'))
														<span class="text-danger">{{ $errors->first('date') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Attendance Date</label>
														<input type="date" class="form-control" placeholder="Date" name="date" value="{{$attendance_data['date']}}" readonly>
														@if($errors->has('date'))
														<span class="text-danger">{{ $errors->first('date') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Location</label>
														<input type="text" class="form-control" placeholder="Location" name="location" value="{{$attendance_data['location']}}" readonly>
														@if($errors->has('location'))
														<span class="text-danger">{{ $errors->first('location') }} </span>
														@endif
													</div>
												</div>
											</div>
											
											<hr>
											<?php
											if(!empty($attendance_data['time'])){ 
												$i = 0; 
												$ii = 0;
												$time_array = array();
												$time_array1 = array();
												$prev_id  = '';
												$reason = '';
												foreach($attendance_data['time'] as  $key => $value){ 
													if(!empty($value->reason)){
														$reason = $value->reason;
													}
												    $prev_id .= $value->id.',';
													$i++;
													$in_time  = "";
													$out_time = "";
													if(empty($time_array[$ii]['in_time'])){
														$time_array[$ii]['in_time'] = "";
													}
													if(empty($time_array[$ii]['out_time'])){
														$time_array[$ii]['out_time'] = "";
													}
													
													if($value->type=="In"){ 
														//$in_time = date("h:i", strtotime($value->time));
														$in_time = $value->time;
														$in_time1 = $value->time;
														if(empty($time_array[$ii]['in_time'])){
															$time_array[$ii]['in_time'] = $in_time;
															$time_array[$ii]['id'] = $value->id;
														}
														else{
															$ii++;
															$time_array[$ii]['in_time'] = $in_time;
															$time_array[$ii]['out_time'] = "";
															$time_array[$ii]['id'] = $value->id;
														}
													}
													else if($value->type=="Out"){ 
														//$out_time = date("h:i", strtotime($value->time));
														$out_time = $value->time;
														$out_time1 = $value->time;
														if(empty($time_array[$ii]['out_time'])){
															$time_array[$ii]['out_time'] = $out_time;
															$time_array[$ii]['id'] = $value->id;
															$ii++;
															
														}
													}
												}
											}
													//echo '<pre>'; print_r($time_array);die;
												if(count($time_array) > 0){	
												foreach($time_array as $time_array_value){	
												?>
												
												<div class="row remove_data">
													<div class="col-md-2 col-12">
														<div class="form-group">
															<label for="">In Time</label>
															<input type="time" class="form-control" placeholder="" name="time[]" value="{{ old('time', $time_array_value['in_time']) }}" required>
															@if($errors->has('time'))
															<span class="text-danger">{{ $errors->first('time') }} </span>
															@endif
														</div>
													</div>
													<div class="col-md-2 col-12">
														<div class="form-group">
															<label for="">Type</label>
															<select class="form-control" name="type[]" required>
																<option value="In" @if(!empty($time_array_value['in_time'])) selected="selected" @endif> In</option>
															</select>
															@if($errors->has('type'))
															<span class="text-danger">{{ $errors->first('type') }} </span>
															@endif
														</div>
													</div>
													
													
													<div class="col-md-2 col-12">
													
														<div class="form-group">
															<label for="">Out Time</label>
															<input type="time" class="form-control" placeholder="" name="time[]" value="{{ old('time', $time_array_value['out_time']) }}" required>
															@if($errors->has('time'))
															<span class="text-danger">{{ $errors->first('time') }} </span>
															@endif
														</div>
													</div>
													<div class="col-md-2 col-12">
														<div class="form-group">
															<label for="">Type</label>
															<select class="form-control" name="type[]" required>
																<option value="Out" @if(!empty($time_array_value['out_time'])) selected="selected" @endif> Out</option>
															</select>
															@if($errors->has('type'))
															<span class="text-danger">{{ $errors->first('type') }} </span>
															@endif
														</div>
													</div>
													
													
													<div class="col-md-2 col-12">
														<div class="form-group">
															<label for="">&nbsp;</label>
															<button class="btn btn-danger remove" type="button" style="margin-top:18px;">Remove</button>
														</div>
													</div>													
													
													 
												
												</div>
												 
												<?php
												}
												}
												/* }
												
												if($i % 2 != 0){
													?>
													
													<?php
												}
											} */
											?>
											<div class="row">
												<div class="col-md-4 col-12">
													<div class="form-label-group">
														<select class="form-control select-multiple1" name="for_reason" required>
															<option value=""> - Select Reason For- </option>
															<option value="0" @if($attendance_data['for_reason'] == '0'){{'selected'}}@endif>Both</option>
															<option value="1" @if($attendance_data['for_reason'] == '1'){{'selected'}}@endif>In</option>
															<option value="2" @if($attendance_data['for_reason'] == '2'){{'selected'}}@endif>Out</option>
														</select>
													</div>
												</div>
												
												<div class="col-md-8 col-12">
													<div class="form-label-group">
														<textarea name="reason" placeholder="Reason" class="form-control remark" required>{{$reason}}</textarea>
													</div>
												</div>
												
												
											</div>
											
											<div class="row">	                                      
												<div class="col-12">
													<input type="hidden" name="emp_id" value="{{$value->emp_id}}">
													<input type="hidden" name="att_id" value="{{$prev_id}}">
													<input type="hidden" name="tbl" value="{{$modl}}">

													<?php 
														//$dbDate	= date('Y-m-d', strtotime('-1 day', strtotime($attendance_data['date'])));
														//if(date('Y-m-d') > $attendance_data['date'] && Auth::user()->role_id ==21){
														$now 	   = time();
														$your_date = strtotime($attendance_data['date']);
														$datediff  = $now - $your_date;

														$result	   = round($datediff / (60 * 60 * 24));
														
														if($result > 2 && Auth::user()->role_id ==21){ 
													?>
														<button type="button" class="btn btn-primary mr-1 mb-1 btn_submit">Previous data can't be change</button>
													<?php }else{ ?>
														<button type="submit" class="btn btn-primary mr-1 mb-1 btn_submit">Update</button>
													<?php } ?>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
			<!-- // Basic Floating Label Form section end -->
		</div>
	</div>
</div>
@endsection

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("body").on("click",".remove",function(){ 
			$(this).parents(".remove_data").remove();
		});
	})
</script>
@endsection
