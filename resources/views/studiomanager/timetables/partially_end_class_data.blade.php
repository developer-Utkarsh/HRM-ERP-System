<?php
use App\Http\Controllers\Controller;
use App\Subject;
use App\Chapter;
use App\Topic;

$timetable = DB::table('timetables')->where('id', $timetable_id)->first();
$batch_id = $timetable->batch_id;
$course_id = $timetable->course_id;
$subject_id = $timetable->subject_id;

$chapters = Chapter::where('subject_id', $subject_id)->where('course_id', $course_id)->get();

$get_parent_timetable = DB::table('timetables')->where('id', $timetable_id)->orWhere('time_table_parent_id', $timetable_id)->get();
if(count($get_parent_timetable) > 0){
	foreach($get_parent_timetable as $parent_value){
		$parent_timetable_id = $parent_value->id;
		$parent_chapter_id = $parent_value->chapter_id;
		$parent_topic_id = $parent_value->topic_id;
	?>
	<div class="row" >
		<div class='col-md-3 col-12'>
			<div class='form-label-group'>
				<select class='form-control' name='partially_chapter_topic_id[{{$parent_timetable_id}}]'>
					<option value=''> Select Chapter and Topic </option>
					<?php
					if (!empty($chapters)){
						foreach ($chapters as $key => $value)
						{
							if (!empty($value->name) && !empty($value->name))
							{
								$chapter_id = $value->id;
								$topics = [];
								$chk_topics = DB::table('timetables')
											->join('start_classes', 'timetables.id', '=', 'start_classes.timetable_id')
											->select('timetables.topic_id')
											->where([['timetables.batch_id', '=', $batch_id],['timetables.course_id', '=', $course_id],['timetables.subject_id', '=', $subject_id],['timetables.chapter_id', '=', $chapter_id],['start_classes.status', '=', 'End Class']])->get();
								
								$expected_ids = [];		
								if(count($chk_topics) > 0){
									foreach($chk_topics as $chk_topics_value){
										$expected_ids[] = $chk_topics_value->topic_id;
										//echo '<pre>'; print_r($topics);die;
									}
								}
								
								$get_topics = Topic::where('chapter_id', '=', $chapter_id)->whereNotIn('id', $expected_ids)->get();
								if(count($get_topics) > 0){
									foreach($get_topics as $topics_value){
										$temp['id']   = $topics_value->id;
										$temp['name'] = $topics_value->name;
										$topics[] = $temp;
									}
								}
								
								if(!empty($topics)){
									$chapter_id = $value->id;
									foreach ($topics as $tvalue)
									{
										if (!empty($tvalue['id']) && !empty($tvalue['name']))
										{
											$topic_id = $tvalue['id'];
											$selected = "";
											if($parent_chapter_id == $chapter_id && $parent_topic_id==$topic_id ){
												$selected = "selected";
											}
											?>
											<option value="<?=$chapter_id.'-'.$topic_id;?>" <?=$selected?>> <?=$value->name .' - '. $tvalue['name']?> </option>
											<?php
										}
									}
								}
								
								
							}
						}
					}
					?>
				</select>
			</div>
		</div>
		
		<div class='col-md-6 col-12'>
			<div class='form-label-group'>
				<input type="radio" name='partially_status[{{$parent_timetable_id}}]' value="Not Completed" /> Not Completed &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="radio" name='partially_status[{{$parent_timetable_id}}]' value="Completed" /> Completed
			</div>
		</div>
	</div>
	<?php
	}
}
?>