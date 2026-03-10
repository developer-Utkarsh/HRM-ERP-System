@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-8 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Chapters</h2>
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
			<div class="content-header-left col-md-4 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<a href="{{ route('admin.chapters.create') }}" class="btn btn-primary">
							Add Chapter
						</a> &nbsp;&nbsp;&nbsp;
						<a href="{{ route('admin.chapter.import') }}" class="btn btn-primary mr-1">Import Chapter</a>
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
								<form action="{{ route('admin.chapters.index') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Course</label>
											<fieldset class="form-group">												
												<select class="form-control" name="course_id">
													<?php $courses = \App\Course::where('status', '1')->where('is_deleted', '0')->orderBy('id','desc')->get(); ?>
													<option value="">Select Any</option>
													@foreach($courses as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('course_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Subjects</label>
											<fieldset class="form-group">												
												<select class="form-control" name="subject_id">
													<?php $subjects = \App\Subject::where('status', '1')->where('is_deleted', '0')->orderBy('id','desc')->get(); ?>
													<option value="">Select Any</option>
													@foreach($subjects as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('subject_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-role">Chapter</label>
											<fieldset class="form-group">
												<input type="text" class="form-control" name="name" placeholder="Chapter" value="{{ app('request')->input('name') }}">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
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
								<th>Course</th>
								<th>Subject</th>
								<th>Chapter</th>
								<th>Duration</th>
								<th>Status</th>
								<th>Created</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							@foreach($chapters as  $key => $value)
							<tr>
								<td>{{ $key + 1 }}</td>
								<td class="product-category">{{ isset($value->course->name) ?  $value->course->name : '' }}</td>
								<td class="product-category">{{ isset($value->subject->name) ?  $value->subject->name : '' }}</td>
								<td class="product-category">{{ $value->name }}</td>
								<td class="product-category">{{ $value->duration }}</td>
								<!--td>@if($value->status == 1) Active @else Inactive @endif</td-->
								<td>
									{{-- @if($value->status == "1")
									<a href="{{ route('admin.chapter.status', $value->id) }}"><i class="fa fa-toggle-on"></i> Active </a>
									@else
									<a href="{{ route('admin.chapter.status', $value->id) }}"><i class="fa fa-toggle-off"></i> Inactive </a>
									@endif --}}
									<a href="{{route('admin.chapter.status', $value->id)}}">
										<strong class="fa fa-lg {{$value->status ? 'fa-toggle-on text-success' : 'fa-toggle-off text-grey'}}" title="Toggle publish"></strong>
									</a>
								</td>
								<td>{{ $value->created_at->format('d-m-Y') }}</td>
								<td class="product-action">
									<a href="{{ route('admin.chapters.edit', $value->id) }}">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
									</a>
									<a href="{{ route('admin.chapter.delete', $value->id) }}" onclick="return confirm('Are You Sure To Delete Chapter')">
										<span class="action-delete"><i class="feather icon-trash"></i></span>
									</a>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
					<div class="d-flex justify-content-center">					
					{!! $chapters->appends($params)->links() !!}
					</div>
				</div>                   
			</section>
		</div>
	</div>
</div>
@endsection

@section('scripts')
@endsection
