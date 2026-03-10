@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div> 
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-8 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Planner View</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
							</ol>
						</div>
					</div> 
				</div>  
			</div>
			<div class="content-header-right text-md-right col-md-4 col-12 d-md-block">
			</div>
		</div>
		<div class="content-body">
			<section id="multiple-column-form">
				<div class="row match-height">
					<div class="col-12">
						<div class="card">
							<div class="card-content collapse show">
								<div class="card-body">
									<div class="users-list-filter">
										@if(count($topic_relation) > 0)
											@foreach($topic_relation as $subject_id => $topics)
												<table class="table table-bordered">
													
													<thead>
														<tr>
															<th colspan="10">
																<h5 class="mb-0"><strong>Subject: {{ $topics[0]->subject_name }}</strong></h5>
															</th>
														</tr>
														<tr>
															<th>Topic</th>
															<th>Sub Topic</th>
															<th>Duration</th>
														</tr>
													</thead>
												
													@foreach($topics as $tr)
													<tr>
														<td>
															{{ $tr->topic_name }} 
														</td>
														<td>
															{{ $tr->sub_topic_name }}
														</td>														
														<td>
															{{ $tr->duration }}
														</td>
													</tr>
													@endforeach
												</table>
											@endforeach
										@else
											<div class="alert alert-warning text-center">
												No planner uploaded.
											</div>
										@endif
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
			<!-- // Basic Floating Label Form section end -->
		</div>
	</div>
</div>
@endsection

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
/*
$(document).ready(function () {
    $('.filter-section').first().show();

    $(document).on('click', '.add-more', function () {
        let clone = $('#filter-section').first().clone();

        clone.find('select').val('');
        clone.find('input').val('');

        clone.find('.sub_topic').html('<option value="">Select Sub Topic</option>');

        clone.find('.add-more').hide();
        clone.find('.remove-section').show();

        $('.filter-section').last().before(clone);
    });

    // Remove section
    $(document).on('click', '.remove-section', function () {
        $(this).closest('.filter-section').remove();
    });
});
*/
$(document).ready(function () {
    $('.filter-section').first().show();

    $(document).on('click', '.add-more', function () {
        let currentBlock = $(this).closest('.subject-form-block');
        let firstSection = currentBlock.find('.filter-section').first();
        let clone = firstSection.clone();

        clone.find('select').val('');
        clone.find('input').val('');
        clone.find('.sub_topic').html('<option value="">Select Sub Topic</option>');

        clone.find('.add-more').hide();
        clone.find('.remove-section').show();
        currentBlock.find('.filter-section').last().after(clone);
    });

    $(document).on('click', '.remove-section', function () {
        $(this).closest('.filter-section').remove();
    });
});





$(document).on("change", ".topic_id", function () {
    var $this = $(this); // current changed select
    var topic_id = $this.val(); // since it's a select, .val() gives selected value directly

    if (topic_id) {
        $.ajax({
            type: 'POST',
            url: '{{ route('admin.multi-course-planner.get-sub-topic') }}',
            data: {
                '_token': '{{ csrf_token() }}',
                'topic_id': topic_id
            },
            dataType: 'html',
            success: function (data) {
                // find the closest .sub_topic inside the same row/group
                $this.closest('.row').find('.sub_topic').html(data);
            }
        });
    }
});
</script>
<script type="text/javascript">
/*
$(document).ready(function () {
    $('.submit-btn').click(function (e) {
        e.preventDefault();

        let dataId = $(this).data('id');
        let form = $('#facultyTimeForm');
        let formData = form.serialize() + '&submit_type=' + dataId;
						
        $.ajax({
            url: '{{ route("admin.multi-course-planner.save-planner-summary") }}', // double quotes for Blade inside JS
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            },
            success: function (response) {
                alert(response.message);
                location.reload();
            },
            error: function (xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || xhr.statusText));
            }
        });
    });
});
*/
$(document).ready(function () {
    $('.submit-btn').on('click', function (e) {
        e.preventDefault();
        
        const button = $(this);
        const submitType = button.data('id');
        const subjectBlock = button.closest('.subject-form-block');

        const inputs = subjectBlock.find(':input[name]');
        const formData = new URLSearchParams();

        inputs.each(function () {
            const input = $(this);
            const name = input.attr('name');
            const value = input.val();

            if (name.endsWith('[]')) {
                formData.append(name, value);
            } else {
                formData.set(name, value);
            }
        });

        formData.append('submit_type', submitType);

        $.ajax({
            url: '{{ route("admin.multi-course-planner.save-planner-summary") }}',
            type: 'POST',
            data: formData.toString(),
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            },
            success: function (response) {
                alert(response.message);
                location.reload();
            },
            error: function (xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || xhr.statusText));
            }
        });
    });
});

</script>

<script>
	function setSubjectId(select) {
		const subjectId = select.options[select.selectedIndex].getAttribute('data-id');		
		const hiddenInput = select.closest('.form-group').querySelector('.subject_id');
		if (hiddenInput) {
			hiddenInput.value = subjectId;
		}
	}
</script>
@endsection
