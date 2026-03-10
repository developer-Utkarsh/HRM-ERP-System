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
						<h2 class="content-header-title float-left mb-0">Sub Topic <?= isset($record->id) ? 'Edit' : 'Add' ?></h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Add Request</a>
								</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
			<div class="content-header-right text-md-right col-md-3 col-12">
				<a href="{{ route('admin.multi-course-planner.sub-topic',[$id]) }}"><button class="btn btn-primary" type="button">Back</button></a>
			</div>
		</div>
		<div class="content-body">
			<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="{{ route('admin.multi-course-planner.save-sub-topic-master') }}" method="post" enctype="multipart/form-data">
										@csrf
										<input type="hidden" class="form-control" name="tsid" value="<?=$record->id??''?>">
										
										<div class="form-body">
											<div class="row">
												<div class="col-12 col-md-4">
													<label for="users-list-status">Topic</label>
													<fieldset class="form-group">	
														<?php $topic = DB::table('topic_master')->where('subject_id',$id)->where('status', '1')->get(); ?>								
														<select class="form-control select-multiple2 topic_id" name="topic_id" id="">
															<option value="">Select Any</option>
															@if(count($topic) > 0)
																@foreach($topic as $to)
																	<option value="{{ $to->id }}" {{ isset($record->topic_id) && $record->topic_id == $to->id ? 'selected' : '' }}>
																		{{ $to->name }}
																	</option>
																@endforeach
															@endif
														</select>
													</fieldset>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Sub Topic Name</label>
														<input type="text" class="form-control"  name="stname" value="<?=$record->name??''?>">
														@if($errors->has('stname'))
														<span class="text-danger">{{ $errors->first('stname') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Status</label>
														<?php $status = [1 => 'Active', 2 => 'Inactive']; ?>
														<select class="form-control select-multiple2 status" name="status">
															<option value="">Select Any</option>
															@foreach($status as $key => $label)
																<option value="{{ $key }}"
																	@if(old('status', $record->status ?? '') == $key) selected @endif>
																	{{ $label }}
																</option>
															@endforeach
														</select>
													</div>
												</div>
												<div class="col-12">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Submit</button>
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
	$('.select-multiple2').select2({
		placeholder: "Select Any",
		allowClear: true
	});
});

function selectAll() {
    $(".select-multiple1 > option").prop("selected", true);
    $(".select-multiple1").trigger("change");
}

function deselectAll() {
    $(".select-multiple1 > option").prop("selected", false);
    $(".select-multiple1").trigger("change");
}
</script>

@endsection
