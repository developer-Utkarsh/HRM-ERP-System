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
						<h2 class="content-header-title float-left mb-0">Add New Task</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">Add New Task
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
									<form class="form" action="{{ route('admin.newtask.store') }}" method="post" enctype="multipart/form-data">
										@csrf
										
										<div class="form-body">
											<div class="row">
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Task Date</label>
														<input type="date" class="form-control" placeholder="Date" name="task_date" min="<?=date('Y-m-d')?>" value="{{ date('Y-m-d') }}" required>
														@if($errors->has('task_date'))
														<span class="text-danger">{{ $errors->first('task_date') }} </span>
														@endif
													</div>
												</div>
											</div>
											<hr>
											<div class="comman">											
											<div class="row fil">
												<div class="col-md-6 col-12">	
													<div class="form-group">	
														<label for="first-name-column">Select Open Task</label>	
														<select class="form-control select-multiple4 parent_id" id="parent_id" name="parent_id[]">
														<option value="0"> - Select Open Task - </option>
														@if(count($open_ask) > 0)	
														@foreach($open_ask as $value)	
														<option value="{{ $value['id'] }}" @if($value['id'] == old('parent_id')) selected="selected" @endif><?=$value['task_title']?></option>	
														@endforeach			
														@endif		
														</select>		
														@if($errors->has('parent_id'))		
														<span class="text-danger">{{ $errors->first('parent_id') }} </span>
														@endif													
													</div>												
												</div>													
												<div class="col-md-6 col-12">
													<div class="form-group">
														<?php 
														//$users = \App\User::where('status', '1')->where('role_id', '!=','1')->where('register_id', '!=', NULL)->get();                                                          
														?>
														<label for="first-name-column">Employee</label>
														
														<select class="form-control task_added_to select-multiple1" id="task_added_to" name="task_added_to[]" required>
															<option value="{{ Auth::user()->id }}">Assign To Self </option>
															<option value="0">Open Task </option>
															@if(count($users) > 0)
															@foreach($users as $value)
															<option value="{{ $value['id'] }}" @if($value['id'] == old('task_added_to')) selected="selected" @endif><?=$value['name'] ." (".$value['register_id'].")" ." (".$value['role_name'].")";?></option>
															@endforeach	
															@endif
														</select>
														@if($errors->has('task_added_to'))
														<span class="text-danger">{{ $errors->first('task_added_to') }} </span>
														@endif
													</div>
												</div>												
											
												<div class="col-md-6 col-12 task_title_hide">
													<div class="form-group">
														<label for="">Task Title</label>
														<input type="text" class="form-control task_title" placeholder="" id="task_title" name="task_title[]" value="" maxlength="100" required>
														 
													</div>
												</div>
												<div class="col-md-6 col-12 plan_hour_hide">
													<div class="form-group">
														<label for="">Plan Hour</label>
														<input type="number" class="form-control plan_hour" placeholder="" id="plan_hour" name="plan_hour[]" value="1" step="any" min="0" required>
													</div>
												</div>
												<div class="col-md-6 col-12 spent_hour_hide">
													<div class="form-group">
														<label for="">Spent Hour</label>
														<input type="number" class="form-control spent_hour" placeholder="" id="spent_hour" name="spent_hour[]" value="0" step="any" min="0" required>
													</div>
												</div>
												<div class="col-md-6 col-12 status_hide" style="display:none;">
													<div class="form-group">
														<label for="">Status</label>
														<select class="form-control status" id="status" name="status[]" required>
															<option value="Pending"> Pending</option>
															<option value="Not Started" > Not Started</option>
															<option value="In Progress" > In Progress</option>
															<option value="Completed" > Completed</option>
															<option value="Dropped" > Dropped</option>
														</select>
														 
													</div>
												</div>	
												
												<div class="col-md-12 col-12 description_hide">
													<div class="form-group">
														<label for="">Description</label>
														<textarea class="form-control description" placeholder="" id="description" name="description[]" value="" maxlength="2500"></textarea>
													</div>
												</div>
												<div class="col-md-5 col-12 task_priority_hide">
													<div class="form-group">
														<label for="">Task Priority</label>
														<select class="form-control task_priority" id="task_priority" name="task_priority[]" required>	
														<option value="Low"> Low</option>															<option value="Medium" selected="selected"> Medium</option>
														<option value="High"> High</option>
														</select>
													</div>
												</div>
												<div class="col-md-1 col-12">
													<div class="form-group">
														<label for="">&nbsp;</label>
														<button class="btn btn-primary add-more" type="button"><i class="ficon feather icon-plus"></i>
													</div>
												</div>
												<span class="col-md-12">
													<hr>
												</span>
											</div>
											</div>
											
											
											<div class="comman append_div">
											
											</div>
											
											<div class="row">	                                      
												<div class="col-12">
													<button type="submit" class="btn btn-primary mr-1 mb-1 btn_submit">Submit</button>
												</div>
											</div>
											 
										</div>
									</form>
									
									<div class="copy-fields" style="display:none;">
										<div class="row fil remove_row">
												<div class="col-md-6 col-12">													
												<div class="form-group">														
												<label for="first-name-column">Select Open Task</label>			
												<select class="form-control parent_id select-multiple4" name="parent_id[]">
											    <option value="0"> - Select Open Task - </option>	
											    @if(count($open_ask) > 0)		
											    @foreach($open_ask as $value)														
												<option value="{{ $value['id'] }}" @if($value['id'] == old('parent_id')) selected="selected" @endif><?=$value['task_title']?></option>															@endforeach															@endif													
												</select>			
												@if($errors->has('parent_id'))													
												<span class="text-danger">{{ $errors->first('parent_id') }} </span>
												@endif										
												</div>												
												</div>	
												<div class="col-md-6 col-12">
													<div class="form-group">
														<?php 
														//$users = \App\User::where('status', '1')->where('role_id', '!=','1')->where('register_id', '!=', NULL)->get(); 
														?>
														<label for="first-name-column">Employee</label>
														@if(count($users) > 0)
														<select class="form-control task_added_to select-multiple1" name="task_added_to[]" required>
															<option value="{{ Auth::user()->id }}"> Assign To Self </option>
															<option value="0">Open Task </option>
															@foreach($users as $value)
															<option value="{{ $value['id'] }}" @if($value['id'] == old('task_added_to')) selected="selected" @endif><?=$value['name'] ." (".$value['register_id'].")"." (".$value['role_name'].")";?></option>
															@endforeach
														</select>
														@endif
														@if($errors->has('task_added_to'))
														<span class="text-danger">{{ $errors->first('task_added_to') }} </span>
														@endif
													</div>
												</div>												
											
												<div class="col-md-6 col-12 task_title_hide">
													<div class="form-group">
														<label for="">Task Title</label>
														<input type="text" class="form-control task_title" placeholder="" id="task_title" name="task_title[]" value="" maxlength="100" required>
														 
													</div>
												</div>
												<div class="col-md-6 col-12 plan_hour_hide">
													<div class="form-group">
														<label for="">Plan Hour</label>
														<input type="number" class="form-control plan_hour" placeholder="" id="plan_hour" name="plan_hour[]" value="1" step="any" min="0" required>
													</div>
												</div>
												<div class="col-md-6 col-12 spent_hour_hide">
													<div class="form-group">
														<label for="">Spent Hour</label>
														<input type="number" class="form-control spent_hour" placeholder="" id="spent_hour" name="spent_hour[]" value="0" step="any" min="0" required>
													</div>
												</div>
												<div class="col-md-6 col-12 status_hide" style="display:none;">
													<div class="form-group">
														<label for="">Status</label>
														<select class="form-control status" id="status" name="status[]" required>
															<option value="Pending"> Pending</option>
															<option value="Not Started" > Not Started</option>
															<option value="In Progress" > In Progress</option>
															<option value="Completed" > Completed</option>
															<option value="Dropped" > Dropped</option>
														</select>
														 
													</div>
												</div>	
												
												<div class="col-md-12 col-12 description_hide">
													<div class="form-group">
														<label for="">Description</label>
														<textarea class="form-control description" placeholder="" id="description" name="description[]" value="" maxlength="2500"></textarea>
													</div>
												</div>
												<div class="col-md-5 col-12 task_priority_hide">
													<div class="form-group">
														<label for="">Task Priority</label>
														<select class="form-control task_priority" id="task_priority" name="task_priority[]" required>	
															<option value="Low"> Low</option>														<option value="Medium" selected="selected"> Medium</option>
															<option value="High"> High</option>
														</select>
													</div>
												</div>
												
												
												
												
											
											<div class="col-md-1 col-12">
												<div class="form-group">
													<label for="">&nbsp;</label>
													<button class="btn btn-danger remove" type="button"><i class="ficon feather icon-delete"></i></button>
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

	function selectRefresh() {
		$('.comman .select-multiple1').select2({
			placeholder: "Select Employee",
			tags: true,
			allowClear: true,
			width:'100%'
		});
		$('.comman .select-multiple4').select2({
			placeholder: "Select Open Task",
			tags: true,
			allowClear: true,
			width:'100%'
		});
	}	
	
	$(document).ready(function() {
		selectRefresh();
		
		$('.select-multiple2').select2({
			placeholder: "Select",
			allowClear: true
		});
		$('.select-multiple3').select2({
			placeholder: "Select Branch",
			allowClear: true
		});
		$('.select-multiple5').select2({			
		    placeholder: "Select Task",	
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
	
	$(document).ready(function() {
		$(".add-more").click(function(){ 
			var html = $(".copy-fields").html();
			$(".append_div").append(html);  
			selectRefresh();
		});
		
		$("body").on("click",".remove",function(){ 
			$(this).parents(".remove_row").remove();
		});	
		
		$(document).on("change",".parent_id",function(){ 
			var thisVal        = $(this);
			var parent_id      = $(this).val();
			if(parent_id==''){
				parent_id = 0;
			}
			var comman_element = thisVal.parents(".fil").children("div").children("div");
			// if (parent_id) {
				$.ajax({
					type : 'POST',
					url : '{{ route('admin.newtask.get-open-task-detail') }}',
					data : {'_token' : '{{ csrf_token() }}', 'parent_id': parent_id},
					dataType : 'json',
					success : function (data){ //alert(data['id']);
						
					  comman_element.children(".task_title").val(data['task_title']);
					  comman_element.children(".plan_hour").val(data['plan_hour']);
					  comman_element.children(".spent_hour").val(data['spent_hour']);
					  comman_element.children(".status").val(data['status']);
					  comman_element.children(".description").val(data['task_description']);
					  comman_element.children(".task_priority").val(data['task_priority']);
						
					}
				});
			// }
		});
	});
</script>
@endsection
