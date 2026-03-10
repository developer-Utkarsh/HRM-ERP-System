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
			<div class="content-header-left col-md-8 col-12 mb-2">
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
										<th>Employee Name</th>
										<th>Quantity</th>
										<th>Created</th>
									</tr>
								</thead>
								<tbody>
								@if(count($asset_history) > 0)
									@foreach($asset_history as  $key => $value)
									<tr>
										<td>{{ $key + 1 }}</td>
										<td class="product-category">{{ $value->name }}</td>
										<td class="product-category">{{ $value->user_name }}</td>
										<td class="product-category">{{ $value->assign_asset_qty }}</td>
										<td class="product-category">{{ date('d-m-Y',strtotime($value->assign_asset_created_at)) }}</td>
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
@endsection
