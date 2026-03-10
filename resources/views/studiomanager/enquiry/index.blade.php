@extends('layouts.studiomanager')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-6">
						<h2 class="content-header-title float-left mb-0">Enquiry</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('studiomanager.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-6">
						@php  $check_add_enquiry = DB::table("support_user")->where("user_id", Auth::user()->id)->first(); @endphp
						@if((!empty($check_add_enquiry) && $check_add_enquiry->role == 'query') || $check_add_enquiry->role == 'admin')
						<a href="{{ route('studiomanager.enquiry.create') }}" class="btn btn-primary" style="float: right;">Add Enquiry</a>
						@endif
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
								<form action="{{ route('studiomanager.enquiry.index') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Name</label>
											<fieldset class="form-group">
												<input type="text" class="form-control" placeholder="Mobile No." name="mobile_no" value="@if(!empty(app('request')->input('mobile_no'))){{app('request')->input('mobile_no')}}@endif">  
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Course Type</label>
											<fieldset class="form-group">												
												<select class="form-control" name="course_type"> 
													<option value="">Select Course Type</option>
													<option value="Online" @if(!empty(app('request')->input('course_type')) && app('request')->input('course_type') == "Online"){{"selected"}}@endif>Online</option>
													<option value="Offline" @if(!empty(app('request')->input('course_type')) && app('request')->input('course_type') == "Offline"){{"selected"}}@endif>Offline</option>
												</select>   												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Priority</label>
											<fieldset class="form-group">												
												<select class="form-control" name="priority"> 
													<option value="">Select Priority</option>
													<option value="low" @if(!empty(app('request')->input('priority')) && app('request')->input('priority') == "low"){{"selected"}}@endif>Low</option>
													<option value="medium" @if(!empty(app('request')->input('priority')) && app('request')->input('priority') == "medium"){{"selected"}}@endif>Medium</option>
													<option value="high" @if(!empty(app('request')->input('priority')) && app('request')->input('priority') == "high"){{"selected"}}@endif>High</option>
												</select>   												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Status</label>
											<fieldset class="form-group">												
												<select class="form-control" name="status"> 
													<option value="">Select Status</option>
													<option value="pending" @if(!empty(app('request')->input('status')) && app('request')->input('status') == "pending"){{"selected"}}@endif>Pending</option>
													<option value="in_progress" @if(!empty(app('request')->input('status')) && app('request')->input('status') == "in_progress"){{"selected"}}@endif>In Progress</option>
													<option value="resolved" @if(!empty(app('request')->input('status')) && app('request')->input('status') == "resolved"){{"selected"}}@endif>Resolved</option>
												</select>   												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('studiomanager.enquiry.index') }}" class="btn btn-warning">Reset</a>
											</fieldset>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				
				<div class="table-responsive">
					<table class="table data-list-view" id="TableSearch">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>User Name</th>
								<th>Number</th>
								<th>Course</th>
								<th>Query in Details</th>
								<th>Departments List</th>
								<th>Priority</th>
								<th>Course Type</th>
								<th>Status</th>
								<th>Created At</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						@if(count($enquiry_result) > 0)
							@foreach($enquiry_result as  $key => $enquiry_result_value)
							@php 
								$cat_name = DB::table('support_category')->where('id', $enquiry_result_value->category_id)->first(); 
								$check_role = DB::table("support_user")->where("user_id", Auth::user()->id)->first();
								
							@endphp
							<tr>
								<td>{{ $key + 1 }}</td>
								<td class="product-category">{{ !empty($enquiry_result_value->name) ? $enquiry_result_value->name : '' }}</td>
								<td>{{ !empty($enquiry_result_value->mobile_no) ? $enquiry_result_value->mobile_no : '' }}</td>
								<td>{{ !empty($enquiry_result_value->course_name) ? $enquiry_result_value->course_name : '' }}</td>
								<td>{{ !empty($enquiry_result_value->description) ? $enquiry_result_value->description : '' }}</td>
								<td>{{ !empty($cat_name) ? $cat_name->name : '' }}</td>
								<td>{{ !empty($enquiry_result_value->priority) ? $enquiry_result_value->priority : '' }}</td>
								<td>{{ !empty($enquiry_result_value->course_type) ? $enquiry_result_value->course_type : '' }}</td>
								<td>{{ !empty($enquiry_result_value->status) ? ucwords(str_replace("_"," ",$enquiry_result_value->status)) : '' }}</td>
								<td>{{ !empty($enquiry_result_value->created_at) ? date("d-m-Y H:i A",strtotime($enquiry_result_value->created_at)) : '' }}</td>
								<td>
								@if((!empty($check_role->role) && !empty($enquiry_result_value->status) && (($check_role->role == 'replier' && ($enquiry_result_value->status == 'in_progress' || $enquiry_result_value->status == 'pending')) || ($check_role->role == 'query' && $enquiry_result_value->status == 'resolved')))  || $check_add_enquiry->role == 'admin')
									<a href="javascript:void(0)" data-toggle="modal" class="btn btn-outline-primary btn-sm mt-1 reply_data" data-id="{{$enquiry_result_value->id}}" title="Reply" style="padding: 0.5rem 0.5rem;"><i class="fa fa-user"></i></a>
								@endif
								@if($check_role->role == 'replier'  || $check_add_enquiry->role == 'admin')
									<a href="javascript:void(0)" data-toggle="modal" class="btn btn-outline-warning btn-sm mt-1 status_data" data-id="{{$enquiry_result_value->id}}" title="Change Status" style="padding: 0.5rem 0.5rem;"><i class="fa fa-exchange"></i></a>
								@endif	
									<a href="javascript:void(0)" data-toggle="modal" class="btn btn-outline-info btn-sm mt-1 old_query_data" data-id="{{$enquiry_result_value->id}}" title="Old Query" style="padding: 0.5rem 0.5rem;"><i class="fa fa-list"></i></a>
								</td>
							
							</tr>
							@endforeach
						@else
						<tr ><td class="text-center text-primary" colspan="9">No Record Found</td></tr>
						@endif	
						</tbody>
					</table>
				</div> 
			
				
			</section>
		</div>
	</div>
</div>


<div id="apply-form" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title">Reply</h5>
		</div>
		<form method="post" action="{{ route('studiomanager.store-enquiry-description') }}">
			<div class="modal-body">
				<div class="form-body">
					@csrf
					<textarea name="description" rows="5" class="form-control"></textarea>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" name="enquiry_id" class="enquiry_id" value="">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button type="submit" class="btn btn-primary">Submit</button>
			</div>
		</form>
		
		</div>
	</div>
</div>


<div id="old-query-form" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title">Old Query</h5>
		</div>
		
		<div class="modal-body">
			<div class="old_data"></div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
		</div>
		</div>
	</div>
</div>

<div id="status-form" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title">Status</h5>
		</div>
		<form method="post" action="{{ route('studiomanager.store-enquiry-status') }}">
			<div class="modal-body">
				<div class="form-body">
					@csrf
					<div class="row"> 
						<div class="col-md-6">
							<div class="radio text-center">
								<label><input type="radio" style="margin-bottom: 20px;" name="status" value="in_progress" checked> In Progress</label>
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="radio text-center">
								<label><input type="radio" style="margin-bottom: 20px;" name="status" value="resolved"> Resolved</label>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" name="enquiry_id" class="status_enquiry_id" value="">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button type="submit" class="btn btn-primary">Submit</button>
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
		$('.select-multiple').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
	
	$(document).on("click",".status_data", function() { 
		var enq_id = $(this).attr("data-id");
		if(enq_id){
			$(".status_enquiry_id").val(enq_id);
			$('#status-form').modal({
					backdrop: 'static',
					keyboard: true, 
					show: true
			});
		}
		else{
			alert('Enquiry Not Found');
		}
	}); 
	
	$(document).on("click",".reply_data", function() { 
		var enq_id = $(this).attr("data-id");
		if(enq_id){
			$(".enquiry_id").val(enq_id);
			$('#apply-form').modal({
					backdrop: 'static',
					keyboard: true, 
					show: true
			});
		}
		else{
			alert('Enquiry Not Found');
		}
	}); 

	$(document).on("click",".old_query_data", function() { 
		var enq_id = $(this).attr("data-id");
		if(enq_id){
			
			$.ajax({
				type : 'POST',
				url : '{{ route('studiomanager.get-old-query') }}',
				data : {'_token' : '{{ csrf_token() }}', 'enq_id': enq_id},
				dataType : 'html',
				success : function (data){
					$('.old_data').empty();
					$('.old_data').html(data);
					
					$('#old-query-form').modal({
					backdrop: 'static',
					keyboard: true, 
					show: true
			});
				}
			});
		}
		else{
			alert('Enquiry Not Found');
		}
	}); 

</script>
@endsection
