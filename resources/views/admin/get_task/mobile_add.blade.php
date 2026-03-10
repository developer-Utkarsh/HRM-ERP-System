<!DOCTYPE html>
<html class="loading" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="">
    <meta name="keywords" content="">   

	<link href="{{url('../laravel/public/logo.png')}}" rel="icon" type="image/ico" />

    <title>{{ config('app.name') }} - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/vendors.min.css') }}">
   
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/colors.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('laravel/public/admin/css/components.css') }}">
 
</head>
<body class="vertical-layout vertical-menu-modern 2-columns  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="2-columns" style="background-image: linear-gradient(to top, #f38800, #f39300, #f29e00, #f1a900, #f0b400, #efbc02, #eec506, #edcd0e, #edd60e, #ecde11, #eae716, #e8f01c);">
	
	<div class="app-content content" style="margin:0;">
		
		<div class="content-wrapper" style="margin:0;">
			<!--
			<div class="content-header row">
				<div class="content-header-left col-md-12 col-12 mb-2">
					<div class="row breadcrumbs-top">
						<div class="col-8">
							<h2 class="content-header-title float-left mb-0">Add Task</h2>
							<div class="breadcrumb-wrapper col-12">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
									</li>
									<li class="breadcrumb-item active">Add Task
									</li>
								</ol>
							</div>
						</div>
					</div>
				</div>
				<div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
				</div>
			</div>
			-->
			<div class="content-header row">
				<div class="content-header-left col-md-9 col-12 mb-2">
					<div class="row breadcrumbs-top">
						<div class="col-12">
							<h2 class="content-header-title float-left mb-0">Add Task</h2>
						</div>
					</div>
				</div>
			</div>
			<div class="content-body">
				<section id="multiple-column-form">
					<div class="row match-height">
						<div class="col-12">
							<div class="card">
								<div class="card-content">
									<div class="card-body">
										<form action="" method="post" id="submit_task" enctype='multipart/form-data'>
											<input type="hidden" class="dddd" value="1">
											<div class="form-body">
												<div class="">
													<div class="row">
														<div class="col-md-6 col-12">
															<div class="form-group">
																<label for="first-name-column">Task Date</label>
																<input type="date" class="form-control date" placeholder="Date" name="date[]" value="<?=date("Y-m-d");?>"  min="{{date('Y-m-d', time() - 86400)}}" required>
																@if($errors->has('date'))
																<span class="text-danger">{{ $errors->first('date') }} </span>
																@endif
															</div>
														</div>
														<div class="col-md-6 col-12">
															<div class="form-group">
																<?php 
																if($role == 29 || $role == 24){ 
																	// $users = \App\User::where('status', 1)->where('role_id', '!=','1')->where('role_id', '=','21')->where('register_id', '!=', NULL)->where('is_deleted', '0')->orWhere('supervisor_id', 'like', '%' . $emp_id . '%')->orderby('name','ASC')->get(); 
																	$users = DB::table('users')
																		->select('users.id','users.name','users.name','users.register_id','userdetails.degination')
																		->leftJoin('userdetails', 'userdetails.user_id', '=', 'users.id')
																		->where('users.status', '1')
																		->where('users.role_id', '!=','1')
																		->where('users.register_id', '!=', NULL)
																		->where('users.is_deleted', '0')
																		->whereRaw('( users.role_id = 21 or users.department_type = "'.$department_type.'" or users.supervisor_id like "%'. $emp_id .'%" )')
																		->orderby('name','ASC')
																		->get();
																}else if($role == 21){ 
																	//$users = \App\User::where('status', 1)->where('role_id', '!=','1')->where('role_id', '=','21')->where('register_id', '!=', NULL)->where('is_deleted', '0')->orwhere('department_type', $department_type)->orwhere('supervisor_id', 'like', '%' . $emp_id . '%')->orderby('name','ASC')->get(); 
																	
																	$users = DB::table('users')
																		->select('users.id','users.name','users.name','users.register_id','userdetails.degination')
																		->leftJoin('userdetails', 'userdetails.user_id', '=', 'users.id')
																		->where('users.status', 1)
																		->where('users.role_id', '!=',1)
																		->where('users.register_id', '!=', NULL)
																		->where('users.is_deleted', '0')
																		->whereRaw('( users.role_id = 21 or users.department_type = "'.$department_type.'" or users.supervisor_id like "%'. $emp_id .'%" )')
																		->orderby('name','ASC')
																		->get();
																}else{
																	// $users = \App\User::where('status', 1)->where('role_id', '!=','1')->where('register_id', '!=', NULL)->where('department_type', $department_type)->orderby('name','ASC')->get(); 
																	
																	$users = DB::table('users')
																		->select('users.id','users.name','users.name','users.register_id','userdetails.degination')
																		->leftJoin('userdetails', 'userdetails.user_id', '=', 'users.id')
																		->where('users.status', '1')
																		->where('users.is_deleted', '0')
																		->where('users.role_id', '!=','1')
																		->where('users.register_id', '!=', NULL)
																		->whereRaw('( users.department_type = "'.$department_type.'" or users.supervisor_id like "%'. $emp_id .'%" )')
																		->orderby('name','ASC')
																		->get();
																}
																?>
																<label for="first-name-column">Employee</label>
																@if(count($users) > 0)
																<select class="form-control emp_id select-multiple1" name="employee_id[]" required>
																	<option value=""> - Select Employee - </option>
																	<option value="<?=$emp_id;?>" selected> Assign to Self </option>
																	@foreach($users as $value)
																	<option value="{{ $value->id }}" @if($value->id == old('emp_id')) selected="selected" @endif><?=$value->name ." (".$value->register_id.")";?></option>
																	@endforeach
																</select>
																@endif
																@if($errors->has('emp_id'))
																<span class="text-danger">{{ $errors->first('emp_id') }} </span>
																@endif
															</div>
														</div>
													</div>	
													<div class="row">
														<div class="col-md-6 col-12">
															<div class="form-group">
																<label for="">Title</label>
																<input type="text" class="form-control title" placeholder="" name="title[]" value="" maxlength="100" required>
																<input type="hidden" name="logged_id" value="<?=$emp_id;?>" required>
																@if($errors->has('title'))
																<span class="text-danger">{{ $errors->first('title') }} </span>
																@endif 
															</div>
														</div>												
														<div class="col-md-6 col-12">
															<div class="row">
																<div class="form-group col-md-12 col-12">
																	<label for="">Plan Hour</label>
																	<input type="number" class="form-control plan_hour" placeholder="" name="plan_hour[]" value="0" step="any" required>
																</div>
															</div>
														</div>
														<div class="col-md-12 col-12">
															
															<!--
															<div class="form-group">
																<label for="">Description</label>
																<textarea class="form-control description" name="description[]" rows="3"></textarea>
															</div>
															-->														
															<div class="form-body">	
																	
																
																<div class="row mx-0 pt-2">
																	<div class="float-left" style="width:80%"><input type="text" name="description[0][0]" value="" class="form-control" placeholder="Description" required /></div>
																	<div class="float-left" style="width:20%;padding-left:10px;">
																		<button type="button" class="add-key-point btn-success border-0" style="padding:5px;" data-index="0" data-row="0"><i class="feather icon-plus"></i></button>
																	</div>
																</div>
																<div class="key_append_div0">
																	
																</div>																		
															</div>												
														</div>												
													</div>
													<div class="row text-right pt-2">
														<div class="col-12">
															<button class="btn-info add-more border-0" type="button" data-row="0">Add More Task</button>
														</div>
													</div>
													<!--
													<div class="row mx-0">												
														<div id="controls">
															<button type="button" class="btn btn-light"	id="recordButton">Record</button>
															<button type="button" class="btn btn-light"	id="pauseButton" disabled>Pause</button>
															<button type="button" class="btn btn-light"	id="stopButton" disabled>Stop</button>
														</div>
														&nbsp;&nbsp; <div id="formats"></div>
														<ol id="recordingsList"></ol>
													</div>
													-->
												</div>
												
												<div class="append_div">
												
												</div>

												<!--
												<div class="row">
													<div class="">
														<label for="">&nbsp;</label>
														<button class="btn btn-primary add-more" type="button" style="margin-top:18px;">Add More</button>
													</div>
												</div>
												-->
												<hr style="background: #000;">
												<div class="row text-right">	                                      
													<div class="col-12">
														<button type="submit" class="btn btn-primary mr-1 mb-1 btn_submit">Submit </button>
													</div>
												</div>											 
											</div>
										</form>
										
										<!--- New Append HTML  -->
										
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

    
    <script src="{{ asset('laravel/public/admin/js/vendors.min.js') }}"></script>

    
	<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>

 
		
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>


		<!--
	<script src="https://cdn.rawgit.com/mattdiamond/Recorderjs/08e7abd9/dist/recorder.js"></script>
	<script src="{{ asset('laravel/public/js/audio_recorder.js')}} "></script>
	<script src="{{ asset('laravel/public/js/audio_app.js')}} "></script>
	-->
	<script type="text/javascript">
		$(document).ready(function() {
		$('.select-multiple1').select2({
			placeholder: "Select Employee",
			allowClear: true
		});
		
	});	


	//$('.add-key-point').click(function() {
	$(document).on('click', '.add-key-point', function(e){
		var row=$(this).attr('data-row');
		var arr_index=$(this).attr('data-index');
		arr_index=parseInt(arr_index)+1;
		$(this).attr('data-index',arr_index);

		var key_html='<div class="key_remove_row"><div class="row pt-2 mx-0"><div class="float-left" style="width:80%;">';
			key_html+='<input type="text" name="description['+row+']['+arr_index+']" value="" class="form-control"placeholder="Description"/>';
			key_html+='</div><div class="float-left" style="width:20%;padding-left:10px;">';
			key_html+='<button type="button" class="key_remove btn-danger border-0" style="padding:5px;"><i class="feather icon-minus"></i></button></div></div></div>';
		$(".key_append_div"+row).append(key_html);  


		//var html = $(".key-copy-fields").html();
		//$(".key_append_div").append(html);  
	});

	$("body").on("click",".key_remove",function(){ 
		$(this).parents(".key_remove_row").remove();
	});

	
var html = '<div class="remove_row"><hr style="background: #000;"><div class="row"><div class="col-md-6 col-12"><div class="form-group"><label for="first-name-column">Task Date</label><input type="date" class="form-control date" placeholder="Date" name="date[]" value="<?=date("Y-m-d");?>"  min="{{date('Y-m-d', time() - 86400)}}" required></div></div><div class="col-md-6 col-12"><div class="form-group"><?php if($role == 29 || $role == 24){
	$users = DB::table('users')
																		->select('users.id','users.name','users.name','users.register_id','userdetails.degination')
																		->leftJoin('userdetails', 'userdetails.user_id', '=', 'users.id')
																		->where('users.status', '1')
																		->where('users.role_id', '!=','1')
																		->where('users.register_id', '!=', NULL)
																		->where('users.is_deleted', '0')
																		->whereRaw('( users.role_id = 21 or users.department_type = "'.$department_type.'" or users.supervisor_id like "%'. $emp_id .'%" )')
																		->orderby('name','ASC')
																		->get();
																
															}else if($role == 21){
																
																$users = DB::table('users')
																		->select('users.id','users.name','users.name','users.register_id','userdetails.degination')
																		->leftJoin('userdetails', 'userdetails.user_id', '=', 'users.id')
																		->where('users.status', 1)
																		->where('users.role_id', '!=',1)
																		->where('users.register_id', '!=', NULL)
																		->where('users.is_deleted', '0')
																		->whereRaw('( users.role_id = 21 or users.department_type = "'.$department_type.'" or users.supervisor_id like "%'. $emp_id .'%" )')
																		->orderby('name','ASC')
																		->get();
															}else{

																$users = DB::table('users')
																		->select('users.id','users.name','users.name','users.register_id','userdetails.degination')
																		->leftJoin('userdetails', 'userdetails.user_id', '=', 'users.id')
																		->where('users.status', '1')
																		->where('users.is_deleted', '0')
																		->where('users.role_id', '!=','1')
																		->where('users.register_id', '!=', NULL)
																		->whereRaw('( users.department_type = "'.$department_type.'" or users.supervisor_id like "%'. $emp_id .'%" )')
																		->orderby('name','ASC')
																		->get();
															}
															?><label for="first-name-column">Employee</label><?php if(count($users) > 0){ ?><select class="form-control emp_id select-multiple2" name="employee_id[]"><option value=""> - Select Employee - </option><option value="<?=$emp_id;?>" selected> Assign to Self </option><?php foreach($users as $value){ 
																 $dd= str_replace("'","",$value->name ." (".$value->register_id." - ".$value->degination.")");?><option value="{{ $value->id }}" <?=($value->id == old('emp_id')) ? "selected":''?>><?=$dd;?></option><?php } ?></select><?php } ?></div></div></div><div class="row"><div class="col-md-6 col-12"><div class="form-group"><label for="">Title</label><input type="text" class="form-control title" placeholder="" name="title[]" value="" maxlength="100"><input type="hidden" name="logged_id" value="<?=$emp_id;?>" required></div></div><div class="col-md-6 row "><div class="form-group col-md-12 col-12"><label for="">Plan Hour</label>																		<input type="number" class="form-control plan_hour" placeholder="" name="plan_hour[]" value="0" step="any" min="0" required>											</div>																</div><div class="col-md-12 col-12">	<div class="form-body">																 <div class="row mx-0 pt-2"><div class="float-left" style="width:80%"><input type="text" name="description[][0]" value="" class="form-control" placeholder="Description" required/></div>																<div class="float-left" style="width:20%;padding-left:10px;"><button type="button" class="add-key-point btn-success border-0" style="padding:5px;" data-index="0" data-row="0"><i class="feather icon-plus"></i></button></div></div><div class="key_append_div">	</div>		</div>                             </div></div><div class="row text-right"><div class="col-12"><button class="btn-danger remove border-0" type="button" style="margin-top:18px;">Remove</button></div></div></div>';
	
	
	
	
	$(document).ready(function() {
		$('.select-multiple1').select2({
			placeholder: "Select Employee",
			allowClear: true
		});
		
	});	
	
	
	$('.add-more').click(function() {
		var row=$(this).attr('data-row');
	    row=parseInt(row)+1;
	    $(this).attr('data-row',row);

	    var append_data=html;

	    append_data = append_data.replace('key_append_div', 'key_append_div'+row);
	    append_data = append_data.replace('data-row="0"', 'data-row="'+row+'"');
	    append_data = append_data.replace('description[][0]', 'description['+row+'][0]');

		// var html = $(".copy-fields").html();
		$(".append_div").append(append_data);    
		
		// $('.select-multiple2').select2();
		
		$('.select-multiple2').select2({
			placeholder: "Select Employee",
			allowClear: true,
			width: '100%',	
		});
	});
	
	
	$("body").on("click",".remove",function(){ 
		$(this).parents(".remove_row").remove();
	});
</script>

	<script type="text/javascript">		
		$("#submit_task").submit(function(e) {
			e.preventDefault();
			if($(".dddd").val() == '1'){
				formsubmit(1,'','');
			}
		});
		
		function formsubmit(panel, blob, filename){
			
			var form = document.getElementById('submit_task');
			var dataForm = new FormData(form); 
					
			if(panel==2){
				dataForm.append("audio_data",blob, filename);
			}
						
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},      
				type		: "POST",
				url 		: '{{ route('admin.mobile-task-store') }}',
				data 		: dataForm,
				processData : false, 
				contentType : false,
				dataType 	: 'json',
				success 	: function(data){
					console.log(data);
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
	</script>
    @include('layouts.notification')
    
</body>
</html>


	
