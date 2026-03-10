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

$get_parent_timetable = DB::table('timetables')->where('id', $timetable_id)->get();
if(count($get_parent_timetable) > 0){
	foreach($get_parent_timetable as $parent_value){
		$parent_timetable_id = $parent_value->id;
		$parent_chapter_id = $parent_value->chapter_id;
		$parent_topic_id = $parent_value->topic_id;
	?>
	<div class="row" >
		<div class='col-md-6 col-12'>
			<div class='form-label-group'>
				<select class='form-control select_multiple_online_1' name='partially_chapter_topic_id'>
					<option value=''> Select Chapter</option>
					<?php
					if (!empty($chapters)){
						foreach ($chapters as $key => $value)
						{
							$chapter_id = $value->id;
							if (!empty($value->name) && !empty($value->name))
							{
								$selected = "";
								if($parent_chapter_id == $chapter_id){
									$selected = "selected";
								}
								$chapter_id = $value->id;
								?>
								<option value="<?=$chapter_id;?>" <?=$selected?>> <?=$value->name?> </option>
								<?php
							}
						}
					}
					?>
				</select>
			</div>
		</div>
		
		<div class='col-md-6 col-12'>
			<div class='form-label-group'>
				<input type="text" class="form-control" name="topic_name" placeholder="Topic">
			</div>
		</div>	
		<div class='col-md-12 col-12'>
			<div class='form-label-group'>
				<textarea class="form-control" name="remark" placeholder="Remark"></textarea>
			</div>
		</div>	
		
	</div>
	<?php
	}
}
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script>
$('.select_multiple_online_1').select2({
	width: '100%',
	placeholder: "Select Any",
	allowClear: true
});
</script><?php /**PATH /var/www/html/laravel/resources/views/admin/timetables/partially_end_class_data.blade.php ENDPATH**/ ?>