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
						<h2 class="content-header-title float-left mb-0">Edit Leave</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">Edit Leave</li>
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
						<a href="{{ route('admin.leave.index', $nCheck) }}" class="btn btn-primary mr-1">Back</a>
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
									<form class="form" action="{{ route('admin.leave.edit-leave-store', $leave_data->emp_id.'?'.$_SERVER['QUERY_STRING']) }}" method="post" enctype="multipart/form-data">
										@csrf
										
										<div class="form-body">
											<div class="row">
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Employee</label>
														
														<select class="form-control emp_id select-multiple1" id="emp_id" name="emp_id" required>
															<!--<option value="{{ Auth::user()->id }}">Assign To Self </option>
															<option value="0">Open Task </option>-->
															<option value="">Select</option>
															@if(count($users) > 0)
															@foreach($users as $value)
															<option value="{{ $value['id'] }}" @if($value['id'] == old('emp_id', $leave_data->emp_id)) selected="selected" @endif><?=$value['name'] ." (".$value['register_id'].")" ." (".$value['role_name'].")";?></option>
															@endforeach	
															@endif
														</select>
														@if($errors->has('emp_id'))
														<span class="text-danger">{{ $errors->first('emp_id') }} </span>
														@endif
													</div>
												</div>	
												
												
												<div class="col-md-4 col-12 task_title_hide">
													<div class="form-group">
														<label for="">Reason</label>
														<input type="text" class="form-control reason" id="reason" name="reason" value="{{ old('reason', $leave_data->reason) }}" required>	
														@if($errors->has('reason'))
														<span class="text-danger">{{ $errors->first('reason') }} </span>
														@endif
													</div>
												</div>
												
												<?php if(Auth::user()->role_id !=20 && Auth::user()->role_id !=27){?>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="">Leave Staus</label>
														<select class="form-control status" id="status" name="status" required disabled>	
															<option value=""> Select</option>
				<option value="Pending" @if('Pending' == old('status', $leave_data->leave_details[0]->status)) selected="selected" @endif> Pending</option>
			<option value="Approved" @if('Approved' == old('status', $leave_data->leave_details[0]->status)) selected="selected" @endif> Approved</option>
															<option value="Rejected" @if('Rejected' == old('status', $leave_data->leave_details[0]->status)) selected="selected" @endif> Rejected</option>
														</select>
													</div>
												</div>
												<?php } ?>
												
												<div class="col-md-4 col-12 date_div">
													<div class="form-group">
														<label for="first-name-column">Date</label>
														<input type="date" class="form-control leave_date" placeholder="Date" name="date" value="@if(!empty($leave_data->leave_details[0]->date)){{ $leave_data->leave_details[0]->date }}@else{{ date('Y-m-d') }}@endif" required @if((Auth::user()->role_id != 29 && Auth::user()->role_id !=21) && Auth::user()->role_id != 24 && Auth::user()->id != '6166'){{"disabled"}}@endif>
														@if($errors->has('date'))
														<span class="text-danger">{{ $errors->first('date') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-4 col-12 category_div">
													<div class="form-group">
														<label for="">Category</label>
														<select class="form-control category" id="category" name="category" required>	
														<?php echo $options; ?>
														</select>
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="">Type</label>
														<select class="form-control type" id="type" name="type" required>	
														<option value=""> Select</option>
														<option value="1st Half" @if('1st Half' == old('type', $leave_data->leave_details[0]->type)) selected="selected" @endif> 1st Half</option>
														<option value="2nd Half" @if('2nd Half' == old('type', $leave_data->leave_details[0]->type)) selected="selected" @endif> 2nd Half</option>
														<option value="Full Day" @if('Full Day' == old('type', $leave_data->leave_details[0]->type)) selected="selected" @endif> Full Day</option>
														</select>
													</div>
												</div>
												
											</div>
											<div class="row">	                                      
												<div class="col-12">
												<?php
												if(date('d') > 10){
													//$todayDate = date('Y-m-01');
													$todayDate = date('Y-m-27', strtotime('-1 MONTH'));
												}
												else{
													$todayDate = date('Y-m-d',strtotime("first day of last month"));
												}
												
												
												?>
													<?php if((date('Y-m-d') <= $leave_data->leave_details[0]->date) || (Auth::user()->role_id !=20 && Auth::user()->role_id !=27 && Auth::user()->role_id !=21)){ 
														if(strtotime($leave_data->leave_details[0]->date) >= strtotime($todayDate)){
													?>
														<input type="hidden" name="leave_id" value="{{ old('id', $leave_data->id) }}">
														<input type="hidden" name="leave_detail_id" value="{{ old('id', $leave_data->leave_details[0]->id) }}">
														<button type="submit" class="btn btn-primary mr-1 mb-1 btn_submit">Submit</button>
														<?php }
														else{
															?>
															<button type="button" class="btn btn-primary mr-1 mb-1">Previous data can't be change.</button>
															<?php
														}
													}
													else{ ?>
														<button type="button" class="btn btn-primary mr-1 mb-1">Previous data can't be change</button>
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
		
		$('.select-multiple1').select2({
			placeholder: "Select",
			allowClear: true
		});
	})
</script>

@endsection
