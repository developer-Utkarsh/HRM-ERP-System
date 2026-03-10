<button type="button" onClick="dataPrint()" class="noprint">Excel Export</button>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

    <head>

        <title><?php //echo $title; ?></title>

        <meta charset="utf-8">

            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    </head>
	

    <body id="printData">
		<div style="text-align:center;">
			<p>Batch Name : <span style="color:red"><?php echo e($batch_name); ?></span></p>
			<p>Course Name : <span style="color:red"><?php echo e($course_name); ?></span></p>
			<p>Start Date : <span style="color:red"><?php echo e($start_date); ?></span></p>
			<p>Subject Name : <span style="color:red"><?php echo e($subject_name); ?></span></p>
		</div>
       
		<table class="table1" cellpadding="4" style='width:100%;' border="1">

			<tbody>
				<tr style="font-size:12px;background-color: #ff9f43;" class="tHead">
					<th style='text-align:center;font-size:12px;width:50px;'>S. No.</th>
					<th style='text-align:center;font-size:12px'>Topic Name</th>
					<th style='text-align:center;font-size:12px'>Sub Topic Name</th>
					<th style='text-align:center;font-size:12px;width:100px;'>Duration</th>
					<th style='text-align:center;font-size:12px;width:100px;'>Date</th>
				</tr>
				
				<?php 
					$i =1;
					foreach($get_topic as $gt){ 
						// $sub_topic = 	DB::table('topic')
										// ->select('topic.name','duration')
										// ->where('chapter_id',$gt->id)	
										// ->where('status',1)										
										// ->get();
				?>
				<tr style="font-size:12px;" class="tHead">
					<td><?php echo e($i); ?></td> 
					<td><?php echo e($gt->name); ?></td>
					<td><?php echo e($gt->topic_name); ?>

						<?php 
							// $duration  = 0;
							// foreach($sub_topic as $st){ 
								// $duration = $st->duration + $duration;
								// echo $st->name.', '; 
							// }
						?>
					</td>
					<td><?php echo e($gt->topic_duration); ?> Minutes</td>
					<td><?php echo e($gt->tdate ? date('d-m-Y', strtotime($gt->tdate)) : ''); ?></td>
				</tr>
				<?php $i++; } ?>
				
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
	  border: thin solid #ff9f43;
	}

	
	
	.table1 .tHead th{
		border: thin solid #000;
	}
	
	
	@media  print {
		
	  @page  {size: auto; margin: 0; }
	  body { margin: 1.6cm; }
	  .noprint {	display: none;	}}
	}
    </style>

</html>

<script src="<?php echo e(asset('laravel/public/admin/js/vendors.min.js')); ?>"></script>
<script>

$(document).ready(function () { 
	window.print();	
});


function dataPrint(){
	//document.getElementById('noprint').style.display = 'none';
	
	var htmltable= document.getElementById('printData');
	var html = htmltable.innerHTML;
	window.open('data:application/vnd.ms-excel,' + encodeURIComponent(html));
}
</script>

<?php /**PATH /var/www/html/laravel/resources/views/admin/batch/pdf_html.blade.php ENDPATH**/ ?>