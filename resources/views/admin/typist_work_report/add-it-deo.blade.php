@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-12 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-8">
						<h2 class="content-header-title float-left mb-0">Add Work Report</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
					<div class="col-4 text-right">
					
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				<div class="table-responsive">
					<form action="{{ route('admin.save-it-deo-work') }}" method="post">
						@csrf
						<table class="table data-list-view">
							<thead>
								<tr>
									<th class="w-60">Task Name</th>
									<th>Number Of</th>
									<th>Paper Type</th>
									<th>Time</th>						
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><input type="text" name="question[]" value="Test Paper/Model Paper/Quiz Upload" class="form-control"/></td>
									<td><input type="text" name="numberof[]" value="0" class="form-control"/></td>
									<td><input type="text" name="papertype[]" value="0" class="form-control"/></td>
									<td><input type="text" name="timeof[]" value="0" class="form-control"/></td>
								</tr>
								<tr>
									<td><input type="text" name="question[]" value="Test Paper/Model Paper/Quiz Correction" class="form-control"/></td>
									<td><input type="text" name="numberof[]" value="0" class="form-control"/></td>
									<td><input type="text" name="papertype[]" value="0" class="form-control"/></td>
									<td><input type="text" name="timeof[]" value="0" class="form-control"/></td>					
								</tr>
								<tr>
									<td><input type="text" name="question[]" value="Notes/Notice Upload" class="form-control"/></td>
									<td><input type="text" name="numberof[]" value="0" class="form-control"/></td>
									<td><input type="text" name="papertype[]" value="0" class="form-control"/></td>
									<td><input type="text" name="timeof[]" value="0" class="form-control"/></td>						
								</tr>
								<tr>
									<td><input type="text" name="question[]" value="Test Result Excel Download" class="form-control"/></td>
									<td><input type="text" name="numberof[]" value="0" class="form-control"/></td>
									<td><input type="text" name="papertype[]" value="0" class="form-control"/></td>
									<td><input type="text" name="timeof[]" value="0" class="form-control"/></td>												
								</tr>
								<tr>
									<td><input type="text" name="question[]" value="Live Class Schedule" class="form-control"/></td>
									<td><input type="text" name="numberof[]" value="0" class="form-control"/></td>
									<td><input type="text" name="papertype[]" value="0" class="form-control"/></td>
									<td><input type="text" name="timeof[]" value="0" class="form-control"/></td>					
								</tr>
								<tr>
									<td><input type="text" name="question[]" value="Recorded Class Upload" class="form-control"/></td>
									<td><input type="text" name="numberof[]" value="0" class="form-control"/></td>
									<td><input type="text" name="papertype[]" value="0" class="form-control"/></td>
									<td><input type="text" name="timeof[]" value="0" class="form-control"/></td>						
								</tr>
								<tr>
									<td><input type="text" name="question[]" value="Test Video Solution/Weekly Test Video Solution/Quiz Video Solution Upload" class="form-control"/></td>
									<td><input type="text" name="numberof[]" value="0" class="form-control"/></td>
									<td><input type="text" name="papertype[]" value="0" class="form-control"/></td>
									<td><input type="text" name="timeof[]" value="0" class="form-control"/></td>						
								</tr>
								<tr>
									<td><input type="text" name="question[]" value="Topic Change/Topic Update" class="form-control"/></td>
									<td><input type="text" name="numberof[]" value="0" class="form-control"/></td>
									<td><input type="text" name="papertype[]" value="0" class="form-control"/></td>
									<td><input type="text" name="timeof[]" value="0" class="form-control"/></td>					
								</tr>
								<tr>
									<td><input type="text" name="question[]" value="Live Class Subject/Faculty Update" class="form-control"/></td>
									<td><input type="text" name="numberof[]" value="0" class="form-control"/></td>
									<td><input type="text" name="papertype[]" value="0" class="form-control"/></td>
									<td><input type="text" name="timeof[]" value="0" class="form-control"/></td>						
								</tr>
								<tr>
									<td><input type="text" name="question[]" value="Course Match According to Server" class="form-control"/></td>
									<td><input type="text" name="numberof[]" value="0" class="form-control"/></td>
									<td><input type="text" name="papertype[]" value="0" class="form-control"/></td>
									<td><input type="text" name="timeof[]" value="0" class="form-control"/></td>						
								</tr>
								<tr>
									<td><input type="text" name="question[]" value="Solve Queries" class="form-control"/></td>
									<td><input type="text" name="numberof[]" value="0" class="form-control"/></td>
									<td><input type="text" name="papertype[]" value="0" class="form-control"/></td>
									<td><input type="text" name="timeof[]" value="0" class="form-control"/></td>						
								</tr>
								<tr>
									<td><input type="text" name="question[]" value="Create Topics" class="form-control"/></td>
									<td><input type="text" name="numberof[]" value="0" class="form-control"/></td>
									<td><input type="text" name="papertype[]" value="0" class="form-control"/></td>
									<td><input type="text" name="timeof[]" value="0" class="form-control"/></td>					
								</tr>
								<tr>
									<td><input type="text" name="question[]" value="Notes/Quiz Copy in Another Book" class="form-control"/></td>
									<td><input type="text" name="numberof[]" value="0" class="form-control"/></td>
									<td><input type="text" name="papertype[]" value="0" class="form-control"/></td>
									<td><input type="text" name="timeof[]" value="0" class="form-control"/></td>						
								</tr>
								<tr>
									<td><input type="text" name="question[]" value="PDF Upload" class="form-control"/></td>
									<td><input type="text" name="numberof[]" value="0" class="form-control"/></td>
									<td><input type="text" name="papertype[]" value="0" class="form-control"/></td>
									<td><input type="text" name="timeof[]" value="0" class="form-control"/></td>						
								</tr>
								<tr>
									<td><input type="text" name="question[]" value="Other" class="form-control"/></td>
									<td><input type="text" name="numberof[]" value="0" class="form-control"/></td>
									<td><input type="text" name="papertype[]" value="0" class="form-control"/></td>
									<td><input type="text" name="timeof[]" value="0" class="form-control"/></td>						
								</tr>
							</body>
						</table>
						
						<button type="submit" class="btn btn-primary">Submit</button>
					</form>					 
				</div>       

			</section>
		</div>
	</div>
</div>

<style>
	input{
		font-size:13px;
	}
	
	.w-60 {
		width:60%;
	}
</style>
@endsection

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.select-multiple1').select2({
			placeholder: "Select Any",
			allowClear: true
		});
		$('.select-multiple2').select2({
			placeholder: "Select Any",
			allowClear: true
		});
		$('.select-multiple3').select2({
			placeholder: "Select Any",
			allowClear: true
		});
	});
	
	$("body").on("click", "#download_typist_work_excel", function (e) {
		var data = {};
			data.emp_id = $('.emp_id').val(),
			data.fdate = $('.fdate').val(),
			data.tdate = $('.tdate').val(),
		window.location.href = "<?php echo URL::to('/admin/'); ?>/typist-work-report-excel?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});
</script>

@endsection
