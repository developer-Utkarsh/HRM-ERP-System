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
						<h2 class="content-header-title float-left mb-0">Topics</h2>
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
						<a href="{{ route('studiomanager.topics.create') }}" class="btn btn-primary">
							Add Topic
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
								<form action="{{ route('studiomanager.topics.index') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Course</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 course_id" name="course_id">
													<?php $courses = \App\Course::where('status', '1')->where('is_deleted', '=', '0')->orderBy('id','desc')->get(); ?>
													<option value="">Select Any</option>
													@foreach($courses as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('course_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
												</select>
												<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Subjects</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 subject_id" name="subject_id">
													<?php 
													$subjects = \App\Subject::where('status', '1')->where('is_deleted', '=', '0')->limit(1)->orderBy('id','desc')->get();
													 ?>
													<option value="">Select Any</option>
													@foreach($subjects as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('subject_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Chapters</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2 chapter_id" name="chapter_id">
													<?php $chapters = \App\Chapter::where('status', '1')->where('is_deleted', '=', '0')->limit(1)->orderBy('id','desc')->get(); ?>
													<option value="">Select Any</option>
													@foreach($chapters as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('chapter_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-role">Topic</label>
											<fieldset class="form-group">
												<input type="text" class="form-control" name="name" placeholder="Topic" value="{{ app('request')->input('name') }}">
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
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('studiomanager.topics.index') }}" class="btn btn-warning">Reset</a>
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
								<th>Topic</th>
								<th>Course</th>
								<th>Subject</th>
								<th>Chapter</th>
								<th>Status</th>
								<th>Created</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							@foreach($topics as  $key => $value)
							<tr>
								<td>{{ $pageNumber++ }}</td>
								<td class="product-category">{{ $value->name }}</td>
								<td class="product-category">{{ isset($value->course->name) ? $value->course->name : '' }}</td>
								<td class="product-category">{{ isset($value->subject->name) ? $value->subject->name : '' }}</td>
								<td class="product-category">{{ isset($value->chapter->name) ? $value->chapter->name : '' }}</td>
								<!--td>@if($value->status == 1) Active @else Inactive @endif</td-->
								<td>
									{{-- @if($value->status == "1")
									<a href="{{ route('studiomanager.topics.status', $value->id) }}"><i class="fa fa-toggle-on"></i> Active </a>
									@else
									<a href="{{ route('studiomanager.topics.status', $value->id) }}"><i class="fa fa-toggle-off"></i> Inactive </a>
									@endif --}}
									<a href="{{route('studiomanager.topics.status', $value->id)}}">
										<strong class="fa fa-lg {{$value->status ? 'fa-toggle-on text-success' : 'fa-toggle-off text-grey'}}" title="Toggle publish"></strong>
									</a>
								</td>
								<td>{{ $value->created_at->format('d-m-Y') }}</td>
								<td class="product-action">
									<a href="{{ route('studiomanager.topics.edit', $value->id) }}">
										<span class="action-edit"><i class="feather icon-edit"></i></span>
									</a>
									<a href="{{ route('studiomanager.topic.delete', $value->id) }}" onclick="return confirm('Are You Sure To Delete Topic')">
										<span class="action-delete"><i class="feather icon-trash"></i></span>
									</a>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
					<div class="d-flex justify-content-center">					
					{!! $topics->appends($params)->links() !!}
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
		$('.select-multiple2,.select-multiple3').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
</script>

<script type="text/javascript">
	$(".course_id").on("change", function () {
		var course_id = $(".course_id option:selected").attr('value');
		if (course_id) {
			$.ajax({
				beforeSend: function(){
					$("#course_loader i").show();
				},
				type : 'POST',
				url : '{{ route('studiomanager.get-batch-subject') }}',
				data : {'_token' : '{{ csrf_token() }}', 'course_id': course_id},
				dataType : 'html',
				success : function (data){
					$("#course_loader i").hide();
					$('.subject_id').empty();
					$('.subject_id').append(data);
				}
			});
		}
	});
</script>
<script type="text/javascript">
	$(".subject_id").on("change", function () {
		var subject_id = $(".subject_id option:selected").attr('value');
		var course_id = $(".course_id option:selected").attr('value');
		if (subject_id) {
			$.ajax({
				beforeSend: function(){
					$("#subject_loader i").show();
				},
				type : 'POST',
				url : '{{ route('studiomanager.get-chapter') }}',
				data : {'_token' : '{{ csrf_token() }}', 'subject_id': subject_id,'course_id':course_id},
				dataType : 'html',
				success : function (data){
					$("#subject_loader i").hide();
					$('.chapter_id').empty();
					$('.chapter_id').append(data);
				}
			});
		}
	});
</script>
@endsection
