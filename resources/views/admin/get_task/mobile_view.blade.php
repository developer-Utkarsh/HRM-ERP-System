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

	<?php $role_id = Input::get('role_id') ?>
	<div class="app-content content" style="margin:0;">
	
		<div class="content-wrapper" style="margin:0;">
			<div class="content-header row">
				<div class="content-header-left col-md-9 col-12 mb-2">
					<div class="row breadcrumbs-top">
						<div class="col-12">
							<h2 class="content-header-title float-left mb-0">Task</h2>
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
								<!--
								<div class="row">
									<div class="col-lg-4"><input type="radio" name="task_type" value="1" checked/> Self Task</div>
									<div class="col-lg-4"><input type="radio" name="task_type" value="2"/> Team Task</div>
								</div>
								<hr>
								-->
								<div class="users-list-filter">
									<form action="{{ route('admin.mobile-view-task') }}" method="get" name="filtersubmit">
										
										<div class="row">
											<div class="col-12 col-sm-6 col-lg-3">
												<div class="form-group">
													<?php 
													if($role == 29 || $role == 24){ 
														$users = \App\User::where('status', '1')->where('role_id', '!=','1')->where('role_id', '=','21')->where('register_id', '!=', NULL)->orWhere('supervisor_id', 'like', '%' .$logged_id . '%')->get(); 													
													}else if($role == 21){ 
														$users = \App\User::where('status', '1')->where('role_id', '!=','1')->where('role_id', '=','21')->orWhere('department_type', $department_type)->orWhere('supervisor_id', 'like', '%' .$logged_id . '%')->get(); 													
													}else{
														$users = \App\User::where('status', '1')->where('role_id', '!=','1')->where('register_id', '!=', NULL)->where('department_type', $department_type)->get(); 
													}
													?>
													<label for="first-name-column">Employee</label>
													@if(count($users) > 0)
													<select class="form-control emp_id select-multiple1" name="emp_id">
														<option value=""> - Select Employee - </option>													
														<option value="<?=$logged_id;?>" @if(!empty(app('request')->input('emp_id')) && 'Self' == app('request')->input('emp_id')) selected="selected" @endif> Assign to Self </option>
														<option value="Other" @if(!empty(app('request')->input('emp_id')) && 'Other' == app('request')->input('emp_id')) selected="selected" @endif> Assign to Other </option>
														<option value="aOther" @if(!empty(app('request')->input('emp_id')) && 'aOther' == app('request')->input('emp_id')) selected="selected" @endif> Assign by Other </option>
														
														
														@foreach($users as $value)
														<option value="{{ $value->id }}" @if($value->id == old('emp_id')) selected="selected" @endif><?=$value->name ." (".$value->register_id.")";?></option>
														@endforeach
													</select>
													@endif
												</div>
											</div>		
											
											<?php 
													if($role == 29 || $role == 24){  ?>
											<div class="col-12 col-sm-6 col-lg-2">
												<div class="form-group">
													<label for="first-name-column">Department Type</label>
													@if(count($allDepartmentTypes) > 0)
													<select class="form-control get_role select-multiple3 department_type" name="department_type" id="se_department_type">
														<option value=""> - Select Any - </option>
														@foreach($allDepartmentTypes as $value)
														<option value="{{ $value['id'] }}" @if($value['id'] == app('request')->input('department_type')) selected="selected" @endif>{{ $value['name'] }}</option>
														@endforeach
													</select>
													@endif
												</div>
											</div>
												<?php } ?>
											<!--
											<div class="col-6 col-sm-6 col-lg-2">
												<label for="first-name-column">Status</label>
												<select class="form-control emp_id select-multiple1" name="status">
													<option value=""> - Select Status - </option>												
													@php $status = ['Assign','Pending','Not Started','In Progress','Completed', 'Dropped','Deleted','Acknowledge']; @endphp
													<option value="">Select Any</option>
													@foreach($status as $key => $value)
													<option value="{{ $value }}" @if($value == app('request')->input('status')) selected="selected" @endif>{{ $value }}</option>
													@endforeach
												</select>
											</div>	
											-->
											<div class="col-6 col-sm-6 col-lg-2">
												<label for="users-list-verified">From</label>
												<fieldset class="form-group">
													<input type="date" name="fdate" class="form-control StartDateClass fdate" value="{{ app('request')->input('fdate') }}" id="">
													<input type="hidden" name="logged_id" value="{{ $logged_id }}" id="">
												</fieldset>
											</div>
											<div class="col-6 col-sm-6 col-lg-2">
												<label for="users-list-verified">To</label>
												<fieldset class="form-group">
													<input type="date" name="tdate" class="form-control EndDateClass tdate" value="{{ app('request')->input('tdate') }}" id="">
												</fieldset>
											</div>
										</div>
										
										<fieldset class="form-group" style="float:right;">		
											<button type="submit" class="btn btn-primary">Search</button>
											<!--
											<a href="{{ route('admin.mobile-view-task') }}" class="btn btn-warning">Reset</a>											
											<a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a>
											-->
										</fieldset>
									</form>
								</div>
							</div>
						</div>
					</div>
					<div>
						<?php 
							if(count($task) > 0){
								$i = 1; 
								foreach($task as $t){
									$t1 = date('Y-m-d H:i:s', strtotime( $t->created_at ));
									$t2 = date('Y-m-d H:i:s', strtotime('-2 day'));
									
									//$t2 = date('Y-m-d H:i:s', strtotime('-2 day', strtotime($t->created_at.'-2 day')));
						?>
						<div class="bg-white p-2 mb-2">
							<b>Date : </b> <?php echo date("d-m-Y", strtotime($t->date)); ?> </br>
							<div style="padding-top:5px;"><b>Meeting Title : </b> {{ $t->title }}</div>
																					
							<div style="padding:10px 0px;font-size:14px;">
								<div style="float:left;width:50%"><b>Plan : </b> {{ $t->plan }}</div>
								<?php if($t->spent!=""){ ?><div style="float:left;width:50%"><b>Spent Hour : </b> {{ $t->spent }}</div><?php } ?>
								<div style="float:left;width:50%;padding-top:5px;"><b>Status : </b>{{ $t->status }}</div>
								<div style="float:left;width:50%;padding-top:5px;"><b>Total Task : </b> {{ $t->total }}</div>
								<div style="clear:both"></div>
							</div>
							
							<div style="text-align:right;font-size:20px;">								
								<?php 
									if($t->assign_id == $logged_id && $t->emp_id == $logged_id){
								?>									
								<a title="Task Edit" href="javascript:void(0)" data-id="{{ $t->id }}" class="get_edit_data">
									<span class="action-edit"><i class="feather icon-edit"></i></span>
								</a>
								<?php 
									}
								?>
								&nbsp; 
								
								<a href="{{ route('admin.mobile-view-task-history', [$t->id, $logged_id])}}" title="View Task">View</a></td>
							</div>
						</div>	
						<?php 
							$i++; }
							}else{
						?>
						<div colspan="10" class="text-center bg-white p-2 mb-2">No Record Found</td>
						<?php } ?>
					</div>
				</section>
			</div>
		</div>
	</div>


	

<div class="modal" id="history">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Spent Hour Update</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <form method="post" action="{{ route('admin.mobile-update-spent-hour') }}">
      		{{ csrf_field() }}
	      <!-- Modal body -->
			<div class="modal-body">
				<table border="1" width="100%;" cellpadding="5" >
					<tr>
						<td><b>Spent Hour</b></td>
						<td>
							<input type="number" class="spent form-control" name="spent_hour" value="" required />
							<input type="hidden" class="task_id form-control" name="task_id" value=""/>
						</td>
					</tr>
				</table>
			</div>

			<div class="modal-footer">
				<button type="submit" class="btn btn-primary">Update</button>
			</div>
		  
      </form>

    </div>
  </div>
</div>


    <script src="{{ asset('laravel/public/admin/js/vendors.min.js') }}"></script>

    
	<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>

	
	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
	<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
	<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple1').select2({
			placeholder: "Select Any",
			allowClear: true
		});
		$('.select-multiple2').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
		
		
	$('.taskStatus').on("change", function(){
		value = $(this).val();
		
		if(value == "In-progress" || value == "Dropped"){
			$('.remark').attr('required', true);
		}else{
			$('.remark').attr('required', false);
		}
	});



	$(".get_edit_data").on("click", function() { 
			var task_id = $(this).attr("data-id");
			if(task_id){
				
				$('#history').modal({
						backdrop: 'static',
						keyboard: true, 
						show: true
				});
				
				
				$.ajax({
					type : 'POST',
					url : '{{ route('admin.mobile-edit-task') }}',
					data : {'_token' : '{{ csrf_token() }}', 'task_id': task_id},
					dataType : 'html',
					success : function (data){
						
						var data= JSON.parse(data);
						$.each(data, function(k, v) {
							// $('.date').html(v.date);
							$('.task_id').val(v.id);
							// $('.emp_id').html(v.emp_id);
							// $('.assign_id').html(v.assign_id);
							// $('.title').html(v.title);
							// $('.plan').html(v.plan);
							// $('.description').html(v.description);
							$('.spent').val(v.spent);
							$('.remark').html(v.remark);
							
							//$('.status').html(v.status);
							//$('.voice').html(v.status);
							$('.taskStatus').find('option').remove();
							
							
						let selectValues=['Pending','Not Started','Assign', 'In Progress', 'Completed', 'Dropped','Deleted','Acknowledge'];
							   selectValues.push(v.status);
							let selectValuess = [...new Set(selectValues)];
							$.each(selectValuess, function(key, value) {  
							  if(v.status==value){						
							   $('.taskStatus').append($("<option></option>").attr("selected","selected").attr("value",value).text(value));
							  }else{
								 $('.taskStatus').append($("<option></option>").attr("value", value).text(value));  
							  }
							  
							});
							
							
							
							// $(".voice").attr("src", "<?php echo URL::to('/laravel/public/task_audio'); ?>/" +v.voice);
							// $(".voice").attr("href", "<?php echo URL::to('/laravel/public/task_audio'); ?>/" +v.voice);
						});
						
						
					}
				});
				
			}
			else{
				$('#history').modal({
						backdrop: 'static',
						keyboard: true, 
						show: true
				});
			}		
		}); 

	</script>
	
	</body>
</html>