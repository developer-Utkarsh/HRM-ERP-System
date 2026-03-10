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
						@if(!empty($check_add_enquiry) && $check_add_enquiry->role == 'query')
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
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Status</label>
											<fieldset class="form-group">												
												<select class="form-control" name="course_type"> 
													<option value="">Select Course Type</option>
													<option value="Online" @if(!empty(app('request')->input('course_type')) && app('request')->input('course_type') == "Online"){{"selected"}}@endif>Online</option>
													<option value="Offline" @if(!empty(app('request')->input('course_type')) && app('request')->input('course_type') == "Offline"){{"selected"}}@endif>Offline</option>
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
				

				<div class="">
					<div class="row">
						<div class="col-md-12">

							<div class="container-fluid" style="padding-bottom: 15px">
						
								<style type="text/css">
									.history_ticket_row {
										border-bottom: 2px solid #27a6f5;
										margin-top: 10px;
										padding: 10px;
										border-radius: 6px;
										background: lightgray;
									}

									.history_ticket_subject {
										color: #27a6f5;
										font-size: 14px;
										padding-left: 10px;
									}

									.history_ticket_description {
										line-height: 22px;
										word-wrap: break-word;
										color: black;
									}


									.ticket_row {
										border-bottom: 2px solid #27a6f5;
										margin-top: 10px;
										padding: 10px;
										border-radius: 6px;
										box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 10px 0 rgba(0, 0, 0, 0.19);
									}

									.ticket_row:hover {
										border: 2px pink solid;
										background: white;
										box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
									}

									.ticket_username {
										color: black;
										font-size: 16px;
									}

									.ticket_usermobile {
										font-size: 12px;
									}

									.ticket_createdtime {
										font-size: 14px;
										float: right;
										color: red;
									}

									.ticket_subject {
										color: #27a6f5;
										font-size: 18px;
										padding-left: 10px;
									}

									.ticket_userbatch {
										color: gray;
										font-size: 12px;
									}

									.ticket_description {
										line-height: 25px;
										word-wrap: break-word;
									}

									.ticket_reviewbtn {
										border: 1px solid gray;
										border-radius: 6px;
										padding: 6px;
										margin: 3px;
										cursor: help;
									}

									.pagination_span {
										font-size: 18px;
										font-weight: bold;
										cursor: pointer;
										color: blue;
									}

									.pagination_span_notclick {
										font-size: 18px;
										font-weight: bold;
										color: black;
									}

								</style>
								@if(count($enquiry_result) > 0)
								@foreach($enquiry_result as $key=>$enquiry_result_value)

								@php 
									$old_query = DB::table("enquiry_description")->select("enquiry_description.description","users.name","users.id as user_id")->leftJoin("users","enquiry_description.user_id","=","users.id")->where("enquiry_description.enquiry_id", $enquiry_result_value->id)->get();
									
									
									$check_role = DB::table("support_user")->where("user_id", Auth::user()->id)->first();
									
								@endphp
								
									<div id="ticket">
										<div class="ticket_row">
											<pre
												class="ticket_username"> {{$enquiry_result_value->name}} <span class="ticket_usermobile"> {{$enquiry_result_value->mobile_no}} - ({{$enquiry_result_value->course_type}})</span><span class="ticket_createdtime"> {{date("d-m-Y H:i A",strtotime($enquiry_result_value->created_at))}}</span>
											</pre>
												
											<div>
												<span class="ticket_subject"><i class="fa fa-bookmark" aria-hidden="true"></i> {{$enquiry_result_value->course_name}}</span>
											</div>
											<h5 class="ticket_description"><br>
												@if(!empty($check_role->role) && !empty($enquiry_result_value->status) && (($check_role->role == 'replier' && $enquiry_result_value->status == 'Open') || ($check_role->role == 'query' && $enquiry_result_value->status == 'Close')))
												<form method="post" action="{{ route('studiomanager.store-enquiry-description') }}">
													@csrf
													<textarea name="description" id="textreply-{{$key}}" rows="5" class="form-control" style="display: none;"></textarea>
													<input type="hidden" name="enquiry_id" value="{{$enquiry_result_value->id}}">
													<input type="submit" name="submit" id="savebtn-{{$key}}" class="ticket_reviewbtn" value="Save" style="display: none;">
												</form>
												@endif
												<div class="row">
													<div class="col-md-8">
														<i class="fa fa-paragraph" style="color:pink;padding:10px 0px 10px 0px"> Description:-</i>
														<span>{{$enquiry_result_value->description}} </span>
														
														@php $t_enquiry = 0; @endphp
														<div id="old_qry{{$key}}" style="display:none;">
															@if(count($old_query) > 0)
															@foreach($old_query as $old_query_value)
															@php $t_enquiry += 1; @endphp
															<p>{{$old_query_value->name}} @if($old_query_value->user_id == Auth::user()->id){{'(You)'}}@endif: <span>{{$old_query_value->description}}</span><p>
															@endforeach
															@endif
														</div>
													</div>

													<div class="col-md-4">
														<span style="float:right;margin-top:20px">
														
															@if(!empty($check_role->role) && !empty($enquiry_result_value->status) && (($check_role->role == 'replier' && $enquiry_result_value->status == 'Open') || ($check_role->role == 'query' && $enquiry_result_value->status == 'Close')))
															<span class="ticket_reviewbtn" aria-hidden="true" onclick="replytoticket({{$key}});">Reply</span>
															@endif
															<span class="ticket_reviewbtn" aria-hidden="true" onclick="oldquery({{$key}});">Old-Query <sup style="color:red;"> * {{$t_enquiry}}</sup></span>
														</span>
													</div>
												</div>
												
												
											   
											</h5>
										</div>

									</div>
								
								@endforeach
								@else
								<p class="text-center">No Record Found</p>
								@endif

							</div>

						</div>
					</div>
				</div>
				
			</section>
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
	
	function oldquery(id){
		$("#old_qry"+id).toggle();
	}
	function replytoticket(id){ 
		$("#textreply-"+id).toggle();
		$("#savebtn-"+id).toggle();
	}
</script>
@endsection
