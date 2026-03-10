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
						<h2 class="content-header-title float-left mb-0">Edit New Task</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Edit New Task</a>
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
									<form class="form" action="{{ route('admin.newtask.update', $user_id) }}" method="post" enctype="multipart/form-data">
										@method('PATCH')
										@csrf
										<div class="form-body">
											<div class="row">
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Task Date</label>
														<input type="date" class="form-control" placeholder="Date" name="task_date" value="{{ old('task_date', $date) }}" readonly>
														@if($errors->has('task_date'))
														<span class="text-danger">{{ $errors->first(task_date) }} </span>
														@endif
													</div>
												</div>
											</div>
											<hr>
											<?php
											foreach($task_details as $key => $task){
												// print_r($task); die;
											?>
												<div class="row fil">
													<input type="hidden" name="task_array[{{$key}}][task_id]" value="<?=$task->id?>">
													<input type="hidden" name="task_array[{{$key}}][task_date]" value="<?=$task->task_date?>">
													
													<div class="col-md-6 col-12">
														<div class="form-group">
															<label for="first-name-column">Employee</label>
															
															<select class="form-control task_added_to select-multiple1" name="task_array[{{$key}}][task_added_to]">
																<option value=""> - Select Employee - </option>																<option value="{{ Auth::user()->id }}" @if(Auth::user()->id == old('task_added_to', $task->task_added_to)) selected="selected" @endif> Assign By Self </option>																@if(count($users) > 0)
																@foreach($users as $value)
																<option value="{{ $value['id'] }}" @if($value['id'] == old('task_added_to', $task->task_added_to)) selected="selected" @endif><?=$value['name'] ." (".$value['register_id'].")" ." (".$value['role_name'].")";?></option>
																@endforeach																@endif
															</select>
															
															@if($errors->has('task_added_to'))
															<span class="text-danger">{{ $errors->first('task_added_to') }} </span>
															@endif
														</div>
													</div>												
												
													<div class="col-md-6 col-12">
														<div class="form-group">
															<label for="">Task Title</label>
															<input type="text" class="form-control" placeholder="" name="task_array[{{$key}}][task_title]" value="{{ old('task_title', $task->task_title) }}" maxlength="100">
															 
														</div>
													</div>
													<div class="col-md-6 col-12">
														<div class="form-group">
															<label for="">Plan Hour</label>
															<input type="number" class="form-control" placeholder="" name="task_array[{{$key}}][plan_hour]" value="{{ old('plan_hour', $task->plan_hour) }}" step="any" min="0">
														</div>
													</div>
													<div class="col-md-6 col-12">
														<div class="form-group">
															<label for="">Spent Hour</label>
															<input type="number" class="form-control" placeholder="" name="task_array[{{$key}}][spent_hour]" value="{{ old('spent_hour', $task->spent_hour) }}" step="any" min="0">
														</div>
													</div>
													<div class="col-md-6 col-12">
														<div class="form-group">
															<label for="">Status</label>
															<select class="form-control" name="task_array[{{$key}}][status]" required>
																<option value="Pending" @if($task->status == 'Pending') selected="selected" @endif> Pending</option>
																	<option value="Not Started" @if($task->status == 'Not Started') selected="selected" @endif> Not Started</option>
																	<option value="In Progress" @if($task->status == 'In Progress') selected="selected" @endif> In Progress</option>
																	<option value="Completed" @if($task->status == 'Completed') selected="selected" @endif> Completed</option>
																	<option value="Dropped" @if($task->status == 'Dropped') selected="selected" @endif> Dropped</option>
															</select>
															 
														</div>
													</div>	
													
													<div class="col-md-6 col-12">
														<div class="form-group">
															<label for="">Description</label>
															<textarea class="form-control" placeholder="" name="task_array[{{$key}}][task_description]" value="{{ old('description', $task->task_description) }}" maxlength="2500"></textarea>
														</div>
													</div>
													<div class="col-md-5 col-12">
														<div class="form-group">
															<label for="">Task Priority</label>
															<select class="form-control" name="task_array[{{$key}}][task_priority]">
																<option value="">Select</option>
																<option value="Low" @if($task->task_priority == 'Low') selected="selected" @endif> Low</option>
																<option value="Medium" @if($task->task_priority == 'Medium') selected="selected" @endif> Medium</option>
																<option value="High" @if($task->task_priority == 'High') selected="selected" @endif> High</option>
															</select>
														</div>
													</div>
												</div>
												<hr>
											<?php
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
</script>
<script type="text/javascript">
	$(document).on("change",".file_type", function () { 
	    var thisVal    = $(this); 
	    var changeVal    = $(this).val(); 
		
		if (changeVal == 'file') {
			thisVal.parents('.fil').children('div').children('.file_link').show();
		}
		else{
			thisVal.parents('.fil').children('div').children('.file_link').hide();
		}
	});
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
