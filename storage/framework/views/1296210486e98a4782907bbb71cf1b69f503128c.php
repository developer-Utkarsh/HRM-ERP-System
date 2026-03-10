<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Timetable </title>
        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    </head>
	<div style="text-align:center;">		
		<h3>Timetable : FROM <?=date('d/m/Y',strtotime($fdate));?> TO <?=date('d/m/Y',strtotime($tdate));?></h3>
	</div>
    <body>
        <?php if (!empty($get_batches)) {
			foreach ($get_batches as $batchArray) {
			?>
			<table class="table2" style="border:1px solid #dee2e6;width: 100%;" >
				<tbody>
					<tr style="">
						<th style="background-color:#1674ba;font-size: 24px;border:0;color:#FFF;"><b style="font-weight: 700;">Batch Name : </b> <span style="font-weight: 100;"><?php echo $batchArray->name; ?> </span>
						</th>
					</tr>
					<tr style="">
						<td>
							<table class="" cellpadding="1" style="width: 100%;font-size: 15px;">
								
								<tbody>
									<tr>
										<td style="vertical-align: top;">
											<table class="table1" cellpadding="1" style="width: 100%;font-size: 14px;border: solid 1px #dee2e6;">
												<tbody>
													<tr>
														<th style='text-align:center;font-size: 13px;'>Branch Name</th>
														<th style='text-align:center;font-size: 13px;'>Studio Name</th>
														<th style='text-align:center;font-size: 13px;'>Assistant Name</th>
														<th style='text-align:center;font-size: 13px;'>Start Time</th>
														<th style='text-align:center;font-size: 13px;'>End Time</th>
														<th style='text-align:center;font-size: 13px;'>Date</th>
														<th style='text-align:center;font-size: 13px;'>Faculty Name</th>
														<th style='text-align:center;font-size: 13px;'>Course Name</th>
														<th style='text-align:center;font-size: 13px;'>Subject Name</th>
														<th style='text-align:center;font-size: 13px;'>Chapter Name</th>
														<th style='text-align:center;font-size: 13px;'>Topic Name</th>
														<th style='text-align:center;font-size: 13px;'>Type</th>
														<th style='text-align:center;font-size: 13px;'>Schedule Time</th>
													</tr>
													
													<?php
														foreach ($batchArray->batch_timetables as $value) {
													?>
													<?php
														
														$schedule_duration  = "00 : 00 Hours"; 	
														$from_time         = new DateTime($value->from_time);
														$to_time           = new DateTime($value->to_time);
														$schedule_interval = $from_time->diff($to_time);
														$schedule_duration = $schedule_interval->format('%H : %I Hours');
														if(!empty($value->studio) && !empty($value->studio->branch->name)){
													?>

													<tr style="">												
														<td style="font-size: 13px;"><?php echo !empty($value->studio->branch->name) ? $value->studio->branch->name : ''; ?></td>
														<td style="font-size: 13px;"><?php echo !empty($value->studio->name) ? $value->studio->name : ''; ?></td>
														<td style="font-size: 13px;"><?php echo !empty($value->assistant->name) ? $value->assistant->name : ''; ?></td>
														<td style="font-size: 13px;"><?php echo date('h:i A', strtotime($value->from_time)); ?></td>
														<td style="font-size: 13px;"><?php echo date('h:i A', strtotime($value->to_time)); ?></td>
														<td style="font-size: 13px;"><?php echo !empty($value->cdate) ? date('d-m-Y',strtotime($value->cdate)) : ''; ?></td>
														<td style="font-size: 13px;"><?php echo !empty($value->faculty->name) ? $value->faculty->name : ''; ?></td>
														<td style="font-size: 13px;"><?php echo !empty($value->course->name) ? $value->course->name: ''; ?></td>
														<td style="font-size: 13px;"><?php echo !empty($value->subject->name) ? $value->subject->name: '' ?></td>
														<td style="font-size: 13px;"><?php echo !empty($value->chapter->name) ? $value->chapter->name : ''; ?></td>
														<td style="font-size: 13px;"><?php echo !empty($value->topic->name) ? $value->topic->name : ''; ?></td>
														<td style="font-size: 13px;"><?php echo e($value->online_class_type??''); ?> - <?php echo e($value->id??''); ?></td>
														<td style="font-size: 13px;"><?php echo !empty($schedule_duration) ? $schedule_duration : ''; ?></td>
														
													</tr>
													<?php } } ?>
												</tbody>
											</table>
										</td>	
									</tr>
								</tbody>
							
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			<?php }} ?>
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
	@media  print {
		
	  @page  {size: auto; margin: 0; }
	  body { margin: 1.6cm; }
	  
	}
    </style>
</html>

<script src="<?php echo e(asset('laravel/public/admin/js/vendors.min.js')); ?>"></script>
<script>

$(document).ready(function () { 
	window.print(); 
	// window.close();
});
</script><?php /**PATH /var/www/html/laravel/resources/views/admin/batch_reports/pdf_html.blade.php ENDPATH**/ ?>