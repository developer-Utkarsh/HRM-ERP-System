
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Faculty Batch Report</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo e(route('studiomanager.dashboard')); ?>">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="<?php echo e(route('studiomanager.faculty-batch-reports')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-md-2">
											<label for="users-list-status">Faculty</label>
											<?php $users = \App\User::where('status', '1')->where('role_id', 2)->where('is_deleted', '0')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2 faculty_id" name="faculty_id" required>
													<option value="">Select Any</option>
													<?php if(count($users) > 0): ?>
													<?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" <?php if($value->id == app('request')->input('faculty_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php endif; ?>
												</select>												
											</fieldset>
										</div>
										<div class="col-md-2">
											<label for="users-list-role">From Date</label>
											<fieldset class="form-group">
												<input type="date" class="form-control from_date" name="from_date" value="<?php echo e($from_date); ?>">
											</fieldset>
										</div>
										
										<div class="col-md-2">
											<label for="users-list-role">To Date</label>
											<fieldset class="form-group">
												<input type="date" class="form-control to_date" name="to_date" value="<?php echo e($to_date); ?>">
											</fieldset>
										</div>
										
										<div class="col-12 col-md-4 mt-2">
										<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary" name="search" value="search">Search</button>
											<a href="<?php echo e(route('studiomanager.faculty-batch-reports')); ?>" class="btn btn-warning">Reset</a>
											<!--<a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a>-->
										</fieldset>
									</div>
									</div>
									
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<div class="text-right"><input id="myInput" type="text" placeholder="Search.." class="pr-5"></div>
					<table class="table data-list-view" style=''>					 
						<thead>
							<tr style="">
								<th scope="col">S. No.</th>
								<th scope="col">Batch Name</th>
								<th scope="col">Subejct</th>
								<th scope="col">Schedule Hour</th>
								<th scope="col">Spent Hour</th>
							</tr>
						</thead>
						<tbody  id="myTable">
							<?php
								$user_id 				= Request::get('user_id');
								$total_schedule_hours 	= new DateTime('00:00');
								$total_spent_hours 		= new DateTime('00:00');
								$total              	= new DateTime('00:00');
								$total_plan_hours 		= 0;
                               
								$batchs=DB::table('timetables')->select('timetables.batch_id','timetables.subject_id','subject.name as subject_name','batch.name as batch_name','users.name as faculty_name','timetables.faculty_id')
								->leftJoin('batch', 'batch.id', '=', 'timetables.batch_id')
								->leftJoin('users', 'users.id', '=', 'timetables.faculty_id')
								->leftJoin('subject', 'subject.id', '=', 'timetables.subject_id')
								->where('timetables.faculty_id',$faculty_id)
								->where('timetables.cdate','>=',$from_date)
								->where('timetables.cdate','<=',$to_date)
								->where('timetables.is_publish','1')
								->where('timetables.is_deleted','0')
								->groupby('timetables.batch_id')
								->groupby('timetables.subject_id')
								->get();
								
								if(count($batchs) > 0){ 
                               	    $i = 1;
									foreach($batchs as $get_batch){
									    $subject_id	=	$get_batch->subject_id;
									    $faculty_id	=	$get_batch->faculty_id;
									    $batch_id	=	$get_batch->batch_id;
										$faculty	=	"";
										$schedule_total_tt  = "00 : 00"; 
										$total_tt 	= "00 : 00"; 
										$rmTime  	= "0";
										
										$get_total_time = DB::table('timetables')
										->select('timetables.id as t_id','timetables.time_table_parent_id','timetables.faculty_id','timetables.from_time as start_time','timetables.to_time as end_time','users.name as user_name',
										'start_classes.start_time as start_classes_start_time','start_classes.end_time as start_classes_end_time')
										->leftJoin('start_classes', 'start_classes.timetable_id', '=', 'timetables.id')
										->leftJoin('users', 'users.id', '=', 'timetables.faculty_id')
										->where('timetables.subject_id',$subject_id)
										->where('timetables.faculty_id',$faculty_id)
										->where('timetables.batch_id', $batch_id)
										->where('timetables.cdate','>=',$from_date)
								        ->where('timetables.cdate','<=',$to_date)
										->where('timetables.is_publish','1')
										->where('timetables.is_deleted','0');
												
										if(!empty($from_date) && !empty($to_date)){
											$get_total_time->whereRaw("timetables.cdate >= '$from_date' and  timetables.cdate <= '$to_date'");
										}
										
										$get_total_time = $get_total_time->get();

										$faculty="";
												
										$base_time          = new DateTime('00:00');
										$base_time2          = new DateTime('00:00');
										$total              = new DateTime('00:00');
										$total2              = new DateTime('00:00');
										$subject_arr        = array();
										$schedule_total_tt  = "00 : 00"; 
										$total_tt = "00 : 00"; 
										$rmTime  = "0"; 
										if(count($get_total_time) > 0){
											foreach($get_total_time as $get_total_time_value){
												//array_push($subject_arr, $get_total_time_value->name);
												$first_time = new DateTime($get_total_time_value->start_time);
												$second_time = new DateTime($get_total_time_value->end_time);
												$interval = $first_time->diff($second_time);
												$base_time->add($interval);
												$total_schedule_hours->add($interval);
												
												if($get_total_time_value->time_table_parent_id > 0){
													$t_id = $get_total_time_value->time_table_parent_id;
												}
												else{
													$t_id = $get_total_time_value->t_id;
												}
												
												$interval_1 = 0;
												$get_class_time = DB::table('start_classes')
												->select('start_classes.start_time as start_classes_start_time','start_classes.end_time as start_classes_end_time')
												->where('timetable_id',$t_id)
												->first();
												if(!empty($get_class_time)){
													$first_date = new DateTime($get_class_time->start_classes_start_time);
													$second_date = new DateTime($get_class_time->start_classes_end_time);
													$interval_1 = $first_date->diff($second_date);
													$base_time2->add($interval_1);
													$total_spent_hours->add($interval_1);
												}
														
												$faculty=$get_total_time_value->user_name;
											} 											
																									
											$baseDays = $total->diff($base_time)->format("%a");
											$baseHours = $total->diff($base_time)->format("%H");
											$baseMinute = $total->diff($base_time)->format("%I");
											
											$schedule_total_tt = ($baseDays*24)+$baseHours. ":" . $baseMinute;
											
											$totalDays = $total2->diff($base_time2)->format("%a");
											$totalHours = $total2->diff($base_time2)->format("%H");
											$totalMinute = $total2->diff($base_time2)->format("%I");
											$totalHours = (($totalDays*24)+$totalHours);
											$total_tt = $totalHours. ":" . $totalMinute;													
										}

							?>
							<tr style="">
								<td><?=$i;?></td>
								<td class="<?php echo e($subject_id); ?>"><?=$get_batch->batch_name;?></td>
								<td class="<?php echo e($subject_id); ?>"><?=$get_batch->subject_name;?></td>
								
								<td><?php echo e($schedule_total_tt); ?>

								
								</td>
								<td><?php echo e($total_tt); ?>

								
								</td>
							</tr>
							<?php 
										$i++;  
									}
								}										
							?>
						</tbody>
					
					</table>
				
					<style>
						hr{background:#000;}
					</style>					 
				</div>       
			</section>
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple1').select2({
			placeholder: "Select Any",
			allowClear: true
		});
		$('.select-multiple2').select2({
			placeholder: "Select Any",
			allowClear: true
		});
		$('.select-multiple3').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
	
	$("body").on("click", "#download_excel", function (e) {
		var data = {};
		data.branch_location = $('.branch_location').val(),
		data.studio_id = $('.studio_id').val(),
		data.branch_id = $('.branch_id').val(),
		data.batch_id = $('.batch_id').val(),
		data.assistant_id = $('.assistant_id').val(),
		data.fdate = $('.fdate').val(),
		data.tdate = $('.tdate').val(),
		data.type = $('.type').val(),
		window.location.href = "<?php echo URL::to('/studiomanager/'); ?>/batch-report-report-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});
	
	$("body").on("click", "#download_pdf", function (e) {
		/* if ($userTable.data().count() == 0) {
			swal("Warning!", "Not have any data!", "warning");
			return;
		} */
		var data = {};
			data.branch_location = $('.branch_location').val(),
			data.studio_id = $('.studio_id').val(),
			data.branch_id = $('.branch_id').val(),
			data.batch_id = $('.batch_id').val(),
			data.assistant_id = $('.assistant_id').val(),
			data.fdate = $('.fdate').val(),
			data.tdate = $('.tdate').val(),
			data.type = $('.type').val(),
			// data.tdate = $('.tdate').val(),
			window.open("<?php echo URL::to('/studiomanager/'); ?>/batch-report-pdf?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&'));
		/* window.location.href = "<?php echo URL::to('/studiomanager/'); ?>/studio-report-pdf?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&'); */
	});
</script>
<script>
$(document).ready(function(){
  $("#myInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#myTable tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.studiomanager', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/studiomanager/batch_reports/faculty_batch_report.blade.php ENDPATH**/ ?>