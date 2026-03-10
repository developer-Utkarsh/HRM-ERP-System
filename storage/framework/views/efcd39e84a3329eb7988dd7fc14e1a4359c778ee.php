
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">TimeTable History</h2>
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
								<form action="<?php echo e(route('studiomanager.timetable-history-reports')); ?>" method="get" name="filtersubmit">
									<div class="row">
										<?php if( Auth::user()->role_id != 3){ ?>
										<div class="col-12 col-sm-6 col-lg-3 branch_loader">
											<label for="users-list-status">Location</label>
											<fieldset class="form-group">												
												<select class="form-control branch_location" name="branch_location" id="" required>
													<option value="">Select Any</option>
													<option value="jaipur" <?php if('jaipur' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>jaipur</option>
													<option value="jodhpur" <?php if('jodhpur' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>jodhpur</option>
													<option value="prayagraj" <?php if('prayagraj' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>prayagraj</option>
													<option value="indore" <?php if('indore' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>indore</option>
												</select>
											</fieldset>
										</div>
										
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
										<?php } ?>
										
										<input type="hidden" class="faculty_id_get" value="<?php echo e(json_encode(app('request')->input('faculty_id'))); ?>" multiple>
										
										<div class="col-12 col-sm-6 col-lg-3 d-none">
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
										
										<div class="col-12 col-md-3 d-none">
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
										
										<div class="col-12 col-md-3 d-none">
											<label for="users-list-status">Assistant</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple3 select_assistant_id" name="assistant_id">
													<option value="">Select Any</option>
													<?php if(count($get_assistant) > 0): ?>
													<?php $__currentLoopData = $get_assistant; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" <?php if($value->id == app('request')->input('assistant_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?> (<?php echo e($value->register_id); ?>)</option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php endif; ?>
												</select>												
											</fieldset>
										</div>
										
										<div class="col-12 col-md-3 d-none">
											<label for="users-list-status">Status</label>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2 status" name="status">
													<option value="">Select Status</option>
													<option value="all" <?php if('all' == app('request')->input('status')): ?> selected="selected" <?php endif; ?>>All</option>
													<option value="cancel" <?php if('cancel' == app('request')->input('status')): ?> selected="selected" <?php endif; ?>>Cancelled Classes</option>
													<option value="not_fill_spent_time" <?php if('not_fill_spent_time' == app('request')->input('status')): ?> selected="selected" <?php endif; ?>>Not Fill Spent Time</option>
												</select>												
											</fieldset>
										</div>
										
										<div class="col-12 col-sm-6 col-lg-3">											
											<label for="users-list-status">From Date</label>								
											<fieldset class="form-group">																					
												<input type="date" name="fdate"  placeholder="DD-MM-YYYY" value="<?php echo e(app('request')->input('fdate')); ?>" class="form-control StartDateClass fdate" >	
											</fieldset>	
										</div>	
										
										<div class="col-12 col-sm-6 col-lg-3" style="display:;" >
											<label for="users-list-status">To Date</label>
											<fieldset class="form-group">
												<input type="date" name="tdate" placeholder="DD-MM-YYYY" value="<?php echo e(app('request')->input('tdate')); ?>" class="form-control EndDateClass tdate">
											</fieldset>									
										</div>										
											
									</div>
									
									<fieldset class="form-group" style="float:right;">		
										<button type="submit" class="btn btn-primary">Search</button>
										<a href="<?php echo e(route('studiomanager.timetable-history-reports')); ?>" class="btn btn-warning">Reset</a>
										 
									</fieldset>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
				
				<?php  

				if (count($get_faculty) > 0) {
					foreach ($get_faculty as $key2=>$get_faculty_value) {
						
						$whereCond = '1=1 and timetable_history.timetable_id='.$get_faculty_value->id;	
							
						$total     = new DateTime('00:00'); 
						
						
						$get_faculty_timetable = DB::table('timetable_history')
												  ->select('timetable_history.*','studios.name as studios_name','branches.name as branches_name','branches.id as branches_id','batch.name as batch_name','subject.name as subject_name','users_assistant.name as assistant_name','users_assistant.mobile as assistant_mobile','users_faculty.name as faculty_name','change_by.name as change_by_username')
												  ->leftJoin('studios', 'studios.id', '=', 'timetable_history.studio_id')
												  ->leftJoin('branches', 'branches.id', '=', 'studios.branch_id')
												  ->leftJoin('batch', 'batch.id', '=', 'timetable_history.batch_id')
												  ->leftJoin('subject', 'subject.id', '=', 'timetable_history.subject_id')
												  ->leftJoin('users as users_assistant', 'users_assistant.id', '=', 'timetable_history.assistant_id')
												  ->leftJoin('users as users_faculty', 'users_faculty.id', '=', 'timetable_history.faculty_id')
												  ->leftJoin('users as change_by', 'change_by.id', '=', 'timetable_history.user_id');
												  
						
						if(!empty(app('request')->input('batch_id'))){
							$get_faculty_timetable->where('batch.id', app('request')->input('batch_id'));
						}
						if(!empty(app('request')->input('branch_id'))){
							$get_faculty_timetable->where('studios.branch_id', app('request')->input('branch_id'));
						}
						
						if(!empty(app('request')->input('branch_location'))){
							$get_faculty_timetable->where('branches.branch_location', app('request')->input('branch_location'));
						}
						
						if(!empty(app('request')->input('assistant_id'))){
							$get_faculty_timetable->where('timetable_history.assistant_id', app('request')->input('assistant_id'));
						}
						$get_faculty_timetable = $get_faculty_timetable->whereRaw($whereCond)
												  ->orderBy('timetable_history.id', 'ASC')
												  ->get();
												  
												  //echo "<pre>"; print_r($get_faculty_timetable); die;
					  
						$schedule_duration  = "00 : 00 Hours"; 
						
						
						if(count($get_faculty_timetable) > 0){
						
				?>
					<table class="table data-list-view" style=''>
					 
						<head>
							<tr style="">
								<th scope="col">From Time</th>
								<th scope="col">To Time</th>
								<th scope="col">Date</th>
								<th scope="col">Branch Name</th>
								<th scope="col">Batch Name</th>
								<th scope="col">Studio</th>
								<th scope="col">Subject Name</th>
								<th scope="col">Assistant Name</th>
								<th scope="col">Schedule Time</th>
								<th scope="col">Faculty Name</th>
								<th scope="col">Remark</th>
								<th scope="col">Change By</th>
							</tr>
						</head>
						<body>
							<?php
								
							foreach($get_faculty_timetable as $key => $get_faculty_timetable_value){
								
								$from_time         = new DateTime($get_faculty_timetable_value->from_time);
								$to_time           = new DateTime($get_faculty_timetable_value->to_time);
								$schedule_interval = $from_time->diff($to_time);
								$schedule_duration = $schedule_interval->format('%H : %I Hours');
								
								
								
								
								 
								
							?>
								<tr>
									<td><?php echo isset($get_faculty_timetable_value->from_time) ?  date("h:i A", strtotime($get_faculty_timetable_value->from_time)) : '' ?></td>
									<td><?php echo isset($get_faculty_timetable_value->to_time) ?  date("h:i A", strtotime($get_faculty_timetable_value->to_time)) : '' ?></td>
									<td><?php echo isset($get_faculty_timetable_value->cdate) ?  date('d-m-Y',strtotime($get_faculty_timetable_value->cdate)) : '' ?></td>
									<td><?php echo isset($get_faculty_timetable_value->branches_name) ?  $get_faculty_timetable_value->branches_name : '' ?>
									<?php
									if(!empty($get_faculty_timetable_value->branches_id)){
										$get_data = DB::table('users')
										->leftJoin('userbranches','users.id','=','userbranches.user_id')
										->leftJoin('userdetails','users.id','=','userdetails.user_id')
										->select('users.name as user_name','users.mobile as mobile')
										->where('userbranches.branch_id',$get_faculty_timetable_value->branches_id)
										->where('userdetails.degination','CENTER HEAD')
										->where('users.status',1)
										->get();
										$center_heads = "";
										if(count($get_data) > 0){
											foreach($get_data as $center_data){
												$center_heads .= $center_data->user_name."( ".$center_data->mobile." ) ,";
											}
											echo "<b>CH.-</b> ".rtrim($center_heads,',');
										}
									}
									
									?>
									</td>
									<td>
									<?php 
									$get_batches_name = "";
									$get_batches = DB::table('timetables')->select("batch.name as b_name")->leftJoin('batch','batch.id','=','timetables.batch_id')->where('timetables.is_deleted', '0')->where('timetables.time_table_parent_id', $get_faculty_timetable_value->id)->get();
									if(count($get_batches) > 0){
										foreach($get_batches as $vallll){
											$get_batches_name .= ', '.$vallll->b_name;
										}
									}
									echo isset($get_faculty_timetable_value->batch_name) ?  $get_faculty_timetable_value->batch_name : '';
									echo $get_batches_name;
									?>
									</td>
									<td><?=$get_faculty_timetable_value->studios_name?></td>
									<td><?php echo isset($get_faculty_timetable_value->subject_name) ?  $get_faculty_timetable_value->subject_name : '' ?></td>									
									<td><?php echo isset($get_faculty_timetable_value->assistant_name) ?  $get_faculty_timetable_value->assistant_name : '' ?>
									( <?php echo isset($get_faculty_timetable_value->assistant_mobile) ?  $get_faculty_timetable_value->assistant_mobile : '' ?> )</td> 
									
									
									
									<td><?=$schedule_duration?></td>
									<td><?=$get_faculty_timetable_value->faculty_name?></td>
									<td><?=$get_faculty_timetable_value->remark?></td>
									<td><?=$get_faculty_timetable_value->change_by_username?></td>
									
								</tr>
							<?php
							}
							
							$from_time         = new DateTime($get_faculty_value->from_time);
							$to_time           = new DateTime($get_faculty_value->to_time);
							$schedule_interval = $from_time->diff($to_time);
							$schedule_duration = $schedule_interval->format('%H : %I Hours');
							
							
							?>
							<tr>
								<td><?php echo isset($get_faculty_value->from_time) ?  date("h:i A", strtotime($get_faculty_value->from_time)) : '' ?></td>
								<td><?php echo isset($get_faculty_value->to_time) ?  date("h:i A", strtotime($get_faculty_value->to_time)) : '' ?></td>
								<td><?php echo isset($get_faculty_value->cdate) ?  date('d-m-Y',strtotime($get_faculty_value->cdate)) : '' ?></td>
								<td><?php echo isset($get_faculty_value->branches_name) ?  $get_faculty_value->branches_name : '' ?>
								<?php
								if(!empty($get_faculty_value->branches_id)){
									$get_data = DB::table('users')
									->leftJoin('userbranches','users.id','=','userbranches.user_id')
									->leftJoin('userdetails','users.id','=','userdetails.user_id')
									->select('users.name as user_name','users.mobile as mobile')
									->where('userbranches.branch_id',$get_faculty_value->branches_id)
									->where('userdetails.degination','CENTER HEAD')
									->where('users.status',1)
									->get();
									$center_heads = "";
									if(count($get_data) > 0){
										foreach($get_data as $center_data){
											$center_heads .= $center_data->user_name."( ".$center_data->mobile." ) ,";
										}
										echo "<b>CH.-</b> ".rtrim($center_heads,',');
									}
								}
								
								?>
								</td>
								<td>
								<?php 
								$get_batches_name = "";
								$get_batches = DB::table('timetables')->select("batch.name as b_name")->leftJoin('batch','batch.id','=','timetables.batch_id')->where('timetables.is_deleted', '0')->where('timetables.time_table_parent_id', $get_faculty_value->id)->get();
								if(count($get_batches) > 0){
									foreach($get_batches as $vallll){
										$get_batches_name .= ', '.$vallll->b_name;
									}
								}
								echo isset($get_faculty_value->batch_name) ?  $get_faculty_value->batch_name : '';
								echo $get_batches_name;
								?>
								</td>
								<td><?=$get_faculty_value->studios_name?></td>
								<td><?php echo isset($get_faculty_value->subject_name) ?  $get_faculty_value->subject_name : '' ?></td>									
								<td><?php echo isset($get_faculty_value->assistant_name) ?  $get_faculty_value->assistant_name : '' ?>
								( <?php echo isset($get_faculty_value->assistant_mobile) ?  $get_faculty_value->assistant_mobile : '' ?> )</td> 
								
								
								
								<td><?=$schedule_duration?></td>
								<td><?=$get_faculty_value->faculty_name?></td>
								<td><?=$get_faculty_value->remark?></td>
								<td>-</td>
								
							</tr>
							
							
							 
								
						</body>
					
					</table>
					<p><hr/></p>
					<?php
						}
					?>
					
					
				<?php 
					}
				}
				?>
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
<script src="<?php echo e(asset('laravel/public/admin/js/jquery.validate.min.js')); ?>"></script>


<script type="text/javascript">
	$("body").on("change", ".start_time,.end_time", function (e) {
		var start_time = $('.start_time').val();
		var end_time = $('.end_time').val();
		var str0="01/01/1970 " + start_time;
		var str1="01/01/1970 " + end_time;

		var diff=(Date.parse(str1)-Date.parse(str0))/1000/60;
		var hours=String(100+Math.floor(diff/60)).substr(1);
		var mins=String(100+diff%60).substr(1);
		$(".total_time").text(hours+':'+mins);
	});
</script>
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
		$('.select-multiple4').select2({
			width: '100%',
			placeholder: "Select Any",
			allowClear: true
		});
		
		$('.timepicker').timepicker({ 'step': 1, 'timeFormat': 'h:i A' });
	});
	
	
					
	 
</script>
<script type="text/javascript">

	  
	     
	
</script>


<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>$('.dateddmmyyy').datepicker({ dateFormat: 'dd-mm-yy' }).val();


</script>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.studiomanager', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/studiomanager/faculty_reports/timetable_history_reports.blade.php ENDPATH**/ ?>