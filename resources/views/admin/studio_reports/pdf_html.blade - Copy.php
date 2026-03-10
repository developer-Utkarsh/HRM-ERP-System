<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title><?php //echo $title; ?></title>
        <meta charset="utf-8">
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    </head>
    <body>
        <?php if (!empty($get_studios)) { ?>
		<?php foreach ($get_studios as $value) { ?>
            <table border="1" cellpadding="1" style='text-align: center;border-collapse:collapse;font-size: 16px;margin-bottom:20px;'>
			 
                <tbody>
					<tr style="">
                        <th colspan="3"><b>Studio Name : <?php echo $value->name; ?></b></th>
                        <th colspan="3"><b>Assistant Name : <?php echo isset($value->assistant->name) ?  $value->assistant->name : ''; ?></b></th>
                        <th colspan="3"><b>Assistant Mob. : <?php echo isset($value->assistant->mobile) ?  $value->assistant->mobile : ''; ?></b></th>
                    </tr>
					<tr>
                        <th style='text-align:center;'>From Time</th>
                        <th style='text-align:center;'>To Time</th>
                        <th style='text-align:center;'>Date</th>
                        <th style='text-align:center;'>Faculty Name</th>
                        <th style='text-align:center;'>Batch Name</th>
                        <th style='text-align:center;'>Course Name</th>
                        <th style='text-align:center;'>Subject Name</th>
                        <th style='text-align:center;'>Chapter Name</th>
                        <th style='text-align:center;'>Topic Name</th>
                    </tr>
					<?php
					foreach($value->timetable as $key => $timetable){
					?>
                        <tr>
                            <td><?php echo isset($timetable->from_time) ?  $timetable->from_time : '' ?></td>
							<td><?php echo isset($timetable->to_time) ?  $timetable->to_time : '' ?></td>
							<td><?php echo isset($timetable->cdate) ?  $timetable->cdate : '' ?></td>
							<td><?php echo isset($timetable->faculty->name) ?  $timetable->faculty->name : '' ?></td>
							<td><?php echo isset($timetable->batch->name) ?  $timetable->batch->name : '' ?></td>
							<td><?php echo isset($timetable->course->name) ?  $timetable->course->name : '' ?></td>
							<td><?php echo isset($timetable->subject->name) ?  $timetable->subject->name : '' ?></td>
							<td><?php echo isset($timetable->chapter->name) ?  $timetable->chapter->name : '' ?></td>
							<td><?php echo isset($timetable->topic->name) ?  $timetable->topic->name : '' ?></td>
                        </tr>
					<?php
					}
					?>
                </tbody>
			
            </table>
			<p>&nbsp;</p>
			<?php } ?>
        <?php } ?>
    </body>
</html>
