
<?php 
// $onlinebatch = \App\Batch::where('status', '1')->where('type', 'online')->orderBy('id', 'asc')->get();
$batchs = \App\Batchrelation::with('batch')->select('batch_id')->where('faculty_id', $faculty_id)->groupBy('batch_id')->get();
?>
<div class="row col-md-12 row_1">
	<div class="col-md-3 col-12 batch_online_faculty">
		<div class="form-label-group">
			<select class="form-control batch_id select-multiple-11" name="batch_id[]">
				<option value=""> - Select Batch - </option>
				@if(count($batchs) > 0)
				@foreach($batchs as $value)
				<option value="{{ $value->batch->id }}">{{ $value->batch->name }}</option>
				@endforeach
				@endif
			</select>
			<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
		</div>
	</div>
	<div class="col-md-3 col-12 course_online_faculty" style="display: none;">
		<div class="form-label-group">
			<select class="form-control course_id select-multiple-5" name="course_id[]">
				<option value=""> - Select Course - </option>
			</select>
			<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
		</div>
	</div>

	<div class="col-md-3 col-12 subject_online_faculty">
		<div class="form-label-group">
			<select class="form-control subject_id select-multiple-21" name="subject_id[]">
				<option value=""> - Select Subject - </option>
			</select>
			<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
		</div>
	</div>

	<div class="col-md-3 col-12 faculty_online_faculty">
		<div class="form-label-group">
			<?php //$faculty = \App\User::where('role_id', '2')->where('status', '1')->orderBy('id', 'desc')->get(); ?>
			<select class="form-control faculty_id select-multiple-3" name="faculty_id[]">
				<option value=""> - Select Faculty - </option>
			</select>
			<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
			<span class="text-danger nt_av" id=""></span>
		</div>
	</div>

	<div class="col-md-3 col-12 select_chapter">
		<div class="form-label-group">
			<select class="form-control chapter_id select-multiple-31" name="chapter_id[]">
				<option value=""> - Select Chapter - </option>
			</select>
			<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
		</div>
	</div>
	<div class="col-md-3 col-12 select_topic">
		<div class="form-label-group">
			<select class="form-control topic_id select-multiple-41" name="topic_id[]">
				<option value=""> - Select Topic - </option>
				<i class="fa fa-spinner fa-spin set-loader" style="display: none;"></i>
			</select>
		</div>
	</div>

	<div class="col-md-3 col-12 online_class_type">
		<div class="form-label-group">
			<select class="form-control" name="online_class_type[]">
				<option value=""> - Select Class Type - </option>
				<option value="youtube_live">YouTube Live Classes</option>
				<option value="youtube_premium">YouTube Premium Classes</option>
				<option value="youtube_free_live">YouTube Free Live Classes</option>
				<option value="offline_recorded">Offline Recorded Classes</option>
			</select>
			<span class="text-danger"></span>
		</div>
	</div>



	<div class="col-md-3 col-12">
		<div class="form-label-group">
			<input type="date" class="form-control" placeholder="Date" name="cdate[]" value="{{ date('Y-m-d', strtotime(' +1 day')) }}" autocomplete="off">
		</div>
	</div>
	<div class="col-md-3 col-12">
		<div class="form-label-group">
			<input type="text" class="form-control timepicker" placeholder="From Time" name="from_time[]" value="{{ old('from_time') }}" autocomplete="off">
			<label for="first-name-column">From Time</label>
		</div>
	</div>
	<div class="col-md-3 col-12">
		<div class="form-label-group">
			<input type="text" class="form-control timepicker" placeholder="To Time" name="to_time[]" value="{{ old('to_time') }}" autocomplete="off">
			<label for="first-name-column">To Time</label>
		</div>
	</div>

	<div class="col-md-7 col-12 show_remark" style="display: none;">
		<div class="form-label-group">
			<input type="text" name="remark[]" placeholder="Remark" class="form-control remark" value="">
		</div>
	</div>
	<div class="col-md-2 col-12">
	<a href='javascript:void(0);' title='Remove' class="batch_remove text-danger" style='float: right;'><i class="ficon feather icon-delete"></i> Remove</a>
	</div>

</div>