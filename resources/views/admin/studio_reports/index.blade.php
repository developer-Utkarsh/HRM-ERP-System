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
						<h2 class="content-header-title float-left mb-0">Studio Report</h2>
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
								<form action="{{ route('admin.studio-reports') }}" method="get" name="filtersubmit">
									<div class="row">
										
										<div class="col-12 col-sm-6 col-lg-3 branch_loader">
											<label for="users-list-status">Location</label>
											<fieldset class="form-group">												
												<select class="form-control branch_location" name="branch_location" id="">
													<option value="">Select Any</option>
													<option value="jaipur" @if('jaipur' == app('request')->input('branch_location')) selected="selected" @endif>jaipur</option>
													<option value="jodhpur" @if('jodhpur' == app('request')->input('branch_location')) selected="selected" @endif>jodhpur</option>
													<option value="delhi" @if('delhi' == app('request')->input('branch_location')) selected="selected" @endif>delhi</option>
													<option value="prayagraj" @if('prayagraj' == app('request')->input('branch_location')) selected="selected" @endif>prayagraj</option>
													<option value="indore" @if('indore' == app('request')->input('branch_location')) selected="selected" @endif>indore</option>
												</select>
											</fieldset>
										</div>
									
										<div class="col-12 col-md-3 branch_loader">
											<label for="users-list-status">Branch</label>
											<?php 
											$branches = \App\Branch::where('status', 1)
													->where('is_deleted','0');
											if(!empty($login_brances)){
												$branches->whereIn('id', $login_brances);
											}
											$branches = $branches->orderByRaw('Field(id,37,42,40,41,38,48,49,53,52,54,55,56,36,39,43,44,45,46,47,50,51)')->get();
											?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 branch_id" name="branch_id[]" id="" multiple>
													<option value="">Select Any</option>
													@if(count($branches) > 0)
													@foreach($branches as $key => $value)
													<option value="{{ $value->id }}"  @if(!empty(app('request')->input('branch_id')) && in_array($value->id, app('request')->input('branch_id'))) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>
												<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
											</fieldset>
										</div>
										
										<div class="col-12 col-md-3">
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
										
										<div class="col-12 col-md-3">
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
										
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Type</label>
											<fieldset class="form-group">												
												<select class="form-control type" name="type">
													<option value="">Select Type</option>
													<option value="Online" @if('Online' == app('request')->input('type')) selected="selected" @endif>Online</option>
													<option value="Offline" @if('Offline' == app('request')->input('type')) selected="selected" @endif>Offline</option>
												</select>												
											</fieldset>
										</div>
										
										<div class="col-12 col-md-3">
											<label for="users-list-status">Date</label>
											<fieldset class="form-group">												
												<input type="date" name="fdate" placeholder="Date" value="{{ app('request')->input('fdate') }}" class="form-control StartDateClass fdate">
											</fieldset>
										</div>
										
										<!--div class="col-12 col-md-3">
											<label for="users-list-status">To Date</label>
											<fieldset class="form-group">												
												<input type="date" name="tdate" placeholder="Date" value="{{ app('request')->input('tdate') }}" class="form-control EndDateClass tdate">
											</fieldset>
										</div-->

										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Change After Publish</label>
											<fieldset class="form-group">												
												<select class="form-control type" name="change_after_publish">
													<option value="">Select Type</option>
													<option value="0" @if('0' == app('request')->input('change_after_publish')) selected="selected" @endif>No</option>
													<option value="1" @if('1' == app('request')->input('change_after_publish')) selected="selected" @endif>Yes</option>
													<option value="2" @if('2' == app('request')->input('change_after_publish')) selected="selected" @endif>Checked</option>
													<option value="3" @if('3' == app('request')->input('change_after_publish')) selected="selected" @endif>Deleted</option>
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-md-3">
											<label for="users-list-status">Class Type</label>
											<select class="form-control online_class_type" name="online_class_type">
												<option value=''>Select Class Type</option>
															
												<option value='Online Course Recording' @if('Online Course Recording' == app('request')->input('online_class_type')) selected="selected" @endif >Online Course Recording</option>
												
												<option value='YouTube Live' @if('YouTube Live' == app('request')->input('online_class_type')) selected="selected" @endif >YouTube Live</option>
												
												<option value='YouTube & App Live' @if('YouTube & App Live' == app('request')->input('online_class_type')) selected="selected" @endif >YouTube & App Live</option>
												
												<option value='Model Paper Recording' @if('Model Paper Recording' == app('request')->input('online_class_type')) selected="selected" @endif >Model Paper Recording</option>
												
												<option value='Offline' @if('Offline' == app('request')->input('online_class_type')) selected="selected" @endif >Offline</option>
												
												<option value='Offline & App live' @if('Offline & App live' == app('request')->input('online_class_type')) selected="selected" @endif >Offline & App live</option>
												
												<option value='App Live' @if('App Live' == app('request')->input('online_class_type')) selected="selected" @endif >App Live</option>
											</select>										
										</div>
										
										<div class="col-12 col-md-12 mt-2">
											<fieldset class="form-group">		
												<button type="submit" class="btn btn-primary" name="search" value="search">Search</button>
												<a href="{{ route('admin.studio-reports') }}" class="btn btn-warning">Reset</a>
												<a href="javascript:void(0)" id="download_pdf" class="btn btn-primary">Export in PDF</a>
												<a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a>
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
						if(count($branchArray->studio) > 0){ //echo '<pre>'; print_r($branchArray->studio);
							$dataFound++;
						?>
						<table class="table data-list-view" style=''>
							<head>
								<tr style="">
									<th colspan="3"><h3>Branch Name : <?php echo $branchArray->name; ?> <?php if($branchArray->nickname!=""){ ?>(<?=$branchArray->nickname;?>)<?php } ?></h3></th>
								</tr>
							</head>
							<body>
							<tr style="">
							<td style="border: 1px solid;">
						<?php
						foreach ($branchArray->studio as $value) { 
						if(count($value->timetable) > 0){
						?>
						<table class="table data-list-view" style=''>
						 
							<head>
								<tr style="">
									<th colspan="12"><?php if($branchArray->nickname!=""){ ?>(<?=$branchArray->nickname;?>)<?php } ?> <b>Studio Name : <?php echo $value->name; ?> - <?php echo $value->capacity; ?></b></th>
									<!--th colspan="3"><b>Assistant Name : <?php echo isset($value->assistant->name) ?  $value->assistant->name : ''; ?></b></th>
									<th colspan="3"><b>Assistant Mob. : <?php echo isset($value->assistant->mobile) ?  $value->assistant->mobile : ''; ?></b></th-->
								</tr>
							</head>
							<head>
								<tr style="">
									<th scope="col">Assistant Name</th>
									<th scope="col">Start Time</th>
									<th scope="col">End Time</th>
									<th scope="col">Date</th>
									<th scope="col">Faculty Name</th>
									<th scope="col">Batch Name</th>
									<th scope="col">Course Name</th>
									<th scope="col">Subject Name</th>
									<th scope="col">Chapter Name</th>
									<th scope="col">Topic Name</th>
									<th scope="col">Type</th>
									<th scope="col">Schedule Time</th>
									
								</tr>
							</head>
							<body>
								<?php
									$total_minutes  = 0;
									foreach($value->timetable as $key => $timetable){									
										$schedule_duration  = "00 : 00 Hours"; 	
										$from_time          = new DateTime($timetable->from_time);
										$to_time            = new DateTime($timetable->to_time);
										$schedule_interval  = $from_time->diff($to_time);
										$schedule_duration  = $schedule_interval->format('%H : %I Hours');
										
										$minutes = ($schedule_interval->h * 60) + $schedule_interval->i;
										$total_minutes += $minutes;
								
								?>
									<tr class="@if($timetable->change_after_publish==1) btn-danger @endif">
										<td><?php echo isset($timetable->assistant->name) ?  $timetable->assistant->name : '' ?></td>
										<td><?php echo isset($timetable->from_time) ?  date('h:i A', strtotime($timetable->from_time)) : '' ?></td>
										<td><?php echo isset($timetable->to_time) ?  date('h:i A', strtotime($timetable->to_time)) : '' ?></td>
										<td><?php echo isset($timetable->cdate) ?  date('d-m-Y',strtotime($timetable->cdate)) : '' ?></td>
										<td><?php echo isset($timetable->faculty->name) ?  $timetable->faculty->name : '' ?></td>
										<td><?php echo $timetable->batch->name??''?> - (<?php echo $timetable->batch->erp_course_id??''?>)</td>
										<td><?php echo $timetable->course->name??''?></td>
										<td><?php echo isset($timetable->subject->name) ?  $timetable->subject->name : '' ?></td>
										<td><?php echo isset($timetable->chapter->name) ?  $timetable->chapter->name : '' ?></td>
										<td><?php echo isset($timetable->topic->name) ?  $timetable->topic->name : '' ?></td>
										<td><?php echo isset($timetable->online_class_type) ?  $timetable->online_class_type : '' ?></td>
										@if($timetable->change_after_publish==1)
											<td class="btn-danger">
												<?php echo $schedule_duration ?>
												<span class="btn btn-sm btn-success changeMark" data-id="{{$timetable->id}}">Mark Done</span>	
											</td>
										@else
										    <td>
												<?php echo $schedule_duration ?>	
											</td>
										@endif
									</tr>
								<?php
								}
								//echo $plain_hr;die;
								?>
								<tr>
									<td colspan="11"><b>Total Schedule Hours</b></td>
									<td>	<?php 
											$total_hours = floor($total_minutes / 60);
											$remaining_minutes = $total_minutes % 60;

											echo $total_schedule = sprintf('%02d : %02d Hours', $total_hours, $remaining_minutes);
										?>
									</td>
								</tr>

							</body>
						
						</table>
				<p><hr/></p>
						<?php } } ?>
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
	
	$("body").on("click", "#download_excel", function (e) {
		var data = {};
		data.branch_location = $('.branch_location').val(),
		data.studio_id = $('.studio_id').val(),
		data.branch_id = $('.branch_id').val(),
		data.assistant_id = $('.assistant_id').val(),
		data.fdate = $('.fdate').val(),
		data.type = $('.type').val(),
		data.online_class_type = $('.online_class_type').val(),
		window.location.href = "<?php echo URL::to('/admin/'); ?>/studio-report-report-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});
	
	$("body").on("click", "#download_pdf", function (e) {
		/* if ($userTable.data().count() == 0) {
			swal("Warning!", "Not have any data!", "warning");
			return;
		} */
		var data = {};
			data.branch_location = $('.branch_location').val(),
			data.studio_id = $('.studio_id').val(),
			data.branch_id = $('.branch_id').val(),
			data.assistant_id = $('.assistant_id').val(),
			data.fdate = $('.fdate').val(),
			data.type = $('.type').val(),
			data.online_class_type = $('.online_class_type').val(),
			// data.tdate = $('.tdate').val(),
			window.open("<?php echo URL::to('/admin/'); ?>/studio-report-pdf?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&'));
		/* window.location.href = "<?php echo URL::to('/admin/'); ?>/studio-report-pdf?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&'); */
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
				url : '{{ route('admin.get-branchwise-studio') }}',
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
				url : '{{ route('admin.get-branchwise-assistant') }}',
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
				url : '{{ route('admin.get-branchwise-assistant') }}',
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
	
	$(".branch_location").on("change", function () {
		var b_location = $(this).val();
		if (b_location) {
			$.ajax({
				beforeSend: function(){
					// $(".branch_loader i").show();
				},
				type : 'POST',
				url : '{{ route('admin.get-location-wise-branch') }}',
				data : {'_token' : '{{ csrf_token() }}', 'b_location': b_location},
				dataType : 'html',
				success : function (data){
					// $(".branch_loader i").hide();
					$('.branch_id').empty();
					$('.branch_id').append(data);
				}
			});
			
		}
	});

	$(document).on("click",".changeMark",function (){
		var _this=$(this);
		var timetable_id = $(this).attr("data-id");
		$.ajax({
			type : 'POST',
			url : '{{ route('admin.studio-reports.changeMark') }}',
			data : {'_token' : '{{ csrf_token() }}', 'timetable_id': timetable_id},
			dataType : 'json',
			success : function (data){
				$(_this).text('Done');
				swal("Success!", "Changed Marked Done", "success");
			}
		});
	});
	
</script>
@endsection
