@extends('layouts.admin')
@section('content')
<?php //echo '<pre>'; print_r('http://'.request()->getHttpHost().'/');die;?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Salary</h2>
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
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ route('admin.salary.index') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-role">Month</label>
											<fieldset class="form-group">
												<input type="month" class="form-control year_wise_month" name="year_wise_month" value="@if(!empty(Request::get('year_wise_month'))){{ Request::get('year_wise_month') }}@else{{ date('Y-m', strtotime('-1 month', time())) }}@endif">
											</fieldset>
										</div>
										
										
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
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
						
						//echo '<pre>'; print_r($mt);die;	
						if(!empty(Request::get('year_wise_month'))){	
							$myTime = strtotime(Request::get('year_wise_month').'-01');
						}
						else{
							$myTime = strtotime(date("Y-m-d")); 
						}	
						
						$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $mt, $yr);
						$workDays = 0;

						while($daysInMonth > 0)
						{
							$day = date("D", $myTime); 
							if($day != "Sun")
								$workDays++;

							$daysInMonth--;
							$myTime += 86400; 
						}
						
						
						if(!empty(Request::get('year_wise_month'))){
							$first_day_month = date('Y-m-01', strtotime(Request::get('year_wise_month').'-01'));
							$last_day_month  = date('Y-m-t', strtotime(Request::get('year_wise_month').'-01'));
						}
						else{
							$first_day_month = date('Y-m-01',strtotime("-1 month"));
							$last_day_month  = date('Y-m-t',strtotime("-1 month"));
						}
						
						
					@endphp
				
				<h5>Total working days of this month : <span>{{ $workDays }}</span></h5>
				<div class="table-responsive">
					<table class="table data-list-view" id="TableSearch">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Name</th>
								<th>Mobile</th>
								<th>Woked Daysdd</th>
								<th>Salary</th>
							</tr>
						</thead>
						<tbody>
						
							@foreach($get_emp as  $key => $value)
							
							@php
							
							$per_day_salary = 0;
							if(!empty($value->user_details->net_salary)){
								$per_day_salary = $value->user_details->net_salary/30; 
							}
							
							$this_month_attds = DB::select("SELECT * FROM attendance WHERE MONTH(date) = '$mt' AND YEAR(date) = '$yr' AND emp_id = '".$value->id."' GROUP BY date");
							
							
							
							$count_this_month_attds = count($this_month_attds);
							@endphp
							<tr>
								<td>{{ $key + 1 }}</td>
								<td class="product-category">{{ $value->name ? $value->name : '' }}</td>
								<td class="product-category">{{ $value->mobile ? $value->mobile : '' }}</td>
								@if($count_this_month_attds > 0)
								<td class="product-category"><a href="{{ route('admin.attendance.index', ['name' => $value->name, 'fdate' => $first_day_month, 'tdate' => $last_day_month]) }}">{{ $count_this_month_attds }}</a></td>
								@else
								<td class="product-category"><a href="javascript:void(0)">{{ $count_this_month_attds }}</a></td>
								@endif
								<td class="product-category">{{ $per_day_salary * $count_this_month_attds }}</td>
								
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
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

</script>



@endsection
