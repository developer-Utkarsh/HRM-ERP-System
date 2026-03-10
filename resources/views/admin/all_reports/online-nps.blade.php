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
    <script src="https://kit.fontawesome.com/c92e53a223.js" crossorigin="anonymous"></script>
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
					<div class="content-header row pt-3">
						<div class="content-header-left col-md-9 col-12 mb-2">
							<div class="row breadcrumbs-top">
								<div class="col-12">
									<h2 class="content-header-title float-left mb-0">Online Batch Feedback Report</h2>
								</div>
							</div>
						</div>
					</div>
					
					<div class="row justify-content-center">
						<div class="col-lg-6 pb-3">
							<div class="">
								<button type="button" class="btn btn-dark buttonclass bradius py-2" style="width:48%" onclick="showhide('all_report','batchWise')"><b>Month Wise</b></button> &nbsp;
								<button type="button" class="btn btn-light buttonclass py-2" style="width:48%" onclick="showhide('batchWise','all_report')"><b>Batch Wise Report</b></button>
							</div>
							<div class="all_report">
								<table class="table table-bordered  bg-light text-center">
									<thead style="background: #ffe982;">
										<tr>
											<th scope="col"><b>Total FeedBack</b></th>										
											<th scope="col"><b>Avg Rating</b></th>											
										</tr>
									</thead>
									<tbody>
										<tr class="newRaiting">											
											
										</tr>	
									</tbody>
								</table>
								<table class="table table-bordered  bg-light text-center">
									<thead style="background: #ffe982;">
										<tr>
											<th scope="col">Month</th>
											<th scope="col">Total Feedback</th>
											<th scope="col">Overall Rating</th>											
										</tr>
									</thead>
									<tbody>
										<?php
										  $i =2;
											$curl = curl_init();
											$getURL = "ratting_type=month_wise&teacher_id='".$faculty_id."'";
											curl_setopt_array($curl, array(
											  CURLOPT_URL => 'https://support.utkarshapp.com/FacultyRating',
											  CURLOPT_RETURNTRANSFER => true,
											  CURLOPT_ENCODING => '',
											  CURLOPT_MAXREDIRS => 10,
											  CURLOPT_TIMEOUT => 0,
											  CURLOPT_FOLLOWLOCATION => true,
											  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
											  CURLOPT_CUSTOMREQUEST => 'POST',
											  CURLOPT_POSTFIELDS => $getURL,
											  CURLOPT_HTTPHEADER => array(
												'X-Auth-Token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjUwMzY1ODciLCJkZXZpY2VfdHlwZSI6IjEiLCJ2ZXJzaW9uX2NvZGUiOiIyMDAwIiwiaWF0IjoxNjM5ODAzNTAzLCJleHAiOjE2NDE5NjM1MDN9.cA0LicNUFZRQ99rdQooiAd9B45UrEXjVpJekaUwFca0',
												'Content-Type: application/x-www-form-urlencoded'
											  ),
											));

											$response = curl_exec($curl);

											curl_close($curl);
											$response = json_decode($response); 
											
											$total = 0;
											$avg   = 0;
											$i = 0;
											$w=0;
											foreach($response->data as $key => $value){
										?>
										<tr>
											<td>{{ $value->Month }}</td>
											<td>{{ $value->Total_Rating }}</td>
											<td>{{ round($value->Avg_Rating,2) }}</td>
										</tr>	
										
										<?php
											
												$total = $total  + $value->Total_Rating;
												$avg  = $avg  + round($value->Avg_Rating,2);
												
												$w=$w+$value->Total_Rating*$value->Avg_Rating;
											
												$i++;
											} 
											
											//$avg = round($avg / $i,2);
											$avg = round($w / $total,2);
										 ?>
											
									</tbody>
								</table>
									
							</div>
							<div class="accordion batchWise" id="accordionExample" style="display:none;">
								<div class="">
									<?php 
										$i =2;
										
										$curl = curl_init();
										$getURL = "ratting_type=course_wise&teacher_id=".$faculty_id."";
										curl_setopt_array($curl, array(
										  CURLOPT_URL => 'https://support.utkarshapp.com/FacultyRating',
										  CURLOPT_RETURNTRANSFER => true,
										  CURLOPT_ENCODING => '',
										  CURLOPT_MAXREDIRS => 10,
										  CURLOPT_TIMEOUT => 0,
										  CURLOPT_FOLLOWLOCATION => true,
										  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
										  CURLOPT_CUSTOMREQUEST => 'POST',
										  CURLOPT_POSTFIELDS => $getURL,
										  CURLOPT_HTTPHEADER => array(
											'X-Auth-Token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjUwMzY1ODciLCJkZXZpY2VfdHlwZSI6IjEiLCJ2ZXJzaW9uX2NvZGUiOiIyMDAwIiwiaWF0IjoxNjM5ODAzNTAzLCJleHAiOjE2NDE5NjM1MDN9.cA0LicNUFZRQ99rdQooiAd9B45UrEXjVpJekaUwFca0',
											'Content-Type: application/x-www-form-urlencoded'
										  ),
										));

										$response = curl_exec($curl);

										curl_close($curl);
										$response = json_decode($response); 
										
										foreach($response->data as $key => $value){
									?>
									<div class="accordion-item mt-3">
										<h2 class="accordion-header" id="heading<?=$i;?>">
											<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?=$i;?>" aria-expanded="false" aria-controls="collapse<?=$i;?>">
												<b> <span style="font-size:13px;">{{ $value->title }}</b></span>
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
															<td>{{ $value->Total_Rating }}</td>
															<td>{{ round($value->Avg_Rating,2) }}</td>
														</tr>	
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
					
				</div>
			</div>
		</div>
		
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>
		
		<script type="text/javascript">
			function showhide(fid, sid){
				$('.'+fid).show();
				$('.'+sid).hide();
				
				$('.buttonclass').addClass('btn-light'); //eventually removeClass of some previous class	
				$('.buttonclass').removeClass('btn-dark'); //eventually removeClass of some previous class	
				$('.buttonclass').removeClass('bradius'); //eventually removeClass of some previous class	
			}
			
			$('.buttonclass').on('click', function(){ 
				$(this).addClass('btn-dark');
				$(this).addClass('bradius');
				$(this).removeClass('btn-light');			  
			});
			
			
			var newtd = "<td>"+<?=$total;?>+"</td><td>"+<?=$avg;?>+"</td>";
			$(".newRaiting").append(newtd);
		</script>
	</body>
</html>
