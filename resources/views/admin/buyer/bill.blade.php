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
			<div class="content-header-left col-md-8 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Bill Details</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Bill Details</a>
								</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3"><a href="{{ route('admin.buyer.add-bill', $buyer_id) }}" class="btn btn-outline-primary float-right">Add Bill</a></div>
			<div class="col-md-1"><a href="{{ route('admin.buyer.index') }}" class="btn btn-outline-primary float-right"><i class="feather icon-arrow-left"></i></a></div>
			<div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
			</div>
		</div>
		<div class="content-body">
		
		<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="{{ route('admin.buyer.bill', $buyer_id) }}" method="get" enctype="multipart/form-data">
										@csrf
										<div class="form-body">
											<div class="row">
												<div class="col-md-3">
													<div class="form-group">
														<label>Bill No</label>
														<input type="text" class="form-control" placeholder="Bill No" name="bill_no" value="{{ app('request')->input('bill_no') }}">
													</div>
												</div>

												<div class="col-md-3">
													<div class="form-group">
														<label>From Date</label>
														 <input type="date" class="form-control" name="from_date" value="@if(!empty(app('request')->input('from_date'))){{app('request')->input('from_date')}}@endif">
													</div>
												</div>

												<div class="col-md-3">
													<div class="form-group">
														<label>To Date</label>
														 <input type="date" class="form-control" name="to_date" value="@if(!empty(app('request')->input('to_date'))){{app('request')->input('to_date')}}@endif">
													</div>
												</div>												
												<div class="col-md-3 mt-2">
													<fieldset class="form-group">		
														<button type="submit" class="btn btn-primary">Search</button>
														<a href="{{ route('admin.buyer.bill', $buyer_id) }}" class="btn btn-warning">Reset</a>
													</fieldset>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
			
			<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-md-12">
						
						
						<div class="table-responsive">
							<table class="table data-list-view">
								<thead>
									<tr>
										<th>S. No.</th>
										<th>Bill No</th>
										<th>Bill File</th>
										<th>Created</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
								@if(count($get_buyer_detail) > 0)
									@foreach($get_buyer_detail as  $key => $value)
									<tr>
										<td>{{ $key + 1 }}</td>
										<td class="product-category">{{ $value->bill_no }}</td>
										<td class="product-category"><a href="{{ asset('laravel/public/bill/'.$value->bill_file) }}" target="__blank" class="btn btn-sm btn-primary waves-effect waves-light">View File</a></td>
										<td class="product-category">{{ date('d-m-Y',strtotime($value->created_at)) }}</td>
										<td class="product-category">
										
										<a title="Update Bill" href="{{ route('admin.buyer.edit-bill', [ $buyer_id, $value->id ] ) }}">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
										</a>
										
										<a title="Delete Bill" href="{{ route('admin.buyer.delete-bills', $value->id) }}" onclick="return confirm('Are You Sure To Delete Bill')">
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
