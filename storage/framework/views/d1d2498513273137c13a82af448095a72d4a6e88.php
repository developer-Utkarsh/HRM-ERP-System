<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>Timetable </title>

    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<div style="text-align:center;">
    <!-- <h3 style="color:red;">We are wotking on this report please wait...</h3>-->
    <h3>Timetable <?= !empty($fdate) ? date('d/m/Y', strtotime($fdate)) : date('d/m/Y')?></h3>
</div>

<body>

    <?php if (!empty($get_batches)) { 
			foreach ($get_batches as $batchArray) {
				if(!empty($batchArray->id)){

					$whereCond = '1=1 ';
								
					if(!empty($branch_id)){ 
						$whereCond .= " AND branches.id IN ($branch_id)"; 
					} 
					if(!empty($batch_ids)){ 
						$whereCond .= " AND batch.id IN ($batch_ids)"; 
					} 
					if(!empty($type)){ 
						$whereCond .= " AND studios.type = '$type'"; 
					} 
					if (!empty($fdate)){ 
						$whereCond .= " AND timetables.cdate = '$fdate'"; 
					}
					else{
						$whereCond .= " AND timetables.cdate = date('Y-m-d')"; 
					}
					
					$get_studio = \App\Batch::select('batch.id','batch.name','studios.id as studios_id','studios.name as studios_name','branches.id as branches_id','branches.name as branches_name')->leftJoin('timetables','timetables.batch_id', '=', 'batch.id')->leftJoin('studios','studios.id', '=', 'timetables.studio_id')->leftJoin('branches','branches.id', '=', 'studios.branch_id')->where('batch.id', $batchArray->id)->whereNotNull('studios.id')->whereRaw($whereCond)->groupBy('studios.id')->get();

					//echo '<pre>'; print_r($get_studio);

					$bt_array = array();
					if(count($get_studio) > 0){  
					foreach ($get_studio as $value) {   
					
					$whereCond2 = '1=1 ';
					if (!empty($fdate)){ // && !empty($tdate)
						//$whereCond2 .= " AND timetables.cdate >= '$fdate' AND timetables.cdate <= '$tdate'"; 
						$whereCond2 .= " AND timetables.cdate = '$fdate'"; 
					}
					else{
						$whereCond2 .= " AND timetables.cdate = date('Y-m-d')"; 
					}

					$timetable_result = \App\Timetable::select('timetables.*','users.name as users_name','course.name as course_name','subject.name as subject_name','chapter.name as chapter_name','topic.name as topics_name')->leftJoin('users','users.id', '=', 'timetables.faculty_id')->leftJoin('course','course.id', '=', 'timetables.course_id')->leftJoin('subject','subject.id', '=', 'timetables.subject_id')->leftJoin('chapter','chapter.id', '=', 'timetables.chapter_id')->leftJoin('topic','topic.id', '=', 'timetables.topic_id')->where('timetables.studio_id',$value->studios_id)->where('timetables.batch_id',$value->id)->where('timetables.is_deleted','0')->whereRaw($whereCond2);
					
					$timetable_result = $timetable_result->orderBy('timetables.from_time')->orderBy('timetables.cdate')->get();
					if(count($timetable_result) > 0){

			?>
					<table class="table2" style="border:1px solid #dee2e6;width: 100%;">

						<tbody>
							<?php if(!in_array($batchArray->id, $bt_array)): ?>
							<tr style="">
								<th style="background-color:#cac21e;font-size: 24px;border:0;color:#FFF;"><b
										style="font-weight: 700;">Batch Name : </b> <span style="font-weight: 100;"><?php echo $batchArray->name; ?> </span>
									<?php
									// if (!empty($value->branches_id)) {
										// $get_data = DB::table('users')
											// ->leftJoin('userbranches', 'users.id', '=', 'userbranches.user_id')
											// ->leftJoin('userdetails', 'users.id', '=', 'userdetails.user_id')
											// ->select('users.name as user_name', 'users.mobile as mobile')
											// ->where('userbranches.branch_id', $value->branches_id)
											// ->where('userdetails.degination', 'CENTER HEAD')
											// ->get();
										// $center_heads = '';
										// if (count($get_data) > 0) {
											// foreach ($get_data as $center_data) {
												// $center_heads .= $center_data->user_name . '( ' . $center_data->mobile . ' ) , ';
											// }
											// echo ' -- <b>CH : </b> ' . rtrim($center_heads, ', ');
										// }
									// }
									
									?>
								</th>
							</tr>
							<?php endif; ?>
							<tr style="">
								<td>
									<?php 
									array_push($bt_array,$batchArray->id);
						
									?>
									<table class="" cellpadding="1" style="width: 100%;font-size: 15px;">

										<tbody>
											<tr style="background-color:#f4f5da;font-size: 18px;border: solid 1px #fff;padding: 10px 0px;">
												<th colspan="7" style="">
													<b>Branch Name : <?php echo $value->branches_name; ?></b>
													<b>Studio Name : <?php echo $value->studios_name; ?></b>
												</th>
											</tr>
											<tr>
												<?php 
												$fullArray = [];
												foreach ($timetable_result as $key => $timetable) {
													$fullArray[$key]['from_time'] = $timetable->from_time;
													$fullArray[$key]['to_time'] = $timetable->to_time;
													$fullArray[$key]['cdate'] = $timetable->cdate;
													$fullArray[$key]['faculty_name'] = isset($timetable->users_name) ? $timetable->users_name : '';
													$fullArray[$key]['course_name'] = isset($timetable->course_name) ? $timetable->course_name : '';
													$fullArray[$key]['batch_id'] = isset($timetable->batch_id) ?  $timetable->batch_id : '';
													$fullArray[$key]['subject_name'] = isset($timetable->subject_name) ? $timetable->subject_name : '';
													$fullArray[$key]['chapter_name'] = isset($timetable->chapter_name) ? $timetable->chapter_name : '';
													$fullArray[$key]['topics_name'] = isset($timetable->topics_name) ? $timetable->topics_name : '';
													$fullArray[$key]['online_class_type'] = isset($timetable->online_class_type) ? $timetable->online_class_type : '';
												}  
												$keys = array_column($fullArray, 'batch_id'); 
												array_multisort($keys, SORT_ASC, $fullArray);
												//echo "<pre>"; print_R($fullArray); die;
												?>
												<td style="vertical-align: top;">
													<table class="table1" cellpadding="1"
														style="width: 100%;font-size: 14px;border: solid 1px #dee2e6;">
														<tbody>
															<tr style="padding-bottom:15px;">
																<th colspan="6"
																	style="background-color:#1674ba;font-size: 18px;border:0;color:#FFF;">
																	<b>First Shift</b></th>
															</tr>
															<tr style="background-color:#f4f4da">
																<th style='text-align:center;font-size: 13px;'>Start Time</th>
																<th style='text-align:center;font-size: 13px;'>End Time</th>
																<th style='text-align:center;font-size: 13px;'>Faculty Name</th>
																<th style='text-align:center;font-size: 13px;'>Subject Name</th>
																<th style='text-align:center;font-size: 13px;'>Class Type</th>
																<th style='text-align:center;font-size: 13px;'>Duration Time</th>
															</tr>
															<?php
												$batch_id = "";
												$i	=	1;
												if(count($fullArray) > 0){
													foreach($fullArray as $key2=>$detail){
														$checkTime = strtotime($detail['to_time']);
														$checkTime = date('H.i',$checkTime);
														if((float)$checkTime <= 11){
															$batch_id = $detail['batch_id'];
														}
														
														if($i%2=="0"){
															$bgClr	=	"background-color:#f4f4da";
														}else{
															$bgClr	=	"background-color:#f4f4da";
														}
														
														if($batch_id == $detail['batch_id']){
															$schedule_duration  = "00 : 00 Hours"; 	
															$from_time         = new DateTime($detail['from_time']);
															$to_time           = new DateTime($detail['to_time']);
															$schedule_interval = $from_time->diff($to_time);
															$schedule_duration = $schedule_interval->format('%H : %I Hours');
															
															?>
															<tr style="<?= $bgClr ?>">
																<td style="font-size: 13px;"><?php echo date('h:i A', strtotime($detail['from_time'])); ?></td>
																<td style="font-size: 13px;"><?php echo date('h:i A', strtotime($detail['to_time'])); ?></td>
																<td style="font-size: 13px;"><?php echo $detail['faculty_name']; ?></td>
																<td style="font-size: 13px;"><?php echo $detail['subject_name']; ?></td>
																<td style="font-size: 13px;"><?php echo $detail['online_class_type']; ?></td>
																<td style="font-size: 13px;"><?php echo $schedule_duration; ?></td>
															</tr>
															<?php
															unset($fullArray[$key2]);
														}
														
														$i++;
													}
												}
												?>

														</tbody>
													</table>
												</td>

												<td style="vertical-align: top;">
													<table class="table1" cellpadding="1"
														style="width: 100%;font-size: 14px;border: solid 1px #dee2e6;">
														<tbody>
															<tr style="">
																<th colspan="6"
																	style="background-color:#1674ba;font-size: 18px;border:0;color:#FFF;margin-bottom:15px;">
																	<b>Second Shift</b></th>
															</tr>
															<tr style="background-color:#f4f4da">
																<th style='text-align:center;font-size: 13px;'>Start Time</th>
																<th style='text-align:center;font-size: 13px;'>End Time</th>
																<th style='text-align:center;font-size: 13px;'>Faculty Name</th>
																<th style='text-align:center;font-size: 13px;'>Subject Name</th>
																<th style='text-align:center;font-size: 13px;'>Class Type</th>
																<th style='text-align:center;font-size: 13px;'>Duration Time</th>
															</tr>
															<?php
												$batch_id = "";
												$remarks = "";
												$i	=	1;
												if(count($fullArray) > 0){
													foreach($fullArray as $key2=>$detail){
														$checkTime = strtotime($detail['to_time']);
														$checkTime = date('H.i',$checkTime);
														if((float)$checkTime > 11 && (float)$checkTime <= 16){
															$batch_id = $detail['batch_id'];
														}
														

														if($i%2=="0"){
															$bgClr	=	"background-color:#f4f4da";
														}else{
															$bgClr	=	"background-color:#f4f4da";
														}
														
														if($batch_id == $detail['batch_id']){
															$schedule_duration  = "00 : 00 Hours"; 	
															$from_time         = new DateTime($detail['from_time']);
															$to_time           = new DateTime($detail['to_time']);
															$schedule_interval = $from_time->diff($to_time);
															$schedule_duration = $schedule_interval->format('%H : %I Hours');
															$remarks .= isset($detail['remark']) && !empty($detail['remark']) ?  $detail['remark'] . "<br>" : '';
															?>
															<tr style="<?= $bgClr ?>">
																<td style="font-size: 13px;"><?php echo date('h:i A', strtotime($detail['from_time'])); ?></td>
																<td style="font-size: 13px;"><?php echo date('h:i A', strtotime($detail['to_time'])); ?></td>
																<td style="font-size: 13px;"><?php echo $detail['faculty_name']; ?></td>
																<td style="font-size: 13px;"><?php echo $detail['subject_name']; ?></td>
																<td style="font-size: 13px;"><?php echo $detail['online_class_type']; ?></td>
																<td style="font-size: 13px;"><?php echo $schedule_duration; ?></td>
															</tr>
															<?php
															unset($fullArray[$key2]);
														}
														
														$i++;
													}
												}
												?>

														</tbody>
													</table>
												</td>

												<td style="vertical-align: top;">
													<table class="table1" cellpadding="1"
														style="width: 100%;font-size: 14px;border: solid 1px #dee2e6;">
														<tbody>
															<tr style="">
																<th colspan="6"
																	style="background-color:#1674ba;font-size: 18px;border:0;color:#FFF;margin-bottom:15px;">
																	<b>Third Shift</b></th>
															</tr>
															<tr style="background-color:#f4f4da">
																<th style='text-align:center;font-size: 13px;'>Start Time</th>
																<th style='text-align:center;font-size: 13px;'>End Time</th>
																<th style='text-align:center;font-size: 13px;'>Faculty Name</th>
																<th style='text-align:center;font-size: 13px;'>Subject Name</th>
																<th style='text-align:center;font-size: 13px;'>Class Type</th>
																<th style='text-align:center;font-size: 13px;'>Duration Time</th>
															</tr>
															<?php
												$batch_id = "";
												$remarks = "";
												$i	=	1;
												if(count($fullArray) > 0){
													foreach($fullArray as $key2=>$detail){
														
														$checkTime = strtotime($detail['to_time']);
														$checkTime = date('H.i',$checkTime);
														if((float)$checkTime > 16 && (float)$checkTime <= 23){
															$batch_id = $detail['batch_id'];
														}

														if($i%2=="0"){
															$bgClr	=	"background-color:#f4f4da";
														}else{
															$bgClr	=	"background-color:#f4f4da";
														}
														
														if($batch_id == $detail['batch_id']){
															$schedule_duration  = "00 : 00 Hours"; 	
															$from_time         = new DateTime($detail['from_time']);
															$to_time           = new DateTime($detail['to_time']);
															$schedule_interval = $from_time->diff($to_time);
															$schedule_duration = $schedule_interval->format('%H : %I Hours');
															$remarks .= isset($detail['remark']) && !empty($detail['remark']) ?  $detail['remark'] . "<br>" : '';
															?>
															<tr style="<?= $bgClr ?>">
																<td style="font-size: 13px;"><?php echo date('h:i A', strtotime($detail['from_time'])); ?></td>
																<td style="font-size: 13px;"><?php echo date('h:i A', strtotime($detail['to_time'])); ?></td>
																<td style="font-size: 13px;"><?php echo $detail['faculty_name']; ?></td>
																<td style="font-size: 13px;"><?php echo $detail['subject_name']; ?></td>
																<td style="font-size: 13px;"><?php echo $detail['online_class_type']; ?></td>
																<td style="font-size: 13px;"><?php echo $schedule_duration; ?></td>
															</tr>
															<?php
															unset($fullArray[$key2]);
														}
														
														$i++;
													}
												}
												?>

														</tbody>
													</table>
												</td>




											</tr>
										</tbody>

									</table>
									<p>&nbsp;</p>
								</td>
							</tr>
						</tbody>
					</table>
			<?php } } }?>
    		<?php }
			}	
		}
		?>
</body>
<style>
    body {
        font-family: 'Arial';
    }

    .table1 {
        border-collapse: collapse;
    }

    .table1 td,
    .table1 th,
    .table2 th {
        border: 1px solid #dee2e6;
    }

    .table1 tr:nth-child(even) {
        //background-color: #ff9f43;
    }

    @media  print {

        @page  {
            size: auto;
            margin: 0;
        }

        body {
            margin: 1.6cm;
        }

    }

</style>

</html>

<script src="<?php echo e(asset('laravel/public/admin/js/vendors.min.js')); ?>"></script>
<script>
    $(document).ready(function() {
        window.print();
        // window.close();
    });
</script>
<?php /**PATH /var/www/html/laravel/resources/views/studiomanager/batch_reports/shiftwise_pdf_html.blade.php ENDPATH**/ ?>