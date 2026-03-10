@extends('layouts.studiomanager')
@section('content')
<style>
.select2-selection--single {height: 44px !important; padding: 8px;}
</style>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-md-6">
						<h2 class="content-header-title float-left mb-0">Support User</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('studiomanager.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-md-6"> <a href="javascript:void(0)" data-id="" class="btn btn-outline-primary get_edit_data float-right">Add User</a></div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ route('studiomanager.support_user.index') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Name</label>
											<fieldset class="form-group">
												
												<select class="form-control select-multiple3" name="user_name">
													<option value="">Select User</option>
													@if(count($users) > 0)
													@foreach($users as $usersvalue)
													<option value="{{ $usersvalue->id }}" @if(!empty(app('request')->input('user_name')) && app('request')->input('user_name') == $usersvalue->id){{'selected'}}@endif>{{ $usersvalue->name }}</option>
													@endforeach
													@endif
												</select><br>
					
											</fieldset>
										</div>
										
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('studiomanager.support_user.index') }}" class="btn btn-warning">Reset</a>
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
								<th>Employee Name</th>
								<th>Role</th>
								<th>Department</th>
								<th>Status</th>
								<th>Created</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						@if(count($support_user) > 0)
							@foreach($support_user as  $key => $value)
							<tr>
								<td>{{ $key + 1 }}</td>
								<td class="product-category">{{ !empty($value->name) ? $value->name : '' }}</td>
								<td class="product-category">{{ !empty($value->role) ? ucfirst($value->role) : '' }}</td>
								<td class="product-category">
								<?php
								if($value->role=='replier'){
									$categories_name = "";
									$explode_cat = explode(",",$value->category_id); 
									if(count($category) > 0){
										foreach($category as $categoryvalue){ 
											if(in_array($categoryvalue->id	, $explode_cat)){ 
												$categories_name .= $categoryvalue->name." ,";
											}
										}
									}
									echo rtrim($categories_name,',');
								}
								?>
								</td>
								<td class="product-category">
									<a href="{{route('studiomanager.support_user.status', $value->id)}}">
										<strong class="fa fa-lg {{$value->status == 'Active' ? 'fa-toggle-on text-success' : 'fa-toggle-off text-grey'}}" title="Toggle publish"></strong>
									</a>
								</td>
								<td>{{ $value->created_at->format('d-m-Y') }}</td>
								<td class="product-action">
									<a title="Update Support User" href="javascript:void(0)" data-id="{{ $value->id }}" class="get_edit_data">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
									</a>
									
									<a title="Delete Category" href="{{ route('studiomanager.support_user.delete', $value->id) }}" onclick="return confirm('Are You Sure To Delete Support User')">
										<span class="action-delete"><i class="feather icon-trash"></i></span>
									</a>
									
									
								</td>
							</tr>
							@endforeach
							
						@else
						<tr ><td class="text-center text-primary" colspan="5">No Record Found</td></tr>
						@endif	
						</tbody>
					</table>
				</div>                   
			</section>
			

		</div>
	</div>
</div>


<!-- The Modal -->
<div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Add User</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <form method="post" action="{{ route('studiomanager.support_user.store') }}">
      		{{ csrf_field() }}
	      <!-- Modal body -->
	      <div class="modal-body fill-name">	  
	      </div>

	      <!-- Modal footer -->
	      <div class="modal-footer">
	      	<button type="submit" class="btn btn-primary">Save</button>
	        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
	      </div>
      </form>

    </div>
  </div>
</div>


@endsection
@section('scripts')

<script src="{{ asset('laravel/public/admin/js/jquery.validate.min.js') }}"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">

		$('.select-multiple3').select2({
			width: "100%",
			placeholder: "Select User",
			allowClear: true
		});
		
	function selectRefresh() {
		$('.select-multiple').select2({
			width: "100%",
			placeholder: "Select User",
			allowClear: true
		});
		
		$('.select-multiple1').select2({
			width: "100%",
			placeholder: "Select Role",
			allowClear: true
		});
		
		$('.select-multiple2').select2({
			width: "100%",
			placeholder: "Select Category",
			allowClear: true
		});
	}
	
	$("body").on("change",".role",function(){ 
		var role = $(this).val(); 
		if(role=='replier'){
			$(".category_div").show();
			$(".category").prop('required',true);
		}
		else{
			$(".category_div").hide();
			$(".category").prop('required',false);
		}
	});
</script>
<script type="text/javascript">
	$(".get_edit_data").on("click", function() { 
		var support_user_id = $(this).attr("data-id");
		if(support_user_id){
			
			$('#myModal').modal({
					backdrop: 'static',
					keyboard: true, 
					show: true
			});
			
			$.ajax({
				type : 'POST',
				url : '{{ route('studiomanager.edit-support-user') }}',
				data : {'_token' : '{{ csrf_token() }}', 'support_user_id': support_user_id},
				dataType : 'html',
				success : function (data){
					$('.fill-name').empty();
					
					$('.fill-name').html(data);
					selectRefresh();
				}
			});
			
		}
		else{
			
			$('.fill-name').empty();
			$('#myModal').modal({
					backdrop: 'static',
					keyboard: true, 
					show: true
			});
			$('.fill-name').html(`<div class="row col-md-12">
				<div class="col-md-12 mb-2">
					<select class="form-control select-multiple" name="user_id" required>
						<option value="">Select User</option>
						@if(count($users) > 0)
						@foreach($users as $usersvalue)
						<option value="{{ $usersvalue->id }}">{{ $usersvalue->name }}</option>
						@endforeach
						@endif
					</select><br>
				</div>
				<div class="col-md-12 mb-2">
					<select class="form-control role select-multiple1" name="role" required>
						<option value="">Select Role</option>
						<option value="query">Query</option>
						<option value="replier">Replier</option>
						<option value="admin">Admin</option>
					</select><br>
				</div>
				
				<div class="col-md-12 mb-2 category_div" style="display:none;" >
					<select class="form-control category  select-multiple2" name="category[]" multiple>
						<option value="">Select category</option>
						@if(count($category) > 0)
						@foreach($category as $categoryvalue)
						<option value="{{ $categoryvalue->id }}">{{ $categoryvalue->name }}</option>
						@endforeach
						@endif
					</select><br>
				</div>
				
			  </div>`);
			  selectRefresh();
		}		
	}); 
</script>
@endsection
