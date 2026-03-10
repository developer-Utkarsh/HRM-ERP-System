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
						<h2 class="content-header-title float-left mb-0">Topic <?= isset($record->id) ? 'Edit' : 'Add' ?></h2>

						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>								
							</ol>
						</div>
					</div>
				</div>
			</div>
			<div class="content-header-right text-md-right col-md-3 col-12">
				<a href="{{ route('admin.multi-course-planner.topic',[$subject_id??$id]) }}"><button class="btn btn-primary" type="button">Back</button></a>
			</div>
		</div>
		<div class="content-body">
			<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="{{ route('admin.multi-course-planner.save-topic-master') }}" method="post" enctype="multipart/form-data">
										@csrf
										
										<input type="hidden" class="form-control" name="tid" value="<?=$record->id??''?>">
										<input type="hidden" name="subject_id" value="<?=$subject_id??$id;?>"/>
										<div class="form-body">
											<div class="row">
												<div class="col-12 col-md-4 d-none">
													<label for="users-list-status">Subject</label>
													<fieldset class="form-group">	
														<?php $subject = array(); ?>								
														<select class="form-control select-multiple2 " name="" >
															<option value="">Select Any</option>
															@if(count($subject) > 0)
																@foreach($subject as $su)
																	<option value="{{ $su->id }}" {{ isset($record->subject_id) && $record->subject_id == $su->id ? 'selected' : '' }}>
																		{{ $su->name }}
																	</option>
																@endforeach
															@endif
														</select>
													</fieldset>
												</div>
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Topic Name</label>
														<input type="text" class="form-control" placeholder="Name" name="tname" value="<?=$record->name??'-'?>" required>
														@if($errors->has('tname'))
														<span class="text-danger">{{ $errors->first('tname') }} </span>
														@endif
													</div>
												</div>	
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Topic Name (English)</label>
														<input type="text" class="form-control" placeholder="Name" name="tenname" value="<?=$record->en_name??'-'?>" required>
														@if($errors->has('tname'))
														<span class="text-danger">{{ $errors->first('tname') }} </span>
														@endif
													</div>
												</div>	
												<div class="col-md-4 col-12">
													<div class="form-group">
														<label for="first-name-column">Status</label>
														<?php $status = [1 => 'Active', 2 => 'Inactive']; ?>
														<select class="form-control select-multiple2 status" name="status" required>
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
			<!-- // Basic Floating Label Form section end -->
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

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const dateInput = document.getElementById("timeline");
        const today = new Date();
        today.setDate(today.getDate() + 3); // add 3 days
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        dateInput.min = `${yyyy}-${mm}-${dd}`;
    });
</script>
@endsection
