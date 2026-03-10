
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Faculty Agreement Hours</h2>
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
								<form action="<?php echo e(route('admin.faculty-agreement-hours')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-2 branch_loader">
											<label for="users-list-status">Location</label>
											<fieldset class="form-group">												
												<select class="form-control branch_location" name="branch_location" id="">
													<option value="">Select Any</option>
													<option value="jaipur" <?php if('jaipur' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>jaipur</option>
													<option value="jodhpur" <?php if('jodhpur' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>jodhpur</option>
													<option value="prayagraj" <?php if('prayagraj' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>prayagraj</option>
													<option value="indore" <?php if('indore' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>indore</option>
												</select>
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-4">
											<label for="users-list-status">Faculty</label>
											<?php $faculty = \App\User::where('role_id', '2')->where('status', '1')->where('is_deleted', '0')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2 faculty_id" name="faculty_id[]" multiple>
													<option value="">Select Any</option>
													<?php if(count($faculty) > 0): ?>
													<?php $__currentLoopData = $faculty; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>"  <?php if(!empty( app('request')->input('faculty_id')) && in_array($value->id, app('request')->input('faculty_id'))){ echo "selected"; } ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php endif; ?>
												</select>												
											</fieldset>
										</div>
																				
										
										<div class="col-12 col-sm-6 col-lg-2">											
											<label for="users-list-status">Month</label>								
											<fieldset class="form-group">																					
												<input type="month" name="fmonth" placeholder="Month" value="<?="$yr-$mt"?>" class="form-control fmonth">	
											</fieldset>	
										</div>	
										
										<!--
										<div class="col-12 col-sm-6 col-lg-2">											
											<label for="users-list-status">Year</label>									
											<fieldset class="form-group">																					
												<input type="year" name="tdate" placeholder="Date" value="<?php echo e(app('request')->input('tdate')); ?>" class="form-control tdate">	
											</fieldset>									
										</div>	
										-->
											
									</div>
									
									<fieldset class="form-group" style="float:right;">		
										<button type="submit" class="btn btn-primary">Search</button>
										<a href="<?php echo e(route('admin.faculty-agreement-hours')); ?>" class="btn btn-warning">Reset</a>
										<!--<a href="javascript:void(0)" id="download_pdf" class="btn btn-primary">Export in PDF</a>-->
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
								<th scope="col">S No.</th>
								<th scope="col">Name</th>
								<th scope="col">Contact No</th>
								<th scope="col">Agreement Hour</th>
								<th scope="col">Schedule Hour</th>
								<th scope="col">Spent Hour</th>
								<!--th scope="col">From Date</th>
								<th scope="col">To Date</th-->
							</tr>
						</thead>
						<tbody>
							<?php if(count($get_faculty) > 0): ?>
							<?php $s_no = 0; ?>
								<?php $__currentLoopData = $get_faculty; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$get_faculty_val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<?php 
									// $f_date = date('Y-m-d'); $t_date = date('Y-m-d');
									// if(!empty($selectFromDate)){
										// $f_date = $selectFromDate;
									// }
									// if(!empty($selectToDate)){
										// $t_date = $selectToDate;
									// }
									/*$get_total_time = DB::table('timetables')
													->select('start_classes.start_time','start_classes.end_time','subject.name')
													->leftJoin('start_classes', 'start_classes.timetable_id', '=', 'timetables.id')
													->leftJoin('subject', 'subject.id', '=', 'timetables.subject_id')
													->where('timetables.faculty_id', $get_faculty_val->id)
													->whereRaw(' timetables.cdate >= "'.$f_date.'" AND timetables.cdate <= "'.$t_date.'"')
													->get();
									//echo "<pre>"; print_r($get_total_time); die;					
									$duration           = "00 : 00 Hours"; 
									$schedule_duration  = "00 : 00 Hours"; 
									$base_time          = new DateTime('00:00');
									$total              = new DateTime('00:00');
									$subject_arr        = array();
									if(count($get_total_time) > 0){
										foreach($get_total_time as $get_total_time_value){
											array_push($subject_arr, $get_total_time_value->name);
											$first_time = new DateTime($get_total_time_value->start_time);
											$second_time = new DateTime($get_total_time_value->end_time);
											$interval = $first_time->diff($second_time);
											$duration = $interval->format('%H : %I Hours');
											$base_time->add($interval); 
										}
									}*/

									$whereCond  	= ' 1=1';
									$monCondition 	= " (MONTH(timetables.cdate) = $mt and YEAR(timetables.cdate) = $yr)";
									
									// if(!empty($branch_location)){
										// $whereCond .= ' AND branches.branch_location = "'.$branch_location.'"';
									// }
									

									$get_total_time = DB::table('timetables')
													->select('timetables.from_time as start_time','timetables.to_time as end_time','subject.name','start_classes.start_time as start_classes_start_time','start_classes.end_time as start_classes_end_time')
													->leftJoin('subject', 'subject.id', '=', 'timetables.subject_id')
													->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
													->leftJoin('branches', 'branches.id', '=', 'studios.branch_id')
													->leftJoin('start_classes', 'start_classes.timetable_id', '=', 'timetables.id')
													->where('timetables.faculty_id', $get_faculty_val->id)
													->where('timetables.time_table_parent_id', '0')
													->where('timetables.is_deleted', '0')
													->where('branches.branch_location', $branch_location)
													->whereRaw($whereCond)
													->whereRaw($monCondition)
													->get();
													
												
									$base_time2          = new DateTime('00:00');
									$base_time          = new DateTime('00:00');
									$total              = new DateTime('00:00');
									$total2              = new DateTime('00:00');
									$subject_arr        = array();
									$schedule_total_tt           = "00 : 00 Hours"; 
									$total_tt           = "00 : 00 Hours"; 
									if(count($get_total_time) > 0){
										foreach($get_total_time as $get_total_time_value){
											array_push($subject_arr, $get_total_time_value->name);
											$first_time = new DateTime($get_total_time_value->start_time);
											$second_time = new DateTime($get_total_time_value->end_time);
											$interval = $first_time->diff($second_time);
											$base_time->add($interval);


											$first_date = new DateTime($get_total_time_value->start_classes_start_time);
											$second_date = new DateTime($get_total_time_value->start_classes_end_time);
											$interval = $first_date->diff($second_date);
											$base_time2->add($interval); 											
										}
										
										$baseDays = $total->diff($base_time)->format("%a");
										$baseHours = $total->diff($base_time)->format("%H");
										$baseMinute = $total->diff($base_time)->format("%I");
										
										$schedule_total_tt = ($baseDays*24)+$baseHours. ":" . $baseMinute;
										
										$totalDays = $total2->diff($base_time2)->format("%a");
										$totalHours = $total2->diff($base_time2)->format("%H");
										$totalMinute = $total2->diff($base_time2)->format("%I");
										
										$total_tt = ($totalDays*24)+$totalHours. ":" . $totalMinute;
									}
									
									
									if(count($get_total_time) > 0){
										$s_no++;
									?>
									<tr>
										<td class="product-category"><?php echo e($s_no); ?></td>
										<td class="product-category"><?php echo e($get_faculty_val->name); ?></td>
										<td class="product-category"><?php echo e($get_faculty_val->mobile); ?></td>
										<td class="product-category"><?php echo e($get_faculty_val->committed_hours); ?> </td>
										<!--td class="product-category"><?php echo e($total->diff($base_time)->format("%H:%I")); ?> </td-->
										<td class="product-category"><?php echo e($schedule_total_tt); ?> </td>
										<td class="product-category"><?php echo e($total_tt); ?> </td>
									</tr>
									<?php
									}
								?>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php else: ?>
								<tr>
									<td class="text-center" colspan="7">No Record Found</td>
								</tr>								
							<?php endif; ?>
						</tbody>
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
		 
		$('.select-multiple2').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
	
	$("body").on("click", "#download_excel", function (e) {
		var data = {};
		data.faculty_id = $("[name='faculty_id[]']").val(),
		// data.fdate = $('.fdate').val(),
		// data.tdate = $('.tdate').val(),
		data.year_wise_month    = $('.fmonth').val(), 
		data.branch_location = $('.branch_location').val(),
		window.location.href = "<?php echo URL::to('/admin/'); ?>/faculty-agreement-hours-report-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});
	
	$("body").on("click", "#download_pdf", function (e) {
		var data = {}; 
		data.faculty_id = $("[name='faculty_id[]']").val(),
		data.fdate = $('.fdate').val(),
		data.tdate = $('.tdate').val(),
		window.location.href = "<?php echo URL::to('/admin/'); ?>/faculty-hours-report-pdf?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/faculty_hours_reports/agreement_hours.blade.php ENDPATH**/ ?>