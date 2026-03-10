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
						<h2 class="content-header-title float-left mb-0">Transfer Details</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Transfer Details</a>
								</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
			<!--div class="col-md-3"><a href="{{ route('admin.inventory.inventory-transfer', [$product_id]) }}" class="btn btn-outline-primary float-right">Transfer</a></div-->
			<div class="col-md-4"><a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-primary float-right">Back</a></div>
			<div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
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
										<th>Transfer From</th>
										<th>Transfer To</th>
										<th>Employee</th>
										<th>Quantity</th>
										<th>Status</th>
										<th>Remark</th>
										<th>Created</th>
										<!--th>Action</th-->
									</tr>
								</thead>
								<tbody>
								@if(count($get_product_detail) > 0)
									@foreach($get_product_detail as  $key => $value)
									<tr>
										<td>{{ $key + 1 }}</td>
										<td class="product-category">{{ $value->transfer_from == 0 ? 'Warehouse' : $value->transfer_from_name }}</td>
										<td class="product-category">{{ $value->transfer_to_name }}</td>
										<td class="product-category">{{ isset($value->name) ? $value->name : '-' }}</td>
										<td class="product-category">{{ $value->qty }}</td>
										<td class="product-category">{{ $value->tstatus }}</td>
										<td class="product-category">{{ isset($value->remark) ? $value->remark : '-' }}</td>
										<td class="product-category">{{ date('d-m-Y h:i:s',strtotime($value->tdate)) }}</td>
										@if($value->status == 'Pending')
										<!--td class="product-category">
										
										<a title="Edit Transfer" href="{{ route('admin.product.edit-transfer-product', [ $product_id, $value->id ] ) }}">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
										</a>
										
										<a title="Delete Transfer" href="{{ route('admin.product.delete-transfer-product', $value->id) }}" onclick="return confirm('Are You Sure To Delete Transfer Product')">
											<span class="action-delete"><i class="feather icon-trash"></i></span>
										</a>
										</td-->
										@endif
										
									</tr>
									@endforeach
								@else
									<tr ><td class="text-center text-primary" colspan="8">No Record Found</td></tr>
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
