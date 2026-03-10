
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Batch Hours Report</h2>
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
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="<?php echo e(route('admin.batch-hours-reports')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-md-2">
											<label for="users-list-status">Batch</label>
											<?php $batchs = \App\Batch::where('status', '1')->where('is_deleted', '0')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2 batch_id" name="batch_id">
													<option value="">Select Any</option>
													<?php if(count($batchs) > 0): ?>
													<?php $__currentLoopData = $batchs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" <?php if($value->id == app('request')->input('batch_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php endif; ?>
												</select>												
											</fieldset>
										</div>
										<div class="col-md-2">
											<label for="users-list-role">From Date</label>
											<fieldset class="form-group">
												<input type="date" class="form-control from_date" name="from_date" value="<?php if(!empty(Request::get('from_date'))): ?><?php echo e(Request::get('from_date')); ?><?php endif; ?>">
											</fieldset>
										</div>
										
										<div class="col-md-2">
											<label for="users-list-role">To Date</label>
											<fieldset class="form-group">
												<input type="date" class="form-control to_date" name="to_date" value="<?php if(!empty(Request::get('to_date'))): ?><?php echo e(Request::get('to_date')); ?><?php endif; ?>">
											</fieldset>
										</div>
										<div class="col-12 col-md-6 mt-2">
										<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="<?php echo e(route('admin.batch-hours-reports')); ?>" class="btn btn-warning">Reset</a>
											<!--<a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a>-->
										</fieldset>
									</div>
									</div>
									
								</form>
							</div>
							<?php
							if(!empty($batch_details)){
							?>
							<div>
								<p >
									<b style="background: yellow;">Batch Start Date : <?=date('d-m-Y',strtotime($batch_details->start_date))?></b>
									
									&nbsp;&nbsp;
									<b style="background: blue;color:#fff">
										Total Day : 
										<?php
											$startDate = date('d-m-Y', strtotime($batch_details->start_date));
											
											// $currentDate = date('d-m-y');
											$currentDate = date('d-m-y', strtotime('+1 day'));

											$startDateTime = DateTime::createFromFormat('d-m-Y', $startDate);
											$currentDateTime = DateTime::createFromFormat('d-m-y', $currentDate);

											$dateDifference = $startDateTime->diff($currentDateTime);

											// Output the difference in days
											echo $dateDifference->days;
										?>
									</b>
									&nbsp;&nbsp; 
									<b style="background: green;color:#fff">
										Working Day : 
										<?php 
											$totalUniqueCount = DB::table(function ($query) use ($batch_id) {
											$query->selectRaw('COUNT(DISTINCT batch_id) AS unique_count')
												->from('timetables')
												->where('batch_id', $batch_id)
												->where('is_deleted', '0')
												->where('is_publish', '1')
												->where('is_cancel', 0)
												->groupBy('cdate');
										}, 'grouped_counts')
										->selectRaw('SUM(unique_count) AS total_unique_count')
										->value('total_unique_count');

											
											echo $totalUniqueCount;

										?>
										</b>
									&nbsp;&nbsp;
									<b style="background: red;color:#fff">
										Off Day : <?php echo $dateDifference->days - $totalUniqueCount; ?>
									</b>
								</p>
							</div>
							<?php
							}
							?>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<div class="text-right"><input id="myInput" type="text" placeholder="Search.." class="pr-5"></div>
					<table class="table data-list-view" style=''>					 
						<thead>
							<tr style="">
								<th scope="col">S. No.</th>
								<th scope="col">Subject</th>
								<th scope="col">Faculty</th>
								<th scope="col">Plan Hour</th>
								<th scope="col">Schedule Hour</th>
								<th scope="col">Spent Hour</th>
								<th scope="col">Remaining</th>
							</tr>
						</thead>
						<tbody  id="myTable">
							<?php
								$user_id 				= Request::get('user_id');
								$batch_id 				= Request::get('batch_id');
								$from_date 				= Request::get('from_date');
								$to_date 				= Request::get('to_date');
								$total_schedule_hours 	= new DateTime('00:00');
								$total_spent_hours 		= new DateTime('00:00');
								$total              	= new DateTime('00:00');
								$total_plan_hours 		= 0;
                               
								$get_subjects=DB::table('batchrelations')->select('batchrelations.subject_id','batchrelations.no_of_hours','subject.name as subject_name')
								->leftJoin('subject', 'subject.id', '=', 'batchrelations.subject_id')
								->where('batchrelations.batch_id',$batch_id)
								->where('batchrelations.is_deleted','0')
								->get();
								
                if(count($get_subjects) > 0){ 
                  $i = 1;
									foreach($get_subjects as $get_subject){
									  $subject_id=$get_subject->subject_id;
										$faculty="";
										$schedule_total_tt  = "00 : 00"; 
										$total_tt = "00 : 00"; 
										$rmTime  = "0";
										$get_faculty = DB::table('timetables')->select('faculty_id')
												->where('subject_id',$subject_id)
												->where('batch_id', $batch_id);
									
										
										//->where('time_table_parent_id', '0')
										$get_faculty = $get_faculty->where('is_deleted', '0')->groupBy('faculty_id')->get();
												
										if(count($get_faculty) > 0){
											foreach($get_faculty as $f_detail){
												$faculty_id = $f_detail->faculty_id;
												$get_total_time = DB::table('timetables')
												->select('timetables.id as t_id','timetables.time_table_parent_id','timetables.faculty_id','timetables.from_time as start_time','timetables.to_time as end_time','users.name as user_name')
												->leftJoin('users', 'users.id', '=', 'timetables.faculty_id')
												->where('timetables.subject_id',$subject_id)
												->where('timetables.batch_id', $batch_id);
												
											
												$get_total_time->where('timetables.faculty_id', $faculty_id);
												$get_total_time->where('timetables.is_deleted', '0');
												$get_total_time->where('timetables.is_publish', '1');
												$get_total_time->where('timetables.is_cancel', 0);
												
												if(!empty($from_date) && !empty($to_date)){
													$get_total_time->whereRaw("timetables.cdate >= '$from_date' and  timetables.cdate <= '$to_date'");
												}

												$get_total_time = $get_total_time->get();

												//echo count($get_total_time);
												//echo "-".$faculty_id."<br>";

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
															//echo "parent<br>";
														}
														else{
															$t_id = $get_total_time_value->t_id;
															//echo "main<br>";
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

													// $rmTime=$get_subject->no_of_hours-$totalHours-$totalMinute/100;
													// $rmTime=number_format($rmTime,2);
													
													$h1s = $get_subject->no_of_hours*3600;
													$h2s = $totalHours*3600 + $totalMinute *60;
													$rmTime = "";;
													if($h1s > $h2s){
														$seconds = $h1s - $h2s;
													}
													else{
														$rmTime="-";
														$seconds = $h2s - $h1s;
													}
													$rmTime =$rmTime . sprintf("%02d:%02d", floor($seconds / 3600), ($seconds / 60) % 60);
													// $rmTime = $rmTime . floor($seconds / 3600) .":".($seconds / 60) % 60;
													
												}

												?>
												<tr style="">
													<td><?=$i;?></td>
													<td class="<?php echo e($subject_id); ?>"><?=$get_subject->subject_name;?> - <?=$subject_id;?></td>
													<td><?=$faculty;?></td>
													<td><?php 
													echo $get_subject->no_of_hours;
													$total_plan_hours += $get_subject->no_of_hours;
													?>
													
													</td>
													<td><?php echo e($schedule_total_tt); ?>

													
													</td>
													<td><?php echo e($total_tt); ?>

													
													</td>
													<td><?php echo e($rmTime); ?>

													
													</td>
												</tr>
											<?php $i++;  }
										}
										else{
											?>
											<tr style="">
												<td><?=$i;?></td>
												<td class="<?php echo e($subject_id); ?>"><?=$get_subject->subject_name;?></td>
												<td><?=$faculty;?></td>
												<td><?php
												echo $get_subject->no_of_hours;
												$total_plan_hours += $get_subject->no_of_hours;
												?></td>
												<td><?php echo e($schedule_total_tt); ?>

												
												</td>
												<td><?php echo e($total_tt); ?>

												
												</td>
												<td><?php echo e($get_subject->no_of_hours); ?>

												
												</td>
											</tr>
											<?php
											$i++;
										}
									}
                } ?>
							   
							   <tr style="">
									<td colspan="3">Total</td>
									<td><?php echo e($total_plan_hours); ?></td>
									<td>
									<?php
									$baseDays = $total->diff($total_schedule_hours)->format("%a");
									$baseHours = $total->diff($total_schedule_hours)->format("%H");
									$baseMinute = $total->diff($total_schedule_hours)->format("%I");
									
									echo ($baseDays*24)+$baseHours. ":" . $baseMinute;
									?>
									</td>
									<td>
									<?php
									$baseDays = $total->diff($total_spent_hours)->format("%a");
									$baseHours = $total->diff($total_spent_hours)->format("%H");
									$baseMinute = $total->diff($total_spent_hours)->format("%I");
									$totalHours = ($baseDays*24)+$baseHours;
									echo $totalHours. ":" . $baseMinute;
									?>
									</td>
									<td>
									<?php
									$h1s = $total_plan_hours*3600;
									$h2s = $totalHours*3600 + $baseMinute *60;
									$rmTime = "";;
									if($h1s > $h2s){
										$seconds = $h1s - $h2s;
									}
									else{
										$rmTime="-";
										$seconds = $h2s - $h1s;
									}
									echo $rmTime . sprintf("%02d:%02d", floor($seconds / 3600), ($seconds / 60) % 60);
									?>
									</td>
								</tr>
								
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
		window.location.href = "<?php echo URL::to('/admin/'); ?>/batch-report-report-excel?" + Object.keys(data).map(function (k) {
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
			window.open("<?php echo URL::to('/admin/'); ?>/batch-report-pdf?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&'));
		/* window.location.href = "<?php echo URL::to('/admin/'); ?>/studio-report-pdf?" + Object.keys(data).map(function (k) {
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/batch_reports/batch_hours_report.blade.php ENDPATH**/ ?>