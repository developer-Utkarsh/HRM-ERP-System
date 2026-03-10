@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Send Comment PDF</h2>
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
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ route('admin.send-faculty-pdf-add') }}" method="post" enctype="multipart/form-data">
									@csrf
									<div class="row">
										<div class="col-12 col-md-4">
											<label for="users-list-status">Select Faculty</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 user_id" name="user_id" required>
													<option value="">Select Any</option>
													@if(count($users) > 0)
													@foreach($users as $key => $value)
													<option value="{{ $value->id }}">{{ $value->uname }} <small>- {{ $value->umobile }} - {{ $value->sname }}</small></option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-md-4">
											<label for="users-list-status">Choess PDF</label>
											<fieldset class="form-group">												
												<input type="file" name="attachment" value="" class="form-control" required />												
											</fieldset>
										</div>
										<div class="col-12 col-md-4 mt-2">
										<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Send PDF</button>
											<a href="{{ route('admin.send-faculty-pdf-view') }}" class="btn btn-warning">Reset</a>
										</fieldset>
									</div>
									</div>
									
								</form>
							</div>
						</div>
					</div>
				</div>
				<form action="{{ route('admin.send-faculty-pdf-view') }}" method="get" enctype="multipart/form-data">
				@csrf
					<div class="row">
						<div class="col-12 col-md-3">
							<label for="users-list-role">Name</label>
							<?php $users = \App\User::where('role_id', 2)->where('status', '1')->where('is_deleted', '0')->get(); ?>
							<fieldset class="form-group">												
								<select class="form-control select-multiple1" name="name" >
									<option value="">Select Any</option>
									@if(count($users) > 0)
									@foreach($users as $key => $value)
									<option value="{{ $value->name }}" @if($value->name == app('request')->input('name')) selected="selected" @endif>{{ $value->name }}</option>
									@endforeach
									@endif
								</select>												
							</fieldset>
						</div>
						<div class="col-12 col-md-3">
							<label for="users-list-status">From Date</label>
							<fieldset class="form-group">												
								<input type="date" name="cdate" value="{{ app('request')->input('cdate') }}" class="form-control">												
							</fieldset>
						</div>
						<div class="col-12 col-md-3">
							<label for="users-list-status">To Date</label>
							<fieldset class="form-group">												
								<input type="date" name="todate" value="{{ app('request')->input('todate') }}" class="form-control">												
							</fieldset>
						</div>
						<div class="col-12 col-md-3 mt-2">
							<fieldset class="form-group">		
								<button type="submit" class="btn btn-primary">Search</button>
								<a href="{{ route('admin.send-faculty-pdf-view') }}" class="btn btn-warning">Reset</a>
							</fieldset>
						</div>
					</div>
				</form>
				<div>
					<table class="table table-bordered">
						<thead>
							<tr>
								<th scope="col">S.No.</th>
								<th scope="col">Name</th>
								<th scope="col">Mobile</th>
								<th scope="col">Preview</th>
								<th scope="col">Send Date</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								$i = 1;
								foreach($fpdf as $key => $val){
							?>
							<tr>
								<th scope="row">{{ $pageNumber++ }}</th>
								<td>{{ $val->name }}</td>
								<td>{{ $val->mobile }}</td>
								<td><a href="{{ asset('laravel/public/faculty_pdf/' . $val->attachment)}}">PDF</a></td>
								<td>{{ date('d-m-Y h:i:s', strtotime($val->created_at)) }}</td>
							</tr>
								<?php $i++; } ?>
						</tbody>
					</table>
					<div class="d-flex justify-content-center">					
					{!! $fpdf->appends($params)->links() !!}
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
		$('.select-multiple1').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
</script>
@endsection