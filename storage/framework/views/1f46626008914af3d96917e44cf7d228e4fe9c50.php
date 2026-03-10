
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-md-8">
						<h2 class="content-header-title float-left mb-0">Attendance Dashboard</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="multiple-column-form">
				<div class="match-height">
					<?php if(!empty($overall['active_batches'])): ?>
						<div class="row text-center dashboard">
							<div class="col card m-1 p-2" style="background:#FEFFE9">
								<b><?php echo e($overall['active_batches']); ?></b>
								Total Active Bataches
							</div>
							<div class="col card m-1 p-2" style="background:#F1F7FF">
								<b><?php echo e($overall['enrolled']); ?></b>
								Enrolled Students
							</div>
							<div class="col card m-1 p-2" style="background:#FFD9D9">
								<b><?php echo e($overall['due_punch']??0); ?></b>
								Due Fee Punch-In Students
							</div>
							<div class="col card m-1 p-2" style="background:#ddfce6">
								<b> <?php if($overall['total_punch']>0): ?> <?php echo e(round($overall['due_punch']*100/$overall['total_punch'],2)); ?>% <?php else: ?> 0% <?php endif; ?></b>
								Due Fee Punch-In Percentage
							</div>
						</div>
					<?php endif; ?>

					<form action="<?php echo e(route('admin.academic.attendance')); ?>" method="get" class="mb-0">
						<?php echo csrf_field(); ?>
						<div class="card row mx-0 mb-0 p-1">
							<div class="row mx-0 w-100">
								<div class="col-3">
									<input type="date" name="fdate" value="<?php echo e($_GET['fdate']??''); ?>" class="form-control"/>
								</div>
								<div class="col-3">
									<input type="date" name="tdate" value="<?php echo e($_GET['tdate']??''); ?>" class="form-control"/>
								</div>
								<div class="col-3">
									<select name="course_id" class="form-control select-multiple1 course_id">
										<option value="">Select Course</option>
										<?php $__currentLoopData = $course; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $co): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<option value="<?php echo e($co->Co_id); ?>" <?php if(isset($_GET['course_id']) && $_GET['course_id']==$co->Co_id): ?> Selected <?php endif; ?>><?php echo e($co->course_name); ?></option>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									</select>
								</div>
								<div class="col-3">
									<select name="batch_id" class="form-control select-multiple1 fill-name">
										<option value="">Select Batch</option>
									</select>
								</div>
							</div>
							<div class="col-6 pt-2 text-right ml-auto">
								<button type="submit" class="btn btn-primary">Submit</button>
								<a href="<?php echo e(route('admin.academic.attendance')); ?>" class="btn btn-warning">Reset</a>
							</div>
						</div>
					</form>
					
					
					<div class="mt-2">						
						<!-- Upcomming -->
						<div class="card">
							<div class="card-content">
								<div class="card-body">
									<div class="pb-1"><b>DAY WISE ATTENDANCE AND DUE FEE</b></div>
									<div class="table-responsive">
										<table class="table data-list-view daywise">
											<thead>
												<tr>
													<th>Date</th>
													<th>Active Batches</th>
													<th>Enrolled</th>
													<th>Total Punch-Ins</th>
													<th>Percentage</th>
													<th>Due Date Student Count</th>
													<th>Student Details (Dues Fees)</th>
												</tr>
											</thead>
											<tbody>
												<?php $__currentLoopData = $record; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<tr>
													<td><?php echo e(date("d-M-Y",strtotime($val['date']))); ?></td>
													<td><?php echo e($val['active_batches']); ?></td>
													<td><?php echo e($val['enrolled']); ?></td>
													<td><?php echo e($val['total_punch']); ?></td>
													<td> <?php if($val['enrolled']>0): ?> <?php echo e(round($val['total_punch']*100/$val['enrolled'],2)); ?>% <?php else: ?> - <?php endif; ?></td>
													<td><?php echo e($val['due_student']); ?></td>
													<td>
														<a href="javascript:void(0)" target="_blank" id="download_excel" data-date="<?php echo e($val['date']); ?>"  data-location="<?php echo e($location); ?>" class="btn btn-primary btn-sm">Export</a>
													</td>
												</tr>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
						
					
						
						<div class="">
							<div class="card p-5">
								<h5>DOD Attendance</h5>
								<canvas id="chartDOD"></canvas>
							</div>
						</div>
						
						<div class="d-none">
							<div class="card p-5">
								<h5>WOW Attendance</h5>
								<canvas id="chartMOM"></canvas>
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
<script type="text/javascript">
$(document).ready(function() {
	$('.select-multiple1').select2({
		width: '100%',
		placeholder: "Select",
		allowClear: true
	});
});

$(".course_id").change(function(){
	course_id = $(this).val();
	$.ajax({
		type : 'POST',
		url : '<?php echo e(route('admin.get-coursewise-batch')); ?>',
		data : {'_token' : '<?php echo e(csrf_token()); ?>', 'course_id': course_id},
		dataType : 'html',
		success : function (data){
			$('.fill-name').empty();
			
			$('.fill-name').html(data);
		}
	});	
});
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script>
  var chart_dod=JSON.parse('<?php echo json_encode($record);?>');
  var chart_mom=JSON.parse('<?php echo json_encode($chart_mom);?>');
  chartDOD(chart_dod);
  chartMOM(chart_mom);
  
  console.log(chart_mom);

  function chartDOD(chart_dod){
	  var labels=[];
	  var present=[];
	  var enrolled=[];
	  var due_present=[];
	  for(var i=0;i<chart_dod.length;i++){
	  	//console.log(chart_dod[i]);
	    labels[i]=chart_dod[i]['day'];
	    enrolled[i]    =chart_dod[i]['enrolled'];
	    present[i]    =chart_dod[i]['present_precent'];
	    due_present[i]=chart_dod[i]['due_precent'];
	    //console.log(chart_dod[i]['week'])
	    if(i>=6){
        break;
	    }
	  }

	  const ctx = document.getElementById('chartDOD');

	  new Chart(ctx, {
	    type: 'line',
	    data: {
	      labels: labels,
	      datasets: [
		      {
		        label: 'Total Present',
		        data: present,
		        borderWidth: 1,
		        borderColor:'#7869ea',
		        backgroundColor:'#7869ea',
		      },
		      {
		        label: 'Due Fee Present',
		        data: due_present,
		        borderWidth: 1,
		        borderColor:'#ff9f43',
		        backgroundColor:'#ff9f43',
		      }
	      ]
	    },
	    options: {
	    	plugins: {
          legend: {
            position: 'top',
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                return context.dataset.label + ': ' + context.raw;
              }
            }
          },
          datalabels: {
            anchor: 'end',
            align: 'top',
            formatter: (value, context) => {
                return value+'%';
            },
            color: '#000',
            font: {
              weight: 'normal'
            }
          }
        },
	      scales: {
	        y: {
            min: 0,
            max: 100,
            ticks: {
              stepSize: 10,
              callback: function (value, index, values) {
                return value + " %";
              }
            }
          }
	      }
	    },
	    plugins: [ChartDataLabels]
	  });
	}

	function chartMOM(chart_dod){
	  var labels=[];
	  var present=[];
	  var due_present=[];
	  for(var i=0;i<chart_dod.length;i++){
	  	//console.log(chart_dod[i]);
	    labels[i]=chart_dod[i]['week'];
	    present[i]=chart_dod[i]['present'];
	    due_present[i]=chart_dod[i]['due_present'];
	    //console.log(chart_dod[i]['week'])
	  }

	  const ctx = document.getElementById('chartMOM');
	  new Chart(ctx, {
	    type: 'line',
	    data: {
	      labels: labels,
	      datasets: [
		      {
		        label: 'Total Present',
		        data: present,
		        borderWidth: 1,
		        borderColor:'#7869ea',
		        backgroundColor:'#7869ea',
		      },
		      {
		        label: 'Due Fee Present',
		        data: due_present,
		        borderWidth: 1,
		        borderColor:'#ff9f43',
		        backgroundColor:'#ff9f43',
		      }
	      ]
	    },
	    options: {
	      scales: {
	        y: {
            /*min: 0,
            max: 100,
            ticks: {
              stepSize: 10,
              callback: function (value, index, values) {
                return value + " %";
              }
            }*/
          }
	      }
	    }
	  });
	}

	/*$.ajax({
		type : 'POST',
		url : '<?php echo e(route('admin.academic.attendancechartmom')); ?>',
		data : {'_token' : '<?php echo e(csrf_token()); ?>',month:'2024-06'},
		dataType : 'json',
		success : function (data){
			console.log(data);
		}
	});*/

</script>

<script>
$("body").on("click", "#download_excel", function (e) {
	var data = {};
	data.data_date = $(this).attr("data-date"),
	data.data_location = $(this).attr("data-location")			
	window.location.href = "<?php echo URL::to('/admin/'); ?>/due-student-report-excel?" + Object.keys(data).map(function (k) {
		return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
	}).join('&');
	
});

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/academic/attendance.blade.php ENDPATH**/ ?>