<button type="button" onClick="dataPrint()" class="noprint" id="noprint">Excel Export</button>

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
    <body id="printData">
		<table class="table data-list-view" style="border:1px solid #dee2e6;width: 100%;" border="1" cellpadding="3" cellspacing="0">
			<thead style="background-color:#1674ba;font-size: 16px;border:0;color:#FFF;">
				<tr>
					<th>S. No.</th>
					<th>Date</th>
					<th>Title</th>
					<th>Assigned By</th>
					<th>Assigned To</th>
					<th>Plan Hour</th>			
					<th>Spent Hour</th>			
					<th>Total Task</th>										
				</tr>
			</thead>
			<tbody >
				<?php 
					if(count($task) > 0){
						$i = 1; 
						foreach($task as $t){
							$t1 = date('Y-m-d H:i:s', strtotime( $t->created_at ));
							$t2 = date('Y-m-d H:i:s', strtotime('-2 day'));
				?>
					<tr>
						<td>{{ $i }}</td>
						<td><?php echo date("d-m-Y", strtotime($t->date)); ?></td>
						<td>{{ $t->title }}</td>								
						<td>{{ $t->assign_name }}</td>								
						<td>{{ $t->emp_name }}</td>								
						<td>{{ $t->plan }}</td>								
						<td>{{ $t->spent }}</td>								
						<td>{{ $t->total }}</td>
					</tr>	
				<?php
					$sub = DB::table('task_key_points')->where('task_id',$t->id)->get();
					foreach($sub as $svalue){	
				?>
					<tr style="background-color:#e4f4ff;font-size: 14px;border:0;color:#000;">					
						<td colspan="6">{{ $svalue->description }}</td>						
						<td colspan="8">{{ $svalue->status }}</td>
					</tr>
	
				<?php 
					}
					$i++; }
					}else{
				?>
				<tr>
					<td colspan="10" class="text-center">No Record Found</td>
				</tr>	
				
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
	  .noprint { display: none; }

	  
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
	document.getElementById('noprint').style.display = 'none';
	
	var htmltable= document.getElementById('printData');
	var html = htmltable.innerHTML;
	window.open('data:application/vnd.ms-excel,' + encodeURIComponent(html));
}
</script>