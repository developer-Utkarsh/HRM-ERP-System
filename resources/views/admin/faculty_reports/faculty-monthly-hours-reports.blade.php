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
						<h2 class="content-header-title float-left mb-0">Faculty Monthly Hours Report</h2>
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
								<form action="{{ route('admin.faculty-reports') }}" method="get" name="filtersubmit">
									<div class="row">
										<input type="hidden" class="faculty_id_get" value="{{ json_encode(app('request')->input('faculty_id')) }}" multiple>
										
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Faculty</label>
											<?php $faculty = \App\User::where('role_id', '2')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2 faculty_id" name="faculty_id[]" multiple>
													@if(count($faculty) > 0)
													@foreach($faculty as $key => $value)
													<option value="{{ $value->id }}" @if(!empty(app('request')->input('faculty_id')) && in_array($value->id, app('request')->input('faculty_id'))) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">											
											<label for="users-list-status">Month</label>								
											<fieldset class="form-group">																					
												<input type="month" name="month" placeholder="Date" value="{{ !empty(app('request')->input('month') ) ? app('request')->input('month') : date('Y-m') }}" class="form-control StartDateClass month">	
											</fieldset>	
										</div>	

										<div class="col-12 col-sm-6 col-lg-2">	
											<fieldset class="form-group" style="float:right;">		
												<a href="javascript:void(0)" id="download_excel" class="btn btn-primary mt-2">Export in Excel</a>
											</fieldset>										
										</div>
											
									</div>
									
									
								</form>
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
<link href="{{ asset('laravel/public/css/jquery.timepicker.css') }}" rel="stylesheet"/>
<script src="{{ asset('laravel/public/js/jquery.timepicker.js') }}"></script>
<script src="{{ asset('laravel/public/admin/js/jquery.validate.min.js') }}"></script>

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
		$('.select-multiple3').select2({
			placeholder: "Select Any",
			allowClear: true
		});
		
		$('.timepicker').timepicker({ 'step': 1, 'timeFormat': 'h:i A' });
	});
	
	
					
	
	$("body").on("click", "#download_excel", function (e) {
		var data = {};
		data.faculty_id = $("[name='faculty_id[]']").val(),
		data.month = $('.month').val(),
		window.location.href = "<?php echo URL::to('/admin/'); ?>/faculty-monthly-hours-report-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});
</script>
@endsection
