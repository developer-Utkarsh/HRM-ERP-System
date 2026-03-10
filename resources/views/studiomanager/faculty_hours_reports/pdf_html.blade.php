<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    </head>
    <body>
            <table class="table1" cellpadding="1" style='' border="1">
                <tbody>
					<tr>

                        <th style='text-align:center;'>Name</th>

                        <th style='text-align:center;'>Contact No</th>
						<th style='text-align:center;'>Dapartment</th>
						<th style='text-align:center;'>Subject</th>
						<th style='text-align:center;'>A. Time</th>
                        <th style='text-align:center;'>Schedule Time</th>
                        <th style='text-align:center;'>Spent Time</th>

                        <th style='text-align:center;'>From Date</th>

                        <th style='text-align:center;'>To Date</th>

                    </tr>

					<?php

					if(count($get_faculty) > 0){
						$s_no = 0;
						foreach($get_faculty as $key=>$get_faculty_val){
							
							$f_date = date('Y-m-d'); $t_date = date('Y-m-d');
							if(!empty($selectFromDate)){
								$f_date = $selectFromDate;
							}
							if(!empty($selectToDate)){
								$t_date = $selectToDate;
							}
							/*$get_total_time = DB::table('timetables')
											->select('start_classes.start_time','start_classes.end_time','subject.name')
											->leftJoin('start_classes', 'start_classes.timetable_id', '=', 'timetables.id')
											->leftJoin('subject', 'subject.id', '=', 'timetables.subject_id')
											->where('timetables.faculty_id', $get_faculty_val->id)
											->whereRaw(' timetables.cdate >= "'.$f_date.'" AND timetables.cdate <= "'.$t_date.'"')
											->get();
												
							$duration           = "00 : 00 Hours"; 
							$schedule_duration  = "00 : 00 Hours"; 
							$base_time          = new DateTime('00:00');
							$total              = new DateTime('00:00');
							$subject_arr        = array();
							if(count($get_total_time) > 0){
								foreach($get_total_time as $get_total_time_value){
									array_push($subject_arr, $get_total_time_value->name);
									$first_time = new DateTime($get_total_time_value->start_time);
									$second_time = new DateTime($get_total_time_value->end_time);
									$interval = $first_time->diff($second_time);
									$duration = $interval->format('%H : %I Hours');
									$base_time->add($interval); 
								}
							}*/

							$whereCond  = ' 1=1';
									
							if(!empty($branch_location)){
								$whereCond .= ' AND branches.branch_location = "'.$branch_location.'"';
							}
							
							if(!empty($online_class_type)){
								$whereCond .= ' AND timetables.online_class_type = "'.$online_class_type.'"';
							}

							$get_total_time = DB::table('timetables')
											->select('timetables.from_time as start_time','timetables.to_time as end_time','subject.name','start_classes.start_time as start_classes_start_time','start_classes.end_time as start_classes_end_time')
											->leftJoin('subject', 'subject.id', '=', 'timetables.subject_id')
											->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
											->leftJoin('branches', 'branches.id', '=', 'studios.branch_id')
											->leftJoin('start_classes', 'start_classes.timetable_id', '=', 'timetables.id')
											->where('timetables.faculty_id', $get_faculty_val->id)
											->where('timetables.time_table_parent_id', '0')
											->where('timetables.is_deleted', '0')
											->whereRaw($whereCond)
											->whereRaw(' timetables.cdate >= "'.$f_date.'" AND timetables.cdate <= "'.$t_date.'"')
											->get();
												
							$base_time2          = new DateTime('00:00');
							$base_time          = new DateTime('00:00');
							$total              = new DateTime('00:00');
							$total2              = new DateTime('00:00');
							$subject_arr        = array();
							$schedule_total_tt           = "00 : 00 Hours"; 
							$total_tt           = "00 : 00 Hours"; 
							if(count($get_total_time) > 0){
								foreach($get_total_time as $get_total_time_value){
									array_push($subject_arr, $get_total_time_value->name);
									$first_time = new DateTime($get_total_time_value->start_time);
									$second_time = new DateTime($get_total_time_value->end_time);
									$interval = $first_time->diff($second_time);
									$base_time->add($interval);


									$first_date = new DateTime($get_total_time_value->start_classes_start_time);
									$second_date = new DateTime($get_total_time_value->start_classes_end_time);
									$interval = $first_date->diff($second_date);
									$base_time2->add($interval); 											
								}
								
								$baseDays = $total->diff($base_time)->format("%a");
								$baseHours = $total->diff($base_time)->format("%H");
								$baseMinute = $total->diff($base_time)->format("%I");
								
								$schedule_total_tt = ($baseDays*24)+$baseHours. ":" . $baseMinute;
								
								$totalDays = $total2->diff($base_time2)->format("%a");
								$totalHours = $total2->diff($base_time2)->format("%H");
								$totalMinute = $total2->diff($base_time2)->format("%I");
								
								$total_tt = ($totalDays*24)+$totalHours. ":" . $totalMinute;
							}

							if(!empty($branch_location) ){
								if(count($get_total_time) > 0){
								$s_no++;
								?>
								<tr>

									<td><?php echo isset($get_faculty_val->name) ?  $get_faculty_val->name : '' ?></td>
		
									<td><?php echo isset($get_faculty_val->mobile) ?  $get_faculty_val->mobile : '' ?></td>
									<td><?php echo isset($get_faculty_val->department_name) ?  $get_faculty_val->department_name : '' ?></td>
									
									<td><?php echo (count($subject_arr) > 0) ? implode(",", array_unique($subject_arr)) : '' ?></td>
									<td><?php echo isset($get_faculty_val->committed_hours) ?  $get_faculty_val->committed_hours : '' ?></td>
									<td><?php echo $schedule_total_tt ?> Hours</td>
									<td><?php echo $total_tt ?> Hours</td>
		
									<td><?php echo $f_date ?></td>
		
									<td><?php echo $t_date ?></td>
		
								</tr> 
								<?php
								}

							}
							else{
							
					?>

                        <tr>

							<td><?php echo isset($get_faculty_val->name) ?  $get_faculty_val->name : '' ?></td>

							<td><?php echo isset($get_faculty_val->mobile) ?  $get_faculty_val->mobile : '' ?></td>
							<td><?php echo isset($get_faculty_val->department_name) ?  $get_faculty_val->department_name : '' ?></td>
							<td><?php echo (count($subject_arr) > 0) ? implode(",", array_unique($subject_arr)) : '' ?></td>
							<td>
							<?php
							if($get_faculty_val->agreement=='Yes'){
								echo isset($get_faculty_val->committed_hours) ?  $get_faculty_val->committed_hours : '';
							}
							else{
								echo "-";
							}
							?>
							</td>
							<!--td><?php //echo $total->diff($base_time)->format("%H:%I") ?> Hours</td-->
							<td><?php echo $schedule_total_tt ?> Hours</td>
							<td><?php echo $total_tt ?> Hours</td>

							<td><?php echo $f_date ?></td>

							<td><?php echo $t_date ?></td>

                        </tr> 
						
					<?php
							}
					}
					}
					?>
                </tbody>

			

            </table>

    </body>

	<style>

      @page {

        size: a4 landscape; 

      } 

      body {

        text-align: center;

        border: thin solid black;

		width:100%;

      }

    </style>

</html>

