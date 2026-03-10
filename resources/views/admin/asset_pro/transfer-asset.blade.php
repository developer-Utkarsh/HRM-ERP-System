@extends('layouts.admin')
@section('content')

@if (Auth::viaRemember())
    {{666}}
@else
    {{777}}
@endif
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-10 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Transfer Asset Details</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Transfer Asset Details</a>
								</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-2"><a href="{{ route('admin.asset_pro.index') }}" class="btn btn-outline-primary float-right"><i class="feather icon-arrow-left"></i></a></div>
		</div>
		<div class="content-body">

			<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						
						
						<div class="table-responsive">
							<table class="table data-list-view">
								<thead>
									<tr>
										<th>S. No.</th>
										<th>Asset Name</th>
										<th>Assigned By</th>
										<th>Assigned To</th>
										<th>Assigned <br/>Quantity / <br/> Status</th>
										<th>Assigned <br/>Date</th>
										<!--th>Status</th-->
									</tr>
								</thead>
								<tbody>
								@if(count($asset_history) > 0)
									@foreach($asset_history as  $key => $value)
									<tr>
										<td>{{ $key + 1 }}</td>
										<td class="product-category">{{ $value->name }}</td>
										<td class="product-category">{{ $value->assigned_by_name }}</td>
										<td class="product-category">
										<a href="javascript:void(0);" class="assigned_to_click" data-assigned_to_id="<?=$value->assigned_to_id?>" data-asset_id="<?=$id?>">{{ $value->assigned_to }}</a>
										<span class="remaining_asset" ></span>
										</td>
										<td class="product-category">
										{{ $value->assign_asset_qty }} / 
										<?php
										
										if($value->is_accepted=='0'){
											echo "Pending";
										}
										else if($value->is_accepted=='1'){
											echo "Return";
										}
										else if($value->is_accepted=='2'){
											echo "Accepted";
										}
										?>
										</td>
										<td class="product-category">{{ date('d-m-Y',strtotime($value->assign_asset_created_at)) }}</td>
										<!--td class="product-category">
										<?php
										
										if($value->is_accepted=='0'){
											echo "Pending";
										}
										else if($value->is_accepted=='1'){
											echo "Return";
										}
										else if($value->is_accepted=='2'){
											echo "Accepted";
										}
										?>
										</td-->
									</tr>
									@endforeach    
								@else
									<tr ><td class="text-center text-primary" colspan="5">No Record Found</td></tr>
								@endif
								</tbody>
							</table>
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
<script>
$(document).on("click",".assigned_to_click", function () { 
	var this_click = $(this);
	var assigned_to_id= $(this).data('assigned_to_id');
	var asset_id= $(this).data('asset_id');
	$.ajax({
		type : 'POST',
		url : '{{ route('admin.remaining_asset_employee') }}',
		data : {'_token' : '{{ csrf_token() }}', 'assigned_to_id': assigned_to_id, 'asset_id': asset_id},
		dataType : 'json',
		success : function (data){		
			$(this_click).siblings('.remaining_asset').html("<strong>( "+data.total+" ) </strong>");
		}
	});
});
</script>
@endsection
