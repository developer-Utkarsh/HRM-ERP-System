<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Timetable </title>

        <meta charset="utf-8">
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    </head>
	<div style="text-align:center;">		
		<!-- <h3 style="color:red;">We are wotking on this report please wait...</h3>-->
		<h3>Timetable <?=date('d/m/Y',strtotime($fdate));?></h3>
	</div>
    <body>
		
        <?php if (!empty($get_studios)) {
			foreach ($get_studios as $branchArray) {
				if(count($branchArray->studio) > 0){
			?>
			<table class="table2" style="border:1px solid #dee2e6;width: 100%;" >
				
				<tbody>
				<tr style="">
					<th style="background-color:#1674ba;font-size: 24px;border:0;color:#FFF;"><b style="font-weight: 700;">Branch Name : </b> <span style="font-weight: 100;"><?php echo $branchArray->name; ?> <?php if($branchArray->nickname!=""){ ?>(<?=$branchArray->nickname;?>)<?php } ?> </span>
					<?php
					if(!empty($branchArray->id)){
						$get_data = DB::table('users')
						->leftJoin('userbranches','users.id','=','userbranches.user_id')
						->leftJoin('userdetails','users.id','=','userdetails.user_id')
						->select('users.name as user_name','users.mobile as mobile')
						->where('users.status',1)
						->where('users.is_deleted','0')
						->where('userbranches.branch_id',$branchArray->id)
						->where('userdetails.degination','CENTER HEAD')->get();
						$center_heads = "";
						if(count($get_data) > 0){
							foreach($get_data as $center_data){
								$center_heads .= $center_data->user_name."( ".$center_data->mobile." ) , ";
							}
							echo " -- <b>CH : </b> ".rtrim($center_heads,', ');
						}
					}
					
					?>
					</th>
				</tr>
				<tr style="">
				<td >
		<?php 
		$i = 0;
		foreach ($branchArray->studio as $value) {
		if(count($value->timetable) > 0){
			if($i==0){
				// For border full With 
				
			}
			$i++;
		?>
            <table class="" cellpadding="1" style="width: 100%;font-size: 15px;">
				
                <tbody>
					<tr style="font-size: 18px;border: solid 1px #fff;padding: 10px 0px;">
                        <th colspan="7" style="">
						<b><?php if($branchArray->nickname!=""){ ?>(<?=$branchArray->nickname;?>)<?php } ?> <?php echo $value->name; ?> -- Assistant : 
						<?php 
							echo isset($value->timetable[0]->assistant->name) ? $value->timetable[0]->assistant->name : '';
							// echo isset($value->assistant->name) ?  $value->assistant->name : '';
						?> 
						( 
						<?php 
						echo isset($value->timetable[0]->assistant->mobile) ? $value->timetable[0]->assistant->mobile : '';
						//echo isset($value->assistant->mobile) ?  $value->assistant->mobile : '';
						?>
						)
						</b>
						<?php if($value->type=='Offline'){ ?>
							--&nbsp;&nbsp;Capacity : <?php echo $value->capacity; ?>
						<?php } ?>
						</th>
                    </tr>
					<tr>
					<?php
					$fullArray = array();
					foreach($value->timetable as $key => $timetable){ 
						$fullArray[$key]['tid'] = $timetable->id;
						$fullArray[$key]['from_time'] = $timetable->from_time;
						$fullArray[$key]['to_time'] = $timetable->to_time;
						$fullArray[$key]['faculty_name'] = isset($timetable->faculty->nickname) ?  $timetable->faculty->nickname : '';
						$fullArray[$key]['subject_name'] = isset($timetable->subject->name) ?  $timetable->subject->name : '';
						$fullArray[$key]['batch_id'] = isset($timetable->batch->id) ?  $timetable->batch->id : '';
						$fullArray[$key]['batch_name'] = isset($timetable->batch->nickname) ?  $timetable->batch->nickname : '';
						$fullArray[$key]['batch_capacity'] = isset($timetable->batch->capacity) ?  $timetable->batch->capacity : '';
						$fullArray[$key]['assistant_name'] = isset($timetable->assistant->name) ?  $timetable->assistant->name : '';
						$fullArray[$key]['online_class_type'] = isset($timetable->online_class_type) ?  $timetable->online_class_type : '';
						$fullArray[$key]['remark'] = isset($timetable->remark) ?  $timetable->remark : '';
					}
					$keys = array_column($fullArray, 'batch_id');
					$to_time_check = 0;
					// array_multisort($keys, SORT_ASC, $fullArray);
					// echo "<pre>"; print_R($fullArray); die;
					?>
					<td style="vertical-align: top;width:50%;">
							<table class="table1" cellpadding="1" style="width: 100%;font-size: 14px;border: solid 1px #dee2e6;">
								<tbody>
								<tr style="padding-bottom:15px;" >
									<th colspan="5" style="background-color:#1674ba;font-size: 18px;border:0;color:#FFF;"><b>First Shift</b></th>
								</tr>
								<tr >
									<th style='text-align:center;font-size: 13px;'>Start Time</th>
									<th style='text-align:center;font-size: 13px;'>End Time</th>
									<th style='text-align:center;font-size: 13px;'>Faculty Name</th>
									<th style='text-align:center;font-size: 13px;'>Subject Name</th>
									<!--th style='text-align:center;font-size: 13px;'>Batch Name</th>
									<th style='text-align:center;font-size: 13px;'>Assistant Name</th-->
									<th style='text-align:center;font-size: 13px;'>Class Type</th>
								</tr>
								<?php
								$batch_id = "";
								$remarks = "";
								$i	=	1;
								if(count($fullArray) > 0){
									foreach($fullArray as $key2=>$detail){
										$checkTime = strtotime($detail['to_time']);
										$checkTime = date('H.i',$checkTime);
										if((float)$checkTime <= 12){
											if($batch_id != $detail['batch_id']){
												$get_batches_name = "";
												$get_batches = DB::table('timetables')->select("batch.name as b_name")->leftJoin('batch','batch.id','=','timetables.batch_id')->where('timetables.is_deleted', '0')->where('timetables.time_table_parent_id', $detail['tid'])->get();
												if(count($get_batches) > 0){
													foreach($get_batches as $vallll){
														$get_batches_name .= ', '.$vallll->b_name;
													}
												}
												
											?>
											<tr style="">
												<th colspan="5" style='background-color:#fdc800;font-size: 15px;text-align:center;'>
												<?=$detail['batch_name']?> 
												<?php
												echo $get_batches_name;
												?>
												- <?=$detail['batch_capacity'];?>
												
												</th>
											</tr>
											<?php
											}
											$batch_id = $detail['batch_id'];
										}
										
										
										if($i%2=="0"){
											$bgClr	=	"background-color:#dee2e6";
										}else{
											$bgClr	=	"";
										}
										
										if($batch_id == $detail['batch_id']){
											$to_time_check = $detail['to_time'];
											$schedule_duration  = "00 : 00 Hours"; 	
											$from_time         = new DateTime($detail['from_time']);
											$to_time           = new DateTime($detail['to_time']);
											$schedule_interval = $from_time->diff($to_time);
											$schedule_duration = $schedule_interval->format('%H : %I Hours');
											$remarks .= isset($detail['remark']) && !empty($detail['remark']) ?  $detail['remark'] . "<br>" : '';
											?>
											<tr style="<?=$bgClr;?>">
												<td style="font-size: 13px;"><?php echo date('h:i A', strtotime($detail['from_time'])); ?></td>
												<td style="font-size: 13px;"><?php echo date('h:i A', strtotime($detail['to_time'])); ?></td>												
												<td style="font-size: 13px;"><?php echo $detail['faculty_name']; ?></td>
												<td style="font-size: 13px;"><?php echo $detail['subject_name']; ?></td>
												<!--td style="font-size: 13px;"><?php echo $detail['batch_name']; ?></td>
												<td style="font-size: 13px;"><?php echo $detail['assistant_name']; ?></td-->
												<td style="font-size: 13px;"><?php echo $detail['online_class_type']; ?></td>
												
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
						<?php if(!empty($remarks)) { ?>
						<table> <tbody><tr><td><b style="margin-top:2px;">Remark:- <?=$remarks?></b></td></tr></tbody></table>
						<?php } ?>
					</td>
					<?php
					if(count($fullArray)== 0 && $to_time_check >= 18){
						
					}
					else{
					?>
					<td style="vertical-align: top;width:50%;">
							<table class="table1" cellpadding="1" style="width: 100%;font-size: 14px;border: solid 1px #dee2e6;">
								<tbody>
								<tr style="" >
									<th colspan="5" style="background-color:#1674ba;font-size: 18px;border:0;color:#FFF;margin-bottom:15px;"><b>Second Shift</b></th>
								</tr>
								<tr>
									<th style='text-align:center;font-size: 13px;'>Start Time</th>
									<th style='text-align:center;font-size: 13px;'>End Time</th>
									<th style='text-align:center;font-size: 13px;'>Faculty Name</th>
									<th style='text-align:center;font-size: 13px;'>Subject Name</th>
									<!--th style='text-align:center;font-size: 13px;'>Batch Name</th-->
									<!--th style='text-align:center;font-size: 13px;'>Assistant Name</th-->
									<th style='text-align:center;font-size: 13px;'>Class Type</th>
								</tr>
								<?php
								$batch_id = "";
								$remarks = "";
								$i	=	1;
								if(count($fullArray) > 0){
									foreach($fullArray as $key2=>$detail){
										$checkTime = strtotime($detail['to_time']);
										$checkTime = date('H.i',$checkTime);
										if((float)$checkTime > 12 && (float)$checkTime <= 23){
											if($batch_id != $detail['batch_id']){
												$get_batches_name = "";
												$get_batches = DB::table('timetables')->select("batch.name as b_name")->leftJoin('batch','batch.id','=','timetables.batch_id')->where('timetables.is_deleted', '0')->where('timetables.time_table_parent_id', $detail['tid'])->get();
												if(count($get_batches) > 0){
													foreach($get_batches as $vallll){
														$get_batches_name .= ', '.$vallll->b_name;
													}
												}
											?>
											<tr style="" >
												<th colspan="5" style='background-color:#fdc800;font-size: 15px;text-align:center;'><?=$detail['batch_name']?> 
												<?php
												echo $get_batches_name;
												?>
												- <?=$detail['batch_capacity'];?></th>
											</tr>
											<?php
											}
											$batch_id = $detail['batch_id'];
										}
										
										if($i%2=="0"){
											$bgClr	=	"background-color:#dee2e6";
										}else{
											$bgClr	=	"";
										}
										
										if($batch_id == $detail['batch_id']){
											$schedule_duration  = "00 : 00 Hours"; 	
											$from_time         = new DateTime($detail['from_time']);
											$to_time           = new DateTime($detail['to_time']);
											$schedule_interval = $from_time->diff($to_time);
											$schedule_duration = $schedule_interval->format('%H : %I Hours');
											$remarks .= isset($detail['remark']) && !empty($detail['remark']) ?  $detail['remark'] . "<br>" : '';
											?>
											<tr style="<?=$bgClr;?>">
												<td style="font-size: 13px;"><?php echo date('h:i A', strtotime($detail['from_time'])); ?></td>
												<td style="font-size: 13px;"><?php echo date('h:i A', strtotime($detail['to_time'])); ?></td>												
												<td style="font-size: 13px;"><?php echo $detail['faculty_name']; ?></td>
												<td style="font-size: 13px;"><?php echo $detail['subject_name']; ?></td>
												<!--td style="font-size: 13px;"><?php echo $detail['batch_name']; ?></td-->
												<!--td style="font-size: 13px;"><?php echo $detail['assistant_name']; ?></td-->
												<td style="font-size: 13px;"><?php echo $detail['online_class_type']; ?></td>
												
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
						<?php if(!empty($remarks)) { ?>
						<table> <tbody><tr><td><b style="margin-top:2px;">Remark:- <?=$remarks?></b></td></tr></tbody></table>
						<?php } ?>
					</td>
					<?php
					}
					?>
					
					<?php /*
					<td style="vertical-align: top;">
							<table class="table1" cellpadding="1" style="width: 100%;font-size: 14px;border: solid 1px #dee2e6;">
								<tbody>
								<tr style="" >
									<th colspan="5" style="background-color:#1674ba;font-size: 18px;border:0;color:#FFF;margin-bottom:15px;"><b>Third Shift</b></th>
								</tr>
								<tr>
									<th style='text-align:center;font-size: 13px;'>Start Time</th>
									<th style='text-align:center;font-size: 13px;'>End Time</th>
									<th style='text-align:center;font-size: 13px;'>Faculty Name</th>
									<th style='text-align:center;font-size: 13px;'>Subject Name</th>
									<!--th style='text-align:center;font-size: 13px;'>Batch Name</th-->
									<!--th style='text-align:center;font-size: 13px;'>Assistant Name</th-->
									<th style='text-align:center;font-size: 13px;'>Class Type</th>
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
											if($batch_id != $detail['batch_id']){
											?>
											<tr style="" >
												<th colspan="5" style='background-color:#fdc800;font-size: 15px;text-align:center;'><?=$detail['batch_name']?> - <?=$detail['batch_capacity'];?></th>
											</tr>
											<?php
											}
											$batch_id = $detail['batch_id'];
										}
										
										if($i%2=="0"){
											$bgClr	=	"background-color:#dee2e6";
										}else{
											$bgClr	=	"";
										}
										
										if($batch_id == $detail['batch_id']){
											$schedule_duration  = "00 : 00 Hours"; 	
											$from_time         = new DateTime($detail['from_time']);
											$to_time           = new DateTime($detail['to_time']);
											$schedule_interval = $from_time->diff($to_time);
											$schedule_duration = $schedule_interval->format('%H : %I Hours');
											$remarks .= isset($detail['remark']) && !empty($detail['remark']) ?  $detail['remark'] . "<br>" : '';
											?>
											<tr style="<?=$bgClr;?>">
												<td style="font-size: 13px;"><?php echo date('h:i A', strtotime($detail['from_time'])); ?></td>
												<td style="font-size: 13px;"><?php echo date('h:i A', strtotime($detail['to_time'])); ?></td>												
												<td style="font-size: 13px;"><?php echo $detail['faculty_name']; ?></td>
												<td style="font-size: 13px;"><?php echo $detail['subject_name']; ?></td>
												<!--td style="font-size: 13px;"><?php echo $detail['batch_name']; ?></td-->
												<!--td style="font-size: 13px;"><?php echo $detail['assistant_name']; ?></td-->
												<td style="font-size: 13px;"><?php echo $detail['online_class_type']; ?></td>
												
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
						<?php if(!empty($remarks)) { ?>
						<table> <tbody><tr><td><b style="margin-top:2px;">Remark:- <?=$remarks?></b></td></tr></tbody></table>
						<?php } ?>
					</td>
					*/ ?>	
						
						
							
                    </tr>
                </tbody>
			
            </table>
			<p>&nbsp;</p>
			<?php }
			
			else{
				?>
				<table class="" cellpadding="1" style="width: 100%;font-size: 15px;border: solid 1px #dee2e6;">
				
                <tbody>
					<tr style="" >
                        <th colspan="7" style="background-color:#fff;">
						<b><?php echo $value->name; ?> 
						<?php if($value->type=='Offline'){ ?>
							--&nbsp;&nbsp;Capacity : <?php echo $value->capacity; ?>
						<?php } ?>
						</th>
                    </tr>
					<tr>
					<td style="vertical-align: top;">
							<table class="table1" cellpadding="1" style="width: 100%;font-size: 14px;border: solid 1px #dee2e6;">
								<tbody>
								<tr style="" >
									<th colspan="5" style="background-color:#fff;font-size: 14px;"><b>First Shift</b></th>
								</tr>
								<tr style="background-color: #fff;" >
									<th style='text-align:center;font-size: 13px;'>Start Time</th>
									<th style='text-align:center;font-size: 13px;'>End Time</th>
									<th style='text-align:center;font-size: 13px;'>Faculty Name</th>
									<th style='text-align:center;font-size: 13px;'>Subject Name</th>
									<!--th style='text-align:center;font-size: 13px;'>Batch Name</th>
									<th style='text-align:center;font-size: 13px;'>Assistant Name</th-->
									<th style='text-align:center;font-size: 13px;'>Class Type</th>
								</tr>
						</tbody>
						</table>
						
					</td>
					
					<td style="vertical-align: top;">
							<table class="table1" cellpadding="1" style="width: 100%;font-size: 14px;border: solid 1px #dee2e6;">
								<tbody>
								<tr style="" >
									<th colspan="5" style="background-color:#fff;font-size: 14px;"><b>Second Shift</b></th>
								</tr>
								<tr style="background-color: #fff;" >
									<th style='text-align:center;font-size: 13px;'>Start Time</th>
									<th style='text-align:center;font-size: 13px;'>End Time</th>
									<th style='text-align:center;font-size: 13px;'>Faculty Name</th>
									<th style='text-align:center;font-size: 13px;'>Subject Name</th>
									<!--th style='text-align:center;font-size: 13px;'>Batch Name</th>
									<th style='text-align:center;font-size: 13px;'>Assistant Name</th-->
									<th style='text-align:center;font-size: 13px;'>Class Type</th>
								</tr>
					
						</tbody>
						</table>
						
					</td>
						
						
						
							
                    </tr>
                </tbody>
			
            </table>
			<p>&nbsp;</p>
				<?php
			}
			}			
		?>
			</td>
			</tr>
			</tbody>
			</table>
        <?php }
			}	
		}
		?>
    </body>
	<style>
	body {
			font-family : 'Arial';
		}

	.table1 {
	  border-collapse: collapse;
	}

	.table1 td, .table1 th, .table2 th {
	  border: 1px solid #dee2e6;
	}

	.table1 tr:nth-child(even) {
	  //background-color: #ff9f43;
	}
	@media print {
		
	  @page {size: auto; margin: 20mm 0mm 20mm 0mm; }
	  body { margin: 1.6cm; }
	  
	  
	}
    </style>
</html>

<script src="{{ asset('laravel/public/admin/js/vendors.min.js') }}"></script>
<script>

$(document).ready(function () { 
	window.print(); 
	// window.close();
});
</script>