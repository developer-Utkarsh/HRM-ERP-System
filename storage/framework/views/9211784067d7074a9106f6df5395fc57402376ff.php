
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Batch Report</h2>
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
								<form action="<?php echo e(route('admin.batch-reports')); ?>" method="get" name="filtersubmit">
									<div class="row">
										
										<div class="col-12 col-sm-6 col-lg-3 branch_loader">
											<label for="users-list-status">Location</label>
											<fieldset class="form-group">												
												<select class="form-control branch_location" name="branch_location" id="">
													<option value="">Select Any</option>
													<option value="jaipur" <?php if('jaipur' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>jaipur</option>
													<option value="jodhpur" <?php if('jodhpur' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>jodhpur</option>
													<option value="prayagraj" <?php if('prayagraj' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>prayagraj</option>
												</select>
											</fieldset>
										</div>
									
										<div class="col-12 col-md-3 branch_loader">
											<label for="users-list-status">Branch</label>
											<?php $branches = \App\Branch::where('status', '1')->orderByRaw('Field(id,37,42,40,41,38,48,49,53,52,54,55,56,36,39,43,44,45,46,47,50,51)')->get(); 
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
											<label for="users-list-status">Batch</label>
											<?php $batchs = \App\Batch::where('status', '1')->where('is_deleted', '0')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2 batch_id" name="batch_id[]" multiple>
													<option value="">Select Any</option>
													<?php if(count($batchs) > 0): ?>
													<?php $__currentLoopData = $batchs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value->id); ?>" 
														<?php if(in_array($value->id, (array) app('request')->input('batch_id'))): ?> 
															selected="selected" 
														<?php endif; ?>>
														<?php echo e($value->name); ?>

													</option>

													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php endif; ?>
												</select>												
											</fieldset>
											
											<input type="text" id="pasteValues" placeholder="Paste comma separated" style="width: 100%; margin-bottom: 10px;">

										</div>
										
										<div class="col-12 col-md-3">
											<label for="users-list-status">From Date</label>
											<fieldset class="form-group">												
												<input type="date" name="fdate" placeholder="Date" value="<?php echo e(app('request')->input('fdate')); ?>" class="form-control StartDateClass fdate">
											</fieldset>
										</div>
										
										<div class="col-12 col-md-3">
											<label for="users-list-status">To Date</label>
											<fieldset class="form-group">												
												<input type="date" name="tdate" placeholder="Date" value="<?php echo e(app('request')->input('tdate')); ?>" class="form-control EndDateClass tdate">
											</fieldset>
										</div>
										<div class="col-12 col-md-3">
											<label for="users-list-status">ERP Course ID</label>
											<fieldset class="form-group">												
												<input type="text" name="erp_id" placeholder="" value="<?php echo e(app('request')->input('erp_id')); ?>" class="form-control">
											</fieldset>
										</div>
										<div class="col-12 col-md-3">
											<label for="users-list-status">Class Type</label>
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
										</div>
										<div class="col-12 col-md-8 mt-2">
										<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="<?php echo e(route('admin.batch-reports')); ?>" class="btn btn-warning">Reset</a>
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
				$final_total_schedule = new DateTime('00:00');
				$final_total_schedule_last = new DateTime('00:00');
				if (count($get_batches) > 0) {
					
					foreach ($get_batches as $batchArray) {
						//if(count($batchArray->batch_timetables->studio) > 0){ //echo '<pre>'; print_r($batchArray->studio);
							$dataFound++;
						?>
						<table class="table data-list-view" style=''>
							<head>
								<tr style="">
									<th colspan="3"><h3>Batch Name : <?php echo $batchArray->name; ?> </h3></th>
								</tr>
							</head>
							<body>
							<tr style="">
							<td style="border: 1px solid;">
						
						<table class="table data-list-view" style=''>
						 
							<head>
								<tr style="">
									<th scope="col">Branch Name</th>
									<th scope="col">Studio Name</th>
									<th scope="col">Assistant Name</th>
									<th scope="col">Start Time</th>
									<th scope="col">End Time</th>
									<th scope="col">Date</th>
									<th scope="col">Faculty Name</th>
									
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
								$batch_total_schedule_time = new DateTime('00:00');
								foreach ($batchArray->batch_timetables as $value) {   //echo '<pre>'; print_r($value->studio->name);die;
								?>
								<?php
								
								$schedule_duration  = "00 : 00 Hours"; 	
								$from_time         = new DateTime($value->from_time);
								$to_time           = new DateTime($value->to_time);
								$schedule_interval = $from_time->diff($to_time);
								$schedule_duration = $schedule_interval->format('%H : %I Hours');
								if(!empty($value->studio) && !empty($value->studio->branch->name)){
									$final_total_schedule->add($schedule_interval);
									$batch_total_schedule_time->add($schedule_interval);
								?>
									<tr>
										<td><?php echo isset($value->studio->branch->name) ?  $value->studio->branch->name : '' ?></td>
										<td><?php echo isset($value->studio->name) ?  $value->studio->name : '' ?></td>
										<td><?php echo isset($value->assistant->name) ?  $value->assistant->name : '' ?></td>
										<td><?php echo isset($value->from_time) ?  date('h:i A', strtotime($value->from_time)) : '' ?></td>
										<td><?php echo isset($value->to_time) ?  date('h:i A', strtotime($value->to_time)) : '' ?></td>
										<td><?php echo isset($value->cdate) ?  date('d-m-Y',strtotime($value->cdate)) : '' ?></td>
										<td><?php echo isset($value->faculty->name) ?  $value->faculty->name : '' ?></td>
										
										<td><?php echo isset($value->course->name) ?  $value->course->name : '' ?></td>
										<td><?php echo isset($value->subject->name) ?  $value->subject->name : '' ?></td>
										<td><?php echo isset($value->chapter->name) ?  $value->chapter->name : '' ?></td>
										<td><?php echo isset($value->topic->name) ?  $value->topic->name : '' ?></td>
										<td><?php echo isset($value->online_class_type) ?  $value->online_class_type : '' ?></td>
										<td><?php echo $schedule_duration ?></td>
									</tr>
									<?php } } ?>
							</body>
							<tfoot>
								<th colspan="9"></th>
								<th colspan="4">
								<b style="color: red;font-size: 14px;">
								<?php
								$totalDays = $final_total_schedule_last->diff($batch_total_schedule_time)->format("%a");
								$totalHours = $final_total_schedule_last->diff($batch_total_schedule_time)->format("%H");
								$totalMinute = $final_total_schedule_last->diff($batch_total_schedule_time)->format("%I");
								?>
								Total Schedule Time: <?php echo ($totalDays*24)+$totalHours. ":" . $totalMinute;?> Hours 
								</b>
								</th>
							
							</tfoot>
						
						</table>
				<p><hr/></p>
				
				
						
				</td>
				</tr>
				</body>
				</table>
				<?php
					//}
					}
				}
				?>
				<p> <b style="color: red;font-size: 18px;">
				<?php
				$totalDays = $final_total_schedule_last->diff($final_total_schedule)->format("%a");
				$totalHours = $final_total_schedule_last->diff($final_total_schedule)->format("%H");
				$totalMinute = $final_total_schedule_last->diff($final_total_schedule)->format("%I");
				?>
				Search According Total Schedule Time: <?php echo ($totalDays*24)+$totalHours. ":" . $totalMinute;?> Hours 
				</b>
				</p>
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
	 $(document).ready(function () {
        $("#pasteValues").on("paste", function (event) {
			setTimeout(function () {
				let pastedText = $("#pasteValues").val().trim();
				let pastedItems = pastedText.split(/\n|,|;/).map(item => item.trim()); // Split by new line, comma, or semicolon
				alert(pastedItems);
				let matchedValues = [];

				$(".batch_id option").each(function () {
					let optionText = $(this).text().trim();
					let optionValue = $(this).val();

					// Check if the pasted text matches the option text
					if (pastedItems.includes(optionText)) {
						matchedValues.push(optionValue);
					}
				});

				// Select matched options in Select2 dropdown
				$(".batch_id").val(matchedValues).trigger("change");
			}, 100);
        });
    });
	
	
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
			window.open("<?php echo URL::to('/admin/'); ?>/batch-report-pdf?" + Object.keys(data).map(function (k) {
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
				url : '<?php echo e(route('admin.get-branchwise-studio')); ?>',
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
				url : '<?php echo e(route('admin.get-branchwise-assistant')); ?>',
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
				url : '<?php echo e(route('admin.get-branchwise-assistant')); ?>',
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
				url : '<?php echo e(route('admin.get-location-wise-branch')); ?>',
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/batch_reports/index.blade.php ENDPATH**/ ?>