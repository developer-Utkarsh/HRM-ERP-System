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
						<h2 class="content-header-title float-left mb-0">Vendor History</h2>
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
			<section id="data-list-view" class="data-list-view-header">				
				<div class="table-responsive">
					<table class="table data-list-view">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Request Type</th>
								<th>PO No.</th>
								<th>PO Date</th>
								<th>Pay Amount</th>
								<th>Advance Amount</th>
							</tr>
						</thead>
						<tbody>
							@if(count($buyer_history) > 0)
							@php $i = 1; @endphp
							@foreach($buyer_history as $index => $bh)
								<tr>
									<td>{{ $i++ }}</td>
									<td>
										@php
											if ($bh->request_type == '1') { 
												$powoText = 'WRL'; 
												$pwText = 'WO'; 
											} else { 
												$powoText = 'MRL'; 
												$pwText = 'PO'; 
											} 
										@endphp
										{{ $powoText }}
									</td>
									<td>
										@php
											if (!empty($bh->po_month)) {
												$po_month = $bh->po_location . '-' . $bh->po_no . "/" . $bh->po_month;
											} else {
												$po_month = $bh->po_no;
											}
										@endphp
										{{ 'UTK' . $pwText . '-' . $po_month }}
									</td>
									<td>{{ date('d-m-Y', strtotime($bh->pdate)) }}</td>
									<td>{{ $bh->final_amt }}</td>
									<td>{{ $bh->advance_amt }}</td>
								</tr>
							@endforeach
						@else
							<tr>
								<td colspan="6" class="text-center">No Record Found</td>
							</tr>
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
