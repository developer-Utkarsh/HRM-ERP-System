@extends('layouts.studiomanager')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Subjects</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('studiomanager.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
			<div class="content-header-left col-md-3 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<a href="{{ route('studiomanager.subjects.create') }}" class="btn btn-primary">
							Add Subject
						</a>
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
								<form action="{{ route('studiomanager.subjects.index') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Name</label>
											<fieldset class="form-group">
												<input type="text" class="form-control" name="name" placeholder="Name" value="{{ app('request')->input('name') }}">
											</fieldset>
										</div>

										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Status</label>
											<fieldset class="form-group">												
												<select class="form-control" name="status">
													@php $status = ['Inactive', 'Active']; @endphp
													<option value="">Select Any</option>
													@foreach($status as $key => $value)
													<option value="{{ $value }}" @if($value == app('request')->input('status')) selected="selected" @endif>{{ $value }}</option>
													@endforeach
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<button type="submit" class="btn btn-primary mt-1">Search</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table class="table data-list-view">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Subject</th>
								<th>Subject ID</th>
								<th>Status</th>
								<th>Created</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>							
							@foreach($subjects as  $key => $value)
							<tr>
								<td>{{ $key + 1 }}</td>
								<td class="product-category">{{ $value->name }}</td>
								<td class="product-category"><b>{{ $value->id }}</b></td>
								<!--td>@if($value->status == 1) Active @else Inactive @endif</td-->
								<td>
									{{-- @if($value->status == "1")
									<a href="{{ route('studiomanager.subject.status', $value->id) }}"><i class="fa fa-toggle-on"></i> Active </a>
									@else
									<a href="{{ route('studiomanager.subject.status', $value->id) }}"><i class="fa fa-toggle-off"></i> Inactive </a>
									@endif --}}
									<a href="{{route('studiomanager.subject.status', $value->id)}}">
										<strong class="fa fa-lg {{$value->status ? 'fa-toggle-on text-success' : 'fa-toggle-off text-grey'}}" title="Toggle publish"></strong>
									</a>
								</td>

								<td>{{ date('d-m-Y', strtotime($value->created_at)) }}</td>
								<td class="product-action">
									<a href="{{ route('studiomanager.subjects.edit', $value->id) }}">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
									</a>
									<a href="{{ route('studiomanager.subject.delete', $value->id) }}" onclick="return confirm('Are You Sure To Delete Subject')">
										<span class="action-delete"><i class="feather icon-trash"></i></span>
									</a>
									
									@if($value->topic_count > 1)
										<a href="{{ route('admin.multi-course-planner.topic', $value->id) }}" class="subject_view">
											<i class="fa fa-book" aria-hidden="true"></i>
										</a>
									@else
										<a href="{{ route('admin.multi-course-planner.topic', $value->id) }}" class="subject_view">
											<i class="fa fa-plus" aria-hidden="true"></i>
										</a>
									@endif
								</td>
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
