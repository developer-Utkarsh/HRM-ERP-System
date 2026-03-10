<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title><?php //echo $title; ?></title>
        <meta charset="utf-8">
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    </head>
    <body>
        <?php if (!empty($get_faculty)) {
			foreach ($get_faculty as $branchArray) {
				if(count($branchArray->user_branches) > 0){
			?>
			<table class="" >
				
				<tbody>
				<tr style="">
					<th><h3>Branch Name : <?php echo $branchArray->name; ?></h3></th>
				</tr>
				<tr style="">
				<td >
		<?php 
		$i = 0;
		foreach ($branchArray->user_branches as $value) {
		if(!empty($value->user)){
		if($i==0){
			// For border full With 
			?>
			<table>
			<tbody>
			<tr style="" >
			 <th colspan="8"></th>
			</tr>
			</tbody>
			</table>
			<?php
		}
		$i++;
		?>
            <table class="table1" cellpadding="1" style='' border="1">
			 
                <tbody>
					<tr style="" >
                        <th colspan="8"><b>Faculty Name : <?php echo $value->user->name; ?></b></th>
                    </tr>
					<tr>
                        <th style='text-align:center;'>From Time</th>
                        <th style='text-align:center;'>To Time</th>
                        <th style='text-align:center;'>Date</th>
                        <th style='text-align:center;'>Batch Name</th>
                        <th style='text-align:center;'>Course Name</th>
                        <th style='text-align:center;'>Subject Name</th>
                        <th style='text-align:center;'>Chapter Name</th>
                        <th style='text-align:center;'>Topic Name</th>
                    </tr>
					<?php
					if(count($value->user->timetable) > 0){
					foreach($value->user->timetable as $key => $timetable){
					?>
                        <tr>
                            <td><?php echo isset($timetable->from_time) ?  $timetable->from_time : '' ?></td>
							<td><?php echo isset($timetable->to_time) ?  $timetable->to_time : '' ?></td>
							<td><?php echo isset($timetable->cdate) ?  date('d-m-Y',strtotime($timetable->cdate)) : '' ?></td>
							<td><?php echo isset($timetable->batch->name) ?  $timetable->batch->name : '' ?></td>
							<td><?php echo isset($timetable->course->name) ?  $timetable->course->name : '' ?></td>
							<td><?php echo isset($timetable->subject->name) ?  $timetable->subject->name : '' ?></td>
							<td><?php echo isset($timetable->chapter->name) ?  $timetable->chapter->name : '' ?></td>
							<td><?php echo isset($timetable->topic->name) ?  $timetable->topic->name : '' ?></td>
                        </tr>
					<?php
					}
					}
					?>
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
			<p>&nbsp;</p>
        <?php }
			}	
		}
		?>
    </body>
	<style>
      @page {
        size: a4 landscape; 
      } 
      body {
        font-family: Times New Roman;
        text-align: center;
        border: thin solid black;
		width:100%;
      }
    </style>
</html>
