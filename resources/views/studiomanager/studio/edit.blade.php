@extends('layouts.studiomanager')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Edit Studio</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('studiomanager.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">Edit Studio
								</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
						<a href="{{ route('studiomanager.studios.index') }}" class="btn btn-primary mr-1">Back</a>
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
									<form class="form" action="{{ route('studiomanager.studios.update', $studio->id) }}" method="post" enctype="multipart/form-data">
										@method('PATCH')
										@csrf
										<div class="form-body">
											<div class="row">
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Studio Name</label>
														<input type="text" class="form-control" placeholder="Studio Name" name="name" value="{{ old('name', $studio->name) }}">
														@if($errors->has('name'))
														<span class="text-danger">{{ $errors->first('name') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-6 col-12" id="branch_loader">
													<div class="form-group">
														<?php $branches = \App\Branch::where('status', '1')->orderBy('id', 'desc')->get(); ?>
														<label for="first-name-column">Branch</label>
														@if(count($branches) > 0)
														<select class="form-control branch_id select-multiple1" name="branch_id">
															<option value=""> - Select Branch - </option>
															@foreach($branches as $value)
															<option value="{{ $value->id }}" @if($value->id == $studio->branch_id) selected="selected" @endif>{{ $value->name }}</option>
															@endforeach
														</select>
														<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
														<input type="hidden" name="assistant_id" value="{{ $studio->assistant_id }}">
														@endif
														@if($errors->has('branch_id'))
														<span class="text-danger">{{ $errors->first('branch_id') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-6 col-12" id="chk_loader">
													<div class="form-group">
														<label for="first-name-column">Studio Assistant</label>
														<select class="form-control assistant_id select-multiple2" name="assistant_id">
															{{-- @if(isset($studio->assistant_id) && !empty($studio->assistant_id))
															<option value="{{ $studio->assistant_id }}">{{ $studio->assistant->name }}</option>
															@endif --}}
														</select>
														<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
														<small class="msg text-danger"></small>
														@if($errors->has('assistant_id'))
														<span class="text-danger">{{ $errors->first('assistant_id') }} </span>
														@endif
													</div>
												</div>												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Floor</label>
														<select class="form-control select-multiple3" name="floor">
															<option value=""> - Select Floor - </option>
															<?php for($i=1;$i<=10;$i++) { ?>
															<option value="{{ $i }}" @if($studio->floor == $i) selected="selected" @endif>{{ $i }}</option>
															<?php } ?>
														</select>
														@if($errors->has('floor'))
														<span class="text-danger">{{ $errors->first('floor') }} </span>
														@endif
													</div>
												</div>
												
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Order No</label>
														<input type="number" class="form-control" placeholder="Order No" name="order_no" value="{{ old('order_no', $studio->order_no) }}" min="1">
														@if($errors->has('order_no'))
														<span class="text-danger">{{ $errors->first('order_no') }} </span>
														@endif
													</div>
												</div> 
												
												<div class="col-md-6 col-12">
													<div class="form-group d-flex align-items-center">
														<label class="mr-2">Status :</label>
														<label>
															<input type="radio" name="status" value="1" {{ ($studio->status == 1) ? "checked" : ""}}>
															Active
														</label>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="status" value="0" {{ ($studio->status == 0) ? "checked" : ""}}>
															Inactive
														</label>
													</div>
												</div> 
												<div class="col-md-6 col-12">
													<div class="form-group">
														<label for="first-name-column">Type</label>
														<select class="form-control change_class" name="type">
															<option value=""> - Select Type - </option>
															<option value="Online" @if($studio->type == 'Online') selected="selected" @endif>Online</option>
															<option value="Offline" @if($studio->type == 'Offline') selected="selected" @endif >Offline</option>
														</select>
														@if($errors->has('type'))
														<span class="text-danger">{{ $errors->first('type') }} </span>
														@endif
													</div>
												</div>	
												
												<div class="col-md-6 col-12 capacity_div" style="display:<?=($studio->type=='Offline')?'':'none';?>">
													<div class="form-group">
														<label for="first-name-column">Capacity</label>
														<input type="number" class="form-control" placeholder="Capacity" name="capacity" value="{{ old('capacity', $studio->capacity) }}" min="0">
														@if($errors->has('capacity'))
														<span class="text-danger">{{ $errors->first('capacity') }} </span>
														@endif
													</div>
												</div>
												<div class="col-md-6 col-12 mt-2">
													<div class="form-group d-flex align-items-center">
														<label class="mr-2">IS OBS System :</label>
														<label>
															<input type="radio" name="is_obs" value="Yes" {{ ($studio->is_obs == 'Yes') ? "checked" : ""}}>
															Yes
														</label>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
														<label>
															<input type="radio" name="is_obs" value="No" {{ ($studio->is_obs == 'No') ? "checked" : ""}}>
															No
														</label>
													</div>
												</div>
												
												<div class="col-12">
													<button type="submit" class="btn btn-primary mr-1 mb-1 btn_submit">Update</button>
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
			placeholder: "Select",
			allowClear: true
		});
		$('.select-multiple2').select2({
			placeholder: "Select",
			allowClear: true
		});
		$('.select-multiple3').select2({
			placeholder: "Select",
			allowClear: true
		});
	})
</script>
<script type="text/javascript">
	$(".branch_id").on("change", function () {
		var branch_id = $(".branch_id option:selected").attr('value');
		var assistant_id = $("input[name=assistant_id]").val();
		if (branch_id) {
			$.ajax({
				beforeSend: function(){
					$("#branch_loader i").show();
				},
				type : 'POST',
				url : '{{ route('studiomanager.get-branchwise-assistant') }}',
				data : {'_token' : '{{ csrf_token() }}', 'branch_id': branch_id, 'assistant_id': assistant_id},
				dataType : 'html',
				success : function (data){
					$("#branch_loader i").hide();
					$('.assistant_id').empty();
					$('.assistant_id').append(data);
				}
			});
		}
	});

	$(document).ready(function() {
		var branch_id = $(".branch_id option:selected").attr('value');
		var assistant_id = $("input[name=assistant_id]").val();
		if (branch_id) {
			$.ajax({
				beforeSend: function(){
					$("#branch_loader i").show();
				},
				type : 'POST',
				url : '{{ route('studiomanager.get-branchwise-assistant') }}',
				data : {'_token' : '{{ csrf_token() }}', 'branch_id': branch_id, 'assistant_id': assistant_id},
				dataType : 'html',
				success : function (data){
					$("#branch_loader i").hide();
					$('.assistant_id').empty();
					$('.assistant_id').append(data);
				}
			});
		}
	});

</script>
<script type="text/javascript">
	$(".assistant_id").on("change", function () {
		var assistant_id = $(".assistant_id option:selected").attr('value');
		if (assistant_id) {
			$.ajax({
				beforeSend: function(){
					$("#chk_loader i").show();
				},
				type : 'POST',
				url : '{{ route('studiomanager.getassistantexits') }}',
				data : {'_token' : '{{ csrf_token() }}', 'assistant_id': assistant_id},
				dataType : 'json',
				success : function (data){
					if (data.status == true) {
						$("#chk_loader i").hide();
						$(".msg").text(data.data);
						$('.btn_submit').attr('disabled', 'disabled');
					} else {
						$("#chk_loader i").hide();
						$(".msg").text(data.data);
						$('.btn_submit').removeAttr('disabled');
					}
				}
			});
		}
	});
	$(".change_class").on("change", function () {
		if($(this).val()=='Offline'){
			$(".capacity_div").css('display','block');
		}
		else{			
			$(".capacity_div").css('display','none');
		}
	});
</script>
@endsection
