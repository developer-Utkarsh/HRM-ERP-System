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
						<h2 class="content-header-title float-left mb-0">Batch Report Shift Wise</h2>
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
								<form action="{{ route('studiomanager.batch-reports-shiftwise') }}" method="get" name="filtersubmit">
									<div class="row">
										
										<div class="col-12 col-sm-6 col-lg-3 branch_loader">
											<label for="users-list-status">Location</label>
											<fieldset class="form-group">												
												<select class="form-control branch_location" name="branch_location" id="">
													<option value="">Select Any</option>
													<option value="jaipur" @if('jaipur' == app('request')->input('branch_location')) selected="selected" @endif>jaipur</option>
													<option value="jodhpur" @if('jodhpur' == app('request')->input('branch_location')) selected="selected" @endif>jodhpur</option>
													<option value="indore" @if('indore' == app('request')->input('branch_location')) selected="selected" @endif>indore</option>
													<option value="prayagraj" @if('indore' == app('request')->input('branch_location')) selected="selected" @endif>prayagraj</option>
												</select>
											</fieldset>
										</div>
									
										<div class="col-12 col-md-3 branch_loader">
											<label for="users-list-status">Branch</label>
											<?php $branches = \App\Branch::where('status', '1')->orderByRaw('Field(id,37,42,40,41,38,48,49,53,52,54,55,56,36,39,43,44,45,46,47,50,51)')->get(); 
											//echo $branches;
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
											<label for="users-list-status">Batch</label>
											<?php $batchs = \App\Batch::where('status', '1')->where('is_deleted', '0')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2 batch_id" name="batch_id[]" multiple>
													<option value="">Select Any</option>
													@if(count($batchs) > 0)
													@foreach($batchs as $key => $value)
													<option value="{{ $value->id }}"  @if(!empty(app('request')->input('batch_id')) && in_array($value->id, app('request')->input('batch_id'))) selected="selected" @endif>{{ $value->name }}</option>
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
										
										
										<div class="col-12 col-md-4 mt-2">
										<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary" name="search" value="search">Search</button>
											<a href="{{ route('studiomanager.batch-reports-shiftwise') }}" class="btn btn-warning">Reset</a>
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
					if (count($get_batches) > 0) {
						
						foreach ($get_batches as $batchArray) { 
							if(!empty($batchArray->id)){ 
								$dataFound++; 
								$whereCond = '1=1 ';
								
								if(!empty($branch_id)){ 
									$whereCond .= " AND branches.id IN (".implode(',', $branch_id).")"; 
								} 
								if(!empty($batch_id)){ 
									$whereCond .= " AND batch.id IN (".implode(',', $batch_id).")"; 
								} 
								if(!empty($type)){ 
									$whereCond .= " AND studios.type = '$type'"; 
								} 
						
								if (!empty($fdate)){ 
									$whereCond .= " AND timetables.cdate = '$fdate'"; 
								}
								else{
									$whereCond .= " AND timetables.cdate = date('Y-m-d')"; 
								}
								
								$get_studio = \App\Batch::select('batch.id','batch.name','studios.id as studios_id','studios.name as studios_name','branches.id as branches_id','branches.name as branches_name')->leftJoin('timetables','timetables.batch_id', '=', 'batch.id')->leftJoin('studios','studios.id', '=', 'timetables.studio_id')->leftJoin('branches','branches.id', '=', 'studios.branch_id')->where('batch.id', $batchArray->id)->whereNotNull('studios.id')->whereRaw($whereCond)->groupBy('studios.id')->get();

								//echo '<pre>'; print_r($get_studio);
								$bt_array = array();
								if(count($get_studio) > 0){
								foreach ($get_studio as $kry=>$value) { 
								
								$whereCond2 = '1=1 ';
								if (!empty($fdate)){ // && !empty($tdate)
									//$whereCond2 .= " AND timetables.cdate >= '$fdate' AND timetables.cdate <= '$tdate'"; 
									$whereCond2 .= " AND timetables.cdate = '$fdate'"; 
								}
								else{
									$whereCond2 .= " AND timetables.cdate = date('Y-m-d')"; 
								}

								$timetable_result = \App\Timetable::select('timetables.*','users.name as users_name','course.name as course_name','subject.name as subject_name','chapter.name as chapter_name','topic.name as topics_name')->leftJoin('users','users.id', '=', 'timetables.faculty_id')->leftJoin('course','course.id', '=', 'timetables.course_id')->leftJoin('subject','subject.id', '=', 'timetables.subject_id')->leftJoin('chapter','chapter.id', '=', 'timetables.chapter_id')->leftJoin('topic','topic.id', '=', 'timetables.topic_id')->where('timetables.studio_id',$value->studios_id)->where('timetables.batch_id',$value->id)->where('timetables.is_deleted','0')->whereRaw($whereCond2);
								
								$timetable_result = $timetable_result->orderBy('timetables.from_time')->orderBy('timetables.cdate')->get();
								if(count($timetable_result) > 0){
								
							?>
							<table class="table data-list-view" style=''>
								@if(!in_array($batchArray->id, $bt_array))
								<head>
									<tr style="">
										<th colspan="3"><h3>Batch Name : <?php echo $batchArray->name; ?></h3></th>
									</tr>
								</head>
								@endif
								<body>
								<tr style="">
								<td style="border: 1px solid;">
							<?php 
							   array_push($bt_array,$batchArray->id);
							?>
							<table class="table data-list-view" style=''>
							 
								<head>
									<tr style="">
										<th colspan="12">
											<b>Branch Name : <?php echo $value->branches_name; ?></b>
											<b>Studio Name : <?php echo $value->studios_name; ?></b>
										</th>
									</tr>
								</head>
								<head>
									<tr style="">
										<th scope="col">Start Time</th>
										<th scope="col">End Time</th>
										<th scope="col">Date</th>
										<th scope="col">Faculty Name</th>
										<th scope="col">Course Name</th>
										<th scope="col">Subject Name</th>
										<th scope="col">Chapter Name</th>
										<th scope="col">Topic Name</th>
										<th scope="col">Date</th>
										<th scope="col">Type</th>
										<th scope="col">Schedule Time</th>
										
									</tr>
								</head>
								<body>
									<?php
									foreach($timetable_result as $key => $timetable){
									
									$schedule_duration  = "00 : 00 Hours"; 	
									$from_time         = new DateTime($timetable->from_time);
									$to_time           = new DateTime($timetable->to_time);
									$schedule_interval = $from_time->diff($to_time);
									$schedule_duration = $schedule_interval->format('%H : %I Hours');
									
									?>
										<tr>
											<td><?php echo isset($timetable->from_time) ?  date('h:i A', strtotime($timetable->from_time)) : '' ?></td>
											<td><?php echo isset($timetable->to_time) ?  date('h:i A', strtotime($timetable->to_time)) : '' ?></td>
											<td><?php echo isset($timetable->cdate) ?  date('d-m-Y',strtotime($timetable->cdate)) : '' ?></td>
											<td><?php echo isset($timetable->users_name) ?  $timetable->users_name : '' ?></td>
											<td><?php echo isset($timetable->course_name) ?  $timetable->course_name : '' ?></td>
											<td><?php echo isset($timetable->subject_name) ?  $timetable->subject_name : '' ?></td>
											<td><?php echo isset($timetable->chapter_name) ?  $timetable->chapter_name : '' ?></td>
											<td><?php echo isset($timetable->topics_name) ?  $timetable->topics_name : '' ?></td>
											<td><?php echo isset($timetable->cdate) ?  $timetable->cdate : '' ?></td>
											<td><?php echo isset($timetable->online_class_type) ?  $timetable->online_class_type : '' ?></td>
											<td><?php echo $schedule_duration ?></td>
										</tr>
									<?php
									}
									//echo $plain_hr;die;
									?>
	
								</body>
							
							</table>
					<p><hr/></p>
							
					</td>
					</tr>
					</body>
					</table>
					<?php } } }?>
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
			tags: true,
			allowClear: true
		});
		$('.select-multiple2').select2({
			tags: false,
			allowClear: true
		});
		$('.select-multiple3').select2({
			tags: true,
			allowClear: true
		});
	});
	

	
	$("body").on("click", "#download_pdf", function (e) {
		var data = {};
			data.branch_location = $('.branch_location').val(),
			data.branch_id = $('.branch_id').val(),
			data.batch_id = $('.batch_id').val(),
			data.fdate = $('.fdate').val(),
			data.tdate = $('.tdate').val(),
			data.type = $('.type').val(),
			window.open("<?php echo URL::to('/studiomanager/'); ?>/batch-report-shiftwise-pdf?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&'));
		/* window.location.href = "<?php echo URL::to('/admstudiomanagerin/'); ?>/studio-report-pdf?" + Object.keys(data).map(function (k) {
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
	
	$(".branch_location").on("change", function () {
		var b_location = $(this).val();
		if (b_location) {
			$.ajax({
				beforeSend: function(){
					// $(".branch_loader i").show();
				},
				type : 'POST',
				url : '{{ route('studiomanager.get-location-wise-branch') }}',
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
	
</script>
@endsection
