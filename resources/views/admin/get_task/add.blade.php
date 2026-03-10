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
		<div class="content-body">
			<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form action="" method="post" id="submit_task" enctype='multipart/form-data'>
									@csrf
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
															if(Auth::user()->role_id == 29 || Auth::user()->role_id == 24){ 
																//$users = \App\User::where('status', '1')->where('role_id', '!=','1')->where('role_id', '=','21')->where('register_id', '!=', NULL)->where('is_deleted', '0')->orWhere('supervisor_id', 'like', '%' . Auth::user()->id . '%')->get(); 	

																$users = DB::table('users')
																		->select('users.id','users.name','users.name','users.register_id','userdetails.degination')
																		->leftJoin('userdetails', 'userdetails.user_id', '=', 'users.id')
																		->where('users.status', '1')
																		->where('users.role_id', '!=','1')
																		->where('users.register_id', '!=', NULL)
																		->where('users.is_deleted', '0')
																		->whereRaw('( users.role_id = 21 or users.department_type = "'.Auth::user()->department_type.'" or users.supervisor_id like "%'. Auth::user()->id .'%" )')
																		->orderby('name','ASC')
																		->get();
																
															}else if(Auth::user()->role_id == 21){ 
																$users = DB::table('users')
																		->select('users.id','users.name','users.name','users.register_id','userdetails.degination')
																		->leftJoin('userdetails', 'userdetails.user_id', '=', 'users.id')
																		->where('users.status', 1)
																		->where('users.role_id', '!=',1)
																		->where('users.register_id', '!=', NULL)
																		->where('users.is_deleted', '0')
																		->whereRaw('( users.role_id = 21 or users.department_type = "'.Auth::user()->department_type.'" or users.supervisor_id like "%'. Auth::user()->id .'%" )')
																		->orderby('name','ASC')
																		->get();
																		
																		// echo $users; die;
															}else{
																//$users = \App\User::where('status', '1')->where('role_id', '!=','1')->where('register_id', '!=', NULL)->where('department_type', Auth::user()->department_type)->get();

																$users = DB::table('users')
																		->select('users.id','users.name','users.name','users.register_id','userdetails.degination')
																		->leftJoin('userdetails', 'userdetails.user_id', '=', 'users.id')
																		->where('users.status', '1')
																		->where('users.is_deleted', '0')
																		->where('users.role_id', '!=','1')
																		->where('users.register_id', '!=', NULL)
																		->whereRaw('( users.department_type = "'.Auth::user()->department_type.'" or users.supervisor_id like "%'. Auth::user()->id .'%" )')
																		->orderby('name','ASC')
																		->get();
															}
															?>
															<label for="first-name-column">Employee</label>
															@if(count($users) > 0)
															<select class="form-control emp_id select-multiple1" name="emp_id[]">
																<option value=""> - Select Employee - </option>
																<option value="<?=Auth::user()->id;?>" selected> Assign to Self </option>
																@foreach($users as $value)
																
																<?php $dd= str_replace("'","",$value->name ." (".$value->register_id." - ".$value->degination.")");?>
																
																<option value="{{ $value->id }}" @if($value->id == old('emp_id')) selected="selected" @endif><?=$dd;?></option>
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
															
															<div class="row pt-2">
																<div class="col-lg-10"><input type="text" name="description[0][0]" value="" class="form-control" placeholder="Description" required /></div>
																<div class="col-lg-2">
																	<button type="button" class="add-key-point btn-success border-0" data-index="0" data-row="0"><i class="feather icon-plus"></i></button>
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
												
											</div>
											
											<div class="append_div">
											
											</div>

											<hr style="background: #000;">
											<div class="row text-right">	                                      
												<div class="col-12">
													<button type="submit" class="btn btn-primary mr-1 mb-1 btn_submit">Submit </button>
												</div>
											</div>											 
										</div>
									</form>
									
									<!--- New Append HTML -->
																
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

<style type="text/css">
.select2-container {
	display: inherit !important;
}


input[type=time]::-webkit-clear-button {
   -webkit-appearance: none;
   -moz-appearance: none;
   -o-appearance: none;
   -ms-appearance:none;
   appearance: none;
   margin: -10px; 
 }
</style>
@endsection



@section('scripts')

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

	var key_html='<div class="key_remove_row"><div class="row pt-2"><div class="col-lg-10">';
	    key_html+='<input type="text" name="description['+row+']['+arr_index+']" value="" class="form-control"placeholder="Description"/>';
	    key_html+='</div><div class="col-lg-2">';
		key_html+='<button type="button" class="key_remove btn-danger border-0"><i class="feather icon-minus"></i></button></div></div></div>';
	$(".key_append_div"+row).append(key_html);  


	//var html = $(".key-copy-fields").html();
	//$(".key_append_div").append(html);  
});

$("body").on("click",".key_remove",function(){ 
	$(this).parents(".key_remove_row").remove();
});


var html = '<div class="remove_row"><hr style="background: #000;"><div class="row"><div class="col-md-6 col-12"><div class="form-group"><label for="first-name-column">Task Date</label><input type="date" class="form-control date" placeholder="Date" name="date[]" value="<?=date("Y-m-d");?>"  min="{{date('Y-m-d', time() - 86400)}}" required></div></div><div class="col-md-6 col-12"><div class="form-group"><?php if(Auth::user()->role_id == 29 || Auth::user()->role_id == 24){
	$users = DB::table('users')
																		->select('users.id','users.name','users.name','users.register_id','userdetails.degination')
																		->leftJoin('userdetails', 'userdetails.user_id', '=', 'users.id')
																		->where('users.status', '1')
																		->where('users.role_id', '!=','1')
																		->where('users.register_id', '!=', NULL)
																		->where('users.is_deleted', '0')
																		->whereRaw('( users.role_id = 21 or users.department_type = "'.Auth::user()->department_type.'" or users.supervisor_id like "%'. Auth::user()->id .'%" )')
																		->orderby('name','ASC')
																		->get();
																
															}else if(Auth::user()->role_id == 21){
																
																$users = DB::table('users')
																		->select('users.id','users.name','users.name','users.register_id','userdetails.degination')
																		->leftJoin('userdetails', 'userdetails.user_id', '=', 'users.id')
																		->where('users.status', 1)
																		->where('users.role_id', '!=',1)
																		->where('users.register_id', '!=', NULL)
																		->where('users.is_deleted', '0')
																		->whereRaw('( users.role_id = 21 or users.department_type = "'.Auth::user()->department_type.'" or users.supervisor_id like "%'. Auth::user()->id .'%" )')
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
																		->whereRaw('( users.department_type = "'.Auth::user()->department_type.'" or users.supervisor_id like "%'. Auth::user()->id .'%" )')
																		->orderby('name','ASC')
																		->get();
															}
															?><label for="first-name-column">Employee</label><?php if(count($users) > 0){ ?><select class="form-control emp_id select-multiple2" name="emp_id[]"><option value=""> - Select Employee - </option><option value="<?=Auth::user()->id;?>" selected> Assign to Self </option><?php foreach($users as $value){ 
																 $dd= str_replace("'","",$value->name ." (".$value->register_id." - ".$value->degination.")");?><option value="{{ $value->id }}" <?=($value->id == old('emp_id')) ? "selected":''?>><?=$dd;?></option><?php } ?></select><?php } ?></div></div></div><div class="row"><div class="col-md-6 col-12"><div class="form-group"><label for="">Title</label><input type="text" class="form-control title" placeholder="" name="title[]" value="" maxlength="100"></div></div><div class="col-md-6 row "><div class="form-group col-md-12 col-12"><label for="">Plan Hour</label>																		<input type="number" class="form-control plan_hour" placeholder="" name="plan_hour[]" value="0" step="any" min="0">											</div>																</div><div class="col-md-12 col-12">	<div class="form-body"><div class="row pt-2"><div class="col-lg-10"><input type="text" name="description[][0]" value="" class="form-control" placeholder="Description" required /></div>																<div class="col-lg-2"><button type="button" class="add-key-point btn-success border-0" data-index="0" data-row="0"><i class="feather icon-plus"></i></button></div></div><div class="key_append_div">	</div>																 </div>                             </div></div><div class="row text-right"><div class="col-12"><button class="btn-danger remove border-0" type="button" style="margin-top:18px;">Remove</button></div></div></div>';
	
	
	
	
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
			url 		: '{{ route('admin.task-store') }}',
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


@endsection
