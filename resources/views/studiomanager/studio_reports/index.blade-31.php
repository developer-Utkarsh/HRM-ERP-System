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
						<h2 class="content-header-title float-left mb-0">Studio Report</h2>
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
		</div>
		<div class="content-body">
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ route('studiomanager.studio-reports') }}" method="get" name="filtersubmit">
									<div class="row">
									
										<div class="col-12 col-sm-6 col-lg-2 branch_loader">
											<label for="users-list-status">Branch</label>
											<?php $branches = \App\Branch::where('status', '1')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 branch_id" name="branch_id" id="">
													<option value="">Select Any</option>
													@if(count($branches) > 0)
													@foreach($branches as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('branch_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>
												<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
											</fieldset>
										</div>
										
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Studio</label>
											<?php $studios = \App\Studio::where('status', '1');
											if(app('request')->input('branch_id')){
												$studios->where('branch_id',app('request')->input('branch_id'));
											}
											$studios = $studios->orderBy('id','desc')->get();
											?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple3 studio_id" name="studio_id" id="">
													<option value="">Select Any</option>
													@if(count($studios) > 0)
													@foreach($studios as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('studio_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										
										<input type="hidden" class="assistant_id_get" value="{{ app('request')->input('assistant_id') }}">
										
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Assistants</label>
											<?php $assistants = \App\User::where('role_id', '3')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2 assistant_id" name="assistant_id">
													<option value="">Select Any</option>
													@if(count($assistants) > 0)
													@foreach($assistants as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('assistant_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-5">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('studiomanager.studio-reports') }}" class="btn btn-warning">Reset</a>
											<a href="javascript:void(0)" id="download_pdf" class="btn btn-primary">Export in PDF</a>
											</fieldset>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
				
				<?php 
				$dataFound = 0;
				if (count($get_studios) > 0) {
					
					foreach ($get_studios as $branchArray) {
						if(count($branchArray->studio) > 0){
							$dataFound++;
						?>
						<table class="table data-list-view" style=''>
							<head>
								<tr style="">
									<th colspan="3"><h3>Branch Name : <?php echo $branchArray->name; ?></h3></th>
								</tr>
							</head>
							<body>
							<tr style="">
							<td style="border: 1px solid;">
						<?php
						foreach ($branchArray->studio as $value) { 
						// echo "<pre>"; print_r($value); die;
						?>
						<table class="table data-list-view" style=''>
						 
							<head>
								<tr style="">
									<th colspan="3"><b>Studio Name : <?php echo $value->name; ?></b></th>
									<!--th colspan="3"><b>Assistant Name : <?php echo isset($value->assistant->name) ?  $value->assistant->name : ''; ?></b></th>
									<th colspan="3"><b>Assistant Mob. : <?php echo isset($value->assistant->mobile) ?  $value->assistant->mobile : ''; ?></b></th-->
								</tr>
							</head>
							<head>
								<tr style="">
									<th scope="col">Assistant Name</th>
									<th scope="col">From Time</th>
									<th scope="col">To Time</th>
									<th scope="col">Date</th>
									<th scope="col">Faculty Name</th>
									<th scope="col">Batch Name</th>
									<th scope="col">Course Name</th>
									<th scope="col">Subject Name</th>
									<th scope="col">Chapter Name</th>
									<th scope="col">Topic Name</th>
								</tr>
							</head>
							<body>
								<?php
								if(count($value->timetable) > 0){
								foreach($value->timetable as $key => $timetable){
								?>
									<tr>
										<td><?php echo isset($timetable->assistant->name) ?  $timetable->assistant->name : '' ?></td>
										<td><?php echo isset($timetable->from_time) ?  $timetable->from_time : '' ?></td>
										<td><?php echo isset($timetable->to_time) ?  $timetable->to_time : '' ?></td>
										<td><?php echo isset($timetable->cdate) ?  $timetable->cdate : '' ?></td>
										<td><?php echo isset($timetable->faculty->name) ?  $timetable->faculty->name : '' ?></td>
										<td><?php echo isset($timetable->batch->name) ?  $timetable->batch->name : '' ?></td>
										<td><?php echo isset($timetable->course->name) ?  $timetable->course->name : '' ?></td>
										<td><?php echo isset($timetable->subject->name) ?  $timetable->subject->name : '' ?></td>
										<td><?php echo isset($timetable->chapter->name) ?  $timetable->chapter->name : '' ?></td>
										<td><?php echo isset($timetable->topic->name) ?  $timetable->topic->name : '' ?></td>
									</tr>
								<?php
								}
								}
								?>
							</body>
						
						</table>
				<p><hr/></p>
				<?php } ?>
				</td>
				</tr>
				</body>
				</table>
				<?php
					}
					}
				}
				?>
		<style>
		hr{background:#000;}
		</style>
					 
				</div>       
<?php
if($dataFound==0){
	?>
	<p style="text-align:center;"><h3>Data not found.</h3></p>
	<?php
}?>
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
		$('.select-multiple3').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
	
	$("body").on("click", "#download_pdf", function (e) {
		/* if ($userTable.data().count() == 0) {
			swal("Warning!", "Not have any data!", "warning");
			return;
		} */
		var data = {};
			data.studio_id = $('.studio_id').val(),
			data.branch_id = $('.branch_id').val(),
			data.assistant_id = $('.assistant_id').val(),
		window.location.href = "<?php echo URL::to('/studiomanager/'); ?>/studio-report-pdf?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});
</script>
<script type="text/javascript">
	$(".branch_id").on("change", function () {
		var branch_id = $(".branch_id option:selected").attr('value');
		var assistant_id = $("input[name=assistant_id]").val();
		if (branch_id) {
			$.ajax({
				beforeSend: function(){
					// $(".branch_loader i").show();
				},
				type : 'POST',
				url : '{{ route('studiomanager.get-branchwise-studio') }}',
				data : {'_token' : '{{ csrf_token() }}', 'branch_id': branch_id},
				dataType : 'html',
				success : function (data){
					// $(".branch_loader i").hide();
					$('.studio_id').empty();
					$('.studio_id').append(data);
				}
			});
			
			$.ajax({
				beforeSend: function(){
					// $(".branch_loader i").show();
				},
				type : 'POST',
				url : '{{ route('studiomanager.get-branchwise-assistant') }}',
				data : {'_token' : '{{ csrf_token() }}', 'branch_id': branch_id, 'assistant_id': assistant_id},
				dataType : 'html',
				success : function (data){
					// $(".branch_loader i").hide();
					$('.assistant_id').empty();
					$('.assistant_id').append(data);
				}
			});
			
			
		}
	});
	
	$(document).ready(function() {
		var branch_id = $(".branch_id option:selected").attr('value');
		var assistant_id = $(".assistant_id_get").val();
		if (branch_id) {
			$.ajax({
				beforeSend: function(){
					// $(".branch_loader i").show();
				},
				type : 'POST',
				url : '{{ route('studiomanager.get-branchwise-assistant') }}',
				data : {'_token' : '{{ csrf_token() }}', 'branch_id': branch_id, 'assistant_id': assistant_id},
				dataType : 'html',
				success : function (data){
					// $(".branch_loader i").hide();
					$('.assistant_id').empty();
					$('.assistant_id').append(data);
				}
			});
		}
	});
</script>
@endsection
