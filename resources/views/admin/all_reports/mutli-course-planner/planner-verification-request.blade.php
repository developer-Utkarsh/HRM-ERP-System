@extends('layouts.without_login_admin')
@section('content')

<link href="{{ asset('laravel/public/course-planner/styles.css') }}" rel="stylesheet">
<body style="background-color: #FFFEF5;">
	<div class="pvr-page mid-section">
		<div class="top-fix-section"> 	
			<h5 class="heading-page">Planner Verification Request</h5>
			<div class="filter-section">
				<button class="filter-btn" data-value="All">All</button>
				<button class="filter-btn" data-value="Completed">Completed</button>
				<button class="filter-btn" data-value="Pending">Pending</button>
				<button class="filter-btn" data-value="Save_as_draft">Saved as Draft</button>
			</div>
		</div>		
		<div class="request-card-list res-data">
			<!-- AJAX response will be injected here -->
		</div>
	</div>
	<script src="{{ asset('laravel/public/course-planner/filter.js') }}" type="text/javascript"></script>
</body>
@endsection

@section('scripts')
<script type="text/javascript">
	$(document).ready(function () {
		// Default load with value 1 (All)
		loadPlannerData(1);

		// Button click handler
		$('.filter-btn').click(function () {
			let filterValue = $(this).data('value');
			loadPlannerData(filterValue);
		});

		// Function to load planner data via AJAX
		function loadPlannerData(filterValue) {
			$.ajax({
				type: 'POST',
				url: '{{ route('faculty-planner') }}',
				data: {
					'_token': '{{ csrf_token() }}',
					'user_id': '<?= $user_id ?>',
					'status': filterValue
				},
				dataType: 'html',
				success: function (data) {
					$('.res-data').empty().html(data);
				}
			});
		}
	});
</script>
@endsection
