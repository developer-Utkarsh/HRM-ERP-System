@extends('layouts.without_login_admin')
@section('content')

<div class="app-content content" style="margin: 0px !important;">
	<div class="content-wrapper" style="margin-top: 0px !important;">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Employee Details</h2>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ route('employee-id') }}" method="get" name="filtersubmit">
									<div class="row">
										
										<div class="col-12 col-md-3">
											<label for="users-list-status">Employee Code</label>
											<fieldset class="form-group">												
												<input type="text" name="emp_code" placeholder="Employee Code" class="form-control emp_code" value="{{app('request')->input('emp_code')}}">
											</fieldset>
										</div>

										<div class="col-12 col-md-6 mt-2">
											<fieldset class="form-group">		
												<button type="submit" class="btn btn-primary">Search</button>
												<a href="{{ route('employee-id') }}" class="btn btn-warning">Reset</a>
											</fieldset>
										</div>

									</div>
									
								</form>
							</div>
						</div>
					</div>
				</div>
				@if(!empty($emp_result))
				<div class="table-responsive">
					<table  class="table table-bordered table-striped">
						<tr>
							<th>Employee Name</th>
							<td>{{ !empty($emp_result->name) ? $emp_result->name : ''}}</td>
							<th>Employee ID</th>
							<td>{{ !empty($emp_result->id) ? $emp_result->id : ''}}</td>
						</tr>
					</table>
				</div>
				@else
				<p>No Record Found</p>
				@endif
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
		$('.select-multiple').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});	
</script>
@endsection
