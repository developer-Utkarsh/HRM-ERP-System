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
	
	 
    <?php /* <link rel="stylesheet" href="./main.css" /> */ ?>
    <script src="https://kit.fontawesome.com/c92e53a223.js" crossorigin="anonymous"></script>
    <script
      src="https://code.jquery.com/jquery-3.6.3.min.js"
      integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU="
      crossorigin="anonymous"
    ></script>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
      crossorigin="anonymous"
    ></script>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  </head>

	<style>
		@import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap");

		body {
		  font-family: "Inter";
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
		.gray-bg{background-color:#f8f8f8;}
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
				
		.skill-main {
		  width: 100%;
		  max-width: 600px;
		  display: flex;
		  flex-direction: column;
		  gap: 20px;
		}
		.skill-main .skill-wrrap {
		  display: flex;
		  flex-direction: column;
		  gap: 10px;
		}
		.skill-main .skill-wrrap .skill-name {
		  color: #000;
		  font-size: 18px;
		  font-weight: 500;
		}
		.skill-main .skill-wrrap .skill-bar {
		  height: 20px;
		  background-color: #fff6c9;
		  border-radius: 8px;
		}
		.skill-main .skill-wrrap .skill-per {
		  height: 20px;
		  background: #23576f;
		  border-radius: 8px;
		  width: 0;
		  transition: 1s linear;
		  position: relative;
		}
		.skill-main .skill-wrrap .skill-per:before {
		  content: attr(per);
		  position: absolute;
		  padding: 4px 6px;
		  background-color: #23576f;
		  color: #fff;
		  font-size: 11px;
		  border-radius: 4px;
		  top: -35px;
		  right: 0;
		  transform: translateX(50%);
		}
		.skill-main .skill-wrrap .skill-per:after {
		  content: "";
		  position: absolute;
		  width: 10px;
		  height: 10px;
		  background-color: #23576f;
		  top: -15px;
		  right: 0;
		  transform: translateX(50%) rotate(45deg);
		  border-radius: 2px;
		}
	</style>
  <body>
   

    <div class="body-container status-report-all">
      <div class="section gray-bg">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-6">
			<div>
				<ul class="nav nav-pills status-report mb-3 flex-nowrap overflow-auto" id="pills-tab" role="tablist">
					<li class="nav-item" role="presentation">
						<button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true"><b>Course Status</b></button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false"><b>Test Status</b></button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact" aria-selected="false"><b>Inventory Status</b></button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" id="pills-attendance-tab" data-bs-toggle="pill" data-bs-target="#pills-attendance" type="button" role="tab" aria-controls="pills-attendance" aria-selected="false"><b>Attendance Status</b></button>
					</li>
				</ul>
			</div>
			<?php
			if(!empty($batche_detail)){
			?>
			<div class="bg-secondary-light batch-details">
				<p>Batch Name - <strong> <?=$batche_detail->name?></strong></p>
				<p>Batch Start Date : <strong> <?=date('d-m-Y',strtotime($batche_detail->start_date))?></strong></p>
				<p class="mb-0">Total No. of Hours : <strong> <?php if(!empty($no_of_hour)){ echo $no_of_hour; }else { '00.00'; } ?></strong></p>
				<p class="mb-0">
					Total Spent of Hours : <strong> <?php  $ss=explode(":",$spent_no_of_hour[0]->totalhours); echo $ss[0];?></strong>
				</p>
			</div>
			<div class="skill-main mb-3">
				<div class="skill-wrrap">					
					<div class="skill-bar">
						<div class="skill-per" per="<?php echo ceil(($ss[0] * 100) / $no_of_hour); ?>"></div>
					</div>
					<div class="skill-name">Course Status</div>
				</div>
			</div>
			<div class="tab-content" id="pills-tabContent">
				<div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
					<div class="accordion" id="accordionExample">
						<?php
							$i = 1;	
							$sum = "00:00"; 							
							$total_schedule_hours = new DateTime('00:00');
							$total_spent_hours = new DateTime('00:00');
							$total              = new DateTime('00:00');
							$total_plan_hours = 0;
                               $get_subjects=DB::table('batchrelations')->select('batchrelations.subject_id','batchrelations.no_of_hours','subject.name as subject_name')
                               ->leftJoin('subject', 'subject.id', '=', 'batchrelations.subject_id')
                               ->where('batchrelations.batch_id',$batch_id)
                               ->where('batchrelations.is_deleted','0')
                               ->get();
                               if(count($get_subjects) > 0){ 
                               	    
									foreach($get_subjects as $get_subject){
									    $subject_id=$get_subject->subject_id;
										$faculty="";
										$schedule_total_tt  = "00 : 00"; 
										$total_tt = "00 : 00"; 
										$rmTime  = "0";
										$get_faculty = DB::table('timetables')->select('faculty_id')
												->where('subject_id',$subject_id)
												->where('batch_id', $batch_id)
												->where('time_table_parent_id', '0')
												->where('is_deleted', '0')
												->groupBy('faculty_id')
												->get();
										if(count($get_faculty) > 0){
											foreach($get_faculty as $f_detail){
												$faculty_id = $f_detail->faculty_id;
												$get_total_time = DB::table('timetables')
												->select('timetables.faculty_id','timetables.from_time as start_time','timetables.to_time as end_time','start_classes.start_time as start_classes_start_time','start_classes.end_time as start_classes_end_time','users.name as user_name')
												->leftJoin('start_classes', 'start_classes.timetable_id', '=', 'timetables.id')
												->leftJoin('users', 'users.id', '=', 'timetables.faculty_id')
												->where('timetables.subject_id',$subject_id)
												->where('timetables.batch_id', $batch_id)
												->where('timetables.faculty_id', $faculty_id)
												->where('timetables.time_table_parent_id', '0')
												->where('timetables.is_deleted', '0')
												->get();

												$faculty="";
												
												$base_time          = new DateTime('00:00');
												$base_time2          = new DateTime('00:00');
												$total              = new DateTime('00:00');
												$total2              = new DateTime('00:00');
												$subject_arr        = array();
												$schedule_total_tt  = "00 : 00"; 
												$total_tt = "00 : 00"; 
												$rmTime  = "0"; 
												if(count($get_total_time) > 0){
													foreach($get_total_time as $get_total_time_value){
														//array_push($subject_arr, $get_total_time_value->name);
														$first_time = new DateTime($get_total_time_value->start_time);
														$second_time = new DateTime($get_total_time_value->end_time);
														$interval = $first_time->diff($second_time);
														$base_time->add($interval);
														$total_schedule_hours->add($interval);


														$first_date = new DateTime($get_total_time_value->start_classes_start_time);
														$second_date = new DateTime($get_total_time_value->start_classes_end_time);
														$interval = $first_date->diff($second_date);
														$base_time2->add($interval);
														$total_spent_hours->add($interval);

														$faculty=$get_total_time_value->user_name;

													} 											
													
													
													$baseDays = $total->diff($base_time)->format("%a");
													$baseHours = $total->diff($base_time)->format("%H");
													$baseMinute = $total->diff($base_time)->format("%I");
													
													$schedule_total_tt = ($baseDays*24)+$baseHours. ":" . $baseMinute;
													
													$totalDays = $total2->diff($base_time2)->format("%a");
													$totalHours = $total2->diff($base_time2)->format("%H");
													$totalMinute = $total2->diff($base_time2)->format("%I");
													$totalHours = (($totalDays*24)+$totalHours);
													$total_tt = $totalHours. ":" . $totalMinute;
												
													// $rmTime=$get_subject->no_of_hours-$totalHours-$totalMinute/100;
													// $rmTime=number_format($rmTime,2);
													
													$h1s = $get_subject->no_of_hours*3600;
													$h2s = $totalHours*3600 + $totalMinute *60;
													$rmTime = "";;
													if($h1s > $h2s){
														$seconds = $h1s - $h2s;
													}
													else{
														$rmTime="-";
														$seconds = $h2s - $h1s;
													}
													$rmTime =$rmTime . sprintf("%02d:%02d", floor($seconds / 3600), ($seconds / 60) % 60);
													// $rmTime = $rmTime . floor($seconds / 3600) .":".($seconds / 60) % 60;
													
												}

												?>
												<div class="accordion-item">
													<h2 class="accordion-header" id="heading<?=$i;?>">
													  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?=$i?>" aria-expanded="<?=($i==1)?'true':'false'?>" aria-controls="collapse<?=$i?>">
														<b>Subject</b> - <?=$get_subject->subject_name;?>
													  </button>
													</h2>
													<div id="collapse<?=$i?>" class="accordion-collapse collapse <?=($i==1)?'show':''?>" aria-labelledby="heading<?=$i;?>" data-bs-parent="#accordionExample">
													
													  <div class="accordion-body">
														<strong>Faculty Name  -</strong> <?=$faculty;?>
														<div class="tag">
															<button type="button" class="btn btn-outline-secondary">
																<b>Plan Hour  :</b> <?php echo $get_subject->no_of_hours; ?> Hr
															</button> 
															<button type="button" class="btn btn-outline-secondary">
																<b>Schedule Hour  :</b> {{ $schedule_total_tt }} Hr
															</button> 
															<button type="button" class="btn btn-outline-secondary">
																<b>Spent Hour  :</b> {{ $total_tt }} Hr
															</button> 
															<button type="button" class="btn btn-outline-secondary">
																<b>Remaining Hour  :</b> {{ $rmTime }} Hr
															</button>
														</div>
													  </div>
													</div>
												</div>
											<?php 
													$i++;  
												}
										}
										else{
											?>
											
											<div class="accordion-item">
												<h2 class="accordion-header">
												  <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?=$i?>" aria-expanded="<?=($i==1)?'true':'false'?>" aria-controls="collapse<?=$i?>">
													<b>Subject</b> - <?=$get_subject->subject_name;?>
												  </button>
												</h2>
												<div id="collapse<?=$i?>" class="accordion-collapse collapse <?=($i==1)?'show':''?>" data-bs-parent="#accordionExample">
												
												  <div class="accordion-body">
													<strong>Faculty Name</strong> - <?=$faculty;?>
													<div class="tag"><button type="button" class="btn btn-outline-secondary">Plan Hour - <?php echo $get_subject->no_of_hours; ?> Hr</button> <button type="button" class="btn btn-outline-secondary">Schedule Hour - {{ $schedule_total_tt }} Hr</button> <button type="button" class="btn btn-outline-secondary">Spent Hour - {{ $total_tt }} Hr</button> <button type="button" class="btn btn-outline-secondary">Remaining Hour - {{ $get_subject->no_of_hours }} Hr</button></div>
												  </div>
												</div>
											</div>
											<?php
											$i++;
										}
									}
                               } 
							   
							   ?>
						  
					  
					  <!--div class="accordion-item">
						<h2 class="accordion-header">
						  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
							<b>Subject</b> - Maths
						  </button>
						</h2>
						<div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
						  <div class="accordion-body">
							<strong>Faculty Name</strong> Gyrashi LalJi Sir 
							<div class="tag"><button type="button" class="btn btn-outline-secondary">Plan Hour - 50Hr</button> <button type="button" class="btn btn-outline-secondary">Plan Hour - 50Hr</button> <button type="button" class="btn btn-outline-secondary">Plan Hour - 50Hr</button> <button type="button" class="btn btn-outline-secondary">Plan Hour - 50Hr</button></div>
						  </div>
						</div>
					  </div>
					  
					  <div class="accordion-item">
						<h2 class="accordion-header">
						  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
							<b>Subject</b> - Maths
						  </button>
						</h2>
						<div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
						  <div class="accordion-body">
							<strong>Faculty Name</strong> Gyrashi LalJi Sir 
							<div class="tag"><button type="button" class="btn btn-outline-secondary">Plan Hour - 50Hr</button> <button type="button" class="btn btn-outline-secondary">Plan Hour - 50Hr</button> <button type="button" class="btn btn-outline-secondary">Plan Hour - 50Hr</button> <button type="button" class="btn btn-outline-secondary">Plan Hour - 50Hr</button></div>
						  </div>
						</div>
					  </div-->
				  
					</div>
				</div>
				
				<div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
				<div class="accordion" id="accordionExample1">
				<?php
				if(!empty($test_status)){
					foreach ($test_status->batch_timetables as $value) {
						$get_test_report = DB::table('test_report')
													->select('test_report.*')
													->where('tt_id', $value->id)
													->first();
					?>
					<div class="accordion-item">
						<h2 class="accordion-header">
						  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?=$i?>" aria-expanded="false" aria-controls="collapse<?=$i?>">
							<b>Test | <?=isset($value->cdate) ?  date('d-F-Y',strtotime($value->cdate)) : ''?></b> 
						  </button>
						</h2>
						<div id="collapse<?=$i?>" class="accordion-collapse collapse" data-bs-parent="#accordionExample1">
						  <div class="accordion-body">
							<ul class="list-group list-group-flush">
								<li class="list-group-item"><strong>Subject</strong> - <?=$value->subject->name?> </li>
								
								<li class="list-group-item"><strong>Number Of Question : </strong> <?=isset($get_test_report->q6_1)?$get_test_report->q6_1:''?></li>
								<li class="list-group-item"><strong>Test Duration : </strong><?=isset($get_test_report->q6)?$get_test_report->q6:''?></li>
								
								<!--li class="list-group-item"><strong>Papaer Code</strong> <button type="button" class="btn btn-outline-secondary">- Utkarsh#</button>  </li>
								<li class="list-group-item"><strong>OMR Send</strong> <button type="button" class="btn btn-outline-secondary">25th Spet. 2023</button></li-->
								<li class="list-group-item"><strong>Result : </strong> <?=isset($get_test_report->q8) ?  date('d-F-Y',strtotime($get_test_report->q8)) : ''?></li>
								
								<li class="list-group-item"><strong>Center Name : </strong> <?=isset($value->studio->branch->name)?$value->studio->branch->name:''?></li>
								
								<!--li class="list-group-item"><strong>Award By and Date</strong> <button type="button" class="btn btn-outline-secondary"> -Kumar Gaurav | 1St Oct.2023</button>  </li>
								
								<li class="list-group-item"><strong>Remark :</strong>  </li-->
							</ul>

							
						  </div>
						</div>
					</div>
					<?php 
					$i++;
					}
				}
				?>
			  
	
				</div>
			</div>
				
				<div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab" tabindex="0">
					<?php foreach($inventory_header as $ihkey => $ihvalue){ ?>
					<div class="bg-yellow batch-details mt-3">
						<p class="m-0"><strong> <?=$ihvalue->inventory_type;?></strong></p>					
					</div>
					<div class="accordion-item">
						<?php 
							$inventory = DB::table('batch_inventory')
							->select(DB::raw("SUM(quantity) as total_qty"),'batch_inventory.*')
							->where('status','1')
							->groupby('name','inventory_type')
							->where("batch_code", $ihvalue->batch_code)
							->where("inventory_type", $ihvalue->inventory_type)
							->get();
							$i = 1;
							foreach($inventory as $key => $value){
								$query = DB::connection('mysql2')->table("tbl_registration")->select(DB::raw('count(assign_inventory) as given'))->whereRaw("find_in_set($value->id , assign_inventory)")->get();
								foreach($query as $key => $assign){
									$total = DB::connection('mysql2')->table("tbl_registration")->where("batch_id", $value->batch_code)->count();
													
									$pending = $total - $assign->given;
									
									$stock	= $value->total_qty - $assign->given;
						?>
						<div class="accordion" id="accordionExample2">
							<h2 class="accordion-header">
								<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?=$i;?>" aria-expanded="<?=($i==1)?'true':'false'?>" aria-controls="collapse<?=$i;?>">
									<b>{{ $value->name }}</b> 
								</button>
							</h2>
							<div id="collapse<?=$i;?>" class="accordion-collapse collapse <?=($i==1)?'hide':''?>" data-bs-parent="#accordionExample2">
								<div class="accordion-body">
									<ul class="list-group list-group-flush">
										<li class="list-group-item"><strong>Total No. Student : </strong> {{ $total }}</li>
										<li class="list-group-item">
											<strong>Given : </strong>{{ $assign->given }}<br>
											<strong>Pending : </strong>{{ $pending }} 
										</li>
										<li class="list-group-item"><strong>Given Date (First) : </strong> - {{ date('d-m-Y h:i:s', strtotime($value->created_at)) }}  </li>
									</ul>
								</div>
							</div>
						</div>
						<?php 
								}
								$i++; 
							} 
						?>
					</div>
					<?php } ?>
				</div>

				<!-- Attendance -->
				<div class="tab-pane fade" id="pills-attendance" role="tabpanel" aria-labelledby="pills-attendance-tab" tabindex="0">
					<?php 
						$checkPersent = DB::table('student_attendance')
										->select(DB::raw("count(id) as persentstudent"),DB::raw('Date(date) as pdate'))
										->where('batch_id', $stdattendance[0]->batch_id) 
										->whereRaw(DB::raw("DATE(date) = '".date('Y-m-d')."'"))
										->get();
					?>
					<div class="bg-secondary-light batch-details">
						<p>Total Student : <strong><?=$stdattendance[0]->total_admission ;?></strong></p>
						<p>Male Student	 : <strong><?=$stdattendance[0]->total_male ;?></strong></p>
						<p>Female Student: <strong><?=$stdattendance[0]->total_admission - $stdattendance[0]->total_male;?></strong></p>
						<p>Today's Present : <strong><?=$checkPersent[0]->persentstudent;?></strong></p>
						<p>Today's Absent  : <strong><?=$stdattendance[0]->total_admission - $checkPersent[0]->persentstudent;?></strong></p>
					</div>
				</div>	
			</div>
			
			<?php }
			else{
				?>
				<div class="bg-secondary-light batch-details"><strong>No data found</strong>
				</div>
				<?php
			}
			?>
			
			
              
              
              
            </div>
          </div>
        </div>
      </div>


      
    </div>
	<script type="text/javascript">
		var skillPers = document.querySelectorAll(".skill-per");

		skillPers.forEach(function(skillPer) {
		  var per = parseFloat(skillPer.getAttribute("per"));
		  
		  if(per > 100){
			skillPer.style.width = "100%";
		  }else{
			  skillPer.style.width = per + "%";
		  }
		  
		  
		  var animatedValue = 0; 
		  var startTime = null;
		  
		  function animate(timestamp) {
			if (!startTime) startTime = timestamp;
			var progress = timestamp - startTime;
			var stepPercentage = progress / 1000; // Dividing by duration in milliseconds (1000ms = 1s)
			
			if (stepPercentage < 1) {
			  animatedValue = per * stepPercentage;
			  skillPer.setAttribute("per", Math.floor(animatedValue) + "%");
			  requestAnimationFrame(animate);
			} else {
			  animatedValue = per;
			  skillPer.setAttribute("per", Math.floor(animatedValue) + "%");
			}
		  }
		  
		  requestAnimationFrame(animate);
		});
	</script>
  </body>
</html>
