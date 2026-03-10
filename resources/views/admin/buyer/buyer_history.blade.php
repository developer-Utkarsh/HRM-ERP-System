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
						<h2 class="content-header-title float-left mb-0">Buyer Changes History</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-md-4"> <a href="{{ route('admin.buyer.create') }}" data-id="" class="btn btn-outline-primary float-right">Add Buyer</a></div>
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
								<th>Employee</th>
								<th>Type</th>
								<th>Data</th>
								<th>Created</th>
							</tr>
						</thead>
						<tbody>
							@if(count($history) > 0)
								@php $i = 1; @endphp
								@foreach($history as  $key => $value)
								<tr>
									<td>{{ $i++ }}</td>
									<td>{{ $value->uname }}</td>
									<td>{{ $value->type }}</td>
									<td>{{ $value->save_data }}</td>								
									<td>{{ $value->created_at }}</td>								
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

<script type="text/javascript">
	$("body").on("click", "#download_excel", function (e) {
		var data = {};					
		data.search  = $('.search').val(),
		data.msme 	 = $('.msme').val(),
		data.status 	 = $('.status').val(),
		window.location.href = "<?php echo URL::to('/admin/'); ?>/buyer-report-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	}); 
</script>
@endsection
