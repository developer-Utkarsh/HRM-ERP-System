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
						<h2 class="content-header-title float-left mb-0">Final Attendance</h2>
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
				<div class="card-content">
					<div class="card-body">
						<form class="form" action="{{ route('admin.store-final-attendence') }}" method="post" enctype="multipart/form-data">
							@csrf
							<div class="form-body">
								<div class="row">
									
									<div class="col-md-4 col-12">
										<div class="form-group">
											<label for="first-name-column">Import File</label>
											<input type="file" class="form-control" name="import_file">
											@if($errors->has('import_file'))
											<span class="text-danger">{{ $errors->first('import_file') }}</span>
											@endif
											<br>
											<a href="{{asset('laravel/public/attendance-sample.xlsx')}}" class="download_sample">Download Sample</a>
										</div>
									</div> 
									<div class="col-md-4">
										<label for="users-list-role">Month</label>
										<fieldset class="form-group">
											<input type="month" class="form-control year_wise_month" name="year_wise_month" value="{{ old('year_wise_month') }}">
											@if($errors->has('year_wise_month'))
											<span class="text-danger">{{ $errors->first('year_wise_month') }}</span>
											@endif
										</fieldset>
									</div>
										
									<div class="col-md-4 col-12 mt-2">
										<button type="submit" class="btn btn-primary mr-1 mb-1">Submit</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
						
						
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ url('admin/final-attendence') }}" method="get" name="filtersubmit">
								<input type="hidden" name="_token" value="{{ csrf_token() }}" />
									<div class="row">
										<div class="col-md-4">
											<label for="users-list-role">Employee Name</label>
											<fieldset class="form-group">
												<input type="text" class="form-control name" id="se_name" name="name" placeholder="Name, EMP Code" value="@if(!empty(app('request')->input('name'))){{app('request')->input('name')}}@endif">
											</fieldset>
										</div>
										<div class="col-md-4">
											<label for="users-list-role">Month</label>
											<fieldset class="form-group">
												<input type="month" class="form-control" id="se_year_wise_month" name="se_year_wise_month" value="@if(!empty(Request::get('se_year_wise_month'))){{ Request::get('se_year_wise_month') }}@else{{ date('Y-m') }}@endif">
												@if($errors->has('se_year_wise_month'))
												<span class="text-danger">{{ $errors->first('se_year_wise_month') }}</span>
												@endif
											</fieldset>
										</div>
										<div class="col-md-4 mt-2">
											<fieldset class="form-group">		
												<button type="submit" class="btn btn-primary search_click">Search</button>
												<a href="<?php echo URL::to('admin/final-attendence'); ?>" class="btn btn-warning">Reset</a>
											</fieldset>
										</div>
									</div>
									
								</form>
							</div>
						</div>
					</div>
				</div>
				
				<div class="table-responsive">
					<table class="table data-list-view" style="background:#fff;width:100%" id="finalAttendanceTable">
						<thead style="text-align: ;">
							<tr>
								<th>S. No.</th>
								<th>Emp Code</th>
								<th>Salary</th>
								<?php
								$i = 1;
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
<script>

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
        var finalAttendanceTable = $('#finalAttendanceTable').DataTable({
			"searching": false, 
			"info": false,
			"ordering": false,
			"lengthChange": false,
			"pageLength": 10000,
            "processing": true,
            "serverSide": true,
            "ajax":{
		     "url": "{{ route('admin.attendance.final-attendence-detail') }}",
		     "dataType": "json",
		     "type": "post",
			 "data": function(data){ 
				 Object.assign(data, $('[name="filtersubmit"]').serializeObject()); console.log(data);
				 return data;
			 },
		    },
			preDrawCallback: function(settings) {
				if ($.fn.DataTable.isDataTable('#finalAttendanceTable')) {
					var dt = $('#finalAttendanceTable').DataTable();

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
			},
	    	"columns": [
				  { "data": null, orderable: false, render: function(data, type, row, meta){
					  return meta.row + meta.settings._iDisplayStart + 1;
				  } },
				  { "data": "emp_code" },
				  { "data": "salary" },
		          { "data": "d_1" },
		          { "data": "d_2" },
		          { "data": "d_3" },
		          { "data": "d_4" },
		          { "data": "d_5" },
		          { "data": "d_6" },
		          { "data": "d_7" },
		          { "data": "d_8" },
		          { "data": "d_9" },
		          { "data": "d_10" },
		          { "data": "d_11" },
		          { "data": "d_12" },
		          { "data": "d_13" },
		          { "data": "d_14" },
		          { "data": "d_15" },
		          { "data": "d_16" },
		          { "data": "d_17" },
		          { "data": "d_18" },
		          { "data": "d_19" },
		          { "data": "d_20" },
		          { "data": "d_21" },
		          { "data": "d_22" },
		          { "data": "d_23" },
		          { "data": "d_24" },
		          { "data": "d_25" },
		          { "data": "d_26" },
		          { "data": "d_27" },
		          { "data": "d_28" },
				  <?php if($setDataFOrJs > '28'){ ?>
					{ "data": "d_29" },
					{ "data": "d_30" },
					<?php if($setDataFOrJs == '31'){ ?>
					{ "data": "d_31" }
					<?php } ?>
				 <?php } ?>
		         
		       ]	 

	    });
		$("body").on("input change","#se_name",function(e){
			e.preventDefault();
			finalAttendanceTable.ajax.reload();
		});
		// $("body").on("input change","#se_year_wise_month",function(e){
			// e.preventDefault();
			// finalAttendanceTable.ajax.reload();
		// });
		
    });
</script>
@endsection
