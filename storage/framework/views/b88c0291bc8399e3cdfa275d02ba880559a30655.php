
<?php $__env->startSection('content'); ?>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Studio Availability Report</h2>
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
						<div class="card-body" style="padding: 8px 0px 0px 18px;">
							<div class="users-list-filter">
								<form action="<?php echo e(route('admin.studio-availability')); ?>" method="get" name="filtersubmit">
									<div class="row">

										<div class="col-12 col-md-3">
											<label for="users-list-status">Date</label>
											<fieldset class="form-group">												
												<input type="date" name="cdate" placeholder="Date" value="<?php echo e(app('request')->input('cdate')); ?>" class="form-control StartDateClass cdate">
											</fieldset>
										</div>

										<div class="col-12 col-md-3 branch_loader">
											<label for="users-list-status">Location</label>
											<fieldset class="form-group">												
												<select class="form-control branch_location" name="branch_location" id="">
													<option value="">Select Any</option>
													<option value="jaipur" <?php if('jaipur' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>jaipur</option>
													<option value="jodhpur" <?php if('jodhpur' == app('request')->input('branch_location')): ?> selected="selected" <?php endif; ?>>jodhpur</option>
												</select>
											</fieldset>
										</div>
									
										<div class="col-12 col-md-3 branch_loader">
											<label for="users-list-status">Branch</label>
											<?php $branches = \App\Branch::where('status', '1')->where('is_deleted', '0');
												if(!empty(app('request')->input('branch_location'))){
													$branches->where('branch_location', app('request')->input('branch_location'));
												}
												$branches = $branches->orderByRaw('Field(id,37,42,40,41,38,48,49,53,52,54,55,56,36,39,43,44,45,46,47,50,51)')->get(); 
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
								
										<div class="col-12 col-md-2 mt-2 text-right">
											<fieldset class="form-group">		
												<button type="submit" class="btn btn-primary">Search</button><br><br>
												<a href="javascript:void(0)" id="download_pdf" class="btn btn-primary d-none">Export in PDF</a>
												<a href="javascript:void(0)" id="download_excel" class="btn btn-primary d-none">Export in Excel</a>
											</fieldset>
										</div>
									</div>
									
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
				<style>
				.table thead th {
					font-size: 10px;
				}
				.table tbody td {
					font-size: 10px;
				}
				.table th, .table td {
					padding: 1ch;
				}
				</style>
				<?php 
				$dataFound = 0;
				if(count($get_studios)>0) { ?>
					<table class="table data-list-view" style='' >
						<thead>
							<tr id="">
								<th>Branch</th>
								<th>Studio /<br/>Class Room</th>
								<th>Capacity</th>
								<th>6:00 AM</th>
								<th>7:00 AM</th>
								<th>8:00 AM</th>
								<th>9:00 AM</th>
								<th>10:00 AM</th>
								<th>11:00 AM</th>
								<th>12:00 PM</th>
								<th>1:00 PM</th>
								<th>2:00 PM</th>
								<th>3:00 PM</th>
								<th>4:00 PM</th>
								<th>5:00 PM</th>
								<th>6:00 PM</th>
								<th>7:00 PM</th>
							</tr>
						</thead>
						<tbody>
					
						<?php 
					    foreach($get_studios as $branchArray) {
							if(count($branchArray->studio)>0){ //echo '<pre>'; print_r($branchArray->studio);
								$dataFound++;
								$rowspan=count($branchArray->studio);
								foreach($branchArray->studio as $value) { ?>
									
										<tr>
										    <?php 
                                             if($rowspan!=0){
										    ?>
											<td rowspan="<?=$rowspan;?>"><?php echo $branchArray->name; ?></td>
											<?php } $rowspan=0; ?>
											<td><?php echo $value->name; ?></td>
											<td><?php echo $value->capacity; ?></td>
											<?php
											if(count($value->timetable)>0){
												$tt_count=count($value->timetable);
												$tt=0;
												$batch_name=$batch_id=$batches=$from_time=$to_time="";
												$last_to_time = "";
												$ii = 0;
												$add_1 = 0;
												$to_time_dot = "";
												foreach($value->timetable as $key => $timetable){ 
													$tt=$tt+1;
													if($batch_id==$timetable->batch_id){
													  $to_time=$timetable->to_time;
													  $to_time_dot = str_replace(':','.',$to_time);
													}

													if($batch_id!=0 && $batch_id!=$timetable->batch_id){ 
														$batch_id=0;
														$batch_name =date('h:i',strtotime($from_time)).'-'.date('h:i',strtotime($to_time)).' ('.$batch_name.')';
														$batches.=$from_time.'-'.$to_time.' ('.$batch_name.')<br>';
														$from_time_new = (int)$from_time;
														if($last_to_time != "" && $last_to_time != (int)$from_time_new){
															?>
															<td colspan="<?=(int)$from_time_new-(int)$last_to_time ;?>" dd="1">  </td>
															<?php
														}
														if($ii==0){
															if((int)$from_time > 6){
																?>
																<td colspan="<?=(int)$from_time-6 ;?>"  dd="2">  </td>
																<?php
															}
														}
														$ii++;
														?>
														<td colspan="<?=(int)$to_time-(int)$from_time ;?>" dd="3">
															<?=$batch_name?> 
															<?php if(!empty($batch_code)){
																$curl = curl_init();
																curl_setopt_array($curl, array(
																  CURLOPT_URL => 'https://utkarshpublications.com/soft/apis/offlineapp-liveapis/registered-student.php',
																  CURLOPT_RETURNTRANSFER => true,
																  CURLOPT_ENCODING => '',
																  CURLOPT_MAXREDIRS => 10,
																  CURLOPT_TIMEOUT => 0,
																  CURLOPT_FOLLOWLOCATION => true,
																  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
																  CURLOPT_CUSTOMREQUEST => 'POST',
																  CURLOPT_POSTFIELDS => array('query' => 'total_admission','batch_code' =>$batch_code),
																));

																$response = curl_exec($curl);
																curl_close($curl);
																$response=json_decode($response,true);
																?>
																<strong style="background: red;border-radius: 50%;padding: 3px;color: #fff;">
																<?php echo $response['total_admission']; ?>
																</strong>
																<?php

															}else{
																?>
																<strong style="background: red;border-radius: 50%;padding: 3px;color: #fff;">
																0
																</strong>
																<?php
															} ?>
														</td>
														<?php

														$last_to_time = (int)$to_time;
													}
													
													if($tt_count==$tt){
														$batch_code=$timetable->batch->batch_code;
														if($batch_id!=$timetable->batch_id){
															$from_time=$timetable->from_time;
														}
														$to_time=$timetable->to_time;
														$to_time_dot = str_replace(':','.',$to_time);
														if($to_time_dot > 19 && (int)$to_time < 20){
															$add_1 = 1;
														}
														$batch_name =date('h:i',strtotime($from_time)).'-'.date('h:i',strtotime($to_time)).' ('.$timetable->batch->name.')';
														
														$from_time_new = (int)$from_time;
														if($last_to_time != "" && $last_to_time != (int)$from_time_new){
															?>
															<td colspan="<?=(int)$from_time_new-(int)$last_to_time ;?>" dd="4<?=$last_to_time.'/'.$from_time_new?>">  </td>
															<?php
														}
														else if($last_to_time ==''){
															?>
															<td colspan="<?=(int)$from_time-6 ;?>" dd="5">  </td>
															<?php
														}
														?>


															<td colspan="<?=(int)$to_time-(int)$from_time+$add_1 ;?>" dd="6" tt="<?php echo e($to_time_dot); ?>">
																<?=$batch_name?>
																<?php if(!empty($batch_code)){
																	$curl = curl_init();
																	curl_setopt_array($curl, array(
																	  CURLOPT_URL => 'https://utkarshpublications.com/soft/apis/offlineapp-liveapis/registered-student.php',
																	  CURLOPT_RETURNTRANSFER => true,
																	  CURLOPT_ENCODING => '',
																	  CURLOPT_MAXREDIRS => 10,
																	  CURLOPT_TIMEOUT => 0,
																	  CURLOPT_FOLLOWLOCATION => true,
																	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
																	  CURLOPT_CUSTOMREQUEST => 'POST',
																	  CURLOPT_POSTFIELDS => array('query' => 'total_admission','batch_code' =>$batch_code),
																	));

																	$response = curl_exec($curl);
																	curl_close($curl);
																	$response=json_decode($response,true);
																	?>
																	<strong style="background: red;border-radius: 50%;padding: 3px;color: #fff;">
																	<?php echo $response['total_admission']; ?>
																	</strong>
																	<?php

																}else{
																	?>
																	<strong style="background: red;border-radius: 50%;padding: 3px;color: #fff;">
																	0
																	</strong>
																	<?php
																	
																} ?>
															</td>

													<?php }

													if($batch_id==0){
													  $from_time=$timetable->from_time;
													  $to_time=$timetable->to_time;
													  $to_time_dot = str_replace(':','.',$to_time);
													}

													$batch_name=$timetable->batch->name;
													$batch_id=$timetable->batch_id;
													$batch_code=$timetable->batch->batch_code;
													
													
												}
												
												if($to_time_dot < 19){
													?>
													<td colspan="<?=19-(int)$to_time+1 ;?>" dd="7" tt="<?php echo e($to_time_dot); ?>">  </td>
													<?php
												}
												
											}
											else{
												?>
												<td colspan="14"></td>
												<?php
											}
											
											$batches="";
										?>
											<!--td><?php //echo $batches;$batches="";?></td-->
										</tr>
									<?php  
								} 
							}
						} ?>
						
						</tbody>
					</table>
                <?php 
				}


				if(1==2 && count($get_batches)>0) {
					
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
								foreach ($batchArray->batch_timetables as $value) {   //echo '<pre>'; print_r($value->studio->name);die;
								?>
								<?php
								
								$schedule_duration  = "00 : 00 Hours"; 	
								$from_time         = new DateTime($value->from_time);
								$to_time           = new DateTime($value->to_time);
								$schedule_interval = $from_time->diff($to_time);
								$schedule_duration = $schedule_interval->format('%H : %I Hours');
								if(!empty($value->studio) && !empty($value->studio->branch->name)){
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
<style>

 
.sticky {
  position: fixed;
  top: 0px;
  width: 1260px;
  z-index:99999999999;
  display: table;

}
 
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>

<script>
window.onscroll = function() {myFunction()};

var header = document.getElementById("myHeader");
var sticky = header.offsetTop;

function myFunction() {
  if (window.pageYOffset > sticky) {
    header.classList.add("sticky");
  } else {
    header.classList.remove("sticky");
  }
}
</script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple1').select2({
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
		data.cdate = $('.cdate').val(),
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
			data.cdate = $('.cdate').val(),
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/batch_reports/studio_availability.blade.php ENDPATH**/ ?>