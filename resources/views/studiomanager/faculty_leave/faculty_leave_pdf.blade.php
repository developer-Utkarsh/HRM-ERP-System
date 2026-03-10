<button type="button" onClick="dataPrint()" class="noprint">Excel Export</button>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Faculty Leave </title>

        <meta charset="utf-8">
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    </head>
	<div style="text-align:center;">		
		<!-- <h3 style="color:red;">We are wotking on this report please wait...</h3>-->
		<h3>Faculty Leave </h3>
	</div>
    <body id="printData">
		<table class="table1" style="border:1px solid #dee2e6;width: 100%;" >
				
				<tbody>
				<tr style="">
					<th style="background-color:#1674ba;font-size: 24px;border:0;color:#FFF;">Name</th>
					<th style="background-color:#1674ba;font-size: 24px;border:0;color:#FFF;">Date</th>
					<th style="background-color:#1674ba;font-size: 24px;border:0;color:#FFF;">Mobile</th>
					<th style="background-color:#1674ba;font-size: 24px;border:0;color:#FFF;">Reason</th>
				</tr>
			
        <?php if (!empty($get_data)) {
			foreach ($get_data as $faculty) {
			?>
			<tr style="" class="rowdata">
				<td><?php echo $faculty->name?></td>
				<td><?php echo date('d-m-Y',strtotime($faculty->date))?></td>
				<td><?php echo $faculty->mobile?></td>
				 
				<td><?=$faculty->reason?></td>
				
				
				
				
				</tr>
		<?php
			}
		}
		?>
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
		
	  @page {size: auto; margin: 20mm 0mm 20mm 0mm; }
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