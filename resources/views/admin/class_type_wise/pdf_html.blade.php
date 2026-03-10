<button type="button" onClick="dataPrint()" class="noprint">Excel Export</button>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

    <head>

        <title><?php //echo $title; ?></title>

        <meta charset="utf-8">

            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    </head>
	<div style="text-align:center;">
		<h3>Class Type Report</h3>
	</div>

    <body id="printData">
	<table class="table1 data-list-view" style=''>
		<head>
				<tr style="">
					<th>Type</th>
					<th>Schedule Time</th>
					<th>Spent Time</th>
				</tr>
				</head>
				<body>
				<?php 
				$final_total_schedule = new DateTime('00:00');
				$final_total_schedule_last = new DateTime('00:00');
				
				$final_total_spent = new DateTime('00:00');
				$final_total_spent_last = new DateTime('00:00');
				
				
				if (count($get_faculty) > 0) {
					foreach ($get_faculty as $key2=>$get_faculty_value) {
						$total_cancel_class = 0;
				?>
					
					  
						
							<?php
							$whereCond = '1=1';;
							 
							if(!empty($selectFromDate) && !empty($selectToDate)){
								$whereCond .= ' AND timetables.cdate >= "'.$selectFromDate.'" AND timetables.cdate <= "'.$selectToDate.'"';
							}
							else{
								$whereCond .= ' AND timetables.cdate >= "'.date('Y-m-d').'" AND timetables.cdate <= "'.date('Y-m-d') .'"';
							}	
							
							$base_time = new DateTime('00:00');
							$total     = new DateTime('00:00');
							
							$total_schedule = new DateTime('00:00');
							$total_base_schedule = new DateTime('00:00');
							$total_base_cancel = new DateTime('00:00');
							
							// $whereCond .= ' AND timetables.assistant_id = "'.$get_faculty_value->assistant_id.'"';
							if(!empty($get_faculty_value->online_class_type)){
							$get_faculty_timetable = DB::table('timetables')
													  ->select('timetables.*','start_classes.status as start_classes_status','start_classes.start_time as start_classes_start_time','start_classes.end_time as start_classes_end_time','start_classes.topic_name')
													  ->leftJoin('start_classes', 'start_classes.timetable_id', '=', 'timetables.id')
													  ->where('timetables.online_class_type', $get_faculty_value->online_class_type)
													  ->where('timetables.time_table_parent_id', '0')
													  ->whereRaw("(timetables.is_deleted='0' OR timetables.is_deleted='2')");
													  
							   
							$get_faculty_timetable = $get_faculty_timetable->whereRaw($whereCond)->get();
													  
													  //echo "<pre>"; print_r($get_faculty_timetable); die;
							$duration  = "00 : 00 Hours"; 
							$schedule_duration  = "00 : 00 Hours"; 
							
							
							if(count($get_faculty_timetable) > 0){ 
							foreach($get_faculty_timetable as $key => $get_faculty_timetable_value){
								
								$from_time         = new DateTime($get_faculty_timetable_value->from_time);
								$to_time           = new DateTime($get_faculty_timetable_value->to_time);
								$schedule_interval = $from_time->diff($to_time);
								$schedule_duration = $schedule_interval->format('%H : %I Hours');
								$total_base_schedule->add($schedule_interval); 
								$final_total_schedule->add($schedule_interval);
								
								if($get_faculty_timetable_value->is_cancel != '1'){
									$first_date = new DateTime($get_faculty_timetable_value->start_classes_start_time);
									$second_date = new DateTime($get_faculty_timetable_value->start_classes_end_time);
									$interval = $first_date->diff($second_date);
									$duration = $interval->format('%H : %I Hours');
									$base_time->add($interval);
									$final_total_spent->add($interval);
								}
								else{
									$total_cancel_class++;
									$duration = 'Cancelled Classes';
									
									$total_base_cancel->add($schedule_interval); 
								}
								
								
								
								 
								
							?>
							<?php
							}
							}
							}
							?>
							
							<tr> 
								<td><b><?php echo isset($get_faculty_value->online_class_type)?$get_faculty_value->online_class_type:''; ?></b> 
								</td> 
								<td><b></b> 
								<?php
								$totalDays = $total_schedule->diff($total_base_schedule)->format("%a");
								$totalHours = $total_schedule->diff($total_base_schedule)->format("%H");
								$totalMinute = $total_schedule->diff($total_base_schedule)->format("%I");
								echo ($totalDays*24)+$totalHours. ":" . $totalMinute;
								?> Hours
								</td> 
								<td><b></b> 
								<?php
								$baseDays = $total->diff($base_time)->format("%a");
								$baseHours = $total->diff($base_time)->format("%H");
								$baseMinute = $total->diff($base_time)->format("%I");
								echo ($baseDays*24)+$baseHours. ":" . $baseMinute;
								?> 
								Hours</td>
								 						
							</tr>
								
						
					
				<?php 
					}
				}
				?>
				</body> 
				<?php
				$totalDays = $final_total_schedule_last->diff($final_total_schedule)->format("%a");
				$totalHours = $final_total_schedule_last->diff($final_total_schedule)->format("%H");
				$totalMinute = $final_total_schedule_last->diff($final_total_schedule)->format("%I");
				?>
				
				<?php
				$totalDays1 = $final_total_spent_last->diff($final_total_spent)->format("%a");
				$totalHours1 = $final_total_spent_last->diff($final_total_spent)->format("%H");
				$totalMinute1 = $final_total_spent_last->diff($final_total_spent)->format("%I");
				?>
				<footer>
				<th>Total</th>
				<th><?php echo ($totalDays*24)+$totalHours. ":" . $totalMinute;?> Hours</th>
				<th><?php echo ($totalDays1*24)+$totalHours1. ":" . $totalMinute1;?> Hours</th>
				</footer>
					
					</table>

    </body>

	<style>
	body {
		font-family : 'Arial';
	}
	
	.table1 {
	  border-collapse: collapse;
	}

	.table1 td, .table1 th, .table2 th {
	  border: thin solid #ff9f43;
	}
	
	.table1 .tHead th{
		border: thin solid #000;
	}

	
	@media print {
		
	  @page {size: auto; margin: 0; }
	  body { margin: 1.6cm; }
	  .noprint {	display: none;	}
	  
	}
    </style>

</html>

<script src="{{ asset('laravel/public/admin/js/vendors.min.js') }}"></script>
<script>

$(document).ready(function () { 
	window.print(); 
	// window.close();
});

function dataPrint(){
	//document.getElementById('noprint').style.display = 'none';
	
	var htmltable= document.getElementById('printData');
	var html = htmltable.innerHTML;
	window.open('data:application/vnd.ms-excel,' + encodeURIComponent(html));
}
</script>

