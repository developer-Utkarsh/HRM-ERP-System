@foreach($topic_relation as $items)
	@php $total_duration = 0; @endphp
	
	<div class="subject-form-block" data-subject="{{ $items->subject_id }}">
		<input type="hidden" name="req_id" value="{{ $record[0]->id??0 }}">
		<input type="hidden" name="course_id" value="{{ $record[0]->course_id??0 }}">
													
		<!-- Subject Wise Add -->
		@include('admin.multi-course-planner.subject_wise')
		
		<!-- your existing subject HTML -->
		
		<?php if(($items->is_subject!=1 && Auth::user()->role_id==21) || Auth::user()->id==8232 || Auth::user()->id==8866){ ?>
		<div class="row filter-section" id="filter-section">
			@php
				$topicn = DB::table('topic_master')
					->orderBy('id', 'desc')
					->where('subject_id', $items->subject_id)
					->where('status', 1)
					->get();
			@endphp
			
			<div class="col-12 col-sm-6 col-lg-3">
				<label for="users-list-role">Topic</label>
				<fieldset class="form-group">
					<select class="form-control select-multiple topic_id" name="topic_id[]" onchange="setSubjectId(this);">

						<option value="">Select Topic</option>
						@foreach($topicn as $to)
						<option value="{{ $to->id }}" data-id="{{ $to->subject_id }}">
							{{ $to->name }}{{ (!empty($to->name) || !empty($to->en_name)) ? ' || ' . $to->en_name : '' }}
						</option>
						@endforeach
					</select>
					<input type="hidden" name="subject_id[]" class="subject_id" />
				</fieldset>
			</div>

			<div class="col-12 col-sm-6 col-lg-3 d-none">
				<label for="users-list-status">Sub Topic</label>
				<fieldset class="form-group">												
					<select class="form-control select-multiple sub_topic" name="sub_topic[]">
						@if(!empty(old('topic_id')))
							@php
								$subtopicData = DB::table('sub_topic_master')->where('topic_id', old('topic_id'))->get();
							@endphp
							@foreach ($subtopicData as $key => $subtopicDataValue)
								<option value="{{ $subtopicDataValue->id }}" {{ old('sub_cat_id', !empty(old('cat_id')) && $subtopicDataValue->id == old('cat_id') ? 'selected' : '' ) }}>									
									{{ $subtopicDataValue->name }}{{ (!empty($subtopicDataValue->name) || !empty($subtopicDataValue->en_name)) ? ' || ' . $subtopicDataValue->en_name : '' }}
								</option>
							@endforeach
						@else
							<option value="">Select Sub Topic</option>
						@endif
					</select>											
				</fieldset>
			</div>
			<?php if(Auth::user()->id==8232 || Auth::user()->id==8866){ ?>
			<div class="col-12 col-sm-6 col-lg-3">
				<label>Duration</label>
				<fieldset class="form-group">
					<input type="number" name="duration[]" class="form-control duration" value="{{ old('duration') }}">
				</fieldset>
			</div>
			<?php } ?>
			<div class="col-12 col-sm-6 col-lg-1">
				<label for="">&nbsp;</label>
				<fieldset class="form-group">
					<button type="button" class="btn btn-sm p-1 btn-primary add-more">Add</button>
					<button type="button" class="btn btn-sm p-1 btn-danger remove-section" style="display:none;">Remove</button>
				</fieldset>
			</div>
		</div>
		
		<!-- place your submit buttons here -->
		<button type="button" class="submit-btn btn btn-secondary submit" data-id="2">Saved As Draft</button>
		<button type="button" class="submit-btn btn btn-primary submit" data-id="1">Submit</button>
		<?php } ?>
	</div>
	@if(!empty($total_duration))
	<div class="row">
		<div class="col-12 text-right">
			<h6><strong>Total Duration: <span class="text-primary">{{ $total_duration }}</span> mins</strong></h6>
		</div>
	</div>
	@endif
	<hr>
@endforeach
