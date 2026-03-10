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
						<h2 class="content-header-title float-left mb-0">Edit Task</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Edit Task</a>
								</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
						<a href="{{ route('admin.task.index') }}" class="btn btn-primary mr-1">Back</a>
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
									<form class="form" action="{{ route('admin.task.update', $task->id) }}" method="post" enctype="multipart/form-data">
										@method('PATCH')
										@csrf
										<div class="form-body">
											<div class="row">
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Task Date</label>
														<input type="date" class="form-control" placeholder="Date" name="date" value="{{ old('date', $task->date) }}" readonly>
														@if($errors->has('date'))
														<span class="text-danger">{{ $errors->first('date') }} </span>
														@endif
													</div>
												</div>
											</div>
											<hr>
											<?php
											if(!empty($task->task_details)){
												foreach($task->task_details as  $key => $value){  //echo "<pre>"; print_r($task->emp_id); die;
												?>
												@if($task->emp_id == $value->assigned_userid && Auth::user()->role_id == 20 || Auth::user()->role_id == 29)
												<div class="row">
													<input type="hidden" name="task_array[{{$key}}][task_detail_id]" value="<?=$value->id?>">
													<div class="col-md-6 col-12">
														<div class="form-group">
															<label for="">Task Name</label>
															<input type="text" class="form-control" placeholder="" name="task_array[{{$key}}][name]" value="{{ old('name', $value->name) }}" maxlength="100">
															@if($errors->has('name'))
															<span class="text-danger">{{ $errors->first('name') }} </span>
															@endif
														</div>
													</div>
													<div class="col-md-6 col-12">
														<div class="form-group">
															<label for="">Plan Hour</label>
															<input type="number" class="form-control" placeholder="" name="task_array[{{$key}}][plan_hour]" value="{{ old('plan_hour', $value->plan_hour) }}" step="any" min="0">
														</div>
													</div>
													<div class="col-md-6 col-12">
														<div class="form-group">
															<label for="">Spent Hour</label>
															<input type="number" class="form-control" placeholder="" name="task_array[{{$key}}][spent_hour]" value="{{ old('spent_hour', $value->spent_hour) }}" step="any" min="0">
														</div>
													</div>
													<div class="col-md-6 col-12">
														<div class="form-group">
															<label for="">Status</label>
															<select class="form-control" name="task_array[{{$key}}][status]" onChange="statusShow(this.value)">
																<!--option value=""> - Select Status - </option-->
																<option value="Pending" @if($value->status == 'Pending') selected="selected" @endif> Pending</option>
																<option value="Not Started" @if($value->status == 'Not Started') selected="selected" @endif> Not Started</option>
																<option value="In Progress" @if($value->status == 'In Progress') selected="selected" @endif> In Progress</option>
																<option value="Completed" @if($value->status == 'Completed') selected="selected" @endif> Completed</option>
																<option value="Dropped" @if($value->status == 'Dropped') selected="selected" @endif> Dropped</option>
																<option value="Correction" @if($value->status == 'Correction') selected="selected" @endif> Correction</option>
															</select>
															@if($errors->has('status'))
															<span class="text-danger">{{ $errors->first('status') }} </span>
															@endif
														</div>
													</div>	
													
													<div class="col-md-6 col-12">
														<div class="form-group">
															<label for="">Description</label>
															<!-- <input type="text" class="form-control" placeholder="" name="task_array[{{$key}}][description]" value="{{ old('description', $value->description) }}" maxlength="250"> -->
															<textarea class="form-control" name="task_array[{{$key}}][description]"  rows="5">{{ old('description', $value->description) }}</textarea>

														</div>
													</div>
													
													<div class="col-md-6 col-12">
														<div class="form-group">
															<?php 
															//$assigned_user = \App\User::where('status', '1')->where('role_id', '!=','1')->where('register_id', '!=', NULL)->get(); 
															if(Auth::user()->role_id == 29 || Auth::user()->role_id == 24){ 
																$assigned_user = \App\User::where('status', '1')->where('role_id', '!=','1')->where('register_id', '!=', NULL)->get(); 
															}
															else{
																$assigned_user = \App\User::where('status', '1')->where('role_id', '!=','1')->where('register_id', '!=', NULL)->whereRaw('(id = "'.Auth::user()->id.'" OR supervisor_id LIKE  \'%"'.Auth::user()->id.'"%\')')->get(); 
															}
															?>
															<label for="first-name-column">Assigned User</label>
															@if(count($assigned_user) > 0)
															<select class="form-control select-multiple1" name="task_array[{{$key}}][assigned_userid]">
																<option value=""> - Select Assigned User - </option>
																@foreach($assigned_user as $assignValue)
																<option value="{{ $assignValue->id }}" @if($assignValue->id == $value->assigned_userid) selected="selected" @endif><?=$assignValue->name ." (".$assignValue->register_id.")";?></option>
																@endforeach
															</select>
															@endif
															@if($errors->has('assigned_userid'))
															<span class="text-danger">{{ $errors->first('assigned_userid') }} </span>
															@endif
														</div>
													</div>		
													<?php 
														if($value->status=="Dropped" || $value->status=="Correction"){
															$display = "block";
														}else{
															$display = "none";
														}
													?>
													<div class="col-md-12 col-12" id="reason" style="display:<?=$display;?>;">
														<div class="form-group">
															<label for="">Reason</label>
															<textarea class="form-control" name="task_array[{{$key}}][dropped_reason]"  rows="5">{{ $value->dropped_reason }}</textarea>
														</div>
													</div>
												</div>
												@else
												<div class="row">
													<div class="col-md-6 col-12">
														<div class="form-group">
															<label for="">Status</label>
															<select class="form-control" name="task_array_status">
																<option value="Pending" @if($value->status == 'Pending') selected="selected" @endif> Pending</option>
																<option value="Not Started" @if($value->status == 'Not Started') selected="selected" @endif> Not Started</option>
																<option value="In Progress" @if($value->status == 'In Progress') selected="selected" @endif> In Progress</option>
																<option value="Completed" @if($value->status == 'Completed') selected="selected" @endif> Completed</option>
																<option value="Dropped" @if($value->status == 'Dropped') selected="selected" @endif> Dropped</option>
															</select>
															@if($errors->has('task_array_status'))
															<span class="text-danger">{{ $errors->first('task_array_status') }} </span>
															@endif
														</div>
													</div>	
													
													<div class="col-md-6 col-12">
														<div class="form-group">
															<label for="">Description</label>
															<textarea class="form-control" name="task_array_description" required>{{ old('task_array_description') }}</textarea>

														</div>
													</div>
													<input type="hidden" name="old_description" value="{{$value->description}}">
													<input type="hidden" name="task_details_id" value="{{$value->id}}">
													<input type="hidden" name="assigned" value="assigned">
												</div>	
												@endif
												<hr>
												<?php
												}
											}
											?>
											
											<div class="row">	                                      
												<div class="col-12">
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
		$('.select-multiple1').select2({
			placeholder: "Select",
			allowClear: true
		});
		$('.select-multiple2').select2({
			placeholder: "Select",
			allowClear: true
		});
		$('.select-multiple3').select2({
			placeholder: "Select",
			allowClear: true
		});
	})
	
	function statusShow(value){
		if(value=="Dropped" || value=="Correction"){
			$('#reason').show();
		}else{
			$('#reason').hide();
		}
	}
</script>
<script type="text/javascript">
	$(".branch_id").on("change", function () {
		var branch_id = $(".branch_id option:selected").attr('value');
		var assistant_id = $("input[name=assistant_id]").val();
		if (branch_id) {
			$.ajax({
				beforeSend: function(){
					$("#branch_loader i").show();
				},
				type : 'POST',
				url : '{{ route('admin.get-branchwise-assistant') }}',
				data : {'_token' : '{{ csrf_token() }}', 'branch_id': branch_id, 'assistant_id': assistant_id},
				dataType : 'html',
				success : function (data){
					$("#branch_loader i").hide();
					$('.assistant_id').empty();
					$('.assistant_id').append(data);
				}
			});
		}
	});

	$(document).ready(function() {
		var branch_id = $(".branch_id option:selected").attr('value');
		var assistant_id = $("input[name=assistant_id]").val();
		if (branch_id) {
			$.ajax({
				beforeSend: function(){
					$("#branch_loader i").show();
				},
				type : 'POST',
				url : '{{ route('admin.get-branchwise-assistant') }}',
				data : {'_token' : '{{ csrf_token() }}', 'branch_id': branch_id, 'assistant_id': assistant_id},
				dataType : 'html',
				success : function (data){
					$("#branch_loader i").hide();
					$('.assistant_id').empty();
					$('.assistant_id').append(data);
				}
			});
		}
	});

</script>
<script type="text/javascript">
	$(".assistant_id").on("change", function () {
		var assistant_id = $(".assistant_id option:selected").attr('value');
		if (assistant_id) {
			$.ajax({
				beforeSend: function(){
					$("#chk_loader i").show();
				},
				type : 'POST',
				url : '{{ route('admin.getassistantexits') }}',
				data : {'_token' : '{{ csrf_token() }}', 'assistant_id': assistant_id},
				dataType : 'json',
				success : function (data){
					if (data.status == true) {
						$("#chk_loader i").hide();
						$(".msg").text(data.data);
						$('.btn_submit').attr('disabled', 'disabled');
					} else {
						$("#chk_loader i").hide();
						$(".msg").text(data.data);
						$('.btn_submit').removeAttr('disabled');
					}
				}
			});
		}
	});
</script>
@endsection
