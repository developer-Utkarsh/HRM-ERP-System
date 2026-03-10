@extends('layouts.admin')
@section('content')
<!--style>
#attendanceTable tbody tr td:nth-child(5){display:none !important;}
</style-->
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Student Attendance Records</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<?php if(Auth::user()->role_id != 32){ ?>
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
								<?php } ?>
								
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
								<form action="{{ url('admin/student-attendence-record') }}" method="get" name="filtersubmit">
									<input type="hidden" name="_token" value="{{ csrf_token() }}" />
									<div class="row">
										<div class="col-md-3">
											<label for="users-list-role">Reg. No.</label>
											<fieldset class="form-group">
												<input type="text" class="form-control name" id="se_name" name="name" placeholder="Reg. No." value="@if(!empty(app('request')->input('name'))){{app('request')->input('name')}}@endif">
											</fieldset>
										</div>
										<div class="col-md-3">
											<label for="users-list-role">Month</label>
											<fieldset class="form-group">
												<input type="month" class="form-control year_wise_month" name="year_wise_month" value="@if(!empty(Request::get('year_wise_month'))){{ Request::get('year_wise_month') }}@else{{ date('Y-m') }}@endif">
											</fieldset>
										</div>
										<?php if(Auth::user()->role_id != 32){ ?>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Location</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple branch_location" name="branch_location" onchange="locationBranch(this.value);">
													@php $branch_location = ['Jodhpur', 'Jaipur','Prayagraj','Indore','Delhi']; @endphp
													<option value="">Select Any</option>
													@foreach($branch_location as $key => $value)
													<option value="{{ $value }}" @if($value == app('request')->input('branch_location')) selected="selected" @endif>{{ $value }}</option>
													@endforeach
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Branch</label>
											<?php
											$branch_location = app('request')->input('branch_location');
											$branches = \App\Branch::where('status', '1'); 
											if(!empty($branch_location)){
												$branches->where('branch_location', $branch_location);
											}
											$branches = $branches->orderBy('id','desc')->get();											
											?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple branch_id" name="branch_id" onchange="locationBatch(this.value);">
													<option value="">Select Any</option>
													@foreach($branches as $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('branch_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Batch</label>
											<?php
											$ttdate=date('Y-m-d',strtotime(date('Y-m-d').' -30 day'));
											$batch = DB::table('batch')
												->select('batch.id','batch.name','batch.batch_code','tt.branch_id')
												->leftjoin('timetables as tt','tt.batch_id','batch.id')
												->where('batch.status', '1')
												->where('tt.is_deleted', '0')
												->where('tt.is_publish', '1')
												->where('tt.is_cancel', 0)
												->where('tt.cdate','>',$ttdate)
												->where('batch.batch_code','!=',0);
												if(!empty(app('request')->input('branch_id'))){
													$batch->where('tt.branch_id',app('request')->input('branch_id'));
												}
											$batch= $batch->groupby('batch.batch_code')->get();
											?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple batch_id" name="batch_id">
													<option value="">Select Any</option>
													@foreach($batch as $value)
													<option value="{{ $value->batch_code }}" @if($value->batch_code == app('request')->input('batch_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
												</select>												
											</fieldset>
										</div>
										
										
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Anuprati</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple anuprati_id" name="anuprati_id" onchange="locationBranch(this.value);">
													@php $anuprati = ['Yes']; @endphp
													<option value="">Select Any</option>
													@foreach($anuprati as $key => $value)
													<option value="{{ $value }}" @if($value == app('request')->input('anuprati_id')) selected="selected" @endif>{{ $value }}</option>
													@endforeach
												</select>												
											</fieldset>
										</div>	
										<?php } ?>
										<div class="col-12 col-sm-6 col-lg-6 pt-2">
											<fieldset class="form-group" style="float:left;">		
												<button type="submit" class="btn btn-primary search_click">Search</button>
											
												<a href="<?php echo URL::to('/admin/student-attendence-record'); ?>" class="btn btn-warning">Reset</a>
												
												<?php if(Auth::user()->role_id != 32){ ?>
												<a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a>
												<?php } ?>
											</fieldset>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				
				
				
				
				<div class="table-responsive">
					<table class="table data-list-view" style="background:#fff;width:100%" id="attendanceTable">
						<thead style="text-align: ;">
							<tr>
								<th>Name</th>
								<th>Reg. No.</th>								
								<?php
								$i = 1;
								//$getWorkSunday = 30;
								$setDataFOrJs = $getWorkSunday;
								while($getWorkSunday > 0)
								{
									$ii = $i++;
									?>
									<th><?=$ii;?></th>
									<?php
									$getWorkSunday--;
								}
								?>
								<th>Total Present</th>
								<th>Total Absent</th>
							</tr>
						</thead>
						<tbody >
						
						</tbody>
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
		$('.select-multiple').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});

	function locationBranch(value){
		$("#overlay_loader").css('display','block');
		$.ajax({
			type : 'POST',
			url : '{{ route('admin.employee.get-branch') }}',
			data : {'_token' : '{{ csrf_token() }}', 'branch_id': value},
			dataType : 'html',
			success : function (data){
				$('.branch_id').empty();
				$('.branch_id').append(data);
				$("#overlay_loader").css('display','none');
			}
		});
	}
	
	function locationBatch(value){
		$("#overlay_loader").css('display','block');
		$.ajax({
			type : 'POST',
			url : '{{ route('admin.get-batch') }}',
			data : {'_token' : '{{ csrf_token() }}', 'branch_id': value},
			dataType : 'json',
			success : function (data){
				if(data.status){
					$('.batch_id').empty();
					$('.batch_id').append(data.batches);
					$('.allbatch_ids').val(data.allbatch_ids);
					$("#overlay_loader").css('display','none');
				}
			}
		});
	}
	
	
	$.fn.serializeObject = function() {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };
	
	$(document).ready(function () {
        var attendanceTable = $('#attendanceTable').DataTable({
			"searching": false, 
			"info": false,
			"ordering": false,
			"lengthChange": false,
			"pageLength": 10000,
            "processing": true,
            "serverSide": true,
            "ajax":{
				"url": "{{ route('admin.student-attendence-record-detail') }}",
				"dataType": "json",
				"type": "post",
				"data": function(data){
					console.log(data);
					Object.assign(data, $('[name="filtersubmit"]').serializeObject());
					return data;
				},
		    },
			preDrawCallback: function(settings) {
				if ($.fn.DataTable.isDataTable('#attendanceTable')) {
					var dt = $('#attendanceTable').DataTable();

					//Abort previous ajax request if it is still in process.
					var settings = dt.settings();
					if (settings[0].jqXHR) {
						settings[0].jqXHR.abort();
					}
				}
			},
			"createdRow": function(row, data, dataIndex){
				console.log(data.total_month_days);
				$('td:eq(3)', row).attr('colspan', 1);
				// $('td:eq(4)', row).remove();
			},
	    	"columns": [
		          { "data": "s_name" },
				  { "data": "s_regnumber" },
		          
					<?php
					$i = 1;
					while($setDataFOrJs > 0)
					{
						$ii = $i++;
						?>
						{ "data": <?=$ii?> },
						<?php
						$setDataFOrJs--;
					}
					?>
		          { "data": "total_present" },
				  { "data": "total_absent" }
		       ]	 

	    });
		// attendanceTable.column(33).visible(false);
		$("body").on("change","#se_name",function(e){
			e.preventDefault();
			attendanceTable.ajax.reload();
		});
	
    });

</script>
@endsection
