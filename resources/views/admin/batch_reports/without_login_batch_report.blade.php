@extends('layouts.without_login_admin')
@section('content')

<style>
    .table-responsive-stack tr {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-orient: horizontal;
  -webkit-box-direction: normal;
      -ms-flex-direction: row;
          flex-direction: row;
}


.table-responsive-stack td,
.table-responsive-stack th {
   display:block;
/*      
   flex-grow | flex-shrink | flex-basis   */
   -ms-flex: 1 1 auto;
    flex: 1 1 auto;
}

.table-responsive-stack .table-responsive-stack-thead {
   font-weight: bold;
}

@media screen and (max-width: 770px) {
   .table-responsive-stack tr {
      -webkit-box-orient: vertical;
      -webkit-box-direction: normal;
          -ms-flex-direction: column;
              flex-direction: column;
      border-bottom: 3px solid #ccc;
      display:block;
      
   }
   /*  IE9 FIX   */
   .table-responsive-stack td {
      float: left\9;
      width:100%;
   }
}
.table tbody + tbody {
    border-top: 2px solid #ccc;
}
    </style>

<!-- Accordion CSS & JS Strat-->
    
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Accordion CSS & JS End-->
    
    <style>
	/* Accordion CSS style */
    .accordion-button::after {
      background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='%23333' xmlns='http://www.w3.org/2000/svg'%3e%3cpath fill-rule='evenodd' d='M8 0a1 1 0 0 1 1 1v6h6a1 1 0 1 1 0 2H9v6a1 1 0 1 1-2 0V9H1a1 1 0 0 1 0-2h6V1a1 1 0 0 1 1-1z' clip-rule='evenodd'/%3e%3c/svg%3e");
      transform: scale(.7) !important;
    }
    .accordion-button:not(.collapsed)::after {
      background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='%23333' xmlns='http://www.w3.org/2000/svg'%3e%3cpath fill-rule='evenodd' d='M0 8a1 1 0 0 1 1-1h14a1 1 0 1 1 0 2H1a1 1 0 0 1-1-1z' clip-rule='evenodd'/%3e%3c/svg%3e");
    }
        .accordion-button{ font-size: 1.75rem;}
        /* Accordion CSS style */
</style>


<div class="app-content content" style="margin: 0px !important;">
	<div class="content-wrapper" style="margin-top: 0px !important;">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Batch Report</h2>
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
								<form action="{{ route('batch-reports') }}" method="get" name="filtersubmit">
									<div class="row">
										
										<div class="col-12 col-sm-6 col-lg-3 branch_loader">
											<label for="users-list-status">Location</label>
											<fieldset class="form-group">												
												<select class="form-control branch_location" name="branch_location" id="">
													<option value="">Select Any</option>
													<option value="jaipur" @if('jaipur' == app('request')->input('branch_location')) selected="selected" @endif>jaipur</option>
													<option value="jodhpur" @if('jodhpur' == app('request')->input('branch_location')) selected="selected" @endif>jodhpur</option>
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
										
										<div class="col-12 col-sm-6 col-lg-2">
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
											<label for="users-list-status">Batch</label>
											<?php $batchs = \App\Batch::where('status', '1')->where('is_deleted', '0')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2 batch_id" name="batch_id">
													<option value="">Select Any</option>
													@if(count($batchs) > 0)
													@foreach($batchs as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('batch_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										
										<div class="col-12 col-md-3">
											<label for="users-list-status">From Date</label>
											<fieldset class="form-group">												
												<input type="date" name="fdate" placeholder="Date" value="{{ app('request')->input('fdate') }}" class="form-control StartDateClass fdate">
											</fieldset>
										</div>
										
										<div class="col-12 col-md-3">
											<label for="users-list-status">To Date</label>
											<fieldset class="form-group">												
												<input type="date" name="tdate" placeholder="Date" value="{{ app('request')->input('tdate') }}" class="form-control EndDateClass tdate">
											</fieldset>
										</div>
										
										
										<div class="col-12 col-md-4 mt-4">
										<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('batch-reports') }}" class="btn btn-warning">Reset</a>
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
				if (count($get_batches) > 0) {
					
					foreach ($get_batches as $batchArray) {
						//if(count($batchArray->batch_timetables->studio) > 0){ //echo '<pre>'; print_r($batchArray->studio);
							$dataFound++;
						?>
						<table class="table data-list-view" style=''>
							<head>
								<tr style="">
									<th colspan="3"><h3>Batch Name : <?php echo $batchArray->name; ?> </h3></th>
								</tr>
							</head>
							<body>
							<tr style="">
							<td style="border: 1px solid;">
						
						<table class="table data-list-view" style=''>
						 
							<head>
								<tr style="">
									<th scope="col">Branch Name</th>
									<th scope="col">Studio Name</th>
									<th scope="col">Assistant Name</th>
									<th scope="col">Start Time</th>
									<th scope="col">End Time</th>
									<th scope="col">Date</th>
									<th scope="col">Faculty Name</th>
									
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
								foreach ($batchArray->batch_timetables as $value) {   //echo '<pre>'; print_r($value->studio->name);die;
								?>
								<?php
								
								$schedule_duration  = "00 : 00 Hours"; 	
								$from_time         = new DateTime($value->from_time);
								$to_time           = new DateTime($value->to_time);
								$schedule_interval = $from_time->diff($to_time);
								$schedule_duration = $schedule_interval->format('%H : %I Hours');
								if(!empty($value->studio) && !empty($value->studio->branch->name)){
								?>
									<tr>
										<td><?php echo isset($value->studio->branch->name) ?  $value->studio->branch->name : '' ?></td>
										<td><?php echo isset($value->studio->name) ?  $value->studio->name : '' ?></td>
										<td><?php echo isset($value->assistant->name) ?  $value->assistant->name : '' ?></td>
										<td><?php echo isset($value->from_time) ?  date('h:i A', strtotime($value->from_time)) : '' ?></td>
										<td><?php echo isset($value->to_time) ?  date('h:i A', strtotime($value->to_time)) : '' ?></td>
										<td><?php echo isset($value->cdate) ?  date('d-m-Y',strtotime($value->cdate)) : '' ?></td>
										<td><?php echo isset($value->faculty->name) ?  $value->faculty->name : '' ?></td>
										
										<td><?php echo isset($value->course->name) ?  $value->course->name : '' ?></td>
										<td><?php echo isset($value->subject->name) ?  $value->subject->name : '' ?></td>
										<td><?php echo isset($value->chapter->name) ?  $value->chapter->name : '' ?></td>
										<td><?php echo isset($value->topic->name) ?  $value->topic->name : '' ?></td>
										<td><?php echo isset($value->online_class_type) ?  $value->online_class_type : '' ?></td>
										<td><?php echo $schedule_duration ?></td>
									</tr>
									<?php } } ?>
							</body>
						
						</table>
				<p><hr/></p>
						
				</td>
				</tr>
				</body>
				</table>
				<?php
					//}
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
		data.batch_id = $('.batch_id').val(),
		data.assistant_id = $('.assistant_id').val(),
		data.fdate = $('.fdate').val(),
		data.tdate = $('.tdate').val(),
		data.type = $('.type').val(),
		window.location.href = "batch-report-report-excel?" + Object.keys(data).map(function (k) {
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
			data.batch_id = $('.batch_id').val(),
			data.assistant_id = $('.assistant_id').val(),
			data.fdate = $('.fdate').val(),
			data.tdate = $('.tdate').val(),
			data.type = $('.type').val(),
			// data.tdate = $('.tdate').val(),
			window.open("batch-report-pdf?" + Object.keys(data).map(function (k) {
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
				url : '{{ route('get-branchwise-studio') }}',
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
				url : '{{ route('get-branchwise-assistant') }}',
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
				url : '{{ route('get-branchwise-assistant') }}',
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
				url : '{{ route('get-location-wise-branch') }}',
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
