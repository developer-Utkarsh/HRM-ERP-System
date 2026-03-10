@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-md-8">
						<h2 class="content-header-title float-left mb-0">Edit Assign Asset</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-md-4">
						<a class="btn btn-outline-primary float-right" href="{{ route('admin.asset_pro.employee-asset-pro') }}"><span class="action-edit"><i class="feather icon-arrow-left"></i></span></a>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="data-list-view" class="data-list-view-header">
				
				<div class="table-responsive">
					<table class="table data-list-view" style="background:#fff;width:100%" id="assetTable">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Asset Name</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						@php $key = 0; @endphp
						@if(count($assigned_asset) > 0)
							@foreach($assigned_asset as  $key => $value)
							
							<tr>
								<td>{{ $key + 1 }}</td>
								<td class="product-category">{{ $value->name }}</td>
								
								<td class="product-action">
									<a title="Update Asset" href="{{ route('admin.asset_pro.update-asset-status-pro', [$value->id, $value->emp_id,'admin_accept']) }}" onclick="return confirm('Are you sure to accept asset')">
										<span class="action-edit">Accept</span>
									</a>
								</td>
							</tr>
							@endforeach
						@else
						<tr ><td class="text-center text-primary" colspan="7">No Record Found</td></tr>
						@endif	
						</tbody>
					</table>
				</div>                   
			</section>
			

		</div>
	</div>
</div>


@endsection
@section('scripts')
@endsection
