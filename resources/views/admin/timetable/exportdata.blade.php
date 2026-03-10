@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Timetable Export</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
						<a href="{{ route('admin.timetable.index') }}" class="btn btn-primary mr-1">Back</a>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="data-list-view" class="data-list-view-header">
				<form action="{{ route('admin.export.data') }}" method="post">
					@csrf
					<button type="submit" class="btn btn-primary mr-1">Export</button>
					<div class="table-responsive">
						<table class="table data-list-view">
							<thead>
								<tr>
									<th><input type="checkbox" name="check_all" id="check-all"></th>
									<th>S. No.</th>
									<th>Name</th>
									<th>Status</th>
									<th>Created</th>
								</tr>
							</thead>
							<?php $branches = \App\Branch::where('status', '1')->orderBy('id', 'desc')->get(); ?>
							<tbody>
								@foreach($branches as  $key => $value)
								<tr>
									<td><input type="checkbox" class="checkbox" name="id[]" value="{{ $value->id }}"></td>
									<td>{{ $key + 1 }}</td>
									<td class="product-category">{{ $value->name }}</td>
									<td>@if($value->status == 1) Active @else Inactive @endif</td>
									<td>{{ $value->created_at->format('d-m-Y') }}</td>
								</tr>
								@endforeach
							</form>
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
	$(document).ready(function(){
		$('#check-all').on('click',function(){
			if(this.checked){
				$('.checkbox').each(function(){
					this.checked = true;
				});
			}else{
				$('.checkbox').each(function(){
					this.checked = false;
				});
			}
		});
	});
</script>
@endsection
