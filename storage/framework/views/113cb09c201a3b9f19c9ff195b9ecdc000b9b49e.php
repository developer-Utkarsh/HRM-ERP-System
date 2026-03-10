
<?php $__env->startSection('content'); ?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
 <style>
	body {
		background-image: url(/laravel/public/Gradient-BG.png);
		background-size: 100% 100%;
		background-repeat: no-repeat;
		background-attachment: fixed;
	}
	
	.table tbody td{
		font-size: 14px !important;
	}
	
    .table-responsive-stack tr {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-orient: horizontal;
  -webkit-box-direction: normal;
      -ms-flex-direction: row;
          flex-direction: row;
}


.table-responsive-stack td,
.table-responsive-stack th {
   display:block;
/*      
   flex-grow | flex-shrink | flex-basis   */
   -ms-flex: 1 1 auto;
    flex: 1 1 auto;
}

.table-responsive-stack .table-responsive-stack-thead {
   font-weight: bold;
}

@media  screen and (max-width: 770px) {
   .table-responsive-stack tr {
      -webkit-box-orient: vertical;
      -webkit-box-direction: normal;
          -ms-flex-direction: column;
              flex-direction: column;
      border-bottom: 3px solid #ccc;
      display:block;
      
   }
   /*  IE9 FIX   */
   .table-responsive-stack td {
      float: left\9;
      width:100%;
   }
}
.table tbody + tbody {
    border-top: 2px solid #ccc;
}

input{
	border: none;
	border-bottom: dotted 1px #000;
	background: transparent;
	width: 100%;
}

.table tbody td {
	color: #000 !important;
}

</style>

<div class="app-content content" style="margin: 0px !important;">
	<div class="content-wrapper" style="margin-top: 0px !important;">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<!--h2 class="content-header-title float-left mb-0">क्लास टेस्ट के बारे में विस्तृत रिपोर्ट </h2-->
						 
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				<div class="card  mb-0 bg-dark text-light text-center">
					<div class="card-content collapse show ">
						<div class="card-body">
							<div class="users-list-filter">
								<h2 class="content-header-title mb-0" style="color:#fff !important;">क्लास टेस्ट के बारे में विस्तृत रिपोर्ट </h2>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
				<strong style="color:red;">
				<?php
				if(!empty($errors)){
					// echo "All fields are required";
					echo "<br/>";
					echo $errors->first().'<br/><br/>';
				}
					
				?>
				</strong>
				<?php 
				if (!empty($user_id)) {
				?>
             <!--  table-responsive-stack -->
				<form action="<?php echo e(route('test_report_save')); ?>" method="post" enctype="multipart/form-data">
					<?php echo csrf_field(); ?>
					
					<table class="table table-bordered table-striped table-condensed " id="tableOne">
					 
						 
						<thead>
							<!--tr style="">
								<th scope="col">&nbsp;</th>
								<th scope="col">&nbsp;</th>
							</tr-->
						</thead>
						<tbody>
							<?php
							$whereCond = '1=1';;
							
							$whereCond .= ' AND timetables.id = "'.$tt_id.'" ';
							$get_timetable = DB::table('timetables')
													  ->select('timetables.*','studios.name as studios_name','branches.name as branches_name','branches.id as branches_id','batch.name as batch_name','batch.batch_code as batch_code','course.name as course_name','subject.name as subject_name')
													  ->leftJoin('studios', 'studios.id', '=', 'timetables.studio_id')
													  ->leftJoin('branches', 'branches.id', '=', 'studios.branch_id')
													  ->leftJoin('batch', 'batch.id', '=', 'timetables.batch_id')
													  ->leftJoin('course', 'course.id', '=', 'timetables.course_id')
													  ->leftJoin('subject', 'subject.id', '=', 'timetables.subject_id')
													  // ->where('timetables.schedule_type', 'test')
													  ->whereRaw($whereCond)
													  ->first();
							$batch_array = array();
							$batch_array[] = $get_timetable->batch_id;
							$batch_name = isset($get_timetable->batch_name) ?  $get_timetable->batch_name : '';
							$batch_code = [];
							if(!empty($get_timetable->batch_code)){
								$batch_code[] = $get_timetable->batch_code;
							}
							
							$get_batch_data = DB::table('timetables')
												  ->select('timetables.batch_id','batch.name as batch_name','batch.batch_code as batch_code')
												  ->leftJoin('batch', 'batch.id', '=', 'timetables.batch_id')
												  ->where('timetables.is_deleted', '0')
												  ->where('timetables.is_publish', '1')
												  ->where('timetables.online_class_type', 'Test')
												  ->where('timetables.time_table_parent_id', $tt_id)
												  ->get();
							if(count($get_batch_data) > 0){
								foreach($get_batch_data as $bval){
									if(!in_array($bval->batch_id,$batch_array)){
										$batch_array[] = $bval->batch_id;
										$batch_name .=", ".$bval->batch_name;
										$batch_code[] = $bval->batch_code;
									}
								}
							}
							
							?>
							<input type="hidden" name="user_id" value="<?php echo e($user_id); ?>">
							<input type="hidden" name="tt_id" value="<?php echo e($tt_id); ?>">
							<input type="hidden" name="fdate" value="<?php echo e($get_timetable->cdate); ?>">
								<tr style="border-bottom-style:hidden;">
									<td style="font-weight:900;">1. बैच का स्थान व बैच का नाम</td>
								</tr>
								<tr style="border-bottom-style:hidden;">
									<td><?php echo $batch_name; ?></td>
								</tr>
								<tr style="border-bottom-style:hidden;">
									<td style="font-weight:900;">2. टेस्ट का विषय </td>
								</tr>
								<tr style="border-bottom-style:hidden;">
									<td><?php echo isset($get_timetable->subject_name) ?  $get_timetable->subject_name : '' ?></td>
								</tr>
								<tr style="border-bottom-style:hidden;">
									<td style="font-weight:900;">3.टेस्ट लेने की दिनांक-</td>
								</tr>
								<tr style="border-bottom-style:hidden;">
									<td><?php echo isset($get_timetable->cdate) ?  date('d-F-Y',strtotime($get_timetable->cdate)) : '' ?></td>
								</tr>
								<tr style="border-bottom-style:hidden;">
									<td style="font-weight:900;">4. बैच में विद्यार्थियों की कुल संख्या-</td>
								</tr>
								<tr style="border-bottom-style:hidden;">
									<td>
									<?php 
									$total_admission = 0;
									
									if(!empty($batch_code)){
										foreach($batch_code as $b_code){
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
											  CURLOPT_POSTFIELDS => array('query' => 'total_admission','batch_code' =>$b_code),
											));

											$response = curl_exec($curl);
											curl_close($curl);
											$response=json_decode($response,true);
											if(!empty($response)){
												$total_admission +=  $response['total_admission'];
											}
										}
									}?>
									<input type="hidden" min="0" name="q1" class="" value="<?php echo e($total_admission); ?>" required>
									<strong><?php echo e($total_admission); ?></strong>
									</td>
								</tr>

								<tr style="border-bottom-style:hidden;">
									<td style="font-weight:900;">5. टेस्ट का प्रकार </td>
								</tr>
								<tr style="border-bottom-style:hidden;">
									<td>
										<table width="100%" border="0">
											<tr style="background-color:transparent;">												
												<td class="radio-inline" width="50%">
													<input type="radio" style="width: 5%;bottom: -2px;position: relative;width:30px;" value="Offline" id="q7Offline" name="q21" class="q21" <?php echo e(old('q21', isset($get_detail->q21) && $get_detail->q21=='Offline'?'checked':'')); ?> > <label for="q7Offline">Offline</label>
												</td>
												<td class="radio-inline" width="50%">
													<input type="radio" style="width: 5%;bottom: -2px;position: relative;width:30px;" value="Online" id="q7Online" name="q21" class="q21" <?php echo e(old('q21', isset($get_detail->q21) && $get_detail->q21=='Online'?'checked':'')); ?> > <label for="q7Online">Online</label>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
						<table class="table table-bordered table-striped table-condensed forHide" style="display: <?php echo e((isset($get_detail->q21) && $get_detail->q21=='Online'?'none':'table')); ?>">
							
							<tbody>
									<tr style="border-bottom-style:hidden;">
										<td style="font-weight:900;">6. टेस्ट देने वालों की संख्या</td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td><input type="number" min="0" max="<?php echo e($total_admission); ?>" name="q2" class="" value="<?php echo e(old('q2', isset($get_detail->q2)?$get_detail->q2:'')); ?>" ></td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td style="font-weight:900;">7. इस टेस्ट की सूचना कौनसी तारीख़ को दी गई व किसने </td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td>
										<input type="date" name="q3" class="" value="<?php echo e(old('q3', isset($get_detail->q3)?$get_detail->q3:'')); ?>" style="width:100%;"></br>
										<input type="text" name="q4" class="" value="<?php echo e(old('q4', isset($get_detail->q4)?$get_detail->q4:'')); ?>" style="width:100%;margin-top:20px;" placeholder="Enter Name Here">
										</td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td style="font-weight:900;">8. टेस्ट में गलत प्रश्न उत्तर, सिलेबस के बाहर के प्रश्न, क्रमांक में ग़लतियाँ थी क्या ? विस्तार से बतायें </td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td>
										<textarea name="q5" rows="4" style="border: none;border-bottom: dotted 1px #000;background: transparent;width: 100%;"><?php echo e(old('q5', isset($get_detail->q5)?$get_detail->q5:'')); ?></textarea>
										</td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td style="font-weight:900;">9. टेस्ट का समय व प्रश्नों की संख्या? </td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td>
											<input type="text" name="q6" class="" value="<?php echo e(old('q6', isset($get_detail->q6)?$get_detail->q6:'')); ?>" style="width:100%;" placeholder="Enter Test Duration">
											<input type="number" min="1" name="q6_1" class="" value="<?php echo e(old('q6_1', isset($get_detail->q6_1)?$get_detail->q6_1:'')); ?>" style="width:100%;margin-top:20px;" placeholder="Enter Total Question Count">
										</td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td style="font-weight:900;">10. क्या टेस्ट द्विभाषी था </td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td>
											<table width="100%" border="0">
												<tr style="background-color:transparent;">												
													<td class="radio-inline" width="50%">
														<input type="radio" style="width: 5%;bottom: -2px;position: relative;width:30px;" value="Yes" id="q7Yes" name="q7" <?php echo e(old('q7', isset($get_detail->q7) && $get_detail->q7=='Yes'?'checked':'')); ?> > <label for="q7Yes">Yes</label>
													</td>
													<td class="radio-inline" width="50%">
														<input type="radio" style="width: 5%;bottom: -2px;position: relative;width:30px;" value="No" id="q7No" name="q7" <?php echo e(old('q7', isset($get_detail->q7) && $get_detail->q7=='No'?'checked':'')); ?> > <label for="q7No">No</label>
													</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td style="font-weight:900;">11. टेस्ट का रिज़ल्ट कौनसी तारीख़ को घोषित करेंगे -</td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td><input type="date" name="q8" class="" value="<?php echo e(old('q8', isset($get_detail->q8)?$get_detail->q8:'')); ?>" ></td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td style="font-weight:900;">12. इस टेस्ट के बारे में विद्यार्थियों के सुझाव या शिकायत हो तो बतायें </td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td>
										<textarea name="q9" rows="4" style="border: none;border-bottom: dotted 1px #000;background: transparent;width: 100%;"><?php echo e(old('q9', isset($get_detail->q9)?$get_detail->q9:'')); ?></textarea>
										</td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td style="font-weight:900;">13. इस बैच में इससे पहले कितने टेस्ट हो चुके है</td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td><input type="number" min="0" name="q10" class="" value="<?php echo e(old('q10', isset($get_detail->q10)?$get_detail->q10:'')); ?>"></td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td style="font-weight:900;">14. क्या ये टेस्ट ऑनलाइन विद्यार्थियों को भी दिया गया ठीक समय पर</td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										
										<td>										
											<table width="100%" border="0">
												<tr style="background-color:transparent;">												
													<td class="radio-inline" width="50%">																							
														<input type="radio" style="width: 5%;bottom: -2px;position: relative;width:30px;" value="Yes" id="q11Yes" name="q11" <?php echo e(old('q11', isset($get_detail->q11) && $get_detail->q11=='Yes'?'checked':'')); ?> > <label for="q11Yes">Yes</label>
													</td>
													<td class="radio-inline" width="50%">	
														<input type="radio" style="width: 5%;bottom: -2px;position: relative;width:30px;" value="No" id="q11No" name="q11" <?php echo e(old('q11', isset($get_detail->q11) && $get_detail->q11=='No'?'checked':'')); ?> > <label for="q11No">No</label>
														
													</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td style="font-weight:900;">15. टेस्ट की विस्तृत व्याख्या व विडियो हल एप में उपलब्ध करवाया ?</td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td>										
											<table width="100%" border="0">
												<tr style="background-color:transparent;">												
													<td class="radio-inline" width="50%">													
														<input type="radio" value="Yes" style="width: 5%;bottom: -2px;position: relative;width:30px;" id="q12Yes" name="q12" <?php echo e(old('q12', isset($get_detail->q12) && $get_detail->q12=='Yes'?'checked':'')); ?> > <label for="q12Yes">Yes</label>
													</td>
													<td class="radio-inline" width="50%">													
														<input type="radio" style="width: 5%;bottom: -2px;position: relative;width:30px;" value="No" id="q12No" name="q12" <?php echo e(old('q12', isset($get_detail->q12) && $get_detail->q12=='No'?'checked':'')); ?> > <label for="q12No">No</label>
													</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td style="font-weight:900;">16. टेस्ट के समय कक्षा में कौन-कौन स्टाफ़ व टीम लीडर उपस्थित थे-</td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td>
										<textarea name="q13" rows="4" style="border: none;border-bottom: dotted 1px #000;background: transparent;width: 100%;"><?php echo e(old('q13', isset($get_detail->q13)?$get_detail->q13:'')); ?></textarea></td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td style="font-weight:900;">17. प्रश्न पुस्तिका की कितनी सीरिज थी</td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td><input type="text" name="q14" class="" value="<?php echo e(old('q14', isset($get_detail->q14)?$get_detail->q14:'')); ?>" ></td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td style="font-weight:900;">18. टेस्ट पेपर में कुल कितने पेज थे</td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td><input type="text" name="q15" class="" value="<?php echo e(old('q15', isset($get_detail->q15)?$get_detail->q15:'')); ?>" ></td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td style="font-weight:900;">19. गलत प्रश्न/सिलेबस से बाहर के प्रश्न का स्क्रीन शॉट शेअर करें।</td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td>
											<input type="file" name="q16[]" class="" multiple> </br></br>
										<?php
										if(!empty($get_detail->q16)){
											foreach(json_decode($get_detail->q16) as $key=>$screenval){
												$screenKey = $key+1;
												$asset = asset("laravel/public/timetable_test/$screenval");
												echo "<a target='_blank' href='$asset' class='btn btn-sm btn-primary'>Screen $screenKey</a>&nbsp;";
											}
										}
										?>
										</td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td style="font-weight:900;">20. इस बैच में विद्यार्थियों को टेस्ट देने के लिए व ईमानदारी से देने के लिए तथा टेस्ट की महत्ता बताने के लिए कभी कोई टीम लीडर आये ? (कौन आये व कौनसी तारीख़ को आये) </td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td>
										<textarea name="q17" rows="4" style="border: none;border-bottom: dotted 1px #000;background: transparent;width: 100%;"><?php echo e(old('q17', isset($get_detail->q17)?$get_detail->q17:'')); ?></textarea></td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td style="font-weight:900;">21. क्या टेस्ट में व्यापकता का गुण था ? अर्थात् जितने पाठ्यक्रम में से टेस्ट लेने के लिए कहा गया उनमें से सभी टॉपिक्स में से प्रश्न आये टेस्ट में</td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td>										
											<table width="100%" border="0">
												<tr style="background-color:transparent;">												
													<td class="radio-inline" width="50%">													
														<input type="radio"  style="width: 5%;bottom: -2px;position: relative;width:30px;" value="Yes" id="q18Yes" name="q18" <?php echo e(old('q18', isset($get_detail->q18) && $get_detail->q18=='Yes'?'checked':'')); ?> > <label for="q18Yes">Yes</label>
													</td>
													<td class="radio-inline" width="50%">													
														<input type="radio" style="width: 5%;bottom: -2px;position: relative;width:30px;" value="No" id="q18No" name="q18" <?php echo e(old('q18', isset($get_detail->q18) && $get_detail->q18=='No'?'checked':'')); ?> > <label for="q18No">No</label>
													</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td style="font-weight:900;">22. क्या टेस्ट में कोई प्रश्न रिपीट हुआ ? (अगर हुआ तो विस्तार से बतायें)</td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td>
											<table width="100%" border="0">
												<tr style="background-color:transparent;">
													<td class="radio-inline" width="50%">
														<input type="radio" style="width: 5%;bottom: -2px;position: relative;width:30px;" value="Yes" name="q19" id="q19Yes" class="q19" <?php echo e(old('q19', isset($get_detail->q19) && $get_detail->q19=='Yes'?'checked':'')); ?> > <label for="q19Yes">Yes</label>
													</td>
													<td class="radio-inline" width="50%">
														<input type="radio" style="width: 5%;bottom: -2px;position: relative;width:30px;" value="No" name="q19" id="q19No" class="q19" <?php echo e(old('q19', isset($get_detail->q19) && $get_detail->q19=='No'?'checked':'')); ?> > <label for="q19No">No</label>
													</td>
												</tr>
											</table>
											<br/>
											<textarea name="q19_1" rows="4" class="q19_1" style="border: none;border-bottom: dotted 1px #000;background: transparent;width: 100%;display:<?php echo e(old('q19', isset($get_detail->q19) && $get_detail->q19=='Yes'?'block':'none')); ?>" placeholder="Please Describe"><?php echo e(old('q19_1', isset($get_detail->q19_1)?$get_detail->q19_1:'')); ?></textarea>
										</td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td style="font-weight:900;">23. इस टेस्ट पेपर को किसने फाईनल करके फोटोकॉपी/प्रेस में भेजा</td>
									</tr>
									<tr style="border-bottom-style:hidden;">
										<td><input type="text" name="q20" class="" value="<?php echo e(old('q20', isset($get_detail->q20)?$get_detail->q20:'')); ?>" ></td>
									</tr>
								
							
							 	
						</tbody>
					</table>
					<div>
					<button type="submit" class="btn btn-dark btn-lg btn-block" style="color:#fff !important">Submit</button>
					<br/>
					<br/>
				</form>
					  
				<?php 
				}else{
					echo "No timetable found";
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
 <script id="rendered-js">
	$(document).ready(function() {
		
		$(window).keydown(function(event){
			if(event.keyCode == 13) {
			  event.preventDefault();
			  return false;
			}
		});
		
		$(document).on("change",".q19",function(){
			if($(this).val()=='Yes'){
				$(".q19_1").css('display','block');
			}
			else{
				$(".q19_1").css('display','none');
			}
		})

		$(document).on("change",".q21",function(){
			if($(this).val()=='Offline'){
				// $(".forHide").css('display','block');
				$(".forHide").css('display','table');
			}
			else{
				$(".forHide").css('display','none');
			}
		})
	
	});
    </script>	
    
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.without_login_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/laravel/resources/views/admin/webview_reports/schedule_test_report_update.blade.php ENDPATH**/ ?>