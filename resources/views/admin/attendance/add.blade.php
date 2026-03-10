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
						<h2 class="content-header-title float-left mb-0">Add Attendance</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">Add Attendance
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
									<form class="form" action="{{ route('admin.attendance.store') }}" method="post" enctype="multipart/form-data">
										@csrf
										
										<div class="form-body">
											<div class="row">
											@if(Auth::user()->role_id != 20)
												<div class="col-md-6 col-12">
													<div class="form-group">
														<?php 
														//$users = \App\User::where('status', '1')->where('role_id', '!=','1')->where('register_id', '!=', NULL)->get(); 
														
														?>
														<label for="first-name-column">Employee</label>
														@if(count($users) > 0)
														<select class="form-control select-multiple1" name="emp_id" required>
															<option value=""> - Select Employee - </option>
															@foreach($users as $value)
															<option value="{{ $value['id'] }}" @if($value['id'] == old('emp_id')) selected="selected" @endif><?=$value['name'] ." (".$value['register_id'].")"." (".$value['role_name'].")";?></option>
															@endforeach
														</select>
														@endif
														@if($errors->has('emp_id'))
														<span class="text-danger">{{ $errors->first('emp_id') }} </span>
														@endif
													</div>
												</div>
												@else
												<input type="hidden" name="emp_id" value="{{ Auth::user()->id }}">
												@endif
												<?php 
												$mindate = "";
												$current_date = date('j');
												$current_date = $current_date -1;
												/* if(Auth::user()->role_id == 24 || Auth::user()->role_id == 29 || Auth::user()->id == 1522 || Auth::user()->id == 1216 || Auth::user()->id == 1050 || Auth::user()->id == 1117 || Auth::user()->id == 1215 || Auth::user()->id == 1812 || Auth::user()->id == 1732 || Auth::user()->id == 5552 || Auth::user()->id == 1912 || Auth::user()->id == 1004 || Auth::user()->id == 901){
													
												} */
												if(Auth::user()->role_id == 24 || Auth::user()->role_id == 29){
													
												}
												else{
													$mindate = date('Y-m-d');
													//date('Y-m-d', strtotime("-$current_date days")) // for current month only
												}
												?>
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Date</label>
														<input type="date" class="form-control" placeholder="Date" name="date" value="{{ date('Y-m-d') }}"  required>
<!-- 														 max="{{ date('Y-m-d') }}" @if(Auth::user()->id != 901)min="{{date('Y-m-d', strtotime("-$current_date days"))}}"@endif
 -->														<!--@if(Auth::user()->id != 901)min="{{date('Y-m-d', strtotime("-$current_date days"))}}"@endif -->
														@if($errors->has('date'))
														<span class="text-danger">{{ $errors->first('date') }} </span>
														@endif
													</div>
												</div>
												
											</div>
											<hr>
											<div class="row">
												<div class="col-md-3 col-12">
													<div class="form-group">
														<label for="">In Time</label>
														<input type="time" class="form-control" placeholder="" name="time[]" value="">
														 
													</div>
												</div>
												<div class="col-md-3 col-12">
													<div class="form-group">
														<label for="">Type</label>
														<select class="form-control" name="type[]">
															<option value="In">In</option>
														</select>
														 
													</div>
												</div>
												<div class="col-md-3 col-12">
													<div class="form-group">
														<label for="">Out Time</label>
														<input type="time" class="form-control" placeholder="" name="time[]" value="">
														 
													</div>
												</div>
												<div class="col-md-3 col-12">
													<div class="form-group">
														<label for="">Type</label>
														<select class="form-control" name="type[]">
															<option value="Out">Out</option>
														</select>
														 
													</div>
												</div>
											</div>
											<hr>
											
											<div class="row">
												<div class="col-md-3 col-12">
													<div class="form-group">
														<label for="">In Time</label>
														<input type="time" class="form-control" placeholder="" name="time[]" value="">
														 
													</div>
												</div>
												<div class="col-md-3 col-12">
													<div class="form-group">
														<label for="">Type</label>
														<select class="form-control" name="type[]">
															<option value="In">In</option>
														</select>
														 
													</div>
												</div>
												<div class="col-md-3 col-12">
													<div class="form-group">
														<label for="">Out Time</label>
														<input type="time" class="form-control" placeholder="" name="time[]" value="">
														 
													</div>
												</div>
												<div class="col-md-3 col-12">
													<div class="form-group">
														<label for="">Type</label>
														<select class="form-control" name="type[]">
															<option value="Out">Out</option>
														</select>
														 
													</div>
												</div>
											</div>
											<hr>
											
											<div class="row">
												<div class="col-md-4 col-12">
													<div class="form-label-group">
														<select class="form-control select-multiple1" name="for_reason" required>
															<option value=""> - Select Reason For- </option>
															<option value="0" selected>Both</option>
															<option value="1">In</option>
															<option value="2">Out</option>
														</select>
													</div>
												</div>											
												<div class="col-md-8 col-12">
													<div class="form-label-group">
														<textarea name="reason" placeholder="Reason" class="form-control remark" required></textarea>
													</div>
												</div>
											</div>
											
											<div class="row">	                                      
												<div class="col-12">
													<button type="submit" class="btn btn-primary mr-1 mb-1 btn_submit">Submit</button>
												</div>
											</div>
											 
										</div>
									</form>
									
									<div class="copy-fields" style="display:none;">
										<div class="row remove_row">
											<div class="col-md-6 col-12">
												<div class="form-group">
													<label for="">Task Name</label>
													<input type="text" class="form-control" placeholder="" name="name[]" value="" maxlength="100" required>
													 
												</div>
											</div>
											<div class="col-md-6 col-12">
												<div class="form-group">
													<label for="">Plan Hour</label>
													<input type="number" class="form-control" placeholder="" name="plan_hour[]" value="0" step="any">
												</div>
											</div>
											<div class="col-md-6 col-12">
												<div class="form-group">
													<label for="">Spent Hour</label>
													<input type="number" class="form-control" placeholder="" name="spent_hour[]" value="0" step="any">
												</div>
											</div>
											<div class="col-md-6 col-12">
												<div class="form-group">
													<label for="">Status</label>
													<select class="form-control" name="status[]" required>
														<option value=""> - Select Status - </option>
														<option value="Pending"> Pending</option>
														<option value="Not Started" > Not Started</option>
														<option value="In Progress" > In Progress</option>
														<option value="Completed" > Completed</option>
														<option value="Dropped" > Dropped</option>
													</select>
													 
												</div>
											</div>	
											
											<div class="col-md-6 col-12">
												<div class="form-group">
													<label for="">Description</label>
													<input type="text" class="form-control" placeholder="" name="description[]" value="" maxlength="250">
												</div>
											</div>
											<div class="col-md-6 col-12">
												<div class="form-group">
													<label for="">&nbsp;</label>
													<button class="btn btn-danger remove" type="button" style="margin-top:18px;">Remove</button>
												</div>
											</div>
											<span class="col-md-12">
												<hr>
											</span>
										</div>
									</div>
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
			placeholder: "Select Employee",
			allowClear: true
		});
		$('.select-multiple2').select2({
			placeholder: "Select",
			allowClear: true
		});
		$('.select-multiple3').select2({
			placeholder: "Select Branch",
			allowClear: true
		});
	})
</script>
<script type="text/javascript">
	$(".branch_id").on("change", function () {
		var branch_id = $(".branch_id option:selected").attr('value');
		if (branch_id) {
			$.ajax({
				beforeSend: function(){
					$("#branch_loader i").show();
				},
				type : 'POST',
				url : '{{ route('admin.get-branchwise-employee') }}',
				data : {'_token' : '{{ csrf_token() }}', 'branch_id': branch_id},
				dataType : 'html',
				success : function (data){
					$("#branch_loader i").hide();
					$('.emp_id').empty();
					$('.emp_id').append(data);
				}
			});
		}
	});
</script>
<script type="text/javascript">
	
	$(document).ready(function() {
		$(".add-more").click(function(){ 
			var html = $(".copy-fields").html();
			$(".append_div").append(html);    
		});
		$("body").on("click",".remove",function(){ 
			$(this).parents(".remove_row").remove();
		});
	});
</script>
@endsection
