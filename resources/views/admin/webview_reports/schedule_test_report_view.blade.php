@extends('layouts.without_login_admin')
@section('content')
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

@media screen and (max-width: 770px) {
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
				<form action="{{ route('test_report_save') }}" method="post" enctype="multipart/form-data">
					@csrf
					
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
							$batch_code = 0;
							if(!empty($get_timetable->batch_code)){
								$batch_code = $get_timetable->batch_code;
							}
								
							?>
							<input type="hidden" name="user_id" value="{{ $user_id }}">
							<input type="hidden" name="tt_id" value="{{ $tt_id }}">
							<input type="hidden" name="fdate" value="{{ $get_timetable->cdate }}">
								<tr style="border-bottom-style:hidden;">
									<td style="font-weight:900;">1. बैच का स्थान व बैच का नाम</td>
									<td><?php echo isset($get_timetable->batch_name) ?  $get_timetable->batch_name : '' ?></td>
								</tr>
								<tr style="border-bottom-style:hidden;">
									<td style="font-weight:900;">2. टेस्ट का विषय </td>
									<td><?php echo isset($get_timetable->subject_name) ?  $get_timetable->subject_name : '' ?></td>
								</tr>
								<tr style="border-bottom-style:hidden;">
									<td style="font-weight:900;">3.टेस्ट लेने की दिनांक-</td>
									<td><?php echo isset($get_timetable->cdate) ?  date('d-F-Y',strtotime($get_timetable->cdate)) : '' ?></td>
								</tr>
								<tr style="border-bottom-style:hidden;">
									<td style="font-weight:900;">4. बैच में विद्यार्थियों की कुल संख्या-</td>
									<td>
									<?php 
									$total_admission = 0;
									
									if(!empty($batch_code)){
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
										if(!empty($response)){
											$total_admission =  $response['total_admission'];
										}
									}?>
									<input type="hidden" min="0" name="q1" class="" value="{{ $total_admission }}" required>
									<strong>{{ $total_admission }}</strong>
									</td>
								</tr>
								 
								<tr style="border-bottom-style:hidden;">
									<td style="font-weight:900;">5. टेस्ट का प्रकार </td>
									<td>{{$get_detail->q21}}</td>
								</tr>
								
								<?php
								if(isset($get_detail->q21) && $get_detail->q21=="Offline"){
								?>
								<tr style="border-bottom-style:hidden;">
									<td style="font-weight:900;">6. टेस्ट देने वालों की संख्या</td>
									<td>{{$get_detail->q2}}</td>
								</tr>
								
								<tr style="border-bottom-style:hidden;">
									<td style="font-weight:900;">7. इस टेस्ट की सूचना कौनसी तारीख़ को दी गई व किसने </td>
									<td>{{$get_detail->q3}}, {{$get_detail->q4}}</td>
								</tr>
								<tr style="border-bottom-style:hidden;">
									<td style="font-weight:900;">8. टेस्ट में गलत प्रश्न उत्तर, सिलेबस के बाहर के प्रश्न, क्रमांक में ग़लतियाँ थी क्या ? विस्तार से बतायें </td>
									<td>{{$get_detail->q5}}</td>
								</tr>
								<tr style="border-bottom-style:hidden;">
									<td style="font-weight:900;">9. टेस्ट का समय व प्रश्नों की संख्या? </td>
									<td> {{$get_detail->q6}}, {{$get_detail->q6_1}} </td>
								</tr>
								<tr style="border-bottom-style:hidden;">
									<td style="font-weight:900;">10. क्या टेस्ट द्विभाषी था </td>
									<td>{{$get_detail->q7}}</td>
								</tr>
								<tr style="border-bottom-style:hidden;">
									<td style="font-weight:900;">11. टेस्ट का रिज़ल्ट कौनसी तारीख़ को घोषित करेंगे -</td>
									<td>{{$get_detail->q8}}</td>
								</tr>
								<tr style="border-bottom-style:hidden;">
									<td style="font-weight:900;">12. इस टेस्ट के बारे में विद्यार्थियों के सुझाव या शिकायत हो तो बतायें </td>
									<td>{{$get_detail->q9}}</td>
								</tr>
								<tr style="border-bottom-style:hidden;">
									<td style="font-weight:900;">13. इस बैच में इससे पहले कितने टेस्ट हो चुके है</td>
									<td>{{$get_detail->q10}}</td>
								</tr>
								<tr style="border-bottom-style:hidden;">
									<td style="font-weight:900;">14. क्या ये टेस्ट ऑनलाइन विद्यार्थियों को भी दिया गया ठीक समय पर</td>
									<td>{{$get_detail->q11}}</td>
								</tr>
								<tr style="border-bottom-style:hidden;">
									<td style="font-weight:900;">15. टेस्ट की विस्तृत व्याख्या व विडियो हल एप में उपलब्ध करवाया ?</td>
									<td>{{$get_detail->q12}}</td>
								</tr>
								<tr style="border-bottom-style:hidden;">
									<td style="font-weight:900;">16. टेस्ट के समय कक्षा में कौन-कौन स्टाफ़ व टीम लीडर उपस्थित थे-</td>
									<td>{{$get_detail->q13}}</td>
								</tr>
								
								<tr style="border-bottom-style:hidden;">
									<td style="font-weight:900;">17. प्रश्न पुस्तिका की कितनी सीरिज थी</td>
									<td>{{$get_detail->q14}}</td>
								</tr>
								<tr style="border-bottom-style:hidden;">
									<td style="font-weight:900;">18. टेस्ट पेपर में कुल कितने पेज थे</td>
									<td>{{$get_detail->q15}}</td>
								</tr>
								<tr style="border-bottom-style:hidden;">
									<td style="font-weight:900;">19. गलत प्रश्न/सिलेबस से बाहर के प्रश्न का स्क्रीन शॉट शेअर करें।</td>
									<td>
									<?php
									if(!empty($get_detail->q16)){
										foreach(json_decode($get_detail->q16) as $key=>$screenval){
											$screenKey = $key+1;
											$asset = asset("laravel/public/timetable_test/$screenval");
											echo "<img src='$asset' width='100px;'>";
										}
									}
									?>
									</td>
								</tr>
								<tr style="border-bottom-style:hidden;">
									<td style="font-weight:900;">20. इस बैच में विद्यार्थियों को टेस्ट देने के लिए व ईमानदारी से देने के लिए तथा टेस्ट की महत्ता बताने के लिए कभी कोई टीम लीडर आये ? (कौन आये व कौनसी तारीख़ को आये) </td>
									<td>{{$get_detail->q17}}</td>
								</tr>
								<tr style="border-bottom-style:hidden;">
									<td style="font-weight:900;">21. क्या टेस्ट में व्यापकता का गुण था ? अर्थात् जितने पाठ्यक्रम में से टेस्ट लेने के लिए कहा गया उनमें से सभी टॉपिक्स में से प्रश्न आये टेस्ट में</td>
									<td>{{$get_detail->q18}}</td>
								</tr>
								<tr style="border-bottom-style:hidden;">
									<td style="font-weight:900;">22. क्या टेस्ट में कोई प्रश्न रिपीट हुआ ? (अगर हुआ तो विस्तार से बतायें)</td>
									<td>{{$get_detail->q19}}
										<br/>
										{{$get_detail->q19_1}}
									</td>
								</tr>
								<tr style="border-bottom-style:hidden;">
									<td style="font-weight:900;">23. इस टेस्ट पेपर को किसने फाईनल करके फोटोकॉपी/प्रेस में भेजा</td>
									<td>{{$get_detail->q20}}</td>
								</tr>
								<?php } ?>
								
							</tbody>
						</table>
						
					<div>
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
 
				
@endsection

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<link href="{{ asset('laravel/public/css/jquery.timepicker.css') }}" rel="stylesheet"/>
<script src="{{ asset('laravel/public/js/jquery.timepicker.js') }}"></script>
<script src="{{ asset('laravel/public/admin/js/jquery.validate.min.js') }}"></script>
 <script id="rendered-js">
	$(document).ready(function() {
		
		$(window).keydown(function(event){
			if(event.keyCode == 13) {
			  event.preventDefault();
			  return false;
			}
		});
	
	});
    </script>	
    
@endsection
