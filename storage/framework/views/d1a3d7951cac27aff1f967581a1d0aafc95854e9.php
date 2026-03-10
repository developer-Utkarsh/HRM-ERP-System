
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Studio Report</h2>
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
								<form action="<?php echo e(route('studiomanager.studio-reports')); ?>" method="get" name="filtersubmit">
									<div class="row">
									
										<div class="col-12 col-sm-6 col-lg-3 branch_loader" style="display:none;">
											<label for="users-list-status">Location</label>
											<fieldset class="form-group">												
												<select class="form-control branch_location" name="branch_location" id="">
													<option value="">Select Any</option>
													<option value="jaipur" <?php if('jaipur' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>jaipur</option>
													<option value="jodhpur" <?php if('jodhpur' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>jodhpur</option>
													<option value="delhi" <?php if('delhi' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>delhi</option>
													<option value="prayagraj" <?php if('prayagraj' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>prayagraj</option>
												</select>
											</fieldset>
										</div>
										
										<div class="col-12 col-md-3 branch_loader">
											<label for="users-list-status">Branch</label>
											<?php $branches = \App\Branch::where('status', '1')->orderByRaw('Field(id,37,42,40,41,38,48,49,53,52,54,55,56,36,39,43,44,45,46,47,50,51,63)')->get(); 
											//echo $branches;
											?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 branch_id" name="branch_id[]" id="" multiple>
													<option value="">Select Any</option>
													<?php if(count($branches) > 0): ?>
													<?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>"  <?php if(!empty(app('request')->input('branch_id')) && in_array($value->id, app('request')->input('branch_id'))): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php endif; ?>
												</select>
												<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
											</fieldset>
										</div>
										
										<div class="col-12 col-md-3">
											<label for="users-list-status">Studio</label>
											<?php $studios = \App\Studio::where('status', '1');
											if(app('request')->input('branch_id')){
												$studios->where('branch_id',app('request')->input('branch_id'));
											}
											$studios = $studios->orderBy('id','desc')->get();
											?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple3 studio_id" name="studio_id" id="">
													<option value="">Select Any</option>
													<?php if(count($studios) > 0): ?>
													<?php $__currentLoopData = $studios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" <?php if($value->id == app('request')->input('studio_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php endif; ?>
												</select>												
											</fieldset>
										</div>
										
										<input type="hidden" class="assistant_id_get" value="<?php echo e(app('request')->input('assistant_id')); ?>">
										
										<div class="col-12 col-md-3">
											<label for="users-list-status">Assistants</label>
											<?php $assistants = \App\User::where('role_id', '3')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2 assistant_id" name="assistant_id">
													<option value="">Select Any</option>
													<?php if(count($assistants) > 0): ?>
													<?php $__currentLoopData = $assistants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" <?php if($value->id == app('request')->input('assistant_id')): ?> selected="selected" <?php endif; ?>><?php echo e($value->name); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php endif; ?>
												</select>												
											</fieldset>
										</div>
										
										<div class="col-12 col-sm-6 col-lg-2">
											<label for="users-list-status">Type</label>
											<fieldset class="form-group">												
												<select class="form-control type" name="type">
													<option value="">Select Type</option>
													<option value="Online" <?php if('Online' == app('request')->input('type')): ?> selected="selected" <?php endif; ?>>Online</option>
													<option value="Offline" <?php if('Offline' == app('request')->input('type')): ?> selected="selected" <?php endif; ?>>Offline</option>
												</select>												
											</fieldset>
										</div>
										
										<div class="col-12 col-md-3">
											<label for="users-list-status">Date</label>
											<fieldset class="form-group">												
												<input type="date" name="fdate" placeholder="Date" value="<?php echo e(app('request')->input('fdate')); ?>" class="form-control StartDateClass fdate">
											</fieldset>
										</div>
										
										<div class="col-12 col-md-3" style="display:none;">
											<label for="users-list-status">To Date</label>
											<fieldset class="form-group">												
												<input type="date" name="tdate" placeholder="Date" value="<?php echo e(app('request')->input('tdate')); ?>" class="form-control EndDateClass tdate">
											</fieldset>
										</div>
										
										
										<div class="col-12 col-md-6 mt-2">
										<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary" name="search" value="search">Search</button>
											<a href="<?php echo e(route('studiomanager.studio-reports')); ?>" class="btn btn-warning">Reset</a>
											<a href="javascript:void(0)" id="download_pdf" class="btn btn-primary">Export in PDF</a>
											<a href="javascript:void(0)" id="download_excel" class="btn btn-primary">Export in Excel</a>  
										</fieldset>
									</div>
									</div>
									
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
				
				<?php 
				$dataFound = 0;
				if (count($get_studios) > 0) {
					
					foreach ($get_studios as $branchArray) {
						if(count($branchArray->studio) > 0){ //echo '<pre>'; print_r($branchArray->studio);
							$dataFound++;
						?>
						<table class="table data-list-view" style=''>
							<head>
								<tr style="">
									<th colspan="3"><h3>Branch Name : <?php echo $branchArray->name; ?> <?php if($branchArray->nickname!=""){ ?>(<?=$branchArray->nickname;?>)<?php } ?></h3></th>
								</tr>
							</head>
							<body>
							<tr style="">
							<td style="border: 1px solid;">
						<?php
						foreach ($branchArray->studio as $value) { 
						if(count($value->timetable) > 0){
						?>
						<table class="table data-list-view" style=''>
						 
							<head>
								<tr style="">
									<th colspan="12"><?php if($branchArray->nickname!=""){ ?>(<?=$branchArray->nickname;?>)<?php } ?> <b>Studio Name : <?php echo $value->name; ?> - <?php echo $value->capacity; ?></b></th>
									<!--th colspan="3"><b>Assistant Name : <?php echo isset($value->assistant->name) ?  $value->assistant->name : ''; ?></b></th>
									<th colspan="3"><b>Assistant Mob. : <?php echo isset($value->assistant->mobile) ?  $value->assistant->mobile : ''; ?></b></th-->
								</tr>
							</head>
							<head>
								<tr style="">
									<th scope="col">Assistant Name</th>
									<th scope="col">Start Time</th>
									<th scope="col">End Time</th>
									<th scope="col">Date</th>
									<th scope="col">Faculty Name</th>
									<th scope="col">Batch Name</th>
									<th scope="col">Course Name</th>
									<th scope="col">Subject Name</th>
									<th scope="col">Chapter Name</th>
									<th scope="col">Topic Name</th>
									<th scope="col">Type</th>
									<th scope="col">Schedule Time</th>
								</tr>
							</head>
							<body>
								<?php
								$total_minutes  = 0;
								foreach($value->timetable as $key => $timetable){
								
								$schedule_duration  = "00 : 00 Hours"; 	
								$from_time         = new DateTime($timetable->from_time);
								$to_time           = new DateTime($timetable->to_time);
								$schedule_interval = $from_time->diff($to_time);
								$schedule_duration = $schedule_interval->format('%H : %I Hours');
								
								$minutes = ($schedule_interval->h * 60) + $schedule_interval->i;
										$total_minutes += $minutes;
								
								?>
									<tr>
										<td><?php echo isset($timetable->assistant->name) ?  $timetable->assistant->name : '' ?></td>
										<td><?php echo isset($timetable->from_time) ?  date('h:i A', strtotime($timetable->from_time)) : '' ?></td>
										<td><?php echo isset($timetable->to_time) ?  date('h:i A', strtotime($timetable->to_time)) : '' ?></td>
										<td><?php echo isset($timetable->cdate) ?  $timetable->cdate : '' ?></td>
										<td><?php echo isset($timetable->faculty->name) ?  $timetable->faculty->name : '' ?></td>
										<td><?php echo isset($timetable->batch->name) ?  $timetable->batch->name : '' ?></td>
										<td><?php echo isset($timetable->course->name) ?  $timetable->course->name : '' ?></td>
										<td><?php echo isset($timetable->subject->name) ?  $timetable->subject->name : '' ?></td>
										<td><?php echo isset($timetable->chapter->name) ?  $timetable->chapter->name : '' ?></td>
										<td><?php echo isset($timetable->topic->name) ?  $timetable->topic->name : '' ?></td>
										<td><?php echo isset($timetable->online_class_type) ?  $timetable->online_class_type : '' ?></td>
										<td><?php echo $schedule_duration ?></td>
									</tr>
								<?php
								}
								//echo $plain_hr;die;
								?>
								<tr>
									<td colspan="11"><b>Total Schedule Hours</b></td>
									<td>	<?php 
											$total_hours = floor($total_minutes / 60);
											$remaining_minutes = $total_minutes % 60;

											echo $total_schedule = sprintf('%02d : %02d Hours', $total_hours, $remaining_minutes);
										?>
									</td>
								</tr>
							</body>
						
						</table>
				<p><hr/></p>
						<?php } } ?>
				</td>
				</tr>
				</body>
				</table>
				<?php
					}
					}
				}
				?>
		<style>
		hr{background:#000;}
		</style>
					 
				</div>       
<?php
if($dataFound==0){
	?>
	<p style="text-align:center;"><h3>Data not found.</h3></p>
	<?php
}?>
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
		data.assistant_id = $('.assistant_id').val(),
		data.fdate = $('.fdate').val(),
		data.tdate = $('.tdate').val(),
		data.type = $('.type').val(),
		window.location.href = "<?php echo URL::to('/studiomanager/'); ?>/studio-report-report-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});
	
	$("body").on("click", "#download_pdf", function (e) {
		/* if ($userTable.data().count() == 0) {
			swal("Warning!", "Not have any data!", "warning");
			return;
		} */
		var data = {};
			data.studio_id = $('.studio_id').val(),
			data.branch_id = $('.branch_id').val(),
			data.assistant_id = $('.assistant_id').val(),
			data.fdate = $('.fdate').val(),
			data.tdate = $('.tdate').val(),
			data.type = $('.type').val(),
			// data.tdate = $('.tdate').val(),
			window.open("<?php echo URL::to('/studiomanager/'); ?>/studio-report-pdf?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&'));
		/* window.location.href = "<?php echo URL::to('/admin/'); ?>/studio-report-pdf?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&'); */
	});
</script>
<script type="text/javascript">
	$(".branch_id").on("change", function () {
		var branch_id = $(".branch_id option:selected").attr('value');
		var assistant_id = $("input[name=assistant_id]").val();
		if (branch_id) {
			$.ajax({
				beforeSend: function(){
					// $(".branch_loader i").show();
				},
				type : 'POST',
				url : '<?php echo e(route('studiomanager.get-branchwise-studio')); ?>',
				data : {'_token' : '<?php echo e(csrf_token()); ?>', 'branch_id': branch_id},
				dataType : 'html',
				success : function (data){
					// $(".branch_loader i").hide();
					$('.studio_id').empty();
					$('.studio_id').append(data);
				}
			});
			
			$.ajax({
				beforeSend: function(){
					// $(".branch_loader i").show();
				},
				type : 'POST',
				url : '<?php echo e(route('studiomanager.get-branchwise-assistant')); ?>',
				data : {'_token' : '<?php echo e(csrf_token()); ?>', 'branch_id': branch_id, 'assistant_id': assistant_id},
				dataType : 'html',
				success : function (data){
					// $(".branch_loader i").hide();
					$('.assistant_id').empty();
					$('.assistant_id').append(data);
				}
			});
			
			
		}
	});
	
	$(document).ready(function() {
		var branch_id = $(".branch_id option:selected").attr('value');
		var assistant_id = $(".assistant_id_get").val();
		if (branch_id) {
			$.ajax({
				beforeSend: function(){
					// $(".branch_loader i").show();
				},
				type : 'POST',
				url : '<?php echo e(route('studiomanager.get-branchwise-assistant')); ?>',
				data : {'_token' : '<?php echo e(csrf_token()); ?>', 'branch_id': branch_id, 'assistant_id': assistant_id},
				dataType : 'html',
				success : function (data){
					// $(".branch_loader i").hide();
					$('.assistant_id').empty();
					$('.assistant_id').append(data);
				}
			});
		}
	});
	
	$(".branch_location").on("change", function () {
		var b_location = $(this).val();
		if (b_location) {
			$.ajax({
				beforeSend: function(){
					// $(".branch_loader i").show();
				},
				type : 'POST',
				url : '<?php echo e(route('studiomanager.get-location-wise-branch')); ?>',
				data : {'_token' : '<?php echo e(csrf_token()); ?>', 'b_location': b_location},
				dataType : 'html',
				success : function (data){
					// $(".branch_loader i").hide();
					$('.branch_id').empty();
					$('.branch_id').append(data);
				}
			});
			
		}
	});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.studiomanager', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/studiomanager/studio_reports/index.blade.php ENDPATH**/ ?>