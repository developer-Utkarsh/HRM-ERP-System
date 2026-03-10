@extends('layouts.admin')
@section('content')

<?php $role_id = Auth::user()->role_id; ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Task</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
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
								<form action="{{ route('admin.view-task') }}" method="get" name="filtersubmit">
									
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<?php 
											if(Auth::user()->role_id == 29 || Auth::user()->role_id == 24 || Auth::user()->department_type==51){ 
												$users = DB::table('users')
														->select('users.id','users.name','users.name','users.register_id','userdetails.degination')
														->leftJoin('userdetails', 'userdetails.user_id', '=', 'users.id')
														->where('users.status', '1')
														->where('users.role_id', '!=','1')
														->where('users.register_id', '!=', NULL)
														->where('users.is_deleted', '0')
														//->whereRaw('( users.role_id = 21 or users.department_type = "'.Auth::user()->department_type.'" or users.supervisor_id like "%'. Auth::user()->id .'%" )')
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
											?>
											<label for="first-name-column">Employee</label>
											@if(count($users) > 0)
											<select class="form-control emp_id select-multiple1" name="emp_id">
												<option value=""> - Select Employee - </option>
												<option value="Self" @if(!empty(app('request')->input('emp_id')) && 'Self' == app('request')->input('emp_id')) selected="selected" @endif> Assign to Self </option>
												<option value="Other" @if(!empty(app('request')->input('emp_id')) && 'Other' == app('request')->input('emp_id')) selected="selected" @endif> Assign to Other </option>
												<option value="aOther" @if(!empty(app('request')->input('emp_id')) && 'aOther' == app('request')->input('emp_id')) selected="selected" @endif> Assign by Other </option>
												@foreach($users as $value)
												<option value="{{ $value->id }}" @if($value->id == app('request')->input('emp_id')) selected="selected" @endif><?=$value->name ." (".$value->register_id." - ".$value->degination.")";?></option>
												@endforeach
											</select>
											@endif
										</div>

										<?php if(Auth::user()->role_id == 29 || Auth::user()->role_id == 24 || Auth::user()->department_type==51){ ?>
										<div class="col-12 col-sm-6 col-lg-3">
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
										
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="first-name-column">Status</label>
											<select class="form-control status select-multiple2" name="status">
												<option value=""> - Select Status - </option>												
												@php $status = ['Assign','In Progress','Completed', 'Dropped']; @endphp
												<option value="">Select Any</option>
												@foreach($status as $key => $value)
												<option value="{{ $value }}" @if($value == app('request')->input('status')) selected="selected" @endif>{{ $value }}</option>
												@endforeach
											</select>
										</div>	
										
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-verified">From</label>
											<fieldset class="form-group">
												<input type="date" name="fdate" class="form-control StartDateClass fdate" value="{{ app('request')->input('fdate') }}" id="">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-verified">To</label>
											<fieldset class="form-group">
												<input type="date" name="tdate" class="form-control EndDateClass tdate" value="{{ app('request')->input('tdate') }}" id="">
											</fieldset>
										</div>
									</div>
									
									<fieldset class="form-group" style="float:right;">		
										<button type="submit" class="btn btn-primary">Search</button>
										<a href="{{ route('admin.view-task') }}" class="btn btn-warning">Reset</a>
										<a href="javascript:void(0)" id="download_pdf" class="btn btn-primary">Export in PDF</a>
									
									</fieldset>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table class="table data-list-view" style="background:#fff;">
						<thead style="text-align: ;">
							<tr>
								<th>S. No.</th>
								<th>Date</th>
								<th>Title</th>
								<th>Assigned By</th>
								<th>Assigned To</th>
								<th>Plan Hours</th>			
								<th>Spent Hours</th>			
								<th>Total Task</th>			
								<th>Action</th>
							</tr>
						</thead>
						<tbody>		
							<?php 
								if(count($task) > 0){
									$i = 1; 
									foreach($task as $t){
										$t1 = date('Y-m-d H:i:s', strtotime( $t->created_at ));
										$t2 = date('Y-m-d H:i:s', strtotime('-2 day'));
										
										//$t2 = date('Y-m-d H:i:s', strtotime('-2 day', strtotime($t->created_at.'-2 day')));
										if($t->assign_id == Auth::user()->id && $t->emp_id == Auth::user()->id){
											$style  = "background:rgba(255,255,255,0.6)";
										}else{
											$style  = "background:rgb(255 209 209 / 40%)";
										}
							?>
							<tr style="<?=$style;?>">
								<td>{{ $pageNumber++ }}</td>
								<td><?php echo date("d-m-Y", strtotime($t->date)); ?></td>
								<td>{{ $t->title }}</td>
								<td>{{ $t->assign_name }}</td>
								<td>{{ $t->emp_name }}</td>								
								<td>{{ $t->plan }}</td>				 				
								<td>{{ $t->spent }}</td>								
								<td>{{ $t->total }}</td>
								<td>
									<?php 
										if($t->emp_id == Auth::user()->id &&  $t->spent ==''){
									?>									
									<a title="Task Edit" href="javascript:void(0)" data-id="{{ $t->id }}" class="get_edit_data">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
									</a>
									<?php 
										}
									?>
									&nbsp; 
									<a href="{{ route('admin.view-task-history', $t->id)}}" title="View Task">View</a></td>
							</tr>	
							<?php 
								$i++; }
								}else{
							?>
							<tr>
								<td colspan="10" class="text-center">No Record Found</td>
							</tr>	
							
							<?php } ?>
						</tbody>
					</table>
					<div class="d-flex justify-content-center">					
					{!! $task->appends($params)->links() !!}
					</div>
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
        <h4 class="modal-title">Update Spent Hour</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <form method="post" action="{{ route('admin.update-spent-hour') }}">
      		{{ csrf_field() }}
	      <!-- Modal body -->
			<div class="modal-body">
				<table border="1" width="100%;" cellpadding="5" >
					<tr>
						<td><b>Spent Hour</b></td>
						<td>
							<input type="number" class="spent form-control" name="spent_hour" value="0" required />
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


@endsection

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$('.select-multiple1').select2({
		placeholder: "Select Name & Code",
		allowClear: true
	});
	$('.select-multiple2').select2({
		placeholder: "Select Status",
		allowClear: true
	});
	
	$('.select-multiple3').select2({
		placeholder: "Select Department",
		allowClear: true
	});
});


$('.taskStatus').on("change", function(){
	value = $(this).val();
	
	if(value == "In Progress" || value == "Dropped"){
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
			
			$('.task_id').val(task_id);
			
			$.ajax({
				type : 'POST',
				url : '{{ route('admin.edit-task') }}',
				data : {'_token' : '{{ csrf_token() }}', 'task_id': task_id},
				dataType : 'html',
				success : function (data){
					
					var data= JSON.parse(data);
					$.each(data, function(k, v) {
						// $('.date').html(v.date);
						
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
	
	
	$("body").on("click", "#download_pdf", function (e) {
			
			var data = {};
			data.emp_id = $('.emp_id').val(),
			data.department_type = $('.department_type').val(),
			data.status = $('.status').val(),
			data.fdate = $('.fdate').val(),
			data.tdate = $('.tdate').val(),
			
			// alert($('.emp_id').val());
			
			// /*
			window.open("<?php echo URL::to('/admin/'); ?>/get-task-report-pdf?" + Object.keys(data).map(function (k) {
				return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
			}).join('&'));
		});

</script>


@endsection
