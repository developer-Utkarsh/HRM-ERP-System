
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-md-8">
						<h2 class="content-header-title float-left mb-0">Student Dashboards</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-md-4 text-right">
						<button type="button" class="btn btn-primary" id="downloadBtn">Export</button>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="multiple-column-form">
				<div class="match-height">					
					<div class="">						
						<!-- First -->
						
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<div class="table-responsive">
										<table class="table data-list-view" id="student_data">
											<thead>
												<tr>
													<th>S. No</th>
													<th>Category</th>
													<th>Batch Name</th>
													<th>Batch ID</th>
													<th>Student ID</th>
													<th>Student Name</th>
													<th>Student Contact No.</th>
													<th>Joining Date</th>
													<th>Total Amount Due</th>
													<th>Days Since Due</th>
													<th>Last Fees Paid Date</th>
													<th>Last Punch In Date</th>
													<th>Total Punch Ins (Last 30 days)</th>
													<th>Online Access Status</th>
													<th>Batch Status</th>
												</tr>
											</thead>
											<tbody>			
												<?php 
												    $thirtdayback=date("Y-m-d",strtotime(date("Y-m-d")." -30 days"));
													//die();
													$i=1;
													foreach($student as $st){
														/*$last_punch = DB::table('student_attendance')->selectraw("count(id) as prsent,date")
														->where('reg_no',$st->reg_number)
														->where('date','>',$thirtdayback)
														->orderby('id','DESC')->first();*/												
												?>
												<tr>
													<td><?php echo $i++?></td>
													<td><?php echo e($st->category); ?></td>
													<td><?php echo e($st->batch); ?></td>
													<td><?php echo e($st->batch_id); ?></td>
													<td><?php echo e($st->reg_number); ?></td>
													<td><?php echo e($st->s_name); ?></td>
													<td><?php echo e($st->contact); ?></td>
													<td><?php echo e(date('d-m-Y', strtotime($st->reg_date))); ?></td>
													<td><?php echo e($st->due_amount); ?></td>
													<td><?php echo e($st->due_days); ?></td>
													<td><?php echo e($st->receipt_date); ?></td>
													<td><?php echo e($last_punch->date??'-'); ?></td>
													<td><?php echo e($last_punch->prsent??'-'); ?></td>
													<td><?php echo e(ucwords($st->course_status)); ?></td>
													<td><?php echo e($st->batch_running_status); ?></td>
												</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
						
						
						
					</div>
				</div>
			</section>
		</div>
	</div>
</div>

<style>
	.dashboard b{
		font-size:22px;
		padding-bottom:10px;
	}
	
	.daywise thead th{
		font-size:11px !important; 
	}
</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$('.select-multiple1').select2({
		width: '100%',
		placeholder: "Select",
		allowClear: true
	});
});


// function ExportToExcel(){
   // var htmltable= document.getElementById('student_data');
   // var html = htmltable.outerHTML;
   // window.open('data:application/vnd.ms-excel,' + encodeURIComponent(html));
// }


$(document).ready(function() {
	$('#downloadBtn').click(function() {
		var table = document.getElementById('student_data');
		var wb = XLSX.utils.table_to_book(table, {sheet: "Sheet1"});
		var wbout = XLSX.write(wb, {bookType: 'xlsx', type: 'binary'});
		
		function s2ab(s) {
			var buf = new ArrayBuffer(s.length);
			var view = new Uint8Array(buf);
			for (var i = 0; i < s.length; i++) {
				view[i] = s.charCodeAt(i) & 0xFF;
			}
			return buf;
		}

		saveAs(new Blob([s2ab(wbout)], {type: "application/octet-stream"}), 'table_data.xlsx');
	});
});

function saveAs(blob, fileName) {
	var a = document.createElement('a');
	a.href = URL.createObjectURL(blob);
	a.download = fileName;
	a.click();
	URL.revokeObjectURL(a.href);
}

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/academic/student.blade.php ENDPATH**/ ?>