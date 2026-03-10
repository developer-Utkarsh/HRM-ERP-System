<!DOCTYPE html>
<html">
<head>
	<style>
		table {
			font-family: arial, sans-serif;
			border-collapse: collapse;
			width: 100%;
			margin-top: 2px;
			border: 1px solid black;
			
		}
		
		td{
			border: 1px solid #d9cfcf;
			text-align: left;
			padding: 5px;
		}
		
		tr:nth-child(even) {
			background-color: #efefef;
		} 
		
	</style>
</head>
<body style="margin:auto; width: 1200px; margin-top: 30px;">

	<div style="text-align:center;background-color: black;color:#fff;font-weight: bold;padding: 8px;border-radius: 5px;font-family:freeserif;">
		क्लास टेस्ट के बारे में विस्तृत रिपोर्ट
	</div>
	
	<table>
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
		<tr>
			<td style="width: 60%;" style="font-family:freeserif;">1. बैच का स्थान व बैच का नाम</td>
			<td style="width: 35%;" style="font-family:freeserif;"><?php echo isset($get_timetable->batch_name) ?  $get_timetable->batch_name : '' ?></td>
		</tr>

		<tr>
			<td style="font-family:freeserif;">2. टेस्ट का विषय </td>
			<td style="font-family:freeserif;"><?php echo isset($get_timetable->subject_name) ?  $get_timetable->subject_name : '' ?></td>
		</tr>

		<tr>
			<td style="font-family:freeserif;">3.टेस्ट लेने की दिनांक-</td>
			<td style="font-family:freeserif;"><?php echo isset($get_timetable->cdate) ?  date('d-F-Y',strtotime($get_timetable->cdate)) : '' ?></td>
		</tr>

		<tr>
			<td style="font-family:freeserif;">4. बैच में विद्यार्थियों की कुल संख्या-</td>
			<td style="font-family:freeserif;">{{$get_detail->q1}}</td>
		</tr>
		
		<tr>
			<td style="font-family:freeserif;">5. टेस्ट का प्रकार </td>
			<td style="font-family:freeserif;">{{$get_detail->q21}}</td>
		</tr>
		<?php 
		if(isset($get_detail->q21) && $get_detail->q21=="Offline")
		{ ?>
		<tr>
			<td style="font-family:freeserif;">6. टेस्ट देने वालों की संख्या</td>
			<td style="font-family:freeserif;">{{$get_detail->q2}}</td>
		</tr>
		
		<tr>
			<td style="font-family:freeserif;">7. इस टेस्ट की सूचना कौनसी तारीख़ को दी गई व किसने </td>
			<td style="font-family:freeserif;">{{$get_detail->q3}}, {{$get_detail->q4}}</td>
		</tr>

		<tr>
			<td style="font-family:freeserif;">8. टेस्ट में गलत प्रश्न उत्तर, सिलेबस के बाहर के प्रश्न, क्रमांक में ग़लतियाँ थी क्या ? विस्तार से बतायें </td>
			<td style="font-family:freeserif;">{{$get_detail->q5}}</td>
		</tr>

		<tr>
			<td style="font-family:freeserif;">9. टेस्ट का समय व प्रश्नों की संख्या? </td>
			<td style="font-family:freeserif;"> {{$get_detail->q6}}, {{$get_detail->q6_1}}</td>
		</tr>

		<tr>
			<td style="font-family:freeserif;">10. क्या टेस्ट द्विभाषी था </td>
			<td style="font-family:freeserif;">{{$get_detail->q7}}</td>
		</tr>

		<tr>
			<td style="font-family:freeserif;">11. टेस्ट का रिज़ल्ट कौनसी तारीख़ को घोषित करेंगे -</td>
			<td style="font-family:freeserif;">{{$get_detail->q8}}</td>
		</tr>

		<tr>
			<td style="font-family:freeserif;">12. इस टेस्ट के बारे में विद्यार्थियों के सुझाव या शिकायत हो तो बतायें </td>
			<td style="font-family:freeserif;">{{$get_detail->q9}}</td>
		</tr>

		<tr>
			<td style="font-family:freeserif;">13. इस बैच में इससे पहले कितने टेस्ट हो चुके है</td>
			<td style="font-family:freeserif;">{{$get_detail->q10}}</td>
		</tr>

		<tr>
			<td style="font-family:freeserif;">14. क्या ये टेस्ट ऑनलाइन विद्यार्थियों को भी दिया गया ठीक समय पर</td>
			<td style="font-family:freeserif;">{{$get_detail->q11}}</td>
		</tr>

		<tr>
			<td style="font-family:freeserif;">15. टेस्ट की विस्तृत व्याख्या व विडियो हल एप में उपलब्ध करवाया ?</td>
			<td style="font-family:freeserif;">{{$get_detail->q12}}</td>
		</tr>

		<tr>
			<td style="font-family:freeserif;">16. टेस्ट के समय कक्षा में कौन-कौन स्टाफ़ व टीम लीडर उपस्थित थे-</td>
			<td style="font-family:freeserif;">{{$get_detail->q13}}</td>
		</tr>
		
		<tr>
			<td style="font-family:freeserif;">17. प्रश्न पुस्तिका की कितनी सीरिज थी</td>
			<td style="font-family:freeserif;">{{$get_detail->q14}}</td>
		</tr>

		<tr>
			<td style="font-family:freeserif;">18. टेस्ट पेपर में कुल कितने पेज थे</td>
			<td style="font-family:freeserif;">{{$get_detail->q15}}</td>
		</tr>

		<tr>
			<td style="font-family:freeserif;">19. गलत प्रश्न/सिलेबस से बाहर के प्रश्न का स्क्रीन शॉट शेअर करें।</td>
			<td style="font-family:freeserif;">
			<?php
			if(!empty($get_detail->q16)){
				foreach(json_decode($get_detail->q16) as $key=>$screenval){
					$screenKey = $key+1;
					$asset = asset("laravel/public/timetable_test/$screenval");
					echo "<img style='width:100px;' src='$asset'>";
				}
			}
			?>
			
			</td>
		</tr>

		<tr>
			<td style="font-family:freeserif;">20. इस बैच में विद्यार्थियों को टेस्ट देने के लिए व ईमानदारी से देने के लिए तथा टेस्ट की महत्ता बताने के लिए कभी कोई टीम लीडर आये ? (कौन आये व कौनसी तारीख़ को आये) </td>
			<td style="font-family:freeserif;">{{$get_detail->q17}}</td>
		</tr>

		<tr>
			<td style="font-family:freeserif;">21. क्या टेस्ट में व्यापकता का गुण था ? अर्थात् जितने पाठ्यक्रम में से टेस्ट लेने के लिए कहा गया उनमें से सभी टॉपिक्स में से प्रश्न आये टेस्ट में</td>
			<td style="font-family:freeserif;">{{$get_detail->q18}}</td>
		</tr>

		<tr>
			<td style="font-family:freeserif;">22. क्या टेस्ट में कोई प्रश्न रिपीट हुआ ? (अगर हुआ तो विस्तार से बतायें)</td>
			<td style="font-family:freeserif;">{{$get_detail->q19}}, {{$get_detail->q19_1}} </td>
		</tr>

		<tr>
			<td style="font-family:freeserif;">23. इस टेस्ट पेपर को किसने फाईनल करके फोटोकॉपी/प्रेस में भेजा</td>
			<td style="font-family:freeserif;">{{$get_detail->q20}}</td>
		</tr>
		<?php } ?>
			
		</table>
		<br><br>
		
	</body>
	</html>