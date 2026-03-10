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
						<h2 class="content-header-title float-left mb-0">Leave Count View</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">Leave View</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="content-body">
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				<?php if(Auth::user()->role_id == 29 || Auth::user()->role_id == 30 || Auth::user()->role_id == 24){ ?>
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ route('admin.leave.get_pending_comp_off_all_users') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-6 col-sm-6 col-lg-3" >
											<label for="users-list-role">Employee ID</label>
											<fieldset class="form-group">
												<input type="text" class="form-control name" name="empCode" placeholder="EMP ID" value="{{ app('request')->input('name') }}">
											</fieldset>
										</div>
										<div class="col-6 col-sm-6 col-lg-3 pt-2" >
											<fieldset class="form-group" style="float:right;">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('admin.leave.get_pending_comp_off_all_users') }}" class="btn btn-warning">Reset</a>
											</fieldset>
										</div>
									</div>
									
								</form>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
				<div class="table-responsive">
					<table class="table data-list-view" style="background:#fff;">
						<thead style="text-align: ;">
							<tr>
								<th>Name</th>
								<th>Total Remaining PL</th>
								<th>Total Remaining CL</th>
								<th>Total Remaining Comp Off</th>
							</tr>
						</thead>
						<tbody >
						
							 
							@foreach($leave as $k=>$pending_leaves)
							<tr >
								<td>{{ $pending_leaves->uName }} ({{$pending_leaves->register_id}})</td>
								<td>{{ $pending_leaves->pending_pl }}</td>
								<td>{{ $pending_leaves->pending_cl }}</td>
								<td>{{ $pending_leaves->pending_comp_off }}</td>
							</tr>

							@endforeach
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
