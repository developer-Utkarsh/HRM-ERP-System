<?php
namespace App\Http\Controllers\Admin;
use App\Attendance;
use App\AttendanceNew;
use App\Holiday;
use DB;
use DateTime;
?>
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
						<h2 class="content-header-title float-left mb-0">All Employee Leaves</h2>
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
								<form action="{{ route('admin.leave.leave-full-detail') }}" method="get" name="filtersubmit">
									<div class="row">
										<?php
										if($logged_role_id==29){
										?>
										<div class="col-12 col-md-3">
											<label for="users-list-role">Search</label>
											<fieldset class="form-group">
												<input type="text" class="form-control search" name="search" placeholder="Ex:Name, Mobile, Employee Code" value="{{ app('request')->input('search') }}" id="myInputSearch">
											</fieldset>
										</div>
										
										<div class="col-12 col-md-3 branch_loader">
											<label for="users-list-status">Department</label>
											<?php $departments = \App\Department::where('status', 'Active')->where('is_deleted', '0')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2" name="department_id" id="">
													<option value="">Select Any</option>
													@if(count($departments) > 0)
													@foreach($departments as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('department_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>
												<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
											</fieldset>
										</div>
										
										<div class="col-12 col-md-3 branch_loader">
											<label for="users-list-status">Branch</label>
											<?php $branches = \App\Branch::where('status', '1')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 select_branch_id" name="branch_id" id="">
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
										
										<div class="col-12 col-md-3 branch_loader">
											<label for="users-list-status">Joining From</label>
											<fieldset class="form-group">												
												<input type="date" class="form-control from_date" placeholder="Date" name="from_date" value="{{app('request')->input('from_date')}}">
											</fieldset>
										</div>
										
										<div class="col-12 col-md-3 branch_loader">
											<label for="users-list-status">Joining To</label>
											<fieldset class="form-group">												
												<input type="date" class="form-control to_date" placeholder="Date" name="to_date" value="{{app('request')->input('to_date')}}">
											</fieldset>
										</div>
										
										
										
										<?php 
										}
										?>
										<div class="col-12 col-md-3">
											<label for="users-list-status">Session</label>
											<fieldset class="form-group">
												<select class="form-control session" name="session">
													<option value="">Select Session</option>
												<?php
												
												for($i = 2021; $i<=date('Y'); $i++){
													$selected = "";
													if(!empty(app('request')->input('session'))){
														if($i == app('request')->input('session'))
														{
															$selected = "selected";
														}
													}else{
														if($i == $session){
															$selected = "selected";
														}
													}
													echo "<option value='$i' $selected>$i</option>";
												}
												?>
													
												</select>
											</fieldset>
										</div>
										<div class="col-12 col-md-4">
											<label for="users-list-status">&nbsp;</label>
											<fieldset class="form-group" >		
												<button type="submit" class="btn btn-primary">Search</button>
												<a href="{{ route('admin.leave.leave-full-detail') }}" class="btn btn-warning">Reset</a>
												<button type="button" class="btn btn-primary export_search">Export</button>
											</fieldset>
										</div>
									</div>
									
									
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive" id="table_export">
				<table class="table data-list-view" >
					<thead>
						<tr>
							<th style=";" colspan="4">Emp Leave Summary</th>
							<th style="" colspan="4">Availed Leave</th>
							<th colspan="4">Taken Leave</th>
							<th colspan="5">Reamained Leave</th>
							<th style="width:10%;">Action</th>
							
						</tr>
						<tr>
							<th style="width:;background-color:yellow;">Emp Code</th>
							<th style="width:;background-color:yellow;">Emp Name</th>
							<th style="width:;background-color:yellow;">DOJ</th>
							<th style="width:;background-color:yellow;">DOL</th>
							<th style="width:;background-color:#a4e1b1;">PL</th>
							<th style="width:;background-color:#a4e1b1;">CL</th>
							<th style="width:;background-color:#a4e1b1;">SL</th>
							<th style="width:;background-color:#a4e1b1;">CO</th>
							<th style="width:;background-color:#80e3d5;">PL</th>
							<th style="width:;background-color:#80e3d5;">CL</th>
							<th style="width:;background-color:#80e3d5;">SL</th>
							<th style="width:;background-color:#80e3d5;">CO</th>
							<th style="width:;background-color:#2add61;">PL</th>
							<th style="width:;background-color:#2add61;">CL</th>
							<th style="width:;background-color:#2add61;">SL</th>
							<th style="width:;background-color:#2add61;">CO</th>
							<th style="width:;background-color:#2add61;">Total</th>
							<th>&nbsp;</th>
							
						</tr>
					</thead>
					<tbody>
				<?php 
				$dataFound = 1;
				if (count($get_data) > 0) { 
					foreach ($get_data as $leave) {
						$is_comp_off = 1;
						$months = array();
						// $current_month = date('m');						
						$current_month = 12;						
						$remaining_co = 0;
						$total_holiday_working = 0;
						$remaining_comp_off = 0;
						$user_id = $leave->id;
						$is_extra_working_salary = $leave->is_extra_working_salary;
						if($is_extra_working_salary=='1'){
							$is_comp_off = 0;
							
						}
						
						if($is_comp_off){
							$total_holiday_working = $leave->last_year_co;
							$total_time = $leave->total_time;
							$holiday_location = 0;
							if($leave->branch_location=='jodhpur'){
								$holiday_location = 1;
							}
							else if($leave->branch_location=='jaipur'){
								$holiday_location = 2;
							}
							else if($leave->branch_location=='delhi'){
								$holiday_location = 3;
							}
							$today_date = date('Y-m-d');
							
							$holiday_array = array();					
							$get_holiday = Holiday::select('date','branch_id','type')->where('status', '1')->where('is_deleted', '0')->whereRaw("DATE(date) <= '$today_date' AND (location = 0 OR location = $holiday_location  )")->get();
							if(count($get_holiday) > 0){
								foreach($get_holiday as $get_holiday_val){
									if(!empty($get_holiday_val->branch_id)){
										$holiday_branch = json_decode($get_holiday_val->branch_id);
										if(!empty($leave->branch_id) && !empty($holiday_branch) && in_array($leave->branch_id, $holiday_branch)){
											array_push($holiday_array, array('date'=>$get_holiday_val->date,'type'=>$get_holiday_val->type));
										}	
									}
									else{
										array_push($holiday_array, array('date'=>$get_holiday_val->date,'type'=>$get_holiday_val->type));
									}
									
								}
							}	
							
							if(!empty($leave->comp_off_start_date)){
								if($session==date('Y',strtotime($leave->comp_off_start_date))){
									$all_months = array();
									$start_month = date("n",strtotime($leave->comp_off_start_date));
									for ($x = $start_month; $x <= 12; $x++) {
										$all_months[] = $x;
									}
								}
								else{
									$all_months = array(1,2,3,4,5,6,7,8,9,10,11,12);
								}
								
							}
							else{
								$all_months = array(1,2,3,4,5,6,7,8,9,10,11,12);
							}
							
							foreach($all_months as $y_month){
								if($session == 2021){
									if($y_month > 9){
										$months[] = $y_month;
									}
								}
								else{
									if($current_month >= $y_month){
										$months[] = $y_month;
									}
								}
							}
							
							$yr = $session;
							foreach($months as $key=>$mt){
								$getWorkSunday = cal_days_in_month(CAL_GREGORIAN, $mt, $yr);
								$first_date = strtotime($yr.'-'.$mt.'-01');
								$last_date = strtotime($yr.'-'.$mt.'-'.$getWorkSunday);	
								
								$attendance_array = array();
									
								$first_date_get = date('Y-m-d',$first_date);
								$last_date_get = date('Y-m-d',$last_date);
								
								$attendance = Attendance::query();
								$attendance->select(\DB::raw("id,emp_id,date,'App' as table_name,MAX(CASE WHEN type = 'Out' AND emp_id = '$user_id' AND date >= '$first_date_get' AND date <= '$last_date_get' THEN time ELSE 0 END) AS out_time, MIN(CASE WHEN type = 'In' AND emp_id = '$user_id' AND date >= '$first_date_get' AND date <= '$last_date_get' THEN time ELSE Null END) AS in_time"));
								$attendance->where('emp_id', $user_id);
								$attendance->whereRaw('date >= "'.$first_date_get.'" AND date <= "'.$last_date_get.'"');
								$attendance = $attendance->groupBy('date');
								
								
								$attendancenew = AttendanceNew::query();
								$attendancenew->select(\DB::raw("id,emp_id,date,'RFID' as table_name,MAX(CASE WHEN type = 'Out' AND emp_id = '$user_id' AND date >= '$first_date_get' AND date <= '$last_date_get' THEN time ELSE 0 END) AS out_time, MIN(CASE WHEN type = 'In' AND emp_id = '$user_id' AND date >= '$first_date_get' AND date <= '$last_date_get' THEN time ELSE Null END) AS in_time"));
								$attendancenew->where('emp_id', $user_id);
								$attendancenew->whereRaw('date >= "'.$first_date_get.'" AND date <= "'.$last_date_get.'"');
								$attendancenew = $attendancenew->groupBy('date');				
								
								$comman = $attendancenew->union($attendance);
								$comman_result1 = DB::table(DB::raw("({$comman->toSql()}) as comman"))
										   ->mergeBindings($comman->getQuery())
										   ->groupBy('comman.emp_id')
										   ->groupBy('comman.date')
										   ->get();
								
								if(count($comman_result1) > 0){
									$attendance_array = json_decode(json_encode($comman_result1),true);
								
									while($getWorkSunday> 0){
										$workday = date("D", $first_date);
										$add_get_date = date("Y-m-d", $first_date);
										if(array_search($add_get_date, array_column($attendance_array, 'date')) !== false) { 
											
											$array_index = array_search($add_get_date, array_column($attendance_array, 'date'));
											
											$in_time = $attendance_array[$array_index]['in_time'];
											$out_time = $attendance_array[$array_index]['out_time'];
											$total_minute = 0;
											if(!empty($in_time) && !empty($out_time)){
												$in_time = date("h:i A", strtotime($in_time));
												$out_time = date("h:i A", strtotime($out_time));
												$intime = new DateTime($in_time);
												$outtime = new DateTime($out_time);
												$interval = $intime->diff($outtime);
												$hours = $interval->format('%H');
												$minute = $interval->format('%I');
												$total_minute = ($hours*60)+$minute;
												$total_minute	=	($total_minute*100)/$total_time;
											}						 
											if($workday=="Sun"){
												if($total_minute <= 44.44){
													//44.44%
													//Absent
												}
												else{
													$total_holiday_working++;
												}
											}
											else if(array_search($add_get_date, array_column($holiday_array, 'date')) !== false){
												$array_index = array_search($add_get_date, array_column($holiday_array, 'date'));
												$is_optional 	= false;
												if($holiday_array[$array_index]['type']=="Optional"){
													$is_optional = true;
												}
												
												if($total_minute >= 66.66){
													if($is_optional){
													}
													else{
														$total_holiday_working++;
													}
												}
											}
											
										}				
										$first_date += 86400; 
										$getWorkSunday--;
									}
								}
							}
							
							$remaining_comp_off = $total_holiday_working - $leave->taken_co;
							
						}
								
						?>
							<tr style="" class="rowdata">
							<td><?php echo $leave->register_id?></td>	
							<td><?php echo $leave->name?> </td>
							<td><?php echo (!empty($leave->joining_date))?date('d/m/Y',strtotime($leave->joining_date)):'';?></td>
							<td>
							<?php
							if($leave->status==0){
								if(!empty($leave->inactive_date)){
									echo (!empty($leave->inactive_date))?date('d/m/Y',strtotime($leave->inactive_date)):'';
								}
							}
							?>
							</td>
							
							<td style="background-color:#a4e1b1;"><?php echo $leave->pl + $leave->last_year_pl?></td>
							<td style="background-color:#a4e1b1;"><?php echo $leave->cl?></td>
							<td style="background-color:#a4e1b1;"><?php echo $leave->sl?></td>
							<td style="background-color:#a4e1b1;"><?php echo $total_holiday_working?></td>
							
							<td style="background-color:#80e3d5;"><?php echo $leave->taken_pl?></td>
							<td style="background-color:#80e3d5;"><?php echo $leave->taken_cl?></td>
							<td style="background-color:#80e3d5;"><?php echo $leave->taken_sl?></td>
							<td style="background-color:#80e3d5;"><?php echo $leave->taken_co?></td>
							
							<td style="background-color:#2add61;"><?php echo $remaining_pl = ($leave->pl + $leave->last_year_pl) - $leave->taken_pl?></td>
							<td style="background-color:#2add61;"><?php echo $remaining_cl = $leave->cl - $leave->taken_cl?></td>
							<td style="background-color:#2add61;"><?php echo $remaining_sl = $leave->sl - $leave->taken_sl?></td>
							<td style="background-color:#2add61;"><?php echo $remaining_comp_off;?></td>
							<?php $total_reamining = $remaining_pl + $remaining_cl + $remaining_sl + $remaining_comp_off;?>
							<td style="<?=($total_reamining >=0)?'background-color:#2add61;':'background-color:red;color:#fff;'?>">
								<strong><?php echo $total_reamining; ?></strong>
							</td>
							
							
							<td>
								<a href="javascript:void(0);" data-user_id="{{$leave->id}}" class="view_history">View History</a>
							</td>
							
							
							
							
							</tr>
						 
						<?php 
						/* if($total_reamining > 0){
							DB::table('leave_records')->insertGetId([ 'user_id' => $leave->id, 'session' => 2023,'pl'=>0,'cl'=>0,'sl'=>0,'last_year_pl'=>$remaining_pl,'last_year_co'=>$remaining_comp_off ]);
						} */ 
						$dataFound++; 
					} 
				}
				?>
				</tbody>
				</table>
					 
				</div>       

			</section>
		</div>
	</div>
</div>

<div id="myModal" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form method="post" id="submit_import_file">
				<div class="modal-header">
					<h5 class="modal-title">View History - <strong class="histroy_emp_code"></strong></h5>
				</div>
				<div class="modal-body">
					<div class="form-body">
						<div class="row">
						<div class="col-md-12">
						<table class="" style="width: 100%;">
							<thead>
								<tr>
									<th style="width:10%;">Month</th>
									<th>Taken Leave Count</th>									
								</tr>
							</thead>
							<tbody class="tr_html">
								
							</tbody>
						</table>
							
						</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</form>	
		</div>
	</div>
</div>

<style>
.table tbody td {
    border: solid 1px #ccc !important;
    font-size: 12px;
}
.table thead th {
    border: solid 1px #ccc !important;
    font-size: 14px;
    background: #ededed;
}
</style>
@endsection

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script src="{{asset('laravel/public/js')}}/table2csv.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple1').select2({
			placeholder: "Select Branch",
			allowClear: true
		});
		$('.select-multiple2').select2({
			placeholder: "Select Department",
			allowClear: true
		});
	});
$(".assistant_id").on("change",function(e) {
	$(this).siblings('.change_assistant').show();
})
$(".view_history").on("click",function(e) {
		e.preventDefault();
		var user_id = $(this).data('user_id');
		var session = $(".session").val();
		$(".tr_html").html("Please Wait...");
		$('#myModal').modal({
				//backdrop: 'static',
				keyboard: true, 
				show: true
		});
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},      
			type: "POST",
			url : '{{ route('admin.leave-full-detail-history') }}',
			data : {'user_id':user_id,'session':session},
			success : function(data){
				if(data.status == false){
					swal("Error!", data.message, "error");
				} else if(data.status == true){					
					$(".tr_html").html(data.html);
					$(".histroy_emp_code").text(data.empcode);
				}
			}
		});
	});	
	
	var TodayDate = new Date();
	$(document).on("change",".from_date, .to_date", function () {
		var thisval = $(this);
		var from_date = $(".from_date").val();
		var to_date = $(".to_date").val();
		if(from_date && to_date){
			var from_date= new Date(Date.parse(from_date));
			var to_date= new Date(Date.parse(to_date));
			if (from_date > to_date) {
				$(".to_date").val('');
				alert('From date less then To date');
			}	
		}
		else{
			$(".to_date").val("");
		}
	});
	
	$(".export_search").on("click",function(e) {
		// $("#table_export").first().table2csv();
		var htmltable= document.getElementById('table_export');
		var html = htmltable.innerHTML;
		window.open('data:application/vnd.ms-excel,' + encodeURIComponent(html));
	});
	 
</script>

@endsection
