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
						<h2 class="content-header-title float-left mb-0">Edit Attendance</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">Edit Attendance
								</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
						<a href="{{ route('admin.attendance.index') }}" class="btn btn-primary mr-1">Back</a>
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
									<form class="form" action="{{ route('admin.attendance.update') }}" method="post" enctype="multipart/form-data">
										@method('PATCH')
										@csrf
										<div class="form-body">
											<div class="row">
												<div class="col-md-5 col-12">
													<div class="form-group">
														<label for="first-name-column">Employee Name</label>
														<input type="text" class="form-control" placeholder="Name" name="emp_name" value="{{$attendance_data['name']}}" readonly>
														@if($errors->has('date'))
														<span class="text-danger">{{ $errors->first('date') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-5 col-12">
													<div class="form-group">
														<label for="first-name-column">Attendance Date</label>
														<input type="date" class="form-control" placeholder="Date" name="date" value="{{$attendance_data['date']}}" readonly>
														@if($errors->has('date'))
														<span class="text-danger">{{ $errors->first('date') }} </span>
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
												foreach($attendance_data['time'] as  $key => $value){ 
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
														$in_time = date("h:i", strtotime($value->time));
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
														$out_time = date("h:i", strtotime($value->time));
														$out_time1 = $value->time;
														if(empty($time_array[$ii]['out_time'])){
															$time_array[$ii]['out_time'] = $out_time;
															$time_array[$ii]['id'] = $value->id;
															$ii++;
															
														}
													}
												}
											}
													//echo '<pre>'; print_r($prev_id);die;
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
												<div class="col-12">
													<input type="hidden" name="emp_id" value="{{$value->emp_id}}">
													<input type="hidden" name="att_id" value="{{$prev_id}}">
													<button type="submit" class="btn btn-primary mr-1 mb-1 btn_submit">Update</button>
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
