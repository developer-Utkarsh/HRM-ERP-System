
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Faculty Early Delay Report</h2>
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
								<form action="<?php echo e(route('admin.faculty-early-delay-reports')); ?>" method="get" name="filtersubmit">
									<div class="row">
										
										<input type="hidden" class="faculty_id_get" value="<?php echo e(json_encode(app('request')->input('faculty_id'))); ?>" multiple>
										
										<div class="col-md-3">
											<label for="users-list-status">Location</label>											
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 branch_location" name="branch_location" onchange="locationBranch(this.value);">
													<option value="">Select Any</option>				
													<option value="jodhpur" <?php if(!empty(app('request')->input('branch_location')) && 'jodhpur' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>Jodhpur</option>
													<option value="jaipur" <?php if(!empty(app('request')->input('branch_location')) && 'jaipur' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>Jaipur</option>
													<option value="delhi" <?php if(!empty(app('request')->input('branch_location')) && 'delhi' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>Delhi</option>
													<option value="prayagraj" <?php if(!empty(app('request')->input('branch_location')) && 'prayagraj' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>Prayagraj</option>
													<option value="indore" <?php if(!empty(app('request')->input('branch_location')) && 'indore' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>Indore</option>
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Faculty</label>
											<?php $faculty = \App\User::where('role_id', '2')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2 faculty_id" name="faculty_id[]" multiple>
													<?php if(count($faculty) > 0): ?>
													<?php $__currentLoopData = $faculty; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" <?php if(!empty(app('request')->input('faculty_id')) && in_array($value->id, app('request')->input('faculty_id'))): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
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

										<?php
										    $delay_type="";
											if(!empty(app('request')->input('delay_type'))){
												$delay_type=app('request')->input('delay_type');
											} 
										?>

										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Delay Type</label>
											<fieldset class="form-group">							<select class="form-control delay_type" name="delay_type">
													<option value=""> Select Delay Type</option>
													<option value="Due to Faculty" <?php if($delay_type=='Due to Faculty'): ?> selected  <?php endif; ?>>DuetoFaculty</option>
													<option value="Due to Managment" <?php if($delay_type=='Due to Managment'): ?> selected <?php endif; ?>>Due to Managment</option>
													<option value="Technical Issue" <?php if($delay_type=='Technical Issue'): ?> selected <?php endif; ?>>Technical Issue</option>
												</select>												
											</fieldset>
										</div>										
											
									</div>
									
									<fieldset class="form-group" style="float:right;">		
										<button type="submit" class="btn btn-primary">Search</button>
										<a href="<?php echo e(route('admin.faculty-early-delay-reports')); ?>" class="btn btn-warning">Reset</a>
										<?php if( Auth::user()->role_id != 3){ ?>
										<a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a>
										<?php } ?>
									</fieldset>
								</form>
							</div>
						</div>
					</div>
				</div>
				
				<?php 
					$fdate = app('request')->input('fdate');
					$tdate = app('request')->input('tdate');
					$branch_location = app('request')->input('branch_location');
					$delay_type=app('request')->input('delay_type');
					$faculty_id=app('request')->input('faculty_id');
					
					$where = ' AND 1=1';
					
					if(!empty($branch_location)){
						$where.= " AND br.branch_location = '$branch_location'";
					}

					if(!empty($faculty_id) && count($faculty_id)>0 && $faculty_id[0]!=""){
						$faculty_id=implode(",", $faculty_id);
						$where.= " AND tt.faculty_id IN($faculty_id)";
					}
					
					if(!empty($delay_type)){
						$where.= " AND (delay_type = '$delay_type' OR is_cancel=1)";
					}else{
                        $where.= " AND (delay_type!='' OR is_cancel=1)";
					}
					
					if(!empty($fdate) && !empty($tdate)){
						$where.= " AND tt.cdate >='$fdate' AND tt.cdate <= '$tdate'";
					}else{
						$where.= " AND tt.cdate = '".date('Y-m-d')."'";
					}
					
					$get_record = DB::select("SELECT br.branch_location,delay_type,is_cancel,count(st.id) as classes FROM start_classes as st	
					left join timetables as tt ON tt.id=st.timetable_id
					left join branches as br ON br.id=tt.branch_id
					where  1=1 ".$where."
					group by br.branch_location,delay_type,is_cancel");
					$city = [];
					$delay_type = [];
					$cancel_class = [];
					foreach($get_record as $gr){
						if($gr->is_cancel==0){
							if(!empty($city[$gr->branch_location])){
							  $city[$gr->branch_location] = $city[$gr->branch_location]+$gr->classes;	
							}else{
							 $city[$gr->branch_location] = $gr->classes;
							}
							
							if(!empty($delay_type[$gr->delay_type])){
								$delay_type[$gr->delay_type] = $delay_type[$gr->delay_type]+$gr->classes;
							}else{
								$delay_type[$gr->delay_type] = $gr->classes;
							}
						}else{
							if(!empty($cancel_class[$gr->branch_location])){
							  $cancel_class[$gr->branch_location] = $cancel_class[$gr->branch_location]+$gr->classes;	
							}else{
							 $cancel_class[$gr->branch_location] = $gr->classes;
							}
						}
					}
						
				?>
				
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body pt-0">
							<div class="pt-2"><h5>Classes Delay & Cancel Counts Dashboard</h5></div>
							<div class="pt-2"><h5>1. City Wise</h5></div>
							<div class="users-list-filter">								
								<div class="row text-center">
									<?php 		
										$total = 0;
										foreach($city as $key => $gr){
											$total = $total + $gr;
									?>
									<div class="col-12 col-sm-6 col-lg-2">										
										<div class="border mt-1 p-1">
											<label for="users-list-status"><b class="text-primary"><?=ucwords($key);?></b></label></br>
											<b class="text-danger"><?=$gr;?></b>
										</div>
									</div>									
									<?php } ?>
									<div class="col-12 col-sm-6 col-lg-2">										
										<div class="border mt-1 p-1">
											<label for="users-list-status"><b class="text-primary">Total</b></label></br>
											<b class="text-danger"><?=$total;?></b>
										</div>
									</div>	
								</div>
							</div>
							
							<div class="pt-2"><h5>2. Delay Type Wise</h5></div>
							<div class="users-list-filter">								
								<div class="row text-center">
									<?php 					
										$total = 0;
										foreach($delay_type as $key => $gr){
											$total = $total + $gr;
									?>
									<div class="col-12 col-sm-6 col-lg-3">										
										<div class="border mt-1 p-1">
											<label for="users-list-status"><b class="text-primary"><?=ucwords($key);?></b></label></br>
											<b class="text-danger"><?=$gr;?></b>
										</div>
									</div>									
									<?php } ?>
									<div class="col-12 col-sm-6 col-lg-3">										
										<div class="border mt-1 p-1">
											<label for="users-list-status"><b class="text-primary">Total</b></label></br>
											<b class="text-danger"><?=$total;?></b>
										</div>
									</div>	
								</div>
							</div>
							
							<div class="pt-2"><h5>3. City and Delay Type Wise</h5></div>
							<div class="users-list-filter">								
								<div class="row text-center">
									<?php 										
										foreach($get_record as $gr){
									?>
									<div class="col-12 col-sm-6 col-lg-3">										
										<div class="border mt-1 p-1">
											<label for="users-list-status"><b class="text-primary"><?=ucwords($gr->branch_location);?></b></label></br>
											<label for="users-list-status"><?=$gr->delay_type;?></label> </br>
											<b class="text-danger"><?=$gr->classes;?></b>
										</div>
									</div>									
									<?php } ?>
								</div>
							</div>
							
							<div class="pt-2"><h5>4. Cancel Classes</h5></div>
							<div class="users-list-filter">								
								<div class="row text-center">
									<?php 					
										$total = 0;
										foreach($cancel_class as $key => $gr){
											$total = $total + $gr;
									?>
									<div class="col-12 col-sm-6 col-lg-3">										
										<div class="border mt-1 p-1">
											<label for="users-list-status"><b class="text-primary"><?=ucwords($key);?></b></label></br>
											<b class="text-danger"><?=$gr;?></b>
										</div>
									</div>									
									<?php } ?>
									<div class="col-12 col-sm-6 col-lg-3">										
										<div class="border mt-1 p-1">
											<label for="users-list-status"><b class="text-primary">Total</b></label></br>
											<b class="text-danger"><?=$total;?></b>
										</div>
									</div>	
								</div>
							</div>
							
							
						</div>
					</div>
				</div>
				
				
				
				<div class="table-responsive">
				
				<?php 
				if (count($get_faculty) > 0) {
					foreach ($get_faculty as $key2=>$get_faculty_value) {

						    $whereCond = '1=1';
							if(!empty($selectFromDate) || !empty($selectToDate)){
									$whereCond .= ' AND timetables.cdate >= "'.$selectFromDate.'" AND timetables.cdate <= "'.$selectToDate.'"';
							}else{
								$whereCond .= ' AND timetables.cdate = "'.date('Y-m-d').'"';
							}

							if(!empty(app('request')->input('delay_type'))){
								$delay_type=app('request')->input('delay_type');
                               $whereCond .= ' AND start_classes.delay_type= "'.$delay_type.'"';
							}	
							
							if(!empty($get_faculty_value->faculty_name)){
							    $get_faculty_timetable=DB::table('timetables')
													  ->select('timetables.*','studios.name as studios_name','branches.name as branches_name','branches.id as branches_id','batch.name as batch_name','course.name as course_name','subject.name as subject_name','chapter.name as chapter_name','start_classes.status as start_classes_status','start_classes.start_time as start_classes_start_time','start_classes.end_time as start_classes_end_time','users_assistant.name as assistant_name','users_assistant.mobile as assistant_mobile','start_classes.early_delay_reason','start_classes.delay_type','start_classes.delay_status',
													  	'start_classes.delay_faculty_reason')
													  ->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
													  ->leftJoin('branches', 'branches.id', '=', 'studios.branch_id')
													  ->leftJoin('batch', 'batch.id', '=', 'timetables.batch_id')
													  ->leftJoin('course', 'course.id', '=', 'timetables.course_id')
													  ->leftJoin('subject', 'subject.id', '=', 'timetables.subject_id')
													  ->leftJoin('chapter', 'chapter.id', '=', 'timetables.chapter_id')
													  ->leftJoin('start_classes', 'start_classes.timetable_id', '=', 'timetables.id')
													  ->leftJoin('users as users_assistant', 'users_assistant.id', '=', 'timetables.assistant_id')
													  ->where('timetables.faculty_id', $get_faculty_value->faculty_id)
													  ->where('timetables.time_table_parent_id', '0')
													  ->where('timetables.is_deleted', '0');
							if(Auth::user()->role_id == 3){
								$get_faculty_timetable->where('timetables.assistant_id', Auth::user()->id);
							}

							$get_faculty_timetable = $get_faculty_timetable->whereRaw($whereCond)
													  ->orderBy('timetables.cdate', 'ASC')
													  ->orderBy('timetables.from_time', 'ASC')
													  ->get();
													  
							//echo "<pre>"; print_r($get_faculty_timetable); die;
							$duration  = "00 : 00 Hours"; 
							$schedule_duration  = "00 : 00 Hours"; 
							
							
							$total_early_schedule = new DateTime('00:00');
							$total_base_early_schedule = new DateTime('00:00');
							
							$total_delay_schedule = new DateTime('00:00');
							$total_base_delay_schedule = new DateTime('00:00');
							
							if(count($get_faculty_timetable) > 0){  ?>
								<table class="table data-list-view" style=''>
									<head>
										<tr style="">
											<th colspan="10"><b>Faculty Name : <?php echo isset($get_faculty_value->faculty_name)?$get_faculty_value->faculty_name:''; ?></b> </th>
										</tr>
									</head>
									<head>
										<tr style="">
											<th scope="col">Date</th>
											<th scope="col">Schedule From</th>
											<th scope="col">Schedule To</th>
											<th scope="col">Spent From</th>
											<th scope="col">Spent To</th>
											<th scope="col">Early</th>
											<th scope="col">Delay</th>
											<th scope="col">Delay Type</th>
											<th scope="col">Early/Delay Reason</th>
											<th scope="col">Faculty Delay Reason</th>
										</tr>
									</head>
									<body>
							<?php foreach($get_faculty_timetable as $key => $get_faculty_timetable_value){
								$early = 0;
								if(!empty($get_faculty_timetable_value->start_classes_end_time) && $get_faculty_timetable_value->to_time > $get_faculty_timetable_value->start_classes_end_time){
									$early_from_time         = new DateTime($get_faculty_timetable_value->to_time);
									$early_to_time           = new DateTime($get_faculty_timetable_value->start_classes_end_time);
									$early_schedule_interval = $early_from_time->diff($early_to_time); 
									$early       = $early_schedule_interval->format('%H : %I Hours');
									$total_base_early_schedule->add($early_schedule_interval); 
								}
								
								$delay = 0;
								if(!empty($get_faculty_timetable_value->start_classes_start_time) && $get_faculty_timetable_value->from_time < $get_faculty_timetable_value->start_classes_start_time){
									$delay_from_time         = new DateTime($get_faculty_timetable_value->start_classes_start_time);
									$delay_to_time           = new DateTime($get_faculty_timetable_value->from_time);
									$delay_schedule_interval = $delay_from_time->diff($delay_to_time); 
									$delay       = $delay_schedule_interval->format('%H : %I Hours');
									$total_base_delay_schedule->add($delay_schedule_interval); 
								}
								
							?>
								<tr>
									<td><?php echo isset($get_faculty_timetable_value->cdate) ?  date('d-m-Y',strtotime($get_faculty_timetable_value->cdate)) : '' ?></td>
									<td><?php echo isset($get_faculty_timetable_value->from_time) ?  date("h:i A", strtotime($get_faculty_timetable_value->from_time)) : '0' ?></td>
									<td><?php echo isset($get_faculty_timetable_value->to_time) ?  date("h:i A", strtotime($get_faculty_timetable_value->to_time)) : '0' ?></td>
									
									
									<td><?php echo isset($get_faculty_timetable_value->start_classes_start_time) ?  date("h:i A", strtotime($get_faculty_timetable_value->start_classes_start_time)) : '0' ?></td>
									<td><?php echo isset($get_faculty_timetable_value->start_classes_end_time) ?  date("h:i A", strtotime($get_faculty_timetable_value->start_classes_end_time)) : '0' ?></td>
									<td><?php echo e($early); ?></td>
									<td><?php echo e($delay); ?></td>
									<td>
										<?=$get_faculty_timetable_value->delay_type?>
										<?php if($get_faculty_timetable_value->delay_status==0 && $delay!='0'): ?>
										  <span class="delayWhatsapp btn btn-sm btn-danger" 
										  data-id="<?php echo e($get_faculty_timetable_value->id); ?>"
										  data-faculty_id="<?php echo e($get_faculty_timetable_value->faculty_id); ?>"
										  data-delay="<?php echo e($delay); ?>"
										  >
										  <i class="fa fa-check"></i> Approve</span>
										<?php elseif($get_faculty_timetable_value->delay_status==1): ?>
										  Delay Approved
										<?php endif; ?>
									</td>
									<td><?=$get_faculty_timetable_value->early_delay_reason?></td>
									<td><?=$get_faculty_timetable_value->delay_faculty_reason?></td>
									
								</tr>
							<?php } ?>
							
							<tr>
								<td colspan="5"><b>Total Early Time:</b> 
								<?php
								$totalDays = $total_early_schedule->diff($total_base_early_schedule)->format("%a");
								$totalHours = $total_early_schedule->diff($total_base_early_schedule)->format("%H");
								$totalMinute = $total_early_schedule->diff($total_base_early_schedule)->format("%I");
								echo ($totalDays*24)+$totalHours. ":" . $totalMinute;
								?> Hours
								</td> 
								<td colspan="5"><b>Total Delay Time:</b> 
								<?php
								$baseDays = $total_delay_schedule->diff($total_base_delay_schedule)->format("%a");
								$baseHours = $total_delay_schedule->diff($total_base_delay_schedule)->format("%H");
								$baseMinute = $total_delay_schedule->diff($total_base_delay_schedule)->format("%I");
								echo ($baseDays*24)+$baseHours. ":" . $baseMinute;
								?> 
								Hours</td> 
							</tr>	
						</body>
					</table>
					<p><hr/></p>
				<?php } } } } ?>
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
<link href="<?php echo e(asset('laravel/public/css/jquery.timepicker.css')); ?>" rel="stylesheet"/>
<script src="<?php echo e(asset('laravel/public/js/jquery.timepicker.js')); ?>"></script>

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
		
		$('.timepicker').timepicker({ 'step': 1, 'timeFormat': 'h:i A' });
	});
	
	$("body").on("click", "#download_excel", function (e) {
		var data = {};
		data.faculty_id = $("[name='faculty_id[]']").val(),
		data.fdate = $('.fdate').val(),
		data.tdate = $('.tdate').val(),
		data.delay_type = $('.delay_type').val(),
		data.branch_location = $('.branch_location').val(),
		data.online_class_type = $('.online_class_type').val(),
		window.location.href = "<?php echo URL::to('/admin/'); ?>/faculty-early-delay-report-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});

	

	$(".delayWhatsapp").on("click", function() {
		if(!confirm('Are you sure to approve dealy class')){
               return;
		}
		
		var timetable_id = $(this).attr("data-id");
		var faculty_id = $(this).attr("data-faculty_id");
		var delay = $(this).attr("data-delay");
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},      
			type: "POST",
			url : '<?php echo e(route('admin.delayWhatsapp')); ?>',
			data : {'_token' : '<?php echo e(csrf_token()); ?>', 'timetable_id': timetable_id,faculty_id:faculty_id,delay:delay},
			dataType : 'json',
			success : function(data){
				if(data.status == false){
					swal("Error!", data.message, "error");
				}else{
					swal("Success", data.message, "success");
				}
			}
		});
	});  
					
	
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/faculty_early_delay_reports/index.blade.php ENDPATH**/ ?>