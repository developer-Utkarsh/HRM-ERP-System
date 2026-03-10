@extends('layouts.without_login_admin')
@section('content')

<link href="{{ asset('laravel/public/course-planner/styles.css') }}" rel="stylesheet">
<body style="background-color: #FFFEF5;">
	<div class="pvr-detail mid-section"> 
    <!-- Header Section -->
    <div class="header">
		<a href="{{ route('faculty-planner-verification') }}?user_id={{$user_id}}">
			<button class="back-button">
				<img src="{{ asset('laravel/public/course-planner/back-arrow.svg') }}" alt="Back">
			</button>
		</a>
    </div>
    <!-- Main Content Section -->
    <div class="mcontent">
        <!-- Planner Information -->
        <div class="planner-info">
            <div class="label">Planner For</div>
            <div class="value"><?=$course;?> - <?=$planner_name;?></div>
        </div>
		
		<div class="planner-info np-sec">
            <div class="label">Subject</div>
            <div class="value"><?=$subject;?></div>
        </div>

        <!-- Subject Information -->
        <div class="subject">
			<form id="facultyRemarkForm" method="POST">
				<div> 
					<div class="value">
						<textarea name="faculty_remark" class="form-control">{{ $topic_relation[0]->faculty_remark??'' }}</textarea>
						<input type="hidden" name="faculty_id" value="{{ $user_id }}"/>
						<input type="hidden" name="req_id" value="{{ $req_id }}"/>
						<input type="hidden" name="cpsr_id" value="{{ $cpsr_id }}"/>
					</div>
					<span class="edit-icon remark-btn"><img src="{{ asset('laravel/public/course-planner/edit.svg') }}" alt=""/></span>
				</div>
            </form>
        </div>

        <!-- Topics Table -->
		<form id="facultyTimeForm" method="POST">
			@csrf
			<div class="scroll-table">
				<table class="topic-table">
					<thead>
						<tr>
							<th>Topic Name</th>
							<th>Sub Topic</th>
							<th>Assign Time (min)</th>
						</tr>
					</thead>
					<tbody>
						@foreach($topic_relation as $tr)
							<tr>
								<td>{{ $tr->topic_name }}</td>
								<td>{{ $tr->sub_topic_name }}</td>
								<td>
									<input type="number" class="time-input" name="duration[]" value="{{ $tr->duration ?? 0 }}" {{ $tr->fstatus == 1 ? 'readonly' : '' }}>

									<input type="hidden" name="tr_id[]" value="{{ $tr->id }}">
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>

			@php
				$hasEditable = collect($topic_relation)->contains(function($item) {
					return $item->fstatus != 1;
				});
			@endphp

			@if($hasEditable)
				<div class="form-actions">
					<button type="button" class="submit-btn btn btn-secondary text-dark" data-id="2">Save as Draft</button>
					<button type="button" class="submit-btn btn btn-primary" data-id="1">Submit</button>
				</div>
			@endif
		</form>


    </div>
	</div>
</body>
<script src="{{ asset('laravel/public/course-planner/filter.js') }}" type="text/javascript"></script>
@endsection
@section('scripts')
<script type="text/javascript">
$(document).ready(function () {
    $('.submit-btn').click(function (e) {
        e.preventDefault();

        let dataId = $(this).data('id');
        let form = $('#facultyTimeForm');
        let formData = form.serialize() + '&submit_type=' + dataId;
        $.ajax({
            url: '{{ route("faculty-add-time") }}', // double quotes for Blade inside JS
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            },
            success: function (response) {
                alert('Time data submitted successfully!');
                location.reload();
            },
            error: function (xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || xhr.statusText));
            }
        });
    });
});


$(document).ready(function () {
    $('.remark-btn').click(function (e) {
        e.preventDefault();
        let form = $('#facultyRemarkForm');
        let formData = form.serialize();
        $.ajax({
            url: '{{ route("faculty-add-remark") }}',
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            },
            success: function (response) {
                alert('Remark updated successfully!');
                location.reload();
            },
            error: function (xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || xhr.statusText));
            }
        });
    });
});
</script>
@endsection
