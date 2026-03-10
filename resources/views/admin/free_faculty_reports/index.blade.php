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
						<h2 class="content-header-title float-left mb-0">Branch Wise Free Faculty Report</h2>
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
								<form action="{{ route('admin.free-faculty-reports') }}" method="get" name="filtersubmit">
									<div class="row">
									
										<div class="col-12 col-sm-6 col-lg-3 branch_loader">
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
										
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Faculty</label>
											<?php $faculty = \App\User::where('role_id', '2')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2 faculty_id" name="faculty_id">
													<option value="">Select Any</option>
													@if(count($faculty) > 0)
													@foreach($faculty as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('faculty_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										
										 <!--div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">From Date</label>
											<fieldset class="form-group">												
												<input type="date" name="fdate" placeholder="Date" value="{{ app('request')->input('fdate') }}" class="form-control StartDateClass fdate">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">To Date</label>
											<fieldset class="form-group">												
												<input type="date" name="tdate" placeholder="Date" value="{{ app('request')->input('tdate') }}" class="form-control EndDateClass tdate">
											</fieldset>
										</div-->
										
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Date</label>
											<fieldset class="form-group">												
												<input type="date" name="fdate" placeholder="Date" value="{{ app('request')->input('fdate') }}" class="form-control StartDateClass fdate">
											</fieldset>
										</div>
									</div>
									<fieldset class="form-group" style="float:right;">		
									<button type="submit" class="btn btn-primary">Search</button>
									<a href="{{ route('admin.free-faculty-reports') }}" class="btn btn-warning">Reset</a>
									<a href="javascript:void(0)" id="download_free_faculty_excel" class="btn btn-primary">Export in Excel</a>
									</fieldset>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
				<table class="table data-list-view">
					<thead>
						<tr>
							<th>S.No.</th>
							<th>Faculty</th>
							<th>Email</th>
							<th>Mobile No</th>
							<th class="text-center">Free Time</th>
						</tr>
					</thead>
					<tbody>
				<?php 
				$dataFound = 1;
				if (count($faculty_record) > 0) { 
					foreach ($faculty_record as $faculty_record_val) { //echo '<pre>'; print_r($faculty_record_val); die;
						$check_timetable = DB::table('timetables');
						
						if(!empty($fdate)){
							$check_timetable->where('cdate', $fdate);
						}
						else{
							$check_timetable->where('cdate', date('Y-m-d'));
						}
						$check_timetable = $check_timetable->where('faculty_id', $faculty_record_val->id)->where('is_deleted', '0')->orderBy('from_time')->get();
								
						?>
							<tr style="">
								<td><?=$dataFound?></td>
								<!-- <td></td> -->
								<td>
								<?php 
								if(!empty($faculty_record_val->name)){
									echo $faculty_record_val->name; 
								} 
								?>
								</td>
								<td>
								<?php 
								if(!empty($faculty_record_val->email)){
									echo $faculty_record_val->email; 
								} 
								?>
								</td>
								<td>
								<?php 
								if(!empty($faculty_record_val->mobile)){
									echo $faculty_record_val->mobile; 
								} 
								?>
								</td>
								<td class="text-center" colspan="3">
									<table class="table" style="background: #fff;">
										<?php
										$f_time = '';$t_time = '';
										if(count($check_timetable) > 0){
											foreach($check_timetable as $key=>$check_timetable_value){ 
											if($key == 0 && date('H:i A', strtotime($check_timetable_value->from_time)) == '06:00 AM'){
												$f_time =  date('h:i A', strtotime($check_timetable_value->to_time));
												continue;	
											}
											elseif($key == 0){  
												$f_time = '06:00 AM'; 
											}
											$t_time = date('h:i A', strtotime($check_timetable_value->from_time));
											
										?>
											<tr>
												<td>{{$f_time}}</td>
												<td>{{$t_time}}</td>
											</tr>
										<?php
											$f_time =  date('h:i A', strtotime($check_timetable_value->to_time));
											} 
										?>
										<?php if($f_time != '11:00 PM'){ ?>
										<tr>
											<td>{{$f_time}}</td>
											<td>11:00 PM</td>
										</tr>
										<?php } ?>
										<?php
										}
										else{
										?>
										<tr>
											<td>06:00 AM</td>
											<td>11:00 PM</td>
										</tr>
										<?php
										}
										?>
									</table>
								</td>
							</tr>
						 
						<?php 
						$dataFound++; 
					} 
				}
				?>
				</body>
				</table>
					 
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
		$('.select-multiple3').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
	
	$("body").on("click", "#download_free_faculty_excel", function (e) {
		var data = {};
			data.branch_id = $('.branch_id').val(),
			data.faculty_id = $('.faculty_id').val(),
			data.fdate = $('.fdate').val(),
			data.tdate = $('.tdate').val(),
		window.location.href = "<?php echo URL::to('/admin/'); ?>/free-faculty-report-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});
</script>
<script type="text/javascript">
	$(".branch_id").on("change", function () {
		var branch_id = $(".branch_id option:selected").attr('value');
		var faculty_id = $("input[name=faculty_id]").val();
		if (branch_id) {
			
			$.ajax({
				beforeSend: function(){
					// $(".branch_loader i").show();
				},
				type : 'POST',
				url : '{{ route('admin.get-branchwise-faculty') }}',
				data : {'_token' : '{{ csrf_token() }}', 'branch_id': branch_id, 'faculty_id': faculty_id},
				dataType : 'html',
				success : function (data){
					// $(".branch_loader i").hide();
					$('.faculty_id').empty();
					$('.faculty_id').append(data);
				}
			});
			
			
		}
	});
	

</script>
@endsection
