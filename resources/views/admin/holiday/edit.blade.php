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
						<h2 class="content-header-title float-left mb-0">Edit Holiday</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active"><a href="#">Edit Holiday</a>
								</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
			<div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
			</div>
		</div>
		<div class="content-body">
			<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<form class="form" action="{{ route('admin.holiday.update', $holiday->id) }}" method="post" enctype="multipart/form-data">
										<!-- @method('PATCH')-->
										@csrf
										<div class="form-body">
											<div class="row">

												<div class="col-12 col-md-4 branch_loader">
													<label for="users-list-status">Location</label>
													<fieldset class="form-group">	
														<?php $locations = \App\Location::where('status', '1')->where('is_deleted', '0')->orderBy('name')->get(); 
														?>											
														<select class="form-control select-multiple2 branch_location" name="branch_location[]" id="" multiple>
															<option value="">Select Any</option>
															@if(count($locations) > 0)
															@foreach($locations as $key => $value)
															<option value="{{ $value->name }}"  @if(!empty(old('branch_location')) && in_array($value->name, old('branch_location'))) selected="selected" @endif>{{ $value->name }}</option>
															@endforeach
															@endif
														</select>
													</fieldset>
												</div>
											
												<div class="col-12 col-md-4 branch_loader">
													<label for="users-list-status">Branch</label>
													<?php $branches = \App\Branch::where('status', '1')->orderByRaw('Field(id,37,42,40,41,38,48,49,53,52,54,55,56,36,39,43,44,45,46,47,50,51)')->get(); 
													//echo $branches;
													?>
													<fieldset class="form-group">												
														<select class="form-control select-multiple1 branch_id" name="branch_id[]" id="" multiple>
															<option value="">Select Any</option>
															@if(count($branches) > 0)
															@foreach($branches as $key => $value)
															<option value="{{ $value->id }}"  @if((!empty(old('branch_id')) && $value->id == old('branch_id')) || (!empty($holiday->branch_id) && in_array($value->id, json_decode($holiday->branch_id)))) selected="selected" @endif>{{ $value->name }}</option>
															@endforeach
															@endif
														</select>
														@if($errors->has('branch_id'))
														<span class="text-danger">{{ $errors->first('branch_id') }} </span>
														@endif
														<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
													</fieldset>
												</div>
												<div class="col-12 col-md-2">
													<button class="btn btn-primary mt-1 float-right" type="button" onclick="selectAll()">Select All</button>
												</div>
												<div class="col-12 col-md-2">
													<button class="btn btn-primary mt-1" type="button" onclick="deselectAll()">Unselect All</button>
												</div>

												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Title</label>
														<input type="text" class="form-control" placeholder="Title" name="title" value="{{ old('name', $holiday->title) }}">
														@if($errors->has('title'))
														<span class="text-danger">{{ $errors->first('title') }} </span>
														@endif
													</div>
												</div>
												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Date</label>
														<input type="date" class="form-control" name="date" value="{{ old('name', $holiday->date) }}">
														@if($errors->has('date'))
														<span class="text-danger">{{ $errors->first('date') }} </span>
														@endif
													</div> 
												</div>
												
												<div class="col-md-6 col-12" style="display:none;">
													<div class="form-group d-flex align-items-center mt-2">
														<label class="mr-2">Type :</label>
														<label>
															<input type="radio" name="type" value="Public" {{ ($holiday->type == 'Public') ? "checked" : ""}}>
															Public
														</label>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="type" value="Optional" {{ ($holiday->type == 'Optional') ? "checked" : ""}}>
															Optional
														</label>
													</div>
												</div>	

												<div class="col-md-3 col-12">
													<div class="form-group d-flex align-items-center mt-2">
														<label class="mr-2">Status :</label>
														<label>
															<input type="radio" name="status" value="1" {{ ($holiday->status == 1) ? "checked" : ""}}>
															Active
														</label>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="status" value="0" {{ ($holiday->status == 0) ? "checked" : ""}}>
															Inactive
														</label>
													</div>
												</div> 											
												                                     
												<div class="col-12">
													<button type="submit" class="btn btn-primary mr-1 mb-1">Update</button>
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

$(".branch_location").on("change", function () {
	var b_location = $(this).val();
	if (b_location) {
		$.ajax({
			type : 'POST',
			url : '{{ route('admin.get-multi-location-wise-branch') }}',
			data : {'_token' : '{{ csrf_token() }}', 'b_location': b_location},
			dataType : 'html',
			success : function (data){
				$('.branch_id').empty();
				$('.branch_id').append(data);
			}
		});
		
	}
});
</script>
@endsection
