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
										<div class="col-md-4">
											<label for="users-list-role">Month</label>
											<fieldset class="form-group">
												<input type="month" class="form-control year_wise_month" name="year_wise_month" value="@if(!empty(Request::get('year_wise_month'))){{ Request::get('year_wise_month') }}@else{{ date('Y-m', strtotime('-1 month', time())) }}@endif">
											</fieldset>
										</div>
										
										
										<div class="col-md-8">
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
				
				<h5>Total working days of this month : <span>{{ $t_workday }}</span></h5>
				<div class="table-responsive">
					<table class="table data-list-view" id="TableSearch">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Name</th>
								<th>Mobile</th>
								<th>Woked Days</th>
								<th>Salary</th>
							</tr>
						</thead>
						<tbody>
						
							<?php
							foreach($get_emp as  $key => $value){
							
								if(!empty(Request::get('year_wise_month'))){	
									$myTime = strtotime(Request::get('year_wise_month').'-01');
								}
								else{
									$myTime = strtotime(date("Y-m-01", strtotime('-1 MONTH'))); 
								}	
							
							// sandwich check
							$workingDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $mt, $yr); 
							$sandwichHoliday = 0;
						
							while($workingDaysInMonth > 0)
							{
								
								//$day =  date('Y-m-d',strtotime("-1 month"));
								$day = date("D", $myTime); 
								if($day == "Sun"){
									$sundayData   = date("Y-m-d", $myTime);
									$prevdayData  = "";
									$nextdayData  = "";
									
									if(date("m", $myTime) == date("m", strtotime($sundayData .' -1 day'))){
										$prevdayData  = date('Y-m-d', strtotime($sundayData .' -1 day'));
									}
									
									if(date("m", $myTime) == date("m", strtotime($sundayData .' +1 day'))){
										$nextdayData  = date('Y-m-d', strtotime($sundayData .' +1 day'));
									}
									
									
						
									if(!empty($prevdayData) && !empty($nextdayData)){
										$check_absent = DB::select("SELECT `leave_details`.`date` FROM `leave` LEFT JOIN `leave_details` ON `leave`.`id` = `leave_details`.`leave_id` WHERE MONTH(leave_details.date) = '$mt' AND YEAR(leave_details.date) = '$yr' AND leave.emp_id = '".$value->id."' AND leave_details.status = 'Approved' AND (leave_details.date = '".$prevdayData."' OR leave_details.date ='".$nextdayData."')"); 
									
									
										if(count($check_absent) > 0){ 
											$sandwichHoliday++;
										}
									}
									
									
								} 
								
								$workingDaysInMonth--;
								$myTime += 86400; 
							}
							

							//Weekly Check
							
							$mm = $mt;
							$yy = $yr;
							$startdate = date($yy . "-" . $mm . "-01");
							$current_date = date('Y-m-t');
							$ld = cal_days_in_month(CAL_GREGORIAN, $mm, $yy);
							$lastday = $yy . '-' . $mm . '-' . $ld;
							$start_date = date('Y-m-d', strtotime($startdate));
							$end_date = date('Y-m-d', strtotime($lastday));
							$end_date1 = date('Y-m-d', strtotime($lastday . " + 7 days"));
							$count_week = 0;
							$week_array = array();

							for ($date = $start_date; $date <= $end_date1; $date = date('Y-m-d', strtotime($date . ' + 7 days'))) {    
								$week = date('W', strtotime($date));
								$year = date('Y', strtotime($date));
								$from = date("Y-m-d", strtotime("{$year}-W{$week}+1"));

								if ($from < $start_date)
									$from = $start_date;

								$to = date("Y-m-d", strtotime("{$year}-W{$week}-6"));
								if ($to > $end_date)
									$to = $end_date;
								if($from > $end_date)
								{
								  $array1 = array();
								}
								else
								{
								  $week_array[] = array(
										"ssdate" => $from,
										"eedate" => $to,
									);
								}

								
							  $count_week++;  
								
							}
							// echo "<pre>"; print_r($week_array); die;
							$weeklyHoliday = 0;
							foreach($week_array as $week_array_val){
								$weekStartingDate  = $week_array_val['ssdate'];
								$weekEndingingDate = $week_array_val['eedate'];
									//echo '<pre>'; print_r($week_array_val['ssdate'].'=>'.$week_array_val['eedate']);die;
								$week_check_absent = DB::select("SELECT `leave_details`.`date` FROM `leave` LEFT JOIN `leave_details` ON `leave`.`id` = `leave_details`.`leave_id` WHERE MONTH(leave_details.date) = '$mt' AND YEAR(leave_details.date) = '$yr' AND leave.emp_id = '".$value->id."' AND leave_details.status = 'Approved' AND (leave_details.date >= '".$weekStartingDate."' AND  leave_details.date <='".$weekEndingingDate."')"); 
								
								
								if(count($week_check_absent) >= 5){ 
									$weeklyHoliday++;
								}
							}
							 	
							$this_month_attds = DB::select("SELECT * FROM attendance WHERE MONTH(date) = '$mt' AND YEAR(date) = '$yr' AND emp_id = '".$value->id."' GROUP BY date");
							
							
							$count_this_month_attds = count($this_month_attds)-($sandwichHoliday + $weeklyHoliday);
							
							
							
							$per_day_salary = 0;
							$per_day_salary = $value->user_details->net_salary/cal_days_in_month(CAL_GREGORIAN, $mt, $yr); 
							$esic           = 0;
							$pf             = 0;
							$salry          = 0;
							if(!empty($value->user_details->net_salary) && $value->user_details->net_salary <= 15000 && $count_this_month_attds > 0){ 
								$esic    = ($value->user_details->net_salary * 0.75)/100; 
								$pf      = ($count_this_month_attds *$per_day_salary) *12 /100;
								$c_salry = ($count_this_month_attds + $t_sunday) * $per_day_salary; 
								$salry   = $c_salry - $esic - $pf;
							}
							
							
							
							?>
							<tr>
								<td>{{ $pageNumber++ }}</td>
								<td class="product-category">{{ $value->name ? $value->name : '' }}</td>
								<td class="product-category">{{ $value->mobile ? $value->mobile : '' }}</td>
								@if($count_this_month_attds > 0)
								<td class="product-category"><a href="{{ route('admin.attendance.index', ['name' => $value->name, 'fdate' => $first_day_month, 'tdate' => $last_day_month]) }}">{{ $count_this_month_attds }}</a></td>
								<td class="product-category">{{ round($salry, 2) }}</td>
								@else
								<td class="product-category"><a href="javascript:void(0)">0</a></td>
								<td class="product-category">0</td>
								@endif
								
							</tr>
							<?php
							} 
							?>
						</tbody>
					</table>
					<div class="d-flex justify-content-center">			
					{!! $get_emp->appends($params)->links() !!}
					</div>
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
