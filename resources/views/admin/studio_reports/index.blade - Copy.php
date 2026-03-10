@extends('layouts.admin')
@section('content')
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Studio Report</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">List View</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ route('admin.studio-reports') }}" method="get" name="filtersubmit">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Studio</label>
											<?php $studios = \App\Studio::where('status', '1')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple3" name="studio_id" id="studio_id">
													<option value="">Select Any</option>
													@if(count($studios) > 0)
													@foreach($studios as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('studio_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										
										<!--div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Branch</label>
											<?php $branches = \App\Branch::where('status', '1')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1" name="branch_id">
													<option value="">Select Any</option>
													@if(count($branches) > 0)
													@foreach($branches as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('branch_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Assistants</label>
											<?php $assistants = \App\User::where('role_id', '3')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple2" name="assistant_id">
													<option value="">Select Any</option>
													@if(count($assistants) > 0)
													@foreach($assistants as $key => $value)
													<option value="{{ $value->id }}" @if($value->id == app('request')->input('assistant_id')) selected="selected" @endif>{{ $value->name }}</option>
													@endforeach
													@endif
												</select>												
											</fieldset>
										</div-->
										<div class="col-12 col-sm-6 col-lg-5">
											<label for="" style="">&nbsp;</label>
											<fieldset class="form-group">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('admin.studio-reports') }}" class="btn btn-warning">Reset</a>
											<a href="javascript:void(0)" id="download_pdf" class="btn btn-primary">Export in PDF</a>
											</fieldset>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
				
				<?php if (!empty($get_studios)) { ?>
		<?php foreach ($get_studios as $value) { ?>
            <table class="table data-list-view" style=''>
			 
				<head>
                    <tr style="">
                        <th colspan="3"><b>Studio Name : <?php echo $value->name; ?></b></th>
                        <th colspan="3"><b>Assistant Name : <?php echo isset($value->assistant->name) ?  $value->assistant->name : ''; ?></b></th>
                        <th colspan="3"><b>Assistant Mob. : <?php echo isset($value->assistant->mobile) ?  $value->assistant->mobile : ''; ?></b></th>
                    </tr>
                </head>
                <head>
                    <tr style="">
                        <th scope="col">From Time</th>
                        <th scope="col">To Time</th>
                        <th scope="col">Date</th>
                        <th scope="col">Faculty Name</th>
                        <th scope="col">Batch Name</th>
                        <th scope="col">Course Name</th>
                        <th scope="col">Subject Name</th>
                        <th scope="col">Chapter Name</th>
                        <th scope="col">Topic Name</th>
                    </tr>
                </head>
                <body>
					<?php
					foreach($value->timetable as $key => $timetable){
					?>
                        <tr>
                            <td><?php echo isset($timetable->from_time) ?  $timetable->from_time : '' ?></td>
							<td><?php echo isset($timetable->to_time) ?  $timetable->to_time : '' ?></td>
							<td><?php echo isset($timetable->cdate) ?  $timetable->cdate : '' ?></td>
							<td><?php echo isset($timetable->faculty->name) ?  $timetable->faculty->name : '' ?></td>
							<td><?php echo isset($timetable->batch->name) ?  $timetable->batch->name : '' ?></td>
							<td><?php echo isset($timetable->course->name) ?  $timetable->course->name : '' ?></td>
							<td><?php echo isset($timetable->subject->name) ?  $timetable->subject->name : '' ?></td>
							<td><?php echo isset($timetable->chapter->name) ?  $timetable->chapter->name : '' ?></td>
							<td><?php echo isset($timetable->topic->name) ?  $timetable->topic->name : '' ?></td>
                        </tr>
					<?php
					}
					?>
                </body>
			
            </table>
			<p><hr/></p>
			<?php } ?>
        <?php } ?>
		<style>
		hr{background:#000;}
		</style>
					<!--table class="table data-list-view">
						<thead>
							<tr>
								<th>S. No.</th>
								<th>Studio Name</th>
								<th>Studio Assistant</th>
								<th>Branch</th>
								<th>From Time</th>
								<th>To Time</th>
								<th>Date</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach($get_studios as  $key => $value){
							
							foreach($value->timetable as $key => $timetable){
								$temp['from_time'] = $timetable->from_time;
								$temp['to_time'] = $timetable->to_time;
								$temp['date'] = $timetable->cdate;
								$temp['faculty_name'] = !empty($timetable->faculty->name)?$timetable->faculty->name:'';
								
								$get_faculty_timetables['classes'][] = $temp;
							?>
							<tr>
								<td>{{ $key + 1 }}</td>
								<td class="">{{ $value->name }}</td>
								<td class="product-category">{{ isset($value->assistant->name) ?  $value->assistant->name : '' }}</td>
								<td class="product-category">{{ isset($value->branch->name) ?  $value->branch->name : '' }}</td>
								<td class="product-category">{{ isset($timetable->from_time) ?  $timetable->from_time : '' }}</td>
								<td class="product-category">{{ isset($timetable->to_time) ?  $timetable->to_time : '' }}</td>
								<td class="product-category">{{ isset($timetable->cdate) ?  $timetable->cdate : '' }}</td>
								
								
							</tr>
							<?php
							}
							?>
							<?php 
							}
							?>
						</tbody>
					</table-->
				</div>                   
			</section>
		</div>
	</div>
</div>
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
	
	$("body").on("click", "#download_pdf", function (e) {
		/* if ($userTable.data().count() == 0) {
			swal("Warning!", "Not have any data!", "warning");
			return;
		} */
		var data = {};
			data.studio_id = $('#studio_id').val(),
		window.location.href = "<?php echo URL::to('/admin/'); ?>/studio-report-pdf?" + Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
		}).join('&');
	});
</script>
@endsection
