@php 
	$status = 'Pending';
	if ($items->topic_sme_status == 2) {
		$status = 'Save As Draft';
	} elseif ($items->topic_sme_status == 1) {
		$status = 'Submit';
	}
@endphp
<div class="row">
	<div class="col-lg-6">
		<h5 class="mb-2">Subject: 
		<span class="text-danger">{{ $items->subject_name ?? 'N/A' }} - {{ $items->sme_name ?? ''}}</span></br>
		Faculty Comment : {{ $items->faculty_remark ?? '-'}}</h5>
	</div>
	<div class="col-lg-6 text-right">
		<h5 class="mb-2"><strong>SME Status: <span class="text-danger">{{ $status }}</span></strong></h5>
	</div>
</div>

@php 
	$get_topic = DB::table('course_planner_topic_relation')->where('subject_id',$items->subject_id)->where('req_id',$items->req_id)->get();
@endphp

@foreach($get_topic as $tr)
<input type="hidden" name="sr_id[]" value="{{ $tr->id }}"/>
<div class="row">
	<div class="col-12 col-sm-6 col-lg-4">
		<label>Topic</label>
		<fieldset class="form-group">											
			<select name="topic_id[]" class="topic_id form-control select-multiple1" onchange="setSubjectId(this);">
				<option value="">-- Select --</option>
				@foreach($topic as $to)
					@if($to->subject_id == $tr->subject_id)
						<option value="{{ $to->id }}" data-id="{{ $to->subject_id }}" {{ $tr->topic_id == $to->id ? 'selected' : '' }}>							
							{{ $to->name }}{{ (!empty($to->name) || !empty($to->en_name)) ? ' || ' . $to->en_name : '' }}
						</option>
					@endif
				@endforeach
			</select>
			<input type="hidden" name="subject_id[]" class="subject_id" value="{{ $tr->subject_id }}"/>
		</fieldset>
	</div>

	<div class="col-12 col-sm-6 col-lg-3 d-none">
		<label>Sub Topic</label>
		<fieldset class="form-group">										
			<select name="sub_topic[]" class="sub_topic form-control select-multiple1">
				<option value="">-- Select --</option>
				@php
					$sub_topic = DB::table('sub_topic_master')
						->where('topic_id', $tr->topic_id)
						->where('status', 1)
						->get();
				@endphp
				@foreach($sub_topic as $st)
					<option value="{{ $st->id }}" {{ $tr->sub_topic_id == $st->id ? 'selected' : '' }}>
						{{ $st->name }}{{ (!empty($st->name) || !empty($st->en_name)) ? ' || ' . $st->en_name : '' }}
					</option>

				@endforeach
			</select>
		</fieldset>
	</div>
	
	@php $total_duration += (int) $tr->duration; @endphp
	<div class="col-12 col-sm-6 col-lg-4">
		<label>Time (In Minutes)</label>
		<fieldset class="form-group">											
			<input type="number" value="{{ $tr->duration }}" name="duration[]" class="form-control" readonly/>
		</fieldset>
	</div>		
</div>
@endforeach
