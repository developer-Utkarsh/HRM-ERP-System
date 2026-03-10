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
						<h2 class="content-header-title float-left mb-0">Attendance Records</h2>
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
								<form action="{{ url('admin/attendence-record') }}" method="get" name="filtersubmit">
								<input type="hidden" name="_token" value="{{ csrf_token() }}" />
									<div class="row">
									    @if(Auth::user()->role_id != 20)
										<div class="col-md-3">
											<label for="users-list-role">Employee Name</label>
											<fieldset class="form-group">
												<input type="text" class="form-control name" id="se_name" name="name" placeholder="Name, EMP Code" value="@if(!empty(app('request')->input('name'))){{app('request')->input('name')}}@endif">
											</fieldset>
										</div>
										@endif
										@if(Auth::user()->role_id == 24 || Auth::user()->role_id == 29)
										<div class="col-md-3">
											<label for="users-list-status">Location</label>											
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 branch_location" name="branch_location" onchange="locationBranch(this.value);">
													<option value="">Select Any</option>													
													<option value="jodhpur" @if(!empty(app('request')->input('branch_location')) && 'jodhpur' == app('request')->input('branch_location')) selected="selected" @endif>Jodhpur</option>
													<option value="jaipur" @if(!empty(app('request')->input('branch_location')) && 'jaipur' == app('request')->input('branch_location')) selected="selected" @endif>Jaipur</option>
													<option value="delhi" @if(!empty(app('request')->input('branch_location')) && 'delhi' == app('request')->input('branch_location')) selected="selected" @endif>Delhi</option>
													<option value="prayagraj" @if(!empty(app('request')->input('branch_location')) && 'prayagraj' == app('request')->input('branch_location')) selected="selected" @endif>Prayagraj</option>
													<option value="indore" @if(!empty(app('request')->input('branch_location')) && 'indore' == app('request')->input('branch_location')) selected="selected" @endif>Indore</option>
												</select>												
											</fieldset>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label for="first-name-column">Branch</label>
												@if(count($allBranches) > 0)
												<select class="form-control get_role select-multiple1 branch_id"  id="se_branch_id" name="branch_id">
													<option value=""> - Select Any - </option>
													@foreach($allBranches as $value)
													<option value="{{ $value['id'] }}" @if($value['id'] == app('request')->input('branch_id')) selected="selected" @endif>{{ $value['name'] }}</option>
													@endforeach
												</select>
												@endif
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label for="first-name-column">Department Type</label>
												@if(count($allDepartmentTypes) > 0)
												<select class="form-control get_role select-multiple1 department_type" id="se_department_type" name="department_type">
													<option value=""> - Select Any - </option>
													@foreach($allDepartmentTypes as $value)
													<option value="{{ $value['id'] }}" @if($value['id'] == app('request')->input('department_type')) selected="selected" @endif>{{ $value['name'] }}</option>
													@endforeach
												</select>
												@endif
											</div>
										</div>
										
										@endif
										
										<!--div class="col-md-2">
											<label for="users-list-role">Month</label>
											<fieldset class="form-group">
												<input type="month" class="form-control year_wise_month" name="year_wise_month" value="@if(!empty(Request::get('year_wise_month'))){{ Request::get('year_wise_month') }}@else{{ date('Y-m') }}@endif">
											</fieldset>
										</div-->
										
										<div class="col-md-2">
											<label for="users-list-role">Start Date</label>
											<fieldset class="form-group">
												<input type="date" class="form-control start_date" name="start_date" value="@if(!empty(Request::get('start_date'))){{ Request::get('start_date') }}@else{{ $start_date }}@endif">
											</fieldset>
										</div>
										
										<div class="col-md-2">
											<label for="users-list-role">End Date</label>
											<fieldset class="form-group">
												<input type="date" class="form-control end_date" name="end_date" value="@if(!empty(Request::get('end_date'))){{ Request::get('end_date') }}@else{{ $end_date }}@endif">
											</fieldset>
										</div>
										
										<div class="col-md-2">
											<div class="form-group">
												<label for="first-name-column">Status</label>
												<select class="form-control get_role status" id="se_status" name="status">
													<option value="">--Select--</option>
													<option value="1" @if('1' == app('request')->input('status')) selected="selected" @endif>Active</option>
													<option value="0" @if('0' == app('request')->input('status')) selected="selected" @endif>Inactive</option>
												</select> 
											</div>
										</div>
										<!--div class="col-md-2">
											<label for="users-list-verified">From</label>
											<fieldset class="form-group">
												<input type="date" name="fdate" class="form-control StartDateClass fdate" id="se_fdate" value="{{ app('request')->input('fdate') }}" id="">
											</fieldset>
										</div>
										<div class="col-md-2">
											<label for="users-list-verified">To</label>
											<fieldset class="form-group">
												<input type="date" name="tdate" class="form-control EndDateClass tdate" id="se_tdate" value="{{ app('request')->input('tdate') }}" id="">
											</fieldset>
										</div-->
									</div>
									<fieldset class="form-group" style="float:right;">		
									<button type="submit" class="btn btn-primary search_click">Search</button>
								
									<a href="<?php echo URL::to('/admin/attendence-record'); ?>" class="btn btn-warning">Reset</a>
									<a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a>
									</fieldset>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form method="get" name="filtersubmit">
									<div class="row">
										<div class="col-md-12">
											<strong for="users-list-role">PH : Half day Present , &nbsp;&nbsp;</strong>
											<strong for="users-list-role">P : Present, &nbsp;&nbsp;</strong>
											<strong for="users-list-role">WO : Week Off, &nbsp;&nbsp;</strong>
											<strong for="users-list-role">A : Absent, &nbsp;&nbsp;</strong>
											<strong for="users-list-role">H : Holiday, &nbsp;&nbsp;</strong>
											<strong for="users-list-role">HW : Holiday Working,&nbsp;&nbsp;</strong>
											<strong for="users-list-role">HW/2 : Holiday Working Half,&nbsp;&nbsp;</strong>
											<strong for="users-list-role">PL : Privilege leaves, &nbsp;&nbsp;</strong>
											<strong for="users-list-role">CL : Casual Leave, &nbsp;&nbsp;</strong>
											<strong for="users-list-role">LWP : Leave without pay &nbsp;&nbsp;</strong>
											<strong for="users-list-role">CO : Compensatory Off, &nbsp;&nbsp;</strong>
											 
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
								<th>S. No.</th>
								<th>Emp Name</th>
								<th>Emp Code</th>
								<!--th>Branch</th>
								<th>Department Type</th>
								<th>Designation</th-->
								<!--th>Contact No</th-->
								<?php
								for($i = $start_from; $i <= $end_to; $i->modify('+1 day')){
									?>
									<th><?=$i->format("d-M");?></th>
									<?php
								} 
								
								$setDataFOrJs = $getWorkSunday;
								/* $i = 1;
								//$getWorkSunday = 30;
								
								while($getWorkSunday > 0)
								{
									$ii = $i++;
									?>
									<th><?=$ii;?></th>
									<?php
									$getWorkSunday--;
								} */
								?>
								<!--th>Total Present Half</th-->
								<th>Actual Present</th>
								<th>Additional Days</th>
								<th>Week Off + Holiday </th>
								<th>Holiday Working</th>
								<th>Total Approved Leaves</th>
								<!--th>Holiday leave balance</th-->
								<th>Actual Paid</th>
								<th>Absent</th>
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
function locationBranch(value){
	$.ajax({
		type : 'POST',
		url : '{{ route('admin.attendance.get-branch') }}',
		data : {'_token' : '{{ csrf_token() }}', 'branch_id': value},
		dataType : 'html',
		success : function (data){
			$('.branch_id').empty();
			$('.branch_id').append(data);
		}
	});
}


$(document).ready(function() {
	$('.select-multiple1').select2({
		placeholder: "Select Any",
		allowClear: true
	});
	$('.select-multiple2').select2({
		placeholder: "Select Any",
		allowClear: true
	});
	
	$('#example').DataTable();
});

$("body").on("click", "#download_excel", function (e) {

	var data = {};
		<?php if(Auth::user()->role_id != 20){ ?>
			data.name = $('.name').val(),
		<?php } ?>
		
		<?php if(Auth::user()->role_id == 24 || Auth::user()->role_id == 29){ ?>
			data.branch_id = $('.branch_id').val(),
			data.department_type = $('.department_type').val(),
		<?php } ?>	
		data.year_wise_month = '',
		data.status = $('.status').val(),
		data.start_date = $('.start_date').val(),
		data.end_date = $('.end_date').val(),
	window.location.href = "<?php echo URL::to('/admin/'); ?>/attendence-record-report-excel?" + Object.keys(data).map(function (k) {
		return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
	}).join('&');
});
</script>

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
        var attendanceTable = $('#attendanceTable').DataTable({
			"searching": false, 
			"info": false,
			"ordering": false,
			"lengthChange": false,
			"pageLength": 10000,
            "processing": true,
            "serverSide": true,
            "ajax":{
		     "url": "{{ route('admin.attendance.attendence-record-detail') }}",
		     "dataType": "json",
		     "type": "post",
			 "data": function(data){
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
		          /*{ "data": null, orderable: false, render: function(data, type, row, meta){
					  return meta.row + meta.settings._iDisplayStart + 1;
				  } },*/
		          { "data": "user_count" },
		          { "data": "name" },
		          // { "data": "id" },
		          { "data": "register_id" },
				  // { "data": "branch_name" },
				  // { "data": "departments_name" },
		          // { "data": "designation_name" },
		          // { "data": "mobile" },
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
		          <!--{ "data": "total_present_half" },-->
		          { "data": "total_present" },
		          { "data": "additional_days" },
		          { "data": "total_week_off" },
		          { "data": "total_holiday_working" },
		          { "data": "total_approved_leaves" },
		          <!--{ "data": "leave_balance" },-->
				  { "data": "actual_paid" },
				  { "data": "total_absent" },
		       ]	 

	    });
		// attendanceTable.column(33).visible(false);
		$("body").on("input change","#se_name",function(e){
			e.preventDefault();
			attendanceTable.ajax.reload();
		});
		
		/* $("body").on("click",".search_click",function(e){
			e.preventDefault();
			attendanceTable.ajax.reload();
		}); */
		
    });

function myFunctionSearch() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInputSearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("attendanceTable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
} 
</script>
@endsection
