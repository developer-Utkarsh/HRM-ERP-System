
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Faculty Hours Report</h2>
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
								<form action="<?php echo e(route('admin.faculty-hours-reports')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<?php if( Auth::user()->role_id != 3){ ?>
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
										<?php } ?>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Faculty</label>
											<?php $faculty = \App\User::where('role_id', '2')->where('status', '1')->where('is_deleted', '0')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2 faculty_id" name="faculty_id[]" multiple>
													<option value="">Select Any</option>
													<?php if(count($faculty) > 0): ?>
													<?php $__currentLoopData = $faculty; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>"  <?php if(!empty( app('request')->input('faculty_id')) && is_array(app('request')->input('faculty_id')) > 0 && in_array($value->id, app('request')->input('faculty_id'))){ echo "selected"; } ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php endif; ?>
												</select>												
											</fieldset>
										</div>
										
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Class Type</label>
											 
											<fieldset class="form-group">												
												<select class="form-control online_class_type" name="online_class_type">
													<option value=''>Select Class Type</option>
																
													<option value='Online Course Recording' <?php if('Online Course Recording' == app('request')->input('online_class_type')): ?> selected="selected" <?php endif; ?> >Online Course Recording</option>
													
													<option value='YouTube Live' <?php if('YouTube Live' == app('request')->input('online_class_type')): ?> selected="selected" <?php endif; ?> >YouTube Live</option>
													
													<option value='YouTube & App Live' <?php if('YouTube & App Live' == app('request')->input('online_class_type')): ?> selected="selected" <?php endif; ?> >YouTube & App Live</option>
													
													<option value='Model Paper Recording' <?php if('Model Paper Recording' == app('request')->input('online_class_type')): ?> selected="selected" <?php endif; ?> >Model Paper Recording</option>
													
													<option value='Offline' <?php if('Offline' == app('request')->input('online_class_type')): ?> selected="selected" <?php endif; ?> >Offline</option>
													
													<option value='Offline & App live' <?php if('Offline & App live' == app('request')->input('online_class_type')): ?> selected="selected" <?php endif; ?> >Offline & App live</option>
													<option value='App Live' <?php if('App Live' == app('request')->input('online_class_type')): ?> selected="selected" <?php endif; ?> >App Live</option>
												</select>												
											</fieldset>
										</div>
										
										<div class="col-12 col-sm-6 col-lg-2">											
											<label for="users-list-status">Date</label>								
											<fieldset class="form-group">																					
												<input type="date" name="fdate" placeholder="Date" value="<?php echo e(app('request')->input('fdate')); ?>" class="form-control fdate">	
											</fieldset>	
										</div>	
										
										<div class="col-12 col-sm-6 col-lg-2">											
											<label for="users-list-status">To Date</label>									
											<fieldset class="form-group">																					
												<input type="date" name="tdate" placeholder="Date" value="<?php echo e(app('request')->input('tdate')); ?>" class="form-control tdate">	
											</fieldset>									
										</div>										
											
									</div>
									
									<fieldset class="form-group" style="float:right;">		
										<button type="submit" class="btn btn-primary">Search</button>
										<a href="<?php echo e(route('admin.faculty-hours-reports')); ?>" class="btn btn-warning">Reset</a>
										<?php if( Auth::user()->role_id != 3){ ?>
										<a href="javascript:void(0)" id="download_pdf" class="btn btn-primary">Export in PDF</a>
										<a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a>
										<?php } ?>
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
								<th scope="col">Subject</th>
								<th scope="col">A. Time</th>
								<th scope="col">Schedule Time</th>
								<th scope="col">Schedule Diffrence</th>
								<th scope="col">Spent Time</th>
								<th scope="col">Taken Diffrence</th>
								<!--th scope="col">From Date</th>
								<th scope="col">To Date</th-->
							</tr>
						</thead>
						<tbody>
							<?php if(count($get_faculty) > 0): ?>
							<?php $s_no = 0; ?>
								<?php $__currentLoopData = $get_faculty; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$get_faculty_val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<?php
								    $f_date = date('Y-m-d'); $t_date = date('Y-m-d');
									if(!empty($selectFromDate)){
										$f_date = $selectFromDate;
									}
									if(!empty($selectToDate)){
										$t_date = $selectToDate;
									}
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

									$whereCond  = ' 1=1';
									
									if(!empty($branch_location)){
										$whereCond .= ' AND branches.branch_location = "'.$branch_location.'"';
									}
									
									if(!empty($online_class_type)){
										$whereCond .= ' AND timetables.online_class_type = "'.$online_class_type.'"';
									}

									$get_total_time = DB::table('timetables')
													->select('timetables.from_time as start_time','timetables.to_time as end_time','timetables.is_cancel','subject.name','start_classes.start_time as start_classes_start_time','start_classes.end_time as start_classes_end_time')
													->leftJoin('subject', 'subject.id', '=', 'timetables.subject_id')
													->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
													->leftJoin('branches', 'branches.id', '=', 'studios.branch_id')
													->leftJoin('start_classes', 'start_classes.timetable_id', '=', 'timetables.id')
													->where('timetables.faculty_id', $get_faculty_val->id)
													->where('timetables.time_table_parent_id', '0')
													->whereRaw("(timetables.is_deleted='0' OR timetables.is_deleted='2')");
									if(Auth::user()->role_id == 3){
										$get_total_time->where('timetables.assistant_id', Auth::user()->id);
									 }				
										$get_total_time = $get_total_time->whereRaw($whereCond)
													->whereRaw(' timetables.cdate >= "'.$f_date.'" AND timetables.cdate <= "'.$t_date.'"')
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
											
											if($get_total_time_value->is_cancel != '1'){
												$first_date = new DateTime($get_total_time_value->start_classes_start_time);
												$second_date = new DateTime($get_total_time_value->start_classes_end_time);
												$interval = $first_date->diff($second_date);
												$base_time2->add($interval);
											}
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
									if(!empty($branch_location) ){
										if(count($get_total_time) > 0){
										$s_no++;
										?>
										<tr>
											<td class="product-category"><?php echo e($s_no); ?></td>
											<td class="product-category"><?php echo e($get_faculty_val->name); ?></td>
											<td class="product-category"><?php echo e($get_faculty_val->mobile); ?></td>
											<td class="product-category"><?php echo e((count($subject_arr) > 0) ? implode(",", array_unique($subject_arr)) : '--'); ?></td>
											<td class="product-category">
											<?php
											if($get_faculty_val->agreement=='Yes'){
												echo $get_faculty_val->committed_hours;
											}
											else{
												echo "-";
											}
											?>
											
											</td>
											<!--td class="product-category"><?php echo e($total->diff($base_time)->format("%H:%I")); ?> Hours</td-->
											<td class="product-category"><?php echo e($schedule_total_tt); ?> Hours</td>
											
											<td class="">
											<?php
											if($get_faculty_val->agreement=='Yes'){
												$h1s = $get_faculty_val->committed_hours*3600;
												$h2s = (($baseDays*24)+$baseHours)*3600 + $baseMinute *60;
												$rmTime = "";;
												if($h1s > $h2s){
													$seconds = $h1s - $h2s;
												}
												else{
													$rmTime="-";
													$seconds = $h2s - $h1s;
												}
												$rmTime =$rmTime . sprintf("%02d:%02d", floor($seconds / 3600), ($seconds / 60) % 60);
												echo $rmTime. " Hours";
											}
											else{
												echo "-";
											}
											?>
											</td>
											<td class="product-category"><?php echo e($total_tt); ?> Hours</td>
											<td class="">
											<?php
											if($get_faculty_val->agreement=='Yes'){
												$h1s = $get_faculty_val->committed_hours*3600;
												$h2s = (($totalDays*24)+$totalHours)*3600 + $totalMinute *60;
												$rmTime = "";;
												if($h1s > $h2s){
													$seconds = $h1s - $h2s;
												}
												else{
													$rmTime="-";
													$seconds = $h2s - $h1s;
												}
												$rmTime =$rmTime . sprintf("%02d:%02d", floor($seconds / 3600), ($seconds / 60) % 60);
												echo $rmTime. " Hours";
											}
											else{
												echo "-";
											}
											?>
											</td>
										</tr>
										<?php
										}

									}
									else{
										$s_no++;	
								?>
								<tr>
									<td class="product-category"><?php echo e($s_no); ?></td>
									<td class="product-category"><?php echo e($get_faculty_val->name); ?></td>
									<td class="product-category"><?php echo e($get_faculty_val->mobile); ?></td>
									<td class="product-category"><?php echo e((count($subject_arr) > 0) ? implode(",", array_unique($subject_arr)) : '--'); ?></td>
									<!--td class="product-category"><?php echo e($total->diff($base_time)->format("%H:%I")); ?> Hours</td-->
									<td class="product-category">
									<?php
									if($get_faculty_val->agreement=='Yes'){
										echo $get_faculty_val->committed_hours;
									}
									else{
										echo "-";
									}
									?>
									</td>
									<td class="product-category"><?php echo e($schedule_total_tt); ?> Hours</td>
									<td class="">
									<?php
									if($get_faculty_val->agreement=='Yes'){
										$h1s = $get_faculty_val->committed_hours*3600;
										$h2s = (($baseDays*24)+$baseHours)*3600 + $baseMinute *60;
										$rmTime = "";;
										if($h1s > $h2s){
											$seconds = $h1s - $h2s;
										}
										else{
											$rmTime="-";
											$seconds = $h2s - $h1s;
										}
										$rmTime =$rmTime . sprintf("%02d:%02d", floor($seconds / 3600), ($seconds / 60) % 60);
										echo $rmTime. " Hours";
									}
									else{
										echo "-";
									}
									?>
									</td>
									<td class="product-category"><?php echo e($total_tt); ?> Hours</td>
									<td class="">
									<?php
									if($get_faculty_val->agreement=='Yes'){
										$h1s = $get_faculty_val->committed_hours*3600;
										$h2s = (($totalDays*24)+$totalHours)*3600 + $totalMinute *60;
										$rmTime = "";;
										if($h1s > $h2s){
											$seconds = $h1s - $h2s;
										}
										else{
											$rmTime="-";
											$seconds = $h2s - $h1s;
										}
										$rmTime =$rmTime . sprintf("%02d:%02d", floor($seconds / 3600), ($seconds / 60) % 60);
										echo $rmTime. " Hours";
									}
									else{
										echo "-";
									}
									?>
									</td>
									<!--td class="product-category"><?php echo e($f_date); ?></td>
									<td class="product-category"><?php echo e($t_date); ?></td-->
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
		data.faculty_id = $("[name='faculty_id[]']").val(),
		data.fdate = $('.fdate').val(),
		data.tdate = $('.tdate').val(),
		data.branch_location = $('.branch_location').val(),
		data.online_class_type = $('.online_class_type').val(),
		window.location.href = "<?php echo URL::to('/admin/'); ?>/faculty-hours-report-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});
	
	$("body").on("click", "#download_pdf", function (e) {
		var data = {}; 
		data.faculty_id = $("[name='faculty_id[]']").val(),
		data.fdate = $('.fdate').val(),
		data.tdate = $('.tdate').val(),
		data.branch_location = $('.branch_location').val(),
		data.online_class_type = $('.online_class_type').val(),
		window.location.href = "<?php echo URL::to('/admin/'); ?>/faculty-hours-report-pdf?" + Object.keys(data).map(function (k) {
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/faculty_hours_reports/index.blade.php ENDPATH**/ ?>