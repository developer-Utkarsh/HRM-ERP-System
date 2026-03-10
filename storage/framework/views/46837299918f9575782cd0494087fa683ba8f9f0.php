
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Subject Wise Report</h2>
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
								<form action="<?php echo e(route('admin.subject-reports')); ?>" method="get" name="filtersubmit">
									<div class="row">
									
										<div class="col-12 col-sm-6 col-lg-3 branch_loader">
											<label for="users-list-status">Branch</label>
											<?php $branches = \App\Branch::where('status', '1')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 branch_id" name="branch_id" id="">
													<option value="">Select Any</option>
													<?php if(count($branches) > 0): ?>
													<?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" <?php if($value->id == app('request')->input('branch_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php endif; ?>
												</select>
												<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
											</fieldset>
										</div>
										
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Subject</label>
											<?php $subjects = \App\Subject::where('status', '1')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2 subject_id" name="subject_id">
													<option value="">Select Any</option>
													<?php if(count($subjects) > 0): ?>
													<?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" <?php if($value->id == app('request')->input('subject_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php endif; ?>
												</select>												
											</fieldset>
										</div>
										
										 <div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">From Date</label>
											<fieldset class="form-group">												
												<input type="date" name="fdate" placeholder="Date" value="<?php echo e(app('request')->input('fdate')); ?>" class="form-control StartDateClass fdate">
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">To Date</label>
											<fieldset class="form-group">												
												<input type="date" name="tdate" placeholder="Date" value="<?php echo e(app('request')->input('tdate')); ?>" class="form-control EndDateClass tdate">
											</fieldset>
										</div>
									</div>
									<fieldset class="form-group" style="float:right;">		
									<button type="submit" class="btn btn-primary">Search</button>
									<a href="<?php echo e(route('admin.subject-reports')); ?>" class="btn btn-warning">Reset</a>
									<a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a>
									</fieldset>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
				<table class="table data-list-view">
					<thead>
						<tr>
							<th>S.No.</th>
							<th>Subject</th>
							<th>Branch</th>
							<th>Faculty</th>
							<th>Start Time</th>
							<th>End Time</th>
							<th>Duration</th>
							<th>Status</th>
							<th>Date</th>
						</tr>
					</thead>
					<tbody>
				<?php 
				$dataFound = 0;
				if (count($get_data) > 0) {
					foreach ($get_data as $subjectArray) {
						// print_R($subjectArray->name); die;
						if(count($subjectArray->timetable) > 0){
							$base_time          = new DateTime('00:00');
							$total              = new DateTime('00:00');
							foreach($subjectArray->timetable as $timetable){
								if(!empty($timetable->studio->branch->name)){
								$dataFound++;
								
								$status = "";
								$duration = "00 : 00 Hours";
								$startClass = \App\StartClass::where('timetable_id', $timetable->id)->first();
								if(!empty($startClass)){
									$status = $startClass->status;
									if(!empty($startClass->start_time) && !empty($startClass->end_time)){
										$first_date = new DateTime($startClass->start_time);
										$second_date = new DateTime($startClass->end_time);
										$interval = $first_date->diff($second_date);
										$duration = $interval->format('%H : %I Hours'); // H Hour I minut
										
										$base_time->add($interval);
										//$interval->format('%Y years %M months and %D days %H hours %I minutes and %S seconds.');
									}
								}
						?>
							<tr style="">
							<td><?=$dataFound?></td>
							<td><?php 
							if(!empty($subjectArray->name)){
								echo $subjectArray->name; 
							} ?>
							</td>
							<td>
							<?php 
							if(!empty($timetable->studio->branch->name)){
								echo $timetable->studio->branch->name; 
							}
							?>
							</td>
							<td>
							<?php if(!empty($timetable->faculty->name)){
								echo $timetable->faculty->name;
							}?>
							</td>
							<td>
							<?php if(!empty($timetable->from_time)){
								echo date('h:i A', strtotime($timetable->from_time));
							}?>
							</td>
							<td>
							<?php if(!empty($timetable->to_time)){
								echo date('h:i A', strtotime($timetable->to_time));
							}?>
							</td>
							<td><?php echo $duration; ?></td>
							<td><?php echo $status; ?></td>
							<td>
							<?php if(!empty($timetable->cdate)){
								echo date('d-m-Y',strtotime($timetable->cdate));
							}?>
							</td>
							</tr>
						 
						<?php  
								}
							}
							$baseDays = $total->diff($base_time)->format("%a");
							$baseHours = $total->diff($base_time)->format("%H");
							$baseMinute = $total->diff($base_time)->format("%I");
							
							$schedule_total_tt = ($baseDays*24)+$baseHours. ":" . $baseMinute;
							
							?>
							<tr style="">
							<td colspan="7"> <strong>Total Duration:</strong> &nbsp; <?php echo e($schedule_total_tt); ?> Hours</td>
							<td colspan="2"></td>
							</tr>
							<?php
						} 
					} 
				}else{
				?>
				<tr>
					<td class="text-center" colspan="10">No Record Found</td>
				</tr>	
				<?php } ?>
				</body>
				</table>
					 
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
		/* if ($userTable.data().count() == 0) {
			swal("Warning!", "Not have any data!", "warning");
			return;
		} */
		var data = {};
			data.branch_id = $('.branch_id').val(),
			data.subject_id = $('.subject_id').val(),
			data.fdate = $('.fdate').val(),
			data.tdate = $('.tdate').val(),
		window.location.href = "<?php echo URL::to('/admin/'); ?>/subject-report-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});
</script>
<script type="text/javascript">
	$(".branch_id").on("change", function () {
		var branch_id = $(".branch_id option:selected").attr('value');
		var faculty_id = $("input[name=faculty_id]").val();
		if (branch_id) {
			
			$.ajax({
				beforeSend: function(){
					// $(".branch_loader i").show();
				},
				type : 'POST',
				url : '<?php echo e(route('admin.get-branchwise-faculty')); ?>',
				data : {'_token' : '<?php echo e(csrf_token()); ?>', 'branch_id': branch_id, 'faculty_id': faculty_id},
				dataType : 'html',
				success : function (data){
					// $(".branch_loader i").hide();
					$('.faculty_id').empty();
					$('.faculty_id').append(data);
				}
			});
			
			
		}
	});
	
	$(document).ready(function() {
		var branch_id = $(".branch_id option:selected").attr('value');
		var faculty_id = $(".faculty_id_get").val();
		if (branch_id) {
			$.ajax({
				beforeSend: function(){
					// $(".branch_loader i").show();
				},
				type : 'POST',
				url : '<?php echo e(route('admin.get-branchwise-faculty')); ?>',
				data : {'_token' : '<?php echo e(csrf_token()); ?>', 'branch_id': branch_id, 'faculty_id': faculty_id},
				dataType : 'html',
				success : function (data){
					// $(".branch_loader i").hide();
					$('.faculty_id').empty();
					$('.faculty_id').append(data);
				}
			});
		}
	});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/subject_reports/index.blade.php ENDPATH**/ ?>