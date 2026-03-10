<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Task Report </title>

        <meta charset="utf-8">
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    </head>
	<div style="background-color:#1674ba;font-size: 24px;border:0;color:#FFF;text-align:center">		
		<h3>Task Report</h3>
	</div>
    <body>
		<table class="table data-list-view" style="border:1px solid #dee2e6;width: 100%;" border="1" cellpadding="3" cellspacing="0">
			<thead style="background-color:#1674ba;font-size: 18px;border:0;color:#FFF;">
				<tr>
					<th style="width:5%">S. No.</th>
					<th style="width:15%">Employee Name</th>
					<th style="width:10%">Date</th>
					<th>Task</th>
					<th>Status</th>
					<th style="width:30%">Remark</th>
				</tr>
			</thead>
			<tbody >
			<?php
				if(count($taskDate) > 0){
				$i = 1;						    
				$statusArray = array();
				// echo "<pre>"; print_r($task); die;
				foreach($taskDate as  $key => $value){
					$date = $value['date'];
					if(!empty($value['employees'])){
						foreach($value['employees'] as $employee){
							$emp_id = $employee['emp_id'];
							$employee_details = DB::table('users')->where('id', $emp_id)->first();
						?>
						<tr >
							<td><?=$i++;?></td>
							<td class="product-category">{{ $employee_details->name ?  $employee_details->name : '' }}</td>
							<td class="product-category">{{ date('d-m-Y',strtotime($date)) }}</td>
							<td class="product-category" colspan="2">
							<table class="table data-list-view" style="width:100%;background: #f7f7f73d;margin:5px;" border="1" cellspacing="0" cellpadding="5">
								<?php
								foreach($employee['task_array'] as $task_details){
									
									?>
									<tr>
										<td style="width:200px;"><?=$task_details['description']?></td>
										<td><?=$task_details['status']?></td>
									</tr>
									<?php
								}
								?>
							</table>
							</td>
							<td class="product-category"><?=$task_details['dropped_reason']?></td>
						</tr>
						<?php
						$statusArray = array();
						}
					}
				}
				}else{	
				?>
					<tr><td class="text-center text-primary" colspan="9">No Record Found</td></tr>	
				<?php } ?>
				
			</tbody>
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
	  border: 1px solid #dee2e6;
	}

	.table1 tr:nth-child(even) {
	  //background-color: #ff9f43;
	}
	@media print {
		
	  @page {size: auto; margin: 0; }
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