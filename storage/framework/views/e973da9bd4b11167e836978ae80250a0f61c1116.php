<!DOCTYPE html>
<html lang="en">
  <head>
    <link rel="icon" type="image/x-icon" href="./Assets/logoPNG.png" />
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta title="Utkarsh offline_report" />
    <title>Faculty Mentor Report</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3"
      crossorigin="anonymous"
    /> 
	
	 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">

    <?php /* <link rel="stylesheet" href="./main.css" /> */ ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script
      src="https://code.jquery.com/jquery-3.6.3.min.js"
      integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU="
      crossorigin="anonymous"
    ></script>
   

  </head>

	<style>
		body {
		  font-family: "Arial";
		  font-size: 16px;
		}
		.h1,.h2,.h3,.h4,.h5,.h6,h1,h2,h3,h4,h5,h6 {
		  font-weight: 800;
		}

		::-webkit-scrollbar {
		  width: 8px;
		  height: 4px;
		}

		::-webkit-scrollbar-track {
		  background: #f1f1f1;
		}

		::-webkit-scrollbar-thumb {
		  background: #ccc;
		  border-radius: 10px;
		}

		::-webkit-scrollbar-thumb:hover {
		  background: #888;
		}

		@media (min-width: 768px) {
		}

		@media (max-width: 768px) {
		  .reply-input input {
			border-radius: 10px 0 0 10px !important;
		  }
		}
		@media (min-width: 1400px) {
		  .container,
		  .container-lg,
		  .container-md,
		  .container-sm,
		  .container-xl,
		  .container-xxl {
			max-width: 1200px;
		  }
		}

		.batch-details{
		  background-color: #fff7cf !important;
		  padding: 20px;
		  border-radius: 10px;
		  margin-bottom: 20px;
		}
		.gray-bg{ 
			background-image: linear-gradient(to top, #f38800, #f39300, #f29e00, #f1a900, #f0b400, #efbc02, #eec506, #edcd0e, #edd60e, #ecde11, #eae716, #e8f01c);
			background-size: 100% 100%;
			background-repeat: no-repeat;
			background-attachment: fixed;
		}
		.status-report{background: #fff;
			padding: 10px;}
		  .nav-pills .nav-link.active, .nav-pills .show>.nav-link {
			color: #070707;
			background-color: #FFE357;
		}
		.nav-link {color:#C9C9C9;}
		.nav-link:focus, .nav-link:hover {
			color: #070707;}
		.accordion-button:not(.collapsed) {
			color: #070707;
			background-color: #fffae2;
			box-shadow: 0 -1px 0 rgba(0,0,0,.125);
		}
		
		.accordion-button {background-color: #fffae2;}
		.tag{padding-top:10px; }
		.tag button{margin: 10px;width: -webkit-fill-available;}
		.bg-yellow {background-color: #ffe357 !important;}
			
		.table { border-color : #8dc6ff !important;}	

		.bradius {
			border-top-left-radius: 15px;
			border-top-right-radius: 15px;
		}
	</style>
	<body class=" gray-bg">
		<div class="body-container status-report-all">
			<div class="section">
				<div class="container">
					<?php if(!empty($firstquery)): ?>
						<div class="content-header row pt-3">
							<div class="content-header-left col-md-9 col-12 mb-2">
								<div class="row breadcrumbs-top">
									<div class="col-12">
										<h2 class="content-header-title float-left mb-0">Batch Wise Feedback Report</h2>
									</div>
								</div>
							</div>
						</div>
						<div class="row justify-content-center">
							<div class="col-lg-6 pb-3">
								<div class="">
									<button type="button" class="btn btn-dark buttonclass bradius py-2" style="width:26%" onclick="showhide('all_report')"><b>Overall Report</b></button> &nbsp;
									<button type="button" class="btn btn-light buttonclass py-2" style="width:35%" onclick="showhide('batchWise')"><b>Batch Wise (NPS 6.0)</b></button>
								</div>
								<div class="all_report">									
									<table class="table table-bordered  bg-light text-center">
										<thead style="background: #ffe982;">
											<tr>
												<!--<th scope="col">Total Feedback</th>-->
												<th scope="col"><b>NPS 6.0</b></th>												
											</tr>
										</thead>
										<tbody>
											<?php 
												$allFeedback = DB::select("SELECT total_feedback,avg_rating FROM `rating_all_6` WHERE `hrms_faculty_id` = '".$firstquery[0]->hrms_faculty_id."'  AND type='a_rating'");
													$first = round($allFeedback[0]->avg_rating,2);
													// $second = round($allFeedback[0]->avg_rating_2,2);
													
													// $third = round($allFeedback[0]->avg_rating_3,2);
														
											?>
											<tr>
												<!--<td><?=$allFeedback[0]->total_feedback;?></td>-->
												<td><?=$first;?></td>
											</tr>	
										</tbody>
									</table>
									
									<div><h5>Question Wise Rating</h5></div>
									<table class="table table-bordered bg-light">
										<thead style="background: #ffe982;">
											<tr class="text-center">
												<th scope="col">Question</th>
												<th scope="col">Rating</th>												
											</tr>
											<!--
											<tr style="vertical-align: baseline;">
												<td style="border:1px solid #000;border-top:0;border-left:0;font-size: 13px;"><b>NPS 1.0</b></td>
												<td style="border:1px solid #000;border-top:0;border-left:0;font-size: 13px;"><b>NPS 2.0</b></td>
												<td style="border:1px solid #000;border-top:0;border-left:0;font-size: 13px;"><b>NPS 3.0</b></td>
											</tr>
											-->
										</thead>
										<tbody>
											<?php 
												$innerQuery = DB::select("SELECT total_feedback as question,avg_rating FROM `rating_all_6` WHERE `hrms_faculty_id` = '".$firstquery[0]->hrms_faculty_id."' AND type='q_rating'");
												foreach($innerQuery as $key2 => $innval){
													$qfirst = number_format((float)$innval->avg_rating, 2, '.', '');
											?>
											<tr style="font-size:13px;">
												<td class="w-75"><?php echo e($innval->question); ?></td>
												<td class="text-center"><?php echo e($qfirst); ?></td>												
											</tr>	
											<?php } ?>
											
										</tbody>
									</table>
								</div>
								<div class="accordion batchWise" id="accordionExample" style="display:none;">
									<div class="">
										<?php 
											$i =2;
											foreach($firstquery as $key => $value){
										?>
										<div class="accordion-item mt-3">
											<h2 class="accordion-header" id="heading<?=$i;?>">
												<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?=$i;?>" aria-expanded="false" aria-controls="collapse<?=$i;?>">
													<b> <span style="font-size:13px;"><?php echo e($value->batch_name); ?></b></span>
												</button>
											</h2>
											<div id="collapse<?=$i;?>" class="accordion-collapse collapse" aria-labelledby="heading<?=$i;?>" data-bs-parent="#accordionExample">
												<div class="accordion-body">
													
													<table class="table table-bordered text-center">
														<thead style="background: #ffe982;">
															<tr>
																<th scope="col">Total Feedback</th>
																<th scope="col">Overall Rating</th>												
															</tr>
														</thead>
														<tbody>
															<tr>
																<td><?php echo e($value->total_rating); ?></td>
																<td><?php echo e(round($value->avg_rating,2)); ?></td>
															</tr>	
														</tbody>
													</table>
													
													<table class="table table-bordered">
														<thead style="background: #ffe982;">
															<tr  class="text-center">
																<th scope="col">Question</th>
																<th scope="col">Rating</th>												
															</tr>
														</thead>
														<tbody>
															<?php 
																$innerQuery = DB::select("SELECT question,avg_rating FROM `questionwise_6` WHERE `hrms_faculty_id` = '".$value->hrms_faculty_id."' AND batch_id = '".$value->batch_id."' AND version=1");
																
																if(count($innerQuery) > 0){
																foreach($innerQuery as $key2 => $innval){
															?>
															<tr style="font-size:13px;">
																<td class="w-75"><?php echo e($innval->question); ?></td>
																<td class="text-center">
																	<?php echo e(number_format((float)$innval->avg_rating, 2, '.', '')); ?>

																</td>
															</tr>	
															<?php } }else{ ?>
															<tr>
																<td colspan="2" align="center">No Data Found</td>
															</tr>
															<?php } ?>
														</tbody>
													</table>
												</div>
											</div>
										</div>
										<?php $i++; } ?>
									</div>
								</div>
							</div>	
						</div>
					<?php else: ?>
            No data found
					<?php endif; ?>
				</div>
			</div>
		</div>
		
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>
		
		<script type="text/javascript">
			function showhide(fid){
				$('.all_report').hide();
				$('.batchWise').hide();
				$('.batchWise3').hide();
				
				$('.'+fid).show();
				
				
				$('.buttonclass').addClass('btn-light'); //eventually removeClass of some previous class	
				$('.buttonclass').removeClass('btn-dark'); //eventually removeClass of some previous class	
				$('.buttonclass').removeClass('bradius'); //eventually removeClass of some previous class	
			}
			
			$('.buttonclass').on('click', function(){ 
				$(this).addClass('btn-dark');
				$(this).addClass('bradius');
				$(this).removeClass('btn-light');			  
			});
		</script>
	</body>
</html>
<?php /**PATH /var/www/html/laravel/resources/views/admin/all_reports/nps-details-six.blade.php ENDPATH**/ ?>