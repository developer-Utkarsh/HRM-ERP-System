@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-md-6">
						<h2 class="content-header-title float-left mb-0">Meeting</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-md-6">
						<a href="{{ route('admin.meeting-add') }}" class="btn btn-primary float-right ">Add Meeting</a>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ route('admin.meeting.index') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Title</label>
											<fieldset class="form-group">
												<input type="text" class="form-control title" name="title" placeholder="Title" value="{{ app('request')->input('title') }}">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-verified">From</label>
											<fieldset class="form-group">
												<input type="date" name="fdate" class="form-control StartDateClass fdate" value="{{ app('request')->input('fdate') }}" id="">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-verified">To</label>
											<fieldset class="form-group">
												<input type="date" name="tdate" class="form-control EndDateClass tdate" value="{{ app('request')->input('tdate') }}" id="">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-6">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('admin.meeting.index') }}" class="btn btn-warning">Reset</a>
											<a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a>
											</fieldset>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table class="table data-list-view">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Creator</th>
								<th>Date</th>
								<th>Start/End Time</th>
                                <th>Title</th>
								<th>Meeting Agenda</th>
								<th>Meeting Place</th>								
								<!--
								<th>Start Time</th>
								<th>End Time</th>
								-->								
								<th>Type</th>
								<th>Meeting URL</th>
								<th>Group</th>								
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
							@if(count($appointment_result) > 0)
							@foreach($appointment_result as  $key => $value)
							<tr>
								<td>{{ $key + 1 }}</td>
								<td class="product-category">{{ !empty($value->user_name) ? $value->user_name : '' }}</td>
								<td class="product-category">{{ !empty($value->appointment_date) ? date('d-m-Y',strtotime($value->appointment_date)) : '' }}</td>
								<td class="product-category">{{ $value->start_time}} <b>-</b> {{ $value->end_time}}</td>
								 <td class="product-category">{{ !empty($value->title) ? $value->title : '' }}</td>
								<td class="product-category">{{ !empty($value->description) ? $value->description : '' }}</td>
								<td class="product-category">{{ !empty($value->meeting_place_name) ? $value->meeting_place_name : '' }}</td>
								
								<!--
								<td class="product-category">{{ !empty($value->start_time) ? $value->start_time : '' }}</td>
								<td class="product-category">{{ !empty($value->end_time) ? $value->end_time : '' }}</td>
								-->
								
								<td class="product-category">
									<?php 
										switch($value->type){
											case 1 : $type = "Physical";	break;
											case 2 : $type = "virtual";		break;
											case 3 : $type = "Both";		break;
											default : $type = "-";			break;
										}
										
										echo $type;
									?>
								</td>
								<td class="product-category">
									<?php 
										if($value->url!=''){ 
											if($value->appointment_date >= date('Y-m-d')){
												echo '<a href="'.$value->url.' target="_blank"">Join Now</a>';
											}else{
												echo 'URL Expired';
											}
										}else{
											echo '-';
										}
									?>
								</td>
								<td class="product-category">
									<?php if($value->is_group==1){
											echo 'Yes';
										}else{
											echo 'No';
										}
									?>
								</td>
								<td class="">
									<a href="javascript:void(0)" title="View Employee Status" data-id="{{ $value->id }}" data-toggle="modal" class="get_emp_data"><i class="fa fa-users" aria-hidden="true"></i></a>		
									&nbsp;
									<a href="{{ route('admin.meeting-history', $value->id) }}" title="View Details"><i class="feather icon-eye"></i></a>	
									&nbsp;
									
									
									<?php if($value->appointment_date >= date('Y-m-d')){ ?>
									<a href="javascript:void(0)" title="Task" data-id="{{ $value->id }}" data-toggle="modal" class="key_point"><i class="fa fa-hand-pointer-o"></i></a>
									<?php } ?>
									
									<?php if($value->user_id == Auth::user()->id && $value->status == 1){ ?>
									<!--
									&nbsp;&nbsp;<a href="{{ route('admin.meeting-add', $value->id) }}" title="Reschedule" target="_blank"><i class="fa fa-repeat" aria-hidden="true"></i></a>&nbsp;&nbsp;
									-->
									
									
									<!--
									<button type="button" class="border-0 text-danger btn-sm" onClick="cancelUpdate('<?=$value->id;?>','2')" title="Cancel Meeting"><i class="fa fa-times-circle"></i></button>
									-->
									<a href="javascript:void(0)" title="Cancel Meeting" data-id="{{ $value->id }}" data-toggle="modal" class="cancel_meeting"><i class="fa fa-times-circle"></i></a>
									
									<?php }else{ 
											if($value->status == 2){
												echo 'Meeting Canceled';
											}
										} 
									?>
									
									
									<?php 
										if($value->emp_id == Auth::user()->id && $value->appointment_date >= date('Y-m-d')){
											$aBtn = 'Accept';
											$rBtn = 'Reject';
											if($value->astatus == 1){
												$aBtn =  'Accepted';
											}else if($value->astatus == 2){
												$rBtn =  'Rejected';
											}
									?>
									<a href="#" class="btn btn-sm btn-success"  type="button" onClick="statusUpdate('<?=$value->id;?>','1')"><?=$aBtn;?></a>
									<a href="#" class="btn btn-sm btn-danger" type="button" onClick="statusUpdate('<?=$value->id;?>','2')"><?=$rBtn;?></a>
									<?php }	?>
									 
									<!--
									<a href="javascript:void(0)" title="View Employee Status" data-id="{{ $value->id }}" data-toggle="modal" class="btn btn-sm btn-success key_point">Reschedule</a>
									-->
									
								</td>
							</tr>
							@endforeach
							@else
							<tr><td class="text-center text-primary" colspan="12">No Record Found</td></tr>	
							@endif
						</tbody>
					</table>
				</div>                   
			</section>
		</div>
	</div>
</div>

<div id="emp_data" class="modal fade">
    <div class="modal-dialog modal-l">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Appointment Employee Details</h5>
        </div>
        <form method="post" id="submit_timetable_online_form" class="online-form">
        <div class="modal-body">
            <div class="form-body">
				
                <div class="row pt-2 emp_app_status_data">
                
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
        </form>
        
        </div>
    </div>
</div>


<div id="key_point" class="modal fade keypoint">
    <div class="modal-dialog modal-l">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Add Meeting Key Task</h5>
        </div>
        <form method="post" id="submit_timetable_online_form" action="{{ route('admin.add-key-point') }}" class="online-form">
			@csrf
			<input type="hidden" name="appointment_id" class="mid" value=""/>
			<div class="modal-body">
				<div class="form-body">				
					
					<div class="row pt-2">
						<div class="col-lg-10"><input type="text" name="key_points[]" value="" class="form-control" placeholder="Key Points" required /></div>
						<div class="col-lg-2"><button type="button" class="add-more btn-success">+</button></div>
					</div>
					
					<div class="append_div">
					
					</div>			
				</div>
				
				
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-success">Submit</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
			</div>
        </form>
        
		<div class="copy-fields" style="display:none;">
			<div class="remove_row">
				<div class="row pt-2">
					<div class="col-lg-10"><input type="text" name="key_points[]" value="" class="form-control" placeholder="Key Points"/></div>
					<div class="col-lg-2">
						<button type="button" class="add-more btn-success">+</button>
						<button type="button" class="remove btn-danger">-</button>
					</div>
				</div>
			</div>
		</div>
        </div>
    </div>
</div>

<div id="cancel_meeting" class="modal fade">
    <div class="modal-dialog modal-l">
        <div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Cancel Meeting</h5>
			</div>
			<form method="post" id="submit_timetable_online_form" action="{{ route('admin.cancel-appointment-status') }}" class="online-form">
				@csrf
				<input type="hidden" name="appointment_id" class="mid" value=""/>
				<div class="modal-body">
					<div class="form-body">				
						<label>Reason</label>
						<textarea name="cancel_reason" value="" class="form-control" required /></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success">Submit</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				</div>
			</form>
        
        </div>
	</div>
</div>

@endsection

<style type="text/css">
	.btn-sm, .btn-group-sm > .btn {
		padding: 0.5rem 0.5rem !important;
	}
</style>

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple').select2({
			placeholder: "Select Any",
			allowClear: true
		});

		$("body").on("click", "#download_excel", function (e) {
			var data = {};
				data.title   = $('.title').val(), 
				data.fdate   = $('.fdate').val(), 
				data.tdate   = $('.tdate').val(), 
				window.location.href = "<?php echo URL::to('/admin/'); ?>/appointment-report-excel?" + Object.keys(data).map(function (k) {
				return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
			}).join('&');
		});

        $(".get_emp_data").on("click", function() { 
            var app_id = $(this).attr("data-id");
            $('#emp_data').modal({
                    backdrop: 'static',
                    keyboard: true, 
                    show: true
            });
				
            $.ajax({type : 'POST',
                url : '{{ route('admin.get-appointment-status') }}',
                data : {'_token' : '{{ csrf_token() }}', 'app_id': app_id},
                dataType : 'html',
                success : function (data){
                    $('.emp_app_status_data').empty();
                    $('.emp_app_status_data').html(data);
                }
            });
        }); 
		
		
		$(".key_point").on("click", function() { 
            var id = $(this).attr("data-id");
            $('#key_point').modal({
                    backdrop: 'static',
                    keyboard: true, 
                    show: true
            });
			
			
			$('.mid').val(id);

            // $.ajax({type : 'POST',
                // url : '{{ route('admin.get-appointment-status') }}',
                // data : {'_token' : '{{ csrf_token() }}', 'app_id': app_id},
                // dataType : 'html',
                // success : function (data){
                    // $('.emp_app_status_data').empty();
                    // $('.emp_app_status_data').html(data);
                // }
            // });
        }); 
		
		
		$(".cancel_meeting").on("click", function() { 
            var id = $(this).attr("data-id");
            $('#cancel_meeting').modal({
                    backdrop: 'static',
                    keyboard: true, 
                    show: true
            });
			
			
			$('.mid').val(id);
        }); 
		
		
	
		$('.add-more').click(function() {
			var html = $(".copy-fields").html();
			$(".append_div").append(html);  
		});
		
		$("body").on("click",".remove",function(){ 
			$(this).parents(".remove_row").remove();
		});
		
		
		//Meeting Status Update
		// $(".meeting_status").on("click", function() { 
		
		
	});
	
	
	function statusUpdate(value, status){
		$.ajax({
			type : 'POST',
			url : '{{ route('admin.update-appointment-status') }}',
			data : {'_token' : '{{ csrf_token() }}', 'value': value, 'status':status},
			dataType : 'json',

			success : function (data){
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
	
	
	function cancelUpdate(value, status){
		$.ajax({
			type : 'POST',
			url : '{{ route('admin.cancel-appointment-status') }}',
			data : {'_token' : '{{ csrf_token() }}', 'value': value, 'status':status},
			dataType : 'json',
			success : function (data){
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
