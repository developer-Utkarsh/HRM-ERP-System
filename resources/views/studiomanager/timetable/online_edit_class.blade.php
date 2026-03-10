<div class="col-md-4 col-12">

	<div class="form-label-group" id="online_batch_loader">
		<select class="form-control online_batch_id select-multiple-11" name="online_batch_id">
			<option value=""> - Select Batch - </option>
			@if(count($batch) > 0)
			@foreach($batch as $value)
			<option value="{{ $value->id }}" @if($get_edit_data->batch_id == $value->id) selected="selected" @endif>{{ $value->name }}</option>
			@endforeach
			@endif
		</select>
		<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
	</div>
</div>
<div class="col-md-4 col-12" id="online_course_loader" style="display: none;">
	<div class="form-label-group">
		<select class="form-control online_course_id select-multiple-5" name="online_course_id">
			<option value="{{ !empty($get_edit_data->course_id) ? $get_edit_data->course_id : '' }}"></option>
		</select>
		<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
	</div>
</div>
@php $subjects = \App\Batchrelation::with('subject')->where('batch_id', $get_edit_data->batch_id)->get(); @endphp
<div class="col-md-4 col-12" id="online_subject_loader">
	<div class="form-label-group">
		<select class="form-control online_subject_id select-multiple-21" name="online_subject_id">
			<option value=""> - Select Subject - </option>
			@if(count($subjects) > 0)
			@foreach($subjects as $subjectsvalue)
			<option value="{{ $subjectsvalue->subject->id }}" @if($get_edit_data->subject_id == $subjectsvalue->subject->id) selected="selected" @endif>{{ $subjectsvalue->subject->name }}</option>
			@endforeach
			@endif
		</select>
		<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
	</div>
</div>
@php $faculty = \App\Batchrelation::with('user')->where('batch_id', $get_edit_data->batch_id)->where('subject_id', $get_edit_data->subject_id)->get();@endphp
<div class="col-md-4 col-12" id="online_faculty_loader">
	<div class="form-label-group">
		<select class="form-control online_faculty_id select-multiple-3" name="online_faculty_id">
			<option value=""> - Select Faculty - </option>
			@if(count($faculty) > 0)
			@foreach($faculty as $facultyvalue)
			<option value="{{ $facultyvalue->user->id }}" @if($get_edit_data->faculty_id == $facultyvalue->user->id) selected="selected" @endif>{{ $facultyvalue->user->name."(". $facultyvalue->user->register_id .")" }}</option>
			@endforeach
			@endif
		</select>
		<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
		<span class="text-danger" id="online_nt_av"></span>
	</div>
</div>
@php $chapters = \App\Chapter::where('subject_id', $get_edit_data->subject_id)->where('course_id', $get_edit_data->course_id)->get(); @endphp
<div class="col-md-4 col-12 select_chapter" id="online_chapter_loader">
	<div class="form-label-group">
		<select class="form-control online_chapter_id select-multiple-31" name="online_chapter_id">
			<option value=""> - Select Chapter - </option>
			@if(count($chapters) > 0)
			@foreach($chapters as $chaptersvalue)
			<option value="{{ $chaptersvalue->id }}" @if($get_edit_data->chapter_id == $chaptersvalue->id) selected="selected" @endif>{{ $chaptersvalue->name }}</option>
			@endforeach
			@endif
		</select>
		<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
	</div>
</div>
@php $topics = \App\Topic::where('chapter_id', '=', $get_edit_data->chapter_id)->get(); @endphp
<div class="col-md-4 col-12 select_topic">
	<div class="form-label-group">
		<select class="form-control online_topic_id select-multiple-41" name="online_topic_id">
			<option value=""> - Select Topic - </option>
			@if(count($topics) > 0)
			@foreach($topics as $topicsvalue)
			<option value="{{ $topicsvalue->id }}" @if($get_edit_data->topic_id == $topicsvalue->id) selected="selected" @endif>{{ $topicsvalue->name }}</option>
			@endforeach
			@endif

			<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
		</select>
	</div>
</div>




<div class="col-md-4 col-12">
	<div class="form-label-group">
		<input type="date" class="form-control" placeholder="Date" name="online_cdate" value="{{ date(!empty($get_edit_data->cdate) ? $get_edit_data->cdate : 'Y-m-d', strtotime(' +1 day')) }}" autocomplete="off">
	</div>
</div>
<div class="col-md-4 col-12">
	<div class="form-label-group">
		<input type="text" class="form-control timepicker" placeholder="From Time" name="from_time" value="{{ !empty($get_edit_data->from_time) ? $get_edit_data->from_time : '' }}" autocomplete="off">
		<label for="first-name-column">From Time</label>
	</div>
</div>
<div class="col-md-4 col-12">
	<div class="form-label-group">
		<input type="text" class="form-control timepicker" placeholder="To Time" name="to_time" value="{{ !empty($get_edit_data->to_time) ? $get_edit_data->to_time : '' }}" autocomplete="off">
		<label for="first-name-column">To Time</label>
	</div>
</div>
@php $remarks = \App\ClassRemark::where('subject_id', $get_edit_data->subject_id)->first(); @endphp
<div class="col-md-12 col-12 online_show_remark">
	<div class="form-label-group">
		<input type="text" name="online_remark" placeholder="Remark" class="form-control online_remark" value="{{ !empty($remarks->remark) ? $remarks->remark : '' }}">
	</div>
</div>