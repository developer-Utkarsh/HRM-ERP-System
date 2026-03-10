@extends('layouts.admin')
@section('content')
<?php //echo '<pre>'; print_r('http://'.request()->getHttpHost().'/');die;?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-md-6">
						<h2 class="content-header-title float-left mb-0">Salary</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-md-3">
						<a href="{{ route('admin.add-increment') }}" class="btn btn-primary float-right">Add Increment/Arrears</a>
					</div>
					<div class="col-md-3">
						<a href="{{ route('admin.add-deduction') }}" class="btn btn-primary float-right">Advance Deduction</a>
					</div>
				</div>
			</div>
		</div>
		
		@php
			$employee_result = \App\User::where('status', '1')->where('is_deleted', '0')->get();
		@endphp
		<div class="content-body" style="display:none;">
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								
								<form class="form" action="{{ route('admin.salary.salary-store-increment') }}" method="post" enctype="multipart/form-data">
									@csrf
									
									<div class="form-body">
										<div class="row">
											<div class="col-md-3 col-12">
												<div class="form-group">
													<label for="first-name-column">Employee</label>
													<select class="form-control select-multiple" name="emp_id">
														<option value="">Select Employee</option>
														@if(count($employee_result) > 0)
														@foreach($employee_result as $employee_result_val)
														<option value="{{$employee_result_val->id}}">{{$employee_result_val->name}}</option>
														@endforeach
														@endif
													</select>
													@if($errors->has('emp_id'))
													<span class="text-danger">{{ $errors->first('emp_id') }}</span>
													@endif
													<br>
												</div>
											</div> 

											<div class="col-md-3 col-12">
												<div class="form-group">
													<label for="first-name-column">salary</label>
													<input type="number" class="form-control" name="salary" placeholder="Enter Salary">
													@if($errors->has('salary'))
													<span class="text-danger">{{ $errors->first('salary') }}</span>
													@endif
													<br>
												</div>
											</div> 
											
											<div class="col-md-3">
												<div class="form-group">
													<label for="first-name-column">Month</label>
													<input type="month" class="form-control year_wise_month" name="year_wise_month" value="@if(!empty(Request::get('year_wise_month'))){{ Request::get('year_wise_month') }}@else{{ date('Y-m') }}@endif">
													@if($errors->has('year_wise_month'))
													<span class="text-danger">{{ $errors->first('year_wise_month') }}</span>
													@endif
													<br>
												</div>
											</div>

											<div class="col-md-3 col-12 mt-2">
												<button type="submit" class="btn btn-primary mr-1 mb-1">Submit</button>
											</div>
										</div>
									</div>
								</form>
				
							</div>
						</div>
					</div>
				</div>
				
				  		
			</section>
		</div>
		
		<div class="content-body">
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ route('admin.salary.index') }}" method="get" name="filtersubmit">
									<div class="row">
									    <div class="col-md-3">
											<label for="users-list-role"></label>
											 <input type="text" class="form-control" placeholder="Search By Name,Email,Contact No" name="search" id="search" value="@if(!empty(app('request')->input('search'))){{app('request')->input('search')}}@endif">
										</div>
										<?php
										// $search_year_month = date('Y-m', strtotime('-1 month', time()));
										$search_year_month = date('Y-m');
										if(!empty(Request::get('year_wise_month'))){ 
											$search_year_month = Request::get('year_wise_month');
										}
										?>
										<div class="col-md-3">
											<label for="users-list-role">Month</label>
											<fieldset class="form-group">
												<input type="month" class="form-control year_wise_month" id="year_wise_month" name="year_wise_month" value="{{$search_year_month}}" max="{{date('Y-m')}}">
											</fieldset>
										</div>
										
										
										<div class="col-md-6">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('admin.salary.index') }}" class="btn btn-warning">Reset</a>
											<a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a>
											</fieldset>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				
				@if(count($get_emp) > 0)
					@php
						if(!empty(Request::get('year_wise_month'))){
							$yr = substr(Request::get('year_wise_month'),0,4);
							$mt = substr(Request::get('year_wise_month'),5,2);
						}
						else{
							$mt = date("m",strtotime("-1 month"));
							$yr = date('Y');
						}
						
						
						
						if(!empty(Request::get('year_wise_month'))){
							$first_day_month = date('Y-m-01', strtotime(Request::get('year_wise_month').'-01'));
							$last_day_month  = date('Y-m-t', strtotime(Request::get('year_wise_month').'-01'));
						}
						else{
							$first_day_month = date('Y-m-01',strtotime("-1 month"));
							$last_day_month  = date('Y-m-t',strtotime("-1 month"));
						}
						
						if(!empty(Request::get('year_wise_month'))){	
							$mySunTime = strtotime(Request::get('year_wise_month').'-01');
						}
						else{
							$mySunTime = strtotime(date("Y-m-d")); 
						}
							
						$getSunday = cal_days_in_month(CAL_GREGORIAN, $mt, $yr);
						$t_sunday  = 0;
						$t_workday = 0;

						while($getSunday > 0)
						{
							$day = date("D", $mySunTime); 
							if($day == "Sun"){
								$t_sunday++;
							}
							elseif($day != "Sun"){
								$t_workday++;
							}	

							$getSunday--;
							$mySunTime += 86400; 
						}
					
					@endphp
				
				<form id="submit_adjusment">
				<input type="hidden" name="search_year_month" value="{{$search_year_month}}">
				<div class="row">
					<div class="col-md-6">
						<h5>Total working days of this month : <span>{{ $t_workday }}</span></h5>
					</div>
					
					<!--div class="col-md-3">
						<p class="text-danger please_wait_adjustment"></p>
						<button type="submit" class="btn btn-primary btn_sub_adjusment">Save Adjustment</button>
					</div-->
				</div>
				<div class="table-responsive">
					<table class="table data-list-view"  id="salaryTable">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Emp Code</th>
								<th>EMP Name</th>
								<th>ESIC</th>
								<th>PF</th>
								<th>New Basic</th>
								<th>Old Basic</th>
								<th>Increment Amount</th>
								<th>Paid Days</th>
								<th>Extra Days</th>
								<th>Arrier Days</th>
								<th>Gross Basic</th>
								<th>Extra Days Amount</th>
								<th>Arrier Amount</th>
								<th>Total Gross Salary</th>
								<th>Net Amount</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
				</form>					
				@endif	
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
	
	$("body").on("click", "#download_excel", function (e) { 
		var data = {};
			data.year_wise_month = $('.year_wise_month').val()
			
		window.location.href = "<?php echo URL::to('/admin/'); ?>/salary-report-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});
	
	
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
        var salaryTable = $('#salaryTable').DataTable({
			"searching": false, 
			"info": false,
			"ordering": false,
			"lengthChange": false,
			"pageLength": 10000,
            "processing": true,
            "serverSide": true,
            "ajax":{
		     "url": "{{ route('admin.salary.salary-record-detail') }}",
		     "dataType": "json",
		     "type": "GET",
			 "data": function(data){ 
				 Object.assign(data, $('[name="filtersubmit"]').serializeObject());
				 return data;
			 },
		    },
			preDrawCallback: function(settings) {
				if ($.fn.DataTable.isDataTable('#salaryTable')) {
					var dt = $('#salaryTable').DataTable();

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
				 
		          { "data": "register_id" },
		          { "data": "name" },
				  { "data": "is_esi" },
				  { "data": "is_pf" },
				  { "data": "new_basic" },
				  { "data": "old_salary" },
				  { "data": "increment_amount" },
				  { "data": "paid_day" },
				  { "data": "total_holiday_working" },
				  { "data": "last_month_pending_sunday" },
				  { "data": "gross_salary" },
				  { "data": "incentive" },
				  { "data": "arrear" },
				  { "data": null, render:function(data){
					return (parseFloat(data.gross_salary) + parseFloat(data.incentive) + parseFloat(data.arrear)).toFixed(2);
				  } },
				  { "data": "final_amount" },
		        //   { "data": null, render:function(data){ 
				// 	var actionHtml ='';
				// 		actionHtml='<input type="number" name="adjustment_amount['+data.user_id+']" value="'+data.adjustment_amount+'" style="width: 100px;">';
				// 		return actionHtml;
				// 	} 
				//   },
		          /* { "data": "total_present_half" },
		          { "data": "total_present" },
		          { "data": "total_absent" },
		          { "data": "total_holiday_working" },
		          { "data": "total_week_off" } */
				  { "data": null, render:function(data){ 
						var actionHtml ='';			
							actionHtml = '<a href="javascript:void(0)" id="download_pdf" data-id="'+data.register_id+'" data-year_wise_month="{{$search_year_month}}" class=""><span class="action-edit"><i class="feather icon-file-text"></i></span></a>';
					  return actionHtml; 
				  } },
		       ]				   

	    });
		
		$("body").on("click", "#download_pdf", function (e) { 
			var data = {};
				data.register_id = $(this).attr('data-id'),
				data.year_wise_month = $(this).attr('data-year_wise_month'),
				window.open("<?php echo URL::to('/admin/'); ?>/salary-slip-report-pdf?" + Object.keys(data).map(function (k) {
				return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
			}).join('&'));
		});
		
		$("body").on("input change","#search",function(e){
			e.preventDefault();
			salaryTable.ajax.reload();
		});
		
		/* $("body").on("click",".year_wise_month",function(e){
			e.preventDefault();
			salaryTable.ajax.reload();
		}); */
		
    });
	
	$("body").on("click", "#download_excel", function (e) {
		var data = {};
			data.search    = $('#search').val(),
			data.year_wise_month = $('#year_wise_month').val(),
		window.location.href = "<?php echo URL::to('/admin/'); ?>/employee-salary-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});
	
	
	$("#submit_adjusment").submit(function(e) { 
	$('.btn_sub_adjusment').hide();
	$('.please_wait_adjustment').text('Please Wait..');
	var form = document.getElementById('submit_adjusment');
	var dataForm = new FormData(form); 
	e.preventDefault();
	
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},      
			type: "POST",
			url : "{{route('admin.submit-salary-adjusment')}}",
			data : dataForm,
			processData : false, 
			contentType : false,
			dataType : 'json',
			success : function(data){
				
				if(data.status){
					swal({
					  title: "Success!",
					  text: data.msg,
					  html: true,
					 type: "success",
					});
				}
				else{
					swal({
					  title: "Error!",
					  text: data.msg,
					  html: true,
					 type: "error",
					});
				}
				
				$('.btn_sub_adjusment').show();
				$('.please_wait_adjustment').text('');
				
			}
		}); 
	    
});

</script>



@endsection
